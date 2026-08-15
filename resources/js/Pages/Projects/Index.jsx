import { Head, router, usePage } from '@inertiajs/react';
import { useState } from 'react';
import AuthenticatedLayout from '../../Layouts/AuthenticatedLayout';
import AddProjectModal from './AddProjectModal';
import ProjectDetailsModal from './ProjectDetailsModal';
import axios from 'axios';

export default function ProjectsIndex({
    projects,
    hackatimeProjects,
    hackatimeConnected,
    hackatimeConfigured,
    hackatimeAuthUrl,
    hackatimeDisconnectRoute,
}) {
    const { flash } = usePage().props;
    const [isAddModalOpen, setIsAddModalOpen] = useState(false);
    const [selectedProjectId, setSelectedProjectId] = useState(null);
    const [isSyncingHackatime, setIsSyncingHackatime] = useState(false);
    const [isConnectModalOpen, setIsConnectModalOpen] = useState(false);
    const [isDisconnectingHackatime, setIsDisconnectingHackatime] = useState(false);

    const syncHackatimeProjects = async () => {
        setIsSyncingHackatime(true);

        try {
            await axios.post('/hackatime-projects/sync');
            router.reload({ only: ['projects', 'hackatimeProjects', 'hackatimeConnected'] });
        } finally {
            setIsSyncingHackatime(false);
        }
    };

    const disconnectHackatime = async () => {
        setIsDisconnectingHackatime(true);

        try {
            await axios.post(hackatimeDisconnectRoute);
            router.reload({ only: ['hackatimeConnected', 'projects', 'hackatimeProjects'] });
        } finally {
            setIsDisconnectingHackatime(false);
        }
    };

    return (
        <AuthenticatedLayout>
            <Head title="Projects" />

            <div className="mb-8 flex items-center justify-between">
                <div>
                    <h1 className="text-2xl font-semibold text-gray-900">Projects</h1>
                    <p className="mt-1 text-sm text-gray-500">Manage your projects.</p>
                    <p className="mt-1 text-xs text-gray-500">
                        Hackatime status: {hackatimeConnected ? 'Connected' : hackatimeConfigured ? 'Ready to connect' : 'OAuth not configured'}
                    </p>
                </div>

                <div className="flex items-center gap-2">
                    {!hackatimeConnected && (
                        <button
                            type="button"
                            onClick={() => setIsConnectModalOpen(true)}
                            className={`rounded-lg border px-4 py-2 text-sm font-medium ${
                                hackatimeConfigured
                                    ? 'border-gray-300 text-gray-700 hover:bg-gray-50'
                                    : 'cursor-not-allowed border-gray-200 text-gray-400'
                            }`}
                            disabled={!hackatimeConfigured}
                        >
                            Connect Hackatime
                        </button>
                    )}

                    {hackatimeConnected && (
                        <button
                            type="button"
                            onClick={disconnectHackatime}
                            disabled={isDisconnectingHackatime}
                            className="rounded-lg border border-red-300 px-4 py-2 text-sm font-medium text-red-700 hover:bg-red-50 disabled:cursor-not-allowed disabled:opacity-60"
                        >
                            {isDisconnectingHackatime ? 'Disconnecting...' : 'Disconnect Hackatime'}
                        </button>
                    )}

                    <button
                        type="button"
                        onClick={syncHackatimeProjects}
                        disabled={isSyncingHackatime || !hackatimeConnected}
                        className="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-60"
                        title={!hackatimeConnected ? 'Connect Hackatime first to enable sync.' : 'Sync projects from Hackatime'}
                    >
                        {isSyncingHackatime ? 'Syncing...' : 'Sync Hackatime'}
                    </button>

                    <button
                        type="button"
                        onClick={() => setIsAddModalOpen(true)}
                        className="rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800"
                    >
                        Add Project
                    </button>
                </div>
            </div>

            {flash?.success && <div className="mb-6 rounded-lg bg-green-100 p-3 text-green-700">{flash.success}</div>}
            {flash?.error && <div className="mb-6 rounded-lg bg-red-100 p-3 text-red-700">{flash.error}</div>}

            <div className="grid grid-cols-1 gap-5 md:grid-cols-2">
                {projects.length > 0 ? (
                    projects.map((project) => (
                        <button
                            key={project.id}
                            type="button"
                            onClick={() => setSelectedProjectId(project.id)}
                            className="rounded-xl border border-gray-200 bg-white p-6 text-left shadow-sm transition hover:border-gray-300 hover:shadow"
                        >
                            <div className="flex items-start justify-between">
                                <h3 className="font-semibold text-gray-900">{project.name}</h3>
                                <span className="rounded-full bg-green-100 px-2.5 py-1 text-xs font-medium text-green-700">
                                    Active
                                </span>
                            </div>

                            <p className="mt-3 text-sm leading-6 text-gray-600">
                                {project.description?.length > 100
                                    ? `${project.description.slice(0, 100)}...`
                                    : project.description || 'No description provided.'}
                            </p>

                            <p className="mt-4 text-xs font-medium uppercase tracking-wide text-gray-500">
                                Linked Hackatime Projects: {project.hackatime_projects?.length || 0}
                            </p>
                        </button>
                    ))
                ) : (
                    <div className="col-span-2 py-10 text-center">
                        <p className="text-gray-500">No projects found. Create your first one!</p>
                    </div>
                )}
            </div>

            <AddProjectModal
                isOpen={isAddModalOpen}
                onClose={() => setIsAddModalOpen(false)}
                hackatimeProjects={hackatimeProjects}
            />
            <ProjectDetailsModal
                projectId={selectedProjectId}
                isOpen={Boolean(selectedProjectId)}
                onClose={() => setSelectedProjectId(null)}
                hackatimeProjects={hackatimeProjects}
            />

            {isConnectModalOpen && (
                <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
                    <div className="w-full max-w-lg rounded-xl bg-white p-6 shadow-xl">
                        <div className="mb-4 flex items-center justify-between">
                            <h2 className="text-lg font-semibold text-gray-900">Connect Hackatime</h2>
                            <button
                                type="button"
                                onClick={() => {
                                    setIsConnectModalOpen(false);
                                    setConnectErrors({});
                                }}
                                className="text-sm font-medium text-gray-500 hover:text-gray-800"
                            >
                                Close
                            </button>
                        </div>

                        <p className="mb-4 text-sm text-gray-600">
                            Continue to Hackatime and authorize this app with your Hack Club account.
                        </p>

                        <div className="flex justify-end gap-2 pt-2">
                            <button
                                type="button"
                                onClick={() => setIsConnectModalOpen(false)}
                                className="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                            >
                                Cancel
                            </button>
                            <a
                                href={hackatimeAuthUrl}
                                className="rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800"
                            >
                                Continue
                            </a>
                        </div>
                    </div>
                </div>
            )}
        </AuthenticatedLayout>
    );
}
