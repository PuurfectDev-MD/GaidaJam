<aside class="flex w-64 shrink-0 flex-col border-r border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">

    {{-- Logo / Brand --}}
    <div class="border-b border-gray-200 p-4 dark:border-gray-800">
        <a href="{{ route('dashboard') }}" class="text-lg font-semibold text-gray-900 dark:text-white">
            {{ config('app.name', 'Laravel') }}
        </a>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 space-y-1 p-4">

        {{-- Dashboard --}}
        <a href="{{ route('dashboard') }}"
            class="flex items-center rounded-lg px-4 py-3 text-sm font-medium
            {{ request()->routeIs('dashboard')
                ? 'bg-gray-100 text-gray-900 dark:bg-gray-800 dark:text-white'
                : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-gray-800 dark:hover:text-white' }}">
            Dashboard
        </a>

        {{-- Projects --}}
        <a href="{{ route('projects.index') }}"
            class="flex items-center rounded-lg px-4 py-3 text-sm font-medium
            {{ request()->routeIs('projects.*')
                ? 'bg-gray-100 text-gray-900 dark:bg-gray-800 dark:text-white'
                : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-gray-800 dark:hover:text-white' }}">
            Projects
        </a>

    </nav>

    {{-- Authenticated User --}}
    <div class="border-t border-gray-200 p-4 dark:border-gray-800">

        <div class="flex items-center gap-3">
            {{-- Avatar --}}
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-gray-200 text-sm font-semibold text-gray-700 dark:bg-gray-700 dark:text-gray-200">
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            </div>

            {{-- User Info --}}
            <div class="min-w-0">
                <p class="truncate text-sm font-medium text-gray-900 dark:text-white">
                    {{ Auth::user()->name }}
                </p>
                <p class="truncate text-xs text-gray-500 dark:text-gray-400">
                    {{ Auth::user()->email }}
                </p>
            </div>
        </div>

        {{-- Logout --}}
        <form method="POST" action="{{ route('logout') }}" class="mt-3">
            @csrf
            <button type="submit"
                class="w-full rounded-lg px-4 py-2 text-left text-sm font-medium text-gray-600 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-gray-800 dark:hover:text-white">
                Logout
            </button>
        </form>

    </div>

</aside>