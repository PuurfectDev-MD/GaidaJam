import { Head, usePage } from '@inertiajs/react';

export default function Login() {
    const { flash } = usePage().props;

    return (
        <>
            <Head title="Login" />

            <div className="flex min-h-screen flex-col items-center justify-center bg-[#FDFDFC] p-6 text-[#1b1b18]">
                <main className="w-full max-w-md">
                    <a
                        href="/auth/hackclub"
                        className="flex w-full items-center justify-center rounded-md bg-orange-500 px-4 py-2 text-white transition duration-200 hover:bg-orange-600"
                    >
                        <span className="ml-2 font-semibold">Login with Hack Club</span>
                    </a>

                    {flash?.error && (
                        <div className="mt-4 rounded-lg bg-red-100 p-3 text-sm text-red-700">{flash.error}</div>
                    )}
                </main>
            </div>
        </>
    );
}