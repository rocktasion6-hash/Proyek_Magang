<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login - Sistem Penilaian Jabatan</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="relative min-h-screen bg-[#2c4a3e] text-[#111827] flex items-center justify-center p-4 sm:p-6 overflow-x-hidden">

    <!-- Foto Full Latar Belakang -->
    <img
        src="{{ asset('image/Baground.webp') }}"
        alt="Latar Login"
        class="fixed inset-0 w-full h-full object-cover z-0"
    >
    <!-- Lapisan hijau tua solid transparan (bukan gradasi) agar teks & form tetap kontras -->
    <div class="fixed inset-0 bg-[#2c4a3e]/45 z-0"></div>

    <!-- Kotak Form Login Rada Transparan di Atas Latar Foto -->
    <div class="relative z-10 w-full max-w-md bg-[#fdfdfc]/85 rounded-2xl border border-[#e2e8e4]/80 px-6 sm:px-8 pt-3 sm:pt-4 pb-7 shadow-lg">

        <!-- Logo di atas judul (sudut lebih melengkung, tidak runcing) -->
        <div class="flex justify-center mb-1.5">
            <img
                src="{{ asset('image/images.jpg') }}"
                alt="Logo"
                class="h-12 sm:h-14 w-auto object-contain rounded-xl"
            >
        </div>

        <h1 class="text-2xl font-bold text-[#2c4a3e] text-center mb-1">
            Sistem Penilaian Jabatan
        </h1>

        <p class="text-sm text-gray-600 text-center mb-5">
            Silakan login untuk melanjutkan
        </p>

        @if ($errors->any())
            <div class="bg-red-50 text-red-700 border border-red-200 px-4 py-3 rounded-lg mb-4 text-sm">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login.process') }}" class="space-y-4">
            @csrf

            <div>
                <label
                    for="username"
                    class="block text-sm font-medium text-gray-700 mb-1"
                >
                    Username
                </label>

                <input
                    type="text"
                    id="username"
                    name="username"
                    value="{{ old('username') }}"
                    required
                    autofocus
                    class="w-full bg-white/90 border border-gray-300 rounded-lg px-3 py-2 text-sm text-gray-900 focus:outline-none focus:border-[#2c4a3e]"
                >

                @error('username')
                    <p class="text-red-600 text-xs mt-1">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div>
                <label
                    for="password"
                    class="block text-sm font-medium text-gray-700 mb-1"
                >
                    Password
                </label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    required
                    class="w-full bg-white/90 border border-gray-300 rounded-lg px-3 py-2 text-sm text-gray-900 focus:outline-none focus:border-[#2c4a3e]"
                >

                @error('password')
                    <p class="text-red-600 text-xs mt-1">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <button
                type="submit"
                class="w-full bg-[#0b7029] hover:bg-[#055c39] text-[#fdfdfc] font-semibold py-2.5 px-4 rounded-lg text-sm transition-colors cursor-pointer"
            >
                Login
            </button>
        </form>

    </div>

</body>
</html>