<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification — {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <style>*, body { font-family: 'DM Sans', sans-serif; } .font-display { font-family: 'Syne', sans-serif; }</style>
</head>
<body class="min-h-screen bg-slate-50 flex items-center justify-center p-6">
    <div class="bg-white rounded-2xl shadow-xl p-10 max-w-md w-full text-center">
        @if($success)
            <div class="w-20 h-20 rounded-full bg-emerald-100 flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <h1 class="font-display font-bold text-2xl text-gray-900 mb-3">Email Verified!</h1>
        @else
            <div class="w-20 h-20 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </div>
            <h1 class="font-display font-bold text-2xl text-gray-900 mb-3">Verification Failed</h1>
        @endif

        <p class="text-gray-500 text-sm leading-relaxed mb-8">{{ $message }}</p>

        <a href="{{ url('/') }}"
           class="inline-flex items-center gap-2 px-6 py-3 text-white font-semibold text-sm rounded-xl transition-all hover:shadow-lg hover:-translate-y-0.5"
           style="background:linear-gradient(135deg,#dc2626,#b91c1c);">
            ← Back to Home
        </a>
    </div>
</body>
</html>
