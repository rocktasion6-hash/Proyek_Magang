<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login - Sistem Penilaian Jabatan</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-gray-100 flex items-center justify-center">

    <div class="w-full max-w-md bg-white rounded-lg shadow-md p-8">

        <h1 class="text-2xl font-bold text-center mb-2">
            Sistem Penilaian Jabatan
        </h1>

        <p class="text-center text-gray-500 mb-6">
            Silakan login untuk melanjutkan
        </p>

        @if ($errors->any())
            <div class="bg-red-100 text-red-700 px-4 py-3 rounded mb-4">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login.process') }}">
            @csrf

            <div class="mb-4">
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
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                >

                @error('username')
                    <p class="text-red-500 text-sm mt-1">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div class="mb-6">
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
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                >

                @error('password')
                    <p class="text-red-500 text-sm mt-1">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <button
                type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg"
            >
                Login
            </button>
        </form>

    </div>

</body>
</html>