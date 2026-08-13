import { Link, usePage } from '@inertiajs/react';

function navClass(active) {
    return `flex items-center rounded-lg px-4 py-3 text-sm font-medium ${
        active
            ? 'bg-gray-100 text-gray-900'
            : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
    }`;
}

export default function AuthenticatedLayout({ children }) {
    const { auth } = usePage().props;
    const url = usePage().url;

    return (
        <div className="flex min-h-screen bg-[#FDFDFC] text-[#1b1b18]">
                <aside className="flex w-64 shrink-0 flex-col border-r border-gray-200 bg-white">
                    <div className="border-b border-gray-200 p-4">
                        <Link href="/dashboard" className="text-lg font-semibold text-gray-900">
                            Laravel
                        </Link>
                    </div>

                    <nav className="flex-1 space-y-1 p-4">
                        <Link href="/dashboard" className={navClass(url === '/dashboard')}>
                            Dashboard
                        </Link>
                        <Link href="/projects" className={navClass(url.startsWith('/projects'))}>
                            Projects
                        </Link>
                    </nav>

                    <div className="border-t border-gray-200 p-4">
                        <div className="flex items-center gap-3">
                            <div className="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-gray-200 text-sm font-semibold text-gray-700">
                                {auth?.user?.name?.[0]?.toUpperCase() ?? '?'}
                            </div>

                            <div className="min-w-0">
                                <p className="truncate text-sm font-medium text-gray-900">
                                    {auth?.user?.name}
                                </p>
                                <p className="truncate text-xs text-gray-500">
                                    {auth?.user?.email}
                                </p>
                            </div>
                        </div>

                        <Link
                            href="/logout"
                            method="post"
                            as="button"
                            className="mt-3 w-full rounded-lg px-4 py-2 text-left text-sm font-medium text-gray-600 hover:bg-gray-100 hover:text-gray-900"
                        >
                            Logout
                        </Link>
                    </div>
                </aside>

                <main className="min-w-0 flex-1 p-8">{children}</main>
            </div>
    );
}
