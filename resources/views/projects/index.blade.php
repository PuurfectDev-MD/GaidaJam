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

            <form method="POST" action="#" class="mt-5 space-y-4">

                @csrf

                <div>
                    <label
                        for="name"
                        class="mb-1 block text-sm font-medium text-gray-700"
                    >
                        Project Name
                    </label>

                    <input
                        type="text"
                        id="name"
                        name="name"
                        placeholder="My Project"
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none focus:border-gray-500 focus:ring-1 focus:ring-gray-500"
                    >
                </div>

                <div>
                    <label
                        for="description"
                        class="mb-1 block text-sm font-medium text-gray-700"
                    >
                        Description
                    </label>

                    <textarea
                        id="description"
                        name="description"
                        rows="3"
                        placeholder="Project description..."
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none focus:border-gray-500 focus:ring-1 focus:ring-gray-500"
                    ></textarea>
                </div>

                <button
                    type="submit"
                    class="w-full rounded-lg bg-gray-900 px-4 py-2.5 text-sm font-medium text-white hover:bg-gray-800"
                >
                    Add Project
                </button>

            </form>

        </div>


        {{-- Project Cards --}}
        <div class="lg:col-span-2">

            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">

                {{-- Project Card --}}
                <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">

                    <div class="flex items-start justify-between">
                        <h3 class="font-semibold text-gray-900">
                            Project One
                        </h3>

                        <span class="rounded-full bg-green-100 px-2.5 py-1 text-xs font-medium text-green-700">
                            Active
                        </span>
                    </div>

                    <p class="mt-3 text-sm leading-6 text-gray-600">
                        This is an example project description.
                    </p>

                    <div class="mt-5 border-t border-gray-100 pt-4">
                        <a
                            href="#"
                            class="text-sm font-medium text-gray-700 hover:text-gray-900"
                        >
                            View Project →
                        </a>
                    </div>

                </div>


                {{-- Project Card --}}
                <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">

                    <div class="flex items-start justify-between">
                        <h3 class="font-semibold text-gray-900">
                            Project Two
                        </h3>

                        <span class="rounded-full bg-yellow-100 px-2.5 py-1 text-xs font-medium text-yellow-700">
                            Pending
                        </span>
                    </div>

                    <p class="mt-3 text-sm leading-6 text-gray-600">
                        Another example project description.
                    </p>

                    <div class="mt-5 border-t border-gray-100 pt-4">
                        <a
                            href="#"
                            class="text-sm font-medium text-gray-700 hover:text-gray-900"
                        >
                            View Project →
                        </a>
                    </div>

                </div>

            </div>

        </div>

    </div>

@endsection