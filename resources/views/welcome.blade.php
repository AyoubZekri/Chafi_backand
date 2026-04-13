<!DOCTYPE html>
<html dir="rtl" lang="ar">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Chafi | Backend API</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800&display=swap" rel="stylesheet" />
    <style>
        :root {
            --primary: #2563eb;
            --primary-light: #60a5fa;
            --bg-start: #f8fafc;
            --bg-end: #e2e8f0;
        }

        body {
            font-family: 'Tajawal', sans-serif;
            background: linear-gradient(135deg, var(--bg-start) 0%, var(--bg-end) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            overflow: hidden;
        }

        .glass-container {
            position: relative;
            z-index: 10;
        }

        .premium-card {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(24px);
            border: 1px solid rgba(255, 255, 255, 0.4);
            box-shadow: 
                0 10px 40px -10px rgba(0, 0, 0, 0.05),
                0 20px 60px -20px rgba(37, 99, 235, 0.1);
            border-radius: 3rem;
            padding: 3.5rem;
            width: 100%;
            max-width: 480px;
            text-align: center;
            transition: all 0.6s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .premium-card:hover {
            transform: translateY(-8px);
            box-shadow: 
                0 15px 50px -10px rgba(0, 0, 0, 0.08),
                0 30px 80px -20px rgba(37, 99, 235, 0.15);
        }

        .logo-box {
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
            width: 80px;
            height: 80px;
            margin: 0 auto 2rem;
            border-radius: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 12px 24px -6px rgba(37, 99, 235, 0.4);
            position: relative;
            overflow: hidden;
        }

        .logo-box::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent, rgba(255,255,255,0.2), transparent);
            transform: rotate(45deg);
            animation: shine 3s infinite;
        }

        @keyframes shine {
            0% { left: -100%; }
            100% { left: 100%; }
        }

        .badge {
            background: rgba(37, 99, 235, 0.08);
            color: #1e40af;
            padding: 0.5rem 1.25rem;
            border-radius: 9999px;
            font-weight: 700;
            font-size: 0.875rem;
            border: 1px solid rgba(37, 99, 235, 0.1);
            display: inline-block;
            margin-bottom: 1.5rem;
        }

        .status-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #10b981;
            position: relative;
        }

        .status-dot::after {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            background: #10b981;
            animation: ping 2s cubic-bezier(0, 0, 0.2, 1) infinite;
        }

        @keyframes ping {
            75%, 100% { transform: scale(3); opacity: 0; }
        }

        .ambient-blob {
            position: absolute;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(37, 99, 235, 0.15) 0%, transparent 70%);
            filter: blur(80px);
            z-index: 1;
            pointer-events: none;
        }

        .blob-1 { top: -200px; right: -200px; animation: float 20s infinite alternate; }
        .blob-2 { bottom: -200px; left: -200px; animation: float 25s infinite alternate-reverse; }

        @keyframes float {
            0% { transform: translate(0, 0); }
            100% { transform: translate(100px, 50px); }
        }
    </style>
</head>

<body class="antialiased">
    <div class="ambient-blob blob-1"></div>
    <div class="ambient-blob blob-2"></div>

    <main class="glass-container px-4">
        <div class="premium-card">
            <div class="logo-box">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path>
                </svg>
            </div>

            <div class="badge">
                BACKEND API v1.0
            </div>

            <h1 class="text-4xl font-extrabold text-slate-900 mb-6 tracking-tight">مشروع شافي</h1>
            
            <p class="text-lg text-slate-500 leading-relaxed mb-10 max-w-sm mx-auto">
                هذا الموقع يعمل كـ <span class="text-blue-600 font-bold">API</span> فقط. 
                جميع الواجهات الرسومية متاحة عبر تطبيق الهاتف والويب.
            </p>

            <!-- <div class="space-y-4">
                <div class="bg-slate-50/50 border border-slate-100 p-5 rounded-2xl flex items-center justify-between group transition-colors hover:bg-white">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center shadow-sm border border-slate-100">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="text-right">
                            <div class="text-[10px] uppercase tracking-widest text-slate-400 font-bold mb-0.5">حالة الخدمة</div>
                            <div class="text-slate-700 font-bold italic">Active & Running</div>
                        </div>
                    </div>
                    <div class="status-dot"></div>
                </div>

               <a href="/api" class="flex items-center justify-center gap-3 w-full py-5 bg-slate-900 text-white rounded-2xl font-bold hover:bg-blue-700 transition-all shadow-xl shadow-slate-200 active:scale-95">
                    <span>التوجه إلى التوثيق</span>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a> 
            </div> -->

            <footer class="mt-12 text-slate-400 text-sm font-medium">
                &copy; {{ date('Y') }} Chafi Team. All rights reserved.
            </footer>
        </div>
    </main>
</body>

</html>