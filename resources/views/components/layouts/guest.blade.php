<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'SiteWatch' }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800,900" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        body { font-family: 'Inter', sans-serif; }

        .bg-animated {
            background: linear-gradient(135deg, #0a0a0f 0%, #0d1117 25%, #0a0f1a 50%, #0d0f14 75%, #0a0a0f 100%);
            background-size: 400% 400%;
            animation: gradientShift 15s ease infinite;
        }
        @keyframes gradientShift {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }

        .orb {
            position: fixed;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.12;
            pointer-events: none;
            animation: float 20s ease-in-out infinite;
        }
        .orb-1 {
            width: 500px; height: 500px;
            background: #3b82f6;
            top: -150px; right: -100px;
            animation-delay: 0s;
        }
        .orb-2 {
            width: 400px; height: 400px;
            background: #8b5cf6;
            bottom: -100px; left: -100px;
            animation-delay: -7s;
        }
        @keyframes float {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(30px, -30px) scale(1.05); }
            66% { transform: translate(-20px, 20px) scale(0.95); }
        }

        .glass {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.06);
        }
        .glass-input {
            background: rgba(255, 255, 255, 0.04);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            transition: all 0.3s ease;
        }
        .glass-input:focus {
            background: rgba(255, 255, 255, 0.06);
            border-color: rgba(59, 130, 246, 0.5);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1), 0 0 30px rgba(59, 130, 246, 0.1);
        }

        .glow-blue { box-shadow: 0 0 60px rgba(59, 130, 246, 0.1), 0 0 120px rgba(59, 130, 246, 0.05); }

        .fade-up {
            animation: fadeUp 0.8s ease-out forwards;
            opacity: 0;
        }
        .fade-up-delay-1 { animation-delay: 0.15s; }
        .fade-up-delay-2 { animation-delay: 0.3s; }
        .fade-up-delay-3 { animation-delay: 0.45s; }
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .btn-glow {
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        .btn-glow::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, transparent 50%);
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .btn-glow:hover::before { opacity: 1; }
        .btn-glow:hover {
            box-shadow: 0 0 30px rgba(59, 130, 246, 0.3), 0 8px 30px rgba(0, 0, 0, 0.3);
            transform: translateY(-1px);
        }

        .shake {
            animation: shake 0.5s ease-in-out;
        }
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            20% { transform: translateX(-8px); }
            40% { transform: translateX(8px); }
            60% { transform: translateX(-4px); }
            80% { transform: translateX(4px); }
        }
    </style>
</head>
<body class="bg-animated min-h-screen flex items-center justify-center overflow-hidden relative">
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>

    <div class="relative z-10 w-full">
        {{ $slot }}
    </div>

    @livewireScripts
</body>
</html>
