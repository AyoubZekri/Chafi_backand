<?php

namespace App\Http\Controllers\AppLication;

use App\Function\Respons;
use App\Http\Controllers\Controller;
use App\Models\ChatConversation;
use App\Models\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * المساعد الذكي: المحادثات تُحفظ هنا، والإجابة تأتي من خدمة Python (ai_service)
 */
class ChatController extends Controller
{
    /** قائمة محادثات المستخدم (الأحدث أولاً) */
    public function conversations(Request $request)
    {
        try {
            $conversations = ChatConversation::where('user_id', $request->user()->id)
                ->orderByDesc('updated_at')
                ->get(['id', 'title', 'created_at', 'updated_at']);

            return Respons::success($conversations);
        } catch (\Throwable $e) {
            return Respons::error($e->getMessage());
        }
    }

    /** رسائل محادثة واحدة */
    public function messages(Request $request)
    {
        $request->validate(['conversation_id' => 'required|integer']);

        try {
            $conversation = $this->findOwned($request, $request->conversation_id);
            if (!$conversation) {
                return Respons::error('المحادثة غير موجودة', 404);
            }

            $messages = $conversation->messages()
                ->orderBy('id')
                ->get(['id', 'role', 'content', 'sources', 'calculations', 'options', 'created_at']);

            return Respons::success($messages);
        } catch (\Throwable $e) {
            return Respons::error($e->getMessage());
        }
    }

    /** إرسال رسالة: بدون conversation_id تُنشأ محادثة جديدة */
    public function send(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:2000',
            'conversation_id' => 'nullable|integer',
        ]);

        $conversation = null;
        if ($request->filled('conversation_id')) {
            $conversation = $this->findOwned($request, $request->conversation_id);
            if (!$conversation) {
                return Respons::error('المحادثة غير موجودة', 404);
            }
        }

        return $this->exchange($request, $conversation, trim($request->message));
    }

    /**
     * تعديل سؤال سابق (مثل ChatGPT): يُحذف السؤال القديم وكل ما بعده من المحادثة،
     * ثم يُرسل السؤال المعدّل مع المحادثة التي قبله فقط
     */
    public function edit(Request $request)
    {
        $request->validate([
            'conversation_id' => 'required|integer',
            'message_id' => 'required|integer',
            'message' => 'required|string|max:2000',
        ]);

        $conversation = $this->findOwned($request, $request->conversation_id);
        if (!$conversation) {
            return Respons::error('المحادثة غير موجودة', 404);
        }
        $target = $conversation->messages()
            ->where('id', $request->message_id)
            ->where('role', 'user')
            ->first();
        if (!$target) {
            return Respons::error('الرسالة غير موجودة', 404);
        }

        return $this->exchange($request, $conversation, trim($request->message), $target);
    }

    /**
     * يرسل السؤال إلى المساعد ويحفظ السؤال والجواب.
     * [$editing]: سؤال سابق يُستبدل (هو وما بعده) بالسؤال الجديد وجوابه
     */
    private function exchange(Request $request, ?ChatConversation $conversation, string $message,
                              ?ChatMessage $editing = null)
    {
        // النموذج على المعالج قد يطول: لا حد لتنفيذ PHP، والانتظار حتى 10 دقائق
        @set_time_limit(0);

        try {
            $user = $request->user();

            // حد يومي للرسائل لكل مستخدم (السؤال المعدَّل يحل محل القديم فلا يُحسب مرتين)
            $limit = (int) config('services.ai_chat.daily_limit', 30);
            $sentToday = ChatMessage::where('role', 'user')
                ->whereDate('chat_messages.created_at', today())
                ->whereHas('conversation', fn ($q) => $q->where('user_id', $user->id))
                ->count();
            if ($editing && $editing->created_at?->isToday()) {
                $sentToday--;
            }
            if ($limit > 0 && $sentToday >= $limit) {
                return Respons::error('وصلت إلى الحد اليومي للأسئلة، حاول غداً', 429);
            }

            // آخر رسائل المحادثة لفهم السياق (عند التعديل: ما قبل السؤال المعدَّل فقط)
            $history = [];
            if ($conversation) {
                $query = $conversation->messages();
                if ($editing) {
                    $query->where('id', '<', $editing->id);
                }
                $history = $query->orderByDesc('id')->limit(8)->get(['role', 'content'])
                    ->reverse()->values()->toArray();
            }

            $response = Http::timeout(600)
                ->withHeaders(['X-AI-Key' => (string) config('services.ai_chat.key')])
                ->post(rtrim(config('services.ai_chat.url'), '/') . '/chat', [
                    'message' => $message,
                    'history' => $history,
                ]);

            if (!$response->successful()) {
                Log::error('AI chat service error', ['status' => $response->status(), 'body' => $response->body()]);
                return Respons::error('ChafiBot غير متاح حالياً، حاول لاحقاً', 503);
            }

            $ai = $response->json();

            // حتى "لم أجد إجابة" رسالة عادية في المحادثة (بصياغة لطيفة من المساعد)، لا خطأ
            // الحفظ بعد نجاح الإجابة فقط، حتى لا يبقى سؤال بدون جواب
            [$conversation, $userMessage, $assistantMessage] = DB::transaction(
                function () use ($conversation, $user, $message, $ai, $editing) {
                    // التعديل: السؤال القديم وكل ما بعده يُستبدل
                    if ($editing) {
                        $conversation->messages()->where('id', '>=', $editing->id)->delete();
                    }
                    if (!$conversation) {
                        $conversation = ChatConversation::create([
                            'user_id' => $user->id,
                            'title' => Str::limit($message, 60),
                        ]);
                    }

                    $userMessage = $conversation->messages()->create([
                        'role' => 'user',
                        'content' => $message,
                    ]);
                    $assistantMessage = $conversation->messages()->create([
                        'role' => 'assistant',
                        'content' => $ai['answer'] ?? '',
                        'sources' => $ai['sources'] ?? [],
                        'calculations' => $ai['calculations'] ?? [],
                        // مواضيع مقترحة عندما لا يفهم المساعد السؤال
                        'options' => $ai['options'] ?? [],
                    ]);

                    // حتى تظهر المحادثة أولاً في القائمة
                    $conversation->touch();

                    return [$conversation, $userMessage, $assistantMessage];
                }
            );

            return Respons::success([
                'conversation' => $conversation->only(['id', 'title', 'created_at', 'updated_at']),
                'user_message' => $userMessage,
                'message' => $assistantMessage,
                // عند التعديل: رقم الرسالة التي حُذفت منها المحادثة (هي وما بعدها)
                'replaced_from' => $editing?->id,
                'remaining_today' => $limit > 0 ? max(0, $limit - $sentToday - 1) : null,
            ]);
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('AI chat service unreachable', ['error' => $e->getMessage()]);
            return Respons::error('ChafiBot غير متاح حالياً، حاول لاحقاً', 503);
        } catch (\Throwable $e) {
            return Respons::error($e->getMessage());
        }
    }

    /** إعادة تسمية محادثة */
    public function rename(Request $request)
    {
        $request->validate([
            'conversation_id' => 'required|integer',
            'title' => 'required|string|max:255',
        ]);

        try {
            $conversation = $this->findOwned($request, $request->conversation_id);
            if (!$conversation) {
                return Respons::error('المحادثة غير موجودة', 404);
            }
            $conversation->update(['title' => $request->title]);

            return Respons::success($conversation->only(['id', 'title', 'created_at', 'updated_at']));
        } catch (\Throwable $e) {
            return Respons::error($e->getMessage());
        }
    }

    /** حذف محادثة مع رسائلها */
    public function delete(Request $request)
    {
        $request->validate(['conversation_id' => 'required|integer']);

        try {
            $conversation = $this->findOwned($request, $request->conversation_id);
            if (!$conversation) {
                return Respons::error('المحادثة غير موجودة', 404);
            }
            $conversation->delete();

            return Respons::success(null, 'تم الحذف');
        } catch (\Throwable $e) {
            return Respons::error($e->getMessage());
        }
    }

    /** المستخدم يصل فقط إلى محادثاته */
    private function findOwned(Request $request, $conversationId): ?ChatConversation
    {
        return ChatConversation::where('id', $conversationId)
            ->where('user_id', $request->user()->id)
            ->first();
    }
}
