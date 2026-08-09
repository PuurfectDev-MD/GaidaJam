@extends('layouts.app')

@section('title', 'Projects')

@section('content')

    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">
                Projects
            </h1>

            <p class="mt-1 text-sm text-gray-500">
                Manage your projects.
            </p>
        </div>
    </div>


    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

        {{-- Add Project --}}
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">

            <h2 class="text-lg font-semibold text-gray-900">
                Add Project
            </h2>

            <form method="POST" action="{{ route('projects.store') }}" class="mt-5 space-y-4">
                @csrf

                <div>
                    <label for="name" class="mb-1 block text-sm font-medium text-gray-700">Project Name</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="My Project"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none focus:border-gray-500 focus:ring-1 focus:ring-gray-500">
                    @error('name')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="description" class="mb-1 block text-sm font-medium text-gray-700">Description</label>
                    <textarea id="description" name="description" rows="3" placeholder="Project description..."
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none focus:border-gray-500 focus:ring-1 focus:ring-gray-500">{{ old('description') }}</textarea>
                    @error('description')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="url" class="mb-1 block text-sm font-medium text-gray-700">Project URL</label>
                    <input type="text" id="url" name="url" value="{{ old('url') }}"
                        placeholder="https://example.com"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none focus:border-gray-500 focus:ring-1 focus:ring-gray-500">
                    @error('url')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit"
                    class="w-full rounded-lg bg-gray-900 px-4 py-2.5 text-sm font-medium hover:bg-gray-800">
                    Add Project
                </button>
            </form>

            <!-- Success Message -->
            @if (session('success'))
                <div class="mt-4 p-3 bg-green-100 text-green-700 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

        </div>

        {{-- Project Cards --}}
<div class="lg:col-span-2">
    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">

        @forelse($projects as $project)
            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">

                <div class="flex items-start justify-between">
                    <h3 class="font-semibold text-gray-900">
                        {{ $project->name }}
                    </h3>

                    {{-- Dynamic Status Badge (Optional) --}}
                    {{-- Assuming you add a 'status' column later, otherwise remove this span --}}
                    <span class="rounded-full bg-green-100 px-2.5 py-1 text-xs font-medium text-green-700">
                        Active
                    </span>
                </div>

                <p class="mt-3 text-sm leading-6 text-gray-600">
                    {{ Str::limit($project->description, 100) }}
                </p>

                <div class="mt-5 border-t border-gray-100 pt-4 flex justify-between items-center">
                    <a href="{{ route('projects.show', $project) }}" class="text-sm font-medium text-gray-700 hover:text-gray-900">
                        View Project →
                    </a>
                </div>

            </div>
        @empty
            <div class="col-span-2 text-center py-10">
                <p class="text-gray-500">No projects found. Create your first one!</p>
            </div>
        @endforelse

    </div>
</div>   
    </div>

@endsection
