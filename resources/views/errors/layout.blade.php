<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title') &middot; {{ config('app.name', 'Onboarding') }}</title>

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
            max-width: 480px;
            background: #fff;
            border-radius: 24px;
            box-shadow: 0 15px 50px rgba(33, 67, 135, .08);
            border: 1px solid #EDF1F7;
            padding: 2.75rem 2.25rem;
            text-align: center;
        }

        .code {
            font-size: 4rem;
            font-weight: 700;
            letter-spacing: -2px;
            color: #692BB2;
            line-height: 1;
        }

        h1 {
            margin: 1rem 0 .5rem;
            font-size: 1.4rem;
            font-weight: 600;
            color: #1E293B;
        }

        p {
            color: #64748B;
            font-size: .95rem;
            line-height: 1.6;
            margin: 0 0 1.75rem;
        }

        .btn {
            display: inline-block;
            padding: .85rem 2rem;
            border-radius: 14px;
            background: linear-gradient(90deg, #692BB2, #501E9C);
            color: #fff;
            font-weight: 600;
            text-decoration: none;
            font-size: .95rem;
            transition: .3s;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 25px rgba(105, 43, 178, .3);
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="code">@yield('code')</div>
        <h1>@yield('heading')</h1>
        <p>@yield('message')</p>
        @yield('action')
    </div>
</body>
</html>
