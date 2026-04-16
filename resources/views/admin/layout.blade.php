<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Panel - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-white dark:bg-black">
    <div class="min-h-screen">
        <nav class="bg-white dark:bg-black border-b border-gray-200 dark:border-gray-800">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <h1 class="text-xl font-medium text-black dark:text-white">Flashcard Admin</h1>
                    </div>
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('dashboard') }}" class="text-gray-500 dark:text-gray-400 hover:text-black dark:hover:text-white">Dashboard</a>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-gray-500 dark:text-gray-400 hover:text-black dark:hover:text-white">Logout</button>
                        </form>
                    </div>
                </div>
            </div>
        </nav>

        <div class="flex">
            <div class="w-64 bg-white dark:bg-black border-r border-gray-200 dark:border-gray-800 min-h-screen">
                <div class="p-4 space-y-1">
                    <a href="{{ route('admin.index') }}" class="block px-4 py-2 {{ request()->routeIs('admin.index') ? 'bg-black text-white dark:bg-white dark:text-black' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-900' }}">
                        Overview
                    </a>
                    <a href="{{ route('admin.users') }}" class="block px-4 py-2 {{ request()->routeIs('admin.users*') ? 'bg-black text-white dark:bg-white dark:text-black' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-900' }}">
                        Users
                    </a>
                    <a href="{{ route('admin.sets') }}" class="block px-4 py-2 {{ request()->routeIs('admin.sets*') ? 'bg-black text-white dark:bg-white dark:text-black' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-900' }}">
                        Flashcard Sets
                    </a>
                </div>
            </div>

            <div class="flex-1 p-8">
                @if(session('success'))
                    <div class="mb-4 p-4 bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-white">
                        {{ session('success') }}
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
    </div>
</body>
</html>
