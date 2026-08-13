import { Head, usePage } from '@inertiajs/react';
import { useState } from 'react';
import AuthenticatedLayout from '../../Layouts/AuthenticatedLayout';
import AddProjectModal from './AddProjectModal';
import ProjectDetailsModal from './ProjectDetailsModal';

export default function ProjectsIndex({ projects }) {
    const { flash } = usePage().props;
    const [isAddModalOpen, setIsAddModalOpen] = useState(false);
    const [selectedProjectId, setSelectedProjectId] = useState(null);

    return (
        <AuthenticatedLayout>
            <Head title="Projects" />

            <div className="mb-8 flex items-center justify-between">
                <div>
                    <h1 className="text-2xl font-semibold text-gray-900">Projects</h1>
                    <p className="mt-1 text-sm text-gray-500">Manage your projects.</p>
                </div>

                <button
                    type="button"
                    onClick={() => setIsAddModalOpen(true)}
                    className="rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800"
                >
                    Add Project
                </button>
            </div>

            {flash?.success && <div className="mb-6 rounded-lg bg-green-100 p-3 text-green-700">{flash.success}</div>}

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
                        </button>
                    ))
                ) : (
                    <div className="col-span-2 py-10 text-center">
                        <p className="text-gray-500">No projects found. Create your first one!</p>
                    </div>
                )}
            </div>

            <AddProjectModal isOpen={isAddModalOpen} onClose={() => setIsAddModalOpen(false)} />
            <ProjectDetailsModal
                projectId={selectedProjectId}
                isOpen={Boolean(selectedProjectId)}
                onClose={() => setSelectedProjectId(null)}
            />
        </AuthenticatedLayout>
    );
}
