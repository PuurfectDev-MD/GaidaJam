import { Head, usePage } from '@inertiajs/react';
import AuthenticatedLayout from '../Layouts/AuthenticatedLayout';

export default function Dashboard() {
    const { auth } = usePage().props;

    return (
        <AuthenticatedLayout>
            <Head title="Dashboard" />

            <h1 className="text-2xl font-semibold text-gray-900">Dashboard</h1>

            <p className="mt-2 text-sm text-gray-600">
                Welcome back, {auth?.user?.name}.
            </p>
        </AuthenticatedLayout>
    );
}
