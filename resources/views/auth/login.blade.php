<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login · Onboarding</title>

    <!-- Vite (placeholder) -->
    @vite(['resources/css/app.css','resources/js/app.js'])

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />

    <style>
        * {
            font-family: 'Inter', sans-serif;
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: #F8F4FB;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }

        .input-style {
            width: 100%;
            height: 64px;
            border: 1px solid #E5E7EB;
            border-radius: 18px;
            padding-left: 50px;
            padding-right: 50px;
            outline: none;
            transition: .25s;
            background: #fff;
            font-size: 1rem;
        }

        .input-style:focus {
            border-color: #692BB2;
            box-shadow: 0 0 0 4px rgba(105, 43, 178, .10);
        }

        /* Browser autofill (saved email/password) paints its own blue/yellow
           background over ours — force it back to match the theme. */
        .input-style:-webkit-autofill,
        .input-style:-webkit-autofill:hover,
        .input-style:-webkit-autofill:focus {
            -webkit-box-shadow: 0 0 0 1000px #fff inset;
            -webkit-text-fill-color: #1E293B;
            transition: background-color 5000s ease-in-out 0s;
        }

        .login-btn {
            width: 100%;
            height: 64px;
            border-radius: 18px;
            background: linear-gradient(90deg, #692BB2, #501E9C);
            color: white;
            font-weight: 600;
            font-size: 1.1rem;
            transition: .3s;
            border: none;
            cursor: pointer;
        }

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 25px rgba(105, 43, 178, .30);
        }

        /* Fix: exact font sizes from reference */
        .left-heading {
            font-size: 50px !important;
        }
        .left-sub {
            font-size: 21px !important;
            line-height: 40px !important;
            color: #5B6478 !important;
        }
        .feature-title {
            font-size: 19px !important;
            font-weight: 600 !important;
        }
        .feature-desc {
            font-size: 12px !important;
            color: #5B6478 !important;
        }
        .feature-icon {
            width: 48px !important;
            height: 48px !important;
        }

        @media (max-width: 1024px) {
            body {
                padding: 1rem;
            }
            .left-heading {
                font-size: 42px !important;
            }
            .left-sub {
                font-size: 20px !important;
                line-height: 32px !important;
            }
        }

        @media (max-width: 768px) {
            .input-style {
                height: 56px;
                padding-left: 44px;
                padding-right: 44px;
                font-size: 0.95rem;
            }
            .login-btn {
                height: 56px;
                font-size: 1rem;
            }
            .left-heading {
                font-size: 32px !important;
            }
            .left-sub {
                font-size: 18px !important;
                line-height: 28px !important;
            }
            .feature-title {
                font-size: 18px !important;
            }
            .welcome-title {
                font-size: 2rem !important;
            }
            .welcome-sub {
                font-size: 0.95rem !important;
            }
        }

        @media (max-width: 480px) {
            .input-style {
                height: 50px;
                padding-left: 40px;
                padding-right: 40px;
                font-size: 0.9rem;
                border-radius: 14px;
            }
            .login-btn {
                height: 50px;
                font-size: 0.95rem;
                border-radius: 14px;
            }
            .left-heading {
                font-size: 28px !important;
            }
            .left-sub {
                font-size: 16px !important;
                line-height: 24px !important;
            }
            .feature-title {
                font-size: 16px !important;
            }
            .welcome-title {
                font-size: 1.75rem !important;
            }
            .welcome-sub {
                font-size: 0.9rem !important;
            }
            .card-padding {
                padding-left: 1.25rem !important;
                padding-right: 1.25rem !important;
            }
        }
    </style>
</head>
<body>

<div class="w-full max-w-7xl h-auto lg:h-[calc(100vh-48px)] flex flex-col lg:flex-row rounded-[28px] overflow-hidden bg-white shadow-2xl mx-auto">

    <!-- ========== LEFT PANEL ========== -->
    <div class="relative lg:flex lg:w-[58%] flex-col overflow-hidden px-6 sm:px-10 lg:px-14 pt-8 lg:pt-12 pb-0">

        <!-- Illustration — hue-shifted from its original blue toward the brand purple -->
        <div class="absolute inset-0 bg-cover bg-center bg-no-repeat"
             style="background-image:url('{{ asset('assets/images/login-bg.png') }}'); filter: hue-rotate(45deg) saturate(1.2);"></div>

        <div class="absolute inset-0 bg-white/5"></div>

        <!-- Logo -->
        <div class="relative z-10 flex items-center gap-3">
            <svg width="42" height="42" viewBox="0 0 42 42" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="21" cy="21" r="16" stroke="#692BB2" stroke-width="8" stroke-linecap="round"
                        stroke-dasharray="82 18" transform="rotate(-38 21 21)"/>
                <path d="M10.5 10.8 A16 16 0 0 1 15.5 6.8" stroke="#B48FDD" stroke-width="8" stroke-linecap="round"/>
            </svg>
            <span class="text-xl sm:text-2xl font-semibold tracking-[-0.3px] text-[#223B78]">Onboarding</span>
        </div>

        <!-- Heading -->
        <div class="relative z-10 mt-10 lg:mt-10">
            <h1 class="left-heading font-bold leading-[1.05] tracking-[-4px] text-[#111827]">
                Client Onboarding
                <br />
                <span class="text-primary">By GGCS</span>
            </h1>
            <p class="left-sub mt-6">
                Onboard clients faster with everything
                <br />
                <span class="text-black-600">
                in one secure platform.</span>
            </p>
        </div>

        <!-- Feature list - exact spacing 32px between items -->
        <div class="relative z-10 mt-8 space-y-8 mb-20 lg:mb-60">
            <div class="flex gap-4">
                <div class="feature-icon rounded-full bg-white border border-[#E9EEF8] shadow-[0_4px_12px_rgba(15,23,42,0.08)] flex items-center justify-center flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <circle cx="12" cy="12" r="8.5" stroke="#692BB2" stroke-width="2"/>
                        <path d="M12 7V12L15 14" stroke="#692BB2" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <div>
                    <h3 class="feature-title">Real-time Tracking</h3>
                    <p class="feature-desc">Track projects, tasks, documents and approvals.</p>
                </div>
            </div>
            <div class="flex gap-4">
                <div class="feature-icon rounded-full bg-white border border-[#E9EEF8] shadow-[0_4px_12px_rgba(15,23,42,0.08)] flex items-center justify-center flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M3 7.5C3 6.67 3.67 6 4.5 6H8.5L10 8H19.5C20.33 8 21 8.67 21 9.5V17.5C21 18.33 20.33 19 19.5 19H4.5C3.67 19 3 18.33 3 17.5V7.5Z" stroke="#692BB2" stroke-width="2" stroke-linejoin="round"/>
                    </svg>
                </div>
                <div>
                    <h3 class="feature-title">Centralized Workspace</h3>
                    <p class="feature-desc">Keep everything in one place.</p>
                </div>
            </div>
            <div class="flex gap-4">
                <div class="feature-icon rounded-full bg-white border border-[#E9EEF8] shadow-[0_4px_12px_rgba(15,23,42,0.08)] flex items-center justify-center flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <circle cx="9" cy="8" r="2.5" stroke="#692BB2" stroke-width="2"/>
                        <path d="M4.5 18C5.2 15.6 7 14.2 9 14.2C11 14.2 12.8 15.6 13.5 18" stroke="#692BB2" stroke-width="2" stroke-linecap="round"/>
                        <circle cx="16.5" cy="9" r="2" stroke="#692BB2" stroke-width="2"/>
                        <path d="M14.5 17.5C15 16 16.2 15 17.8 15" stroke="#692BB2" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </div>
                <div>
                    <h3 class="feature-title">For Teams & Clients</h3>
                    <p class="feature-desc">Collaboration made easy.</p>
                </div>
            </div>
        </div>
        

     <div class="absolute left-14 bottom-[8px] text-sm text-[#5B6478] z-30">
    © 2026 Onboarding. All rights reserved.
</div>
    </div>

    <!-- ========== RIGHT PANEL (Login Card - UNCHANGED) ========== -->
    <div class="w-full lg:w-[42%] flex items-center justify-center px-4 sm:px-6 py-10 lg:py-12 bg-[#F8FAFD]">
        <div class="card-padding w-full max-w-[470px] bg-white rounded-[22px] border border-[#EDF1F7] shadow-[0_15px_50px_rgba(33,67,135,.08)] px-6 sm:px-10 py-8 sm:py-9">

            <!-- Shield -->
            <div class="flex justify-center mt-2 mb-2">
                <img src="{{ asset('assets/images/shield.png') }}"
                     alt="Security Shield"
                     class="w-[120px] sm:w-[165px] h-auto object-contain"
                     style="filter: hue-rotate(45deg) saturate(1.2);" />
            </div>

            <!-- Welcome -->
            <div class="text-center">
                <h2 class="welcome-title text-3xl sm:text-[36px] font-bold text-[#1E293B]">Welcome Back!</h2>
                <p class="welcome-sub mt-3 text-[15px] sm:text-[17px] text-[#64748B]">Please sign in to continue to your account.</p>
            </div>

            <!-- FORM -->
            <form method="POST" action="{{ route('login') }}" class="mt-8 sm:mt-10">
                @csrf

                @if (session('status'))
                    <div class="mb-6 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-medium text-amber-700">
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-600">
                        {{ $errors->first() }}
                    </div>
                @endif

                <!-- EMAIL -->
                <div>
                    <label class="font-semibold text-gray-700 text-sm sm:text-base">Email Address</label>
                    <div class="relative mt-3">
                        <svg class="absolute left-4 top-1/2 -translate-y-1/2" width="20" height="20" fill="none" stroke="#94A3B8" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M4 5h16v14H4z"/>
                            <path d="M22 7L12 14 2 7"/>
                        </svg>
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="you@example.com" class="input-style" />
                    </div>
                </div>

                <!-- PASSWORD -->
                <div class="mt-6 sm:mt-7">
                    <label class="font-semibold text-gray-700 text-sm sm:text-base">Password</label>
                    <div class="relative mt-3">
                        <svg class="absolute left-4 top-1/2 -translate-y-1/2" width="20" height="20" fill="none" stroke="#94A3B8" stroke-width="2" viewBox="0 0 24 24">
                            <rect x="5" y="11" width="14" height="10" rx="2"/>
                            <path d="M8 11V8a4 4 0 018 0v3"/>
                        </svg>
                        <input id="password" type="password" name="password" placeholder="••••••••••" class="input-style" />
                        <button type="button" id="togglePassword" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-primary">
                            <svg id="eyeOpen" xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Remember & Forgot -->
                <div class="flex flex-wrap items-center justify-between mt-6 gap-2">
                    <label class="flex items-center gap-2 text-slate-500 text-sm cursor-pointer">
                        <input type="checkbox" name="remember" class="rounded border-gray-300 text-primary focus:ring-primary" />
                        Remember me
                    </label>
              <a href="{{ route('password.request') }}" class="text-primary font-medium hover:underline text-sm">
    Forgot password?
</a>
</div>

                <!-- Login Button -->
                <button type="submit" class="login-btn mt-8">Log In</button>

                <!-- Divider -->
                <div class="relative my-8">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-200"></div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    (function() {
        const password = document.getElementById('password');
        const toggle = document.getElementById('togglePassword');
        if (password && toggle) {
            toggle.addEventListener('click', function() {
                password.type = (password.type === 'password') ? 'text' : 'password';
            });
        }
    })();
</script>

</body>
</html>