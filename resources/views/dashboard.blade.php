@extends('layouts.app')

@section('title', '- Dashboard')

@section('content')
    <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">
        Dashboard
    </h1>

    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
        Welcome back, {{ Auth::user()->name }}.
    </p>
@endsection