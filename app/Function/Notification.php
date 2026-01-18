<?php


namespace App\Function;

use App\Models\Notifications;
use App\Models\User;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FirebaseNotification;
use Kreait\Firebase\Exception\MessagingException;

class Notification
{
    protected $messaging;

    public function __construct()
    {
        $this->messaging = app('firebase.messaging');
    }

    /**
     * إرسال إشعار FCM + حفظه في قاعدة البيانات
     *
     * @param string $fcmToken
     * @param array $tokens
     * @param array $topic
     * @param string $title
     * @param string $body
     * @param int $userId
     * @param array $userIds
     * @return array
     */
    public function sendNotification(string $fcmToken, string $title, string $body, int $userId, array $data = []): array
    {
        try {
            $notification = FirebaseNotification::create($title, $body);

            // دمج البيانات الإضافية مع الإشعار
            $message = CloudMessage::withTarget('token', $fcmToken)
                ->withNotification($notification)
                ->withData($data); 

            $this->messaging->send($message);

            Notifications::create([
                'title' => $title,
                'content' => $body,
                'is_read' => false,
                'user_id' => $userId,
            ]);

            return ['status' => true, 'message' => 'تم الإرسال والحفظ بنجاح'];
        } catch (MessagingException $e) {
            return ['status' => false, 'message' => 'فشل الإرسال', 'error' => $e->getMessage()];
        } catch (\Throwable $e) {
            return ['status' => false, 'message' => 'خطأ غير متوقع', 'error' => $e->getMessage()];
        }
    }

    public function sendNotificationToTopic(string $topic, string $title, string $body): array
    {
        try {
            $notification = FirebaseNotification::create($title, $body);

            $message = CloudMessage::withTarget('topic', $topic)
                ->withNotification($notification)
                ->withData([
                    'pagename' => $title,
                    'type' => $body
                ]);

            $this->messaging->send($message);

            $users = User::where('user_role', 2)->get();

            $data = [];
            foreach ($users as $user) {
                $data[] = [
                    'title' => $title,
                    'content' => $body,
                    'is_read' => false,
                    'user_id' => $user->id,
                ];
            }

            Notifications::insert($data);

            return ['status' => true, 'message' => 'تم إرسال الإشعار للتوبيك وحفظه بنجاح'];
        } catch (MessagingException $e) {
            return ['status' => false, 'message' => 'فشل الإرسال', 'error' => $e->getMessage()];
        } catch (\Throwable $e) {
            return ['status' => false, 'message' => 'خطأ غير متوقع', 'error' => $e->getMessage()];
        }
    }



    public function sendBulkNotification(array $tokens, string $title, string $body, array $userIds): array
    {
        if (count($tokens) !== count($userIds)) {
            return ['status' => false, 'message' => 'عدد التوكنات لا يطابق عدد معرفات المستخدمين'];
        }

        try {
            $notification = FirebaseNotification::create($title, $body);

            $message = CloudMessage::new()->withNotification($notification);

            $report = $this->messaging->sendMulticast($message, $tokens);

            foreach ($userIds as $index => $userId) {
                Notifications::create([
                    'title' => $title,
                    'content' => $body,
                    'is_read' => false,
                    'user_id' => $userId,
                ]);
            }

            return [
                'status' => 1,
                'message' => 'تم إرسال الإشعارات إلى ' . $report->successes()->count() . ' مستخدم/ين',
                'failures' => $report->failures()->count(),
            ];
        } catch (MessagingException $e) {
            return ['status' => 0, 'message' => 'فشل الإرسال', 'error' => $e->getMessage()];
        } catch (\Throwable $e) {
            return ['status' => 0, 'message' => 'خطأ غير متوقع', 'error' => $e->getMessage()];
        }
    }
}
