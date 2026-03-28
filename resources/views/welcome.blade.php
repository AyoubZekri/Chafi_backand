<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Chafi API</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta description="Secure backend API service for Chafi mobile application.">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #0f172a;
            color: #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }
        .card {
            background: #1e293b;
            padding: 40px;
            border-radius: 16px;
            max-width: 500px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.4);
        }
        h1 {
            margin-bottom: 10px;
            color: #4f46e5;
        }
        p {
            line-height: 1.6;
            color: #cbd5f5;
        }
        .badge {
            margin-top: 20px;
            display: inline-block;
            padding: 8px 14px;
            background: #22c55e;
            color: #022c22;
            border-radius: 999px;
            font-size: 14px;
        }
        footer {
            margin-top: 20px;
            font-size: 12px;
            color: #94a3b8;
        }
        a {
            color: #60a5fa;
            text-decoration: none;
        }
    </style>
</head>
<body>

<div class="card">
    <h1>Chafi API</h1>

    <p>
        This is a secure backend API used by the Chafi mobile application.
        It handles authentication, data processing, and application services.
    </p>

    <p>
        This service is not intended for direct public browsing.
    </p>

    <div class="badge">✔ API Active & Secure</div>

    <footer>
        © <?php echo date('Y'); ?> Chafi <br>
        Contact: support@chafi.net
    </footer>
</div>

</body>
</html>
