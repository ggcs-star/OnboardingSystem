<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>We'll Be Right Back &middot; Onboarding System</title>

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />

    <style>
        * {
            font-family: 'Inter', sans-serif;
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #F8F4FB;
            padding: 1.5rem;
        }

        .card {
            width: 100%;
            max-width: 440px;
            background: #fff;
            border-radius: 24px;
            box-shadow: 0 15px 50px rgba(33, 67, 135, .08);
            border: 1px solid #EDF1F7;
            padding: 2.75rem 2.25rem;
            text-align: center;
        }

        .spinner {
            width: 46px;
            height: 46px;
            margin: 0 auto 1.5rem;
            border-radius: 50%;
            border: 4px solid #EDE4F7;
            border-top-color: #692BB2;
            animation: spin 0.9s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        h1 {
            margin: 0 0 .75rem;
            font-size: 1.4rem;
            font-weight: 700;
            color: #1E293B;
        }

        p {
            color: #64748B;
            font-size: .95rem;
            line-height: 1.6;
            margin: 0 0 1rem;
        }

        .hint {
            color: #692BB2;
            font-size: .85rem;
            font-weight: 500;
            margin: 0;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="spinner" aria-hidden="true"></div>
        <h1>We'll Be Right Back</h1>
        <p>We're currently updating Onboarding System. This only takes a moment &mdash; this page will refresh automatically.</p>
        <p class="hint">No action needed &mdash; please wait.</p>
    </div>

    <script>
        setTimeout(function () {
            window.location.reload();
        }, 10000);
    </script>
</body>
</html>
