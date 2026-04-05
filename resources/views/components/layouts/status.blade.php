<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Sistem Durumu' }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800,900" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        body { font-family: 'Inter', sans-serif; }

        /* Animated gradient background */
        .bg-animated {
            background: linear-gradient(135deg, #0a0a0f 0%, #0d1117 25%, #0a0f1a 50%, #0d0f14 75%, #0a0a0f 100%);
            background-size: 400% 400%;
            animation: gradientShift 15s ease infinite;
        }
        @keyframes gradientShift {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }

        /* Floating orbs */
        .orb {
            position: fixed;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.15;
            pointer-events: none;
            animation: float 20s ease-in-out infinite;
        }
        .orb-1 {
            width: 500px; height: 500px;
            background: #3b82f6;
            top: -150px; left: -100px;
            animation-delay: 0s;
        }
        .orb-2 {
            width: 400px; height: 400px;
            background: #8b5cf6;
            bottom: -100px; right: -100px;
            animation-delay: -7s;
        }
        .orb-3 {
            width: 300px; height: 300px;
            background: #06b6d4;
            top: 50%; left: 50%;
            transform: translate(-50%, -50%);
            animation-delay: -14s;
        }
        @keyframes float {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(30px, -30px) scale(1.05); }
            66% { transform: translate(-20px, 20px) scale(0.95); }
        }

        /* Glass card */
        .glass {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.06);
        }

        /* Pulse ring for status icon */
        .pulse-ring {
            animation: pulseRing 2.5s cubic-bezier(0.215, 0.61, 0.355, 1) infinite;
        }
        @keyframes pulseRing {
            0% { transform: scale(0.9); opacity: 0.7; }
            50% { transform: scale(1); opacity: 0.3; }
            100% { transform: scale(0.9); opacity: 0.7; }
        }

        /* Subtle glow */
        .glow-green { box-shadow: 0 0 60px rgba(34, 197, 94, 0.15), 0 0 120px rgba(34, 197, 94, 0.05); }
        .glow-yellow { box-shadow: 0 0 60px rgba(234, 179, 8, 0.15), 0 0 120px rgba(234, 179, 8, 0.05); }
        .glow-blue { box-shadow: 0 0 60px rgba(59, 130, 246, 0.15), 0 0 120px rgba(59, 130, 246, 0.05); }

        /* Number counter animation */
        .number-glow-green { text-shadow: 0 0 30px rgba(34, 197, 94, 0.4); }
        .number-glow-yellow { text-shadow: 0 0 30px rgba(234, 179, 8, 0.4); }

        /* Fade in */
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
    </style>
</head>
<body class="bg-animated min-h-screen flex items-center justify-center overflow-hidden relative">
    {{-- Background orbs --}}
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>

    {{-- Content --}}
    <div class="relative z-10 w-full">
        {{ $slot }}
    </div>

    @livewireScripts
</body>
</html>
