import { router } from '@inertiajs/react';
import axios from 'axios';
import { useEffect, useState } from 'react';

export default function ProjectDetailsModal({ projectId, isOpen, onClose }) {
    const [project, setProject] = useState(null);
    const [isLoading, setIsLoading] = useState(false);
    const [isEditing, setIsEditing] = useState(false);
    const [isSaving, setIsSaving] = useState(false);
    const [isDeleting, setIsDeleting] = useState(false);
    const [isDeleteConfirmOpen, setIsDeleteConfirmOpen] = useState(false);
    const [errorMessage, setErrorMessage] = useState('');
    const [formErrors, setFormErrors] = useState({});
    const [formData, setFormData] = useState({
        name: '',
        description: '',
        url: '',
    });

    useEffect(() => {
        const loadProject = async () => {
            if (!isOpen || !projectId) {
                return;
            }

            setIsLoading(true);
            setErrorMessage('');
            setFormErrors({});
            setIsEditing(false);

            try {
                const response = await axios.get(`/projects/${projectId}`);
                const fetchedProject = response.data.project;
                setProject(fetchedProject);
                setFormData({
                    name: fetchedProject.name || '',
                    description: fetchedProject.description || '',
                    url: fetchedProject.url || '',
                });
            } catch (error) {
                setErrorMessage(error.response?.data?.message || 'Failed to load project details.');
            } finally {
                setIsLoading(false);
            }
        };

        loadProject();
    }, [isOpen, projectId]);

    const closeModal = () => {
        setProject(null);
        setIsEditing(false);
        setIsDeleteConfirmOpen(false);
        setFormErrors({});
        setErrorMessage('');
        onClose();
    };

    const handleUpdate = async (e) => {
        e.preventDefault();

        if (!project) {
            return;
        }

        setIsSaving(true);
        setErrorMessage('');
        setFormErrors({});

        try {
            const response = await axios.put(`/projects/${project.id}`, formData);
            const updatedProject = response.data.project;

            setProject(updatedProject);
            setFormData({
                name: updatedProject.name || '',
                description: updatedProject.description || '',
                url: updatedProject.url || '',
            });
            setIsEditing(false);
            router.reload({ only: ['projects'] });
        } catch (error) {
            if (error.response?.status === 422) {
                setFormErrors(error.response.data.errors || {});
            } else {
                setErrorMessage(error.response?.data?.message || 'Failed to update project.');
            }
        } finally {
            setIsSaving(false);
        }
    };

    const handleDelete = async () => {
        if (!project) {
            return;
        }

        setIsDeleting(true);
        setErrorMessage('');

        try {
            await axios.delete(`/projects/${project.id}`);
            closeModal();
            router.reload({ only: ['projects'] });
        } catch (error) {
            setErrorMessage(error.response?.data?.message || 'Failed to delete project.');
        } finally {
            setIsDeleting(false);
        }
    };

    if (!isOpen) {
        return null;
    }

    return (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
            <div className="w-full max-w-xl rounded-xl bg-white p-6 shadow-xl">
                <div className="mb-5 flex items-start justify-between gap-4">
                    <div>
                        <h2 className="text-xl font-semibold text-gray-900">
                            {isLoading ? 'Loading...' : project?.name || 'Project'}
                        </h2>
                        <p className="mt-1 text-sm text-gray-500">Project details</p>
                    </div>

                    <button
                        type="button"
                        onClick={onClose}
                        className="text-sm font-medium text-gray-500 hover:text-gray-800"
                    >
                        Close
                    </button>
                </div>

                {errorMessage && <div className="mb-4 rounded-lg bg-red-100 p-3 text-sm text-red-700">{errorMessage}</div>}

                {isLoading ? (
                    <p className="text-sm text-gray-600">Fetching project details...</p>
                ) : isEditing ? (
                    <form onSubmit={handleUpdate} className="space-y-4">
                        <div>
                            <label htmlFor="edit-name" className="mb-1 block text-sm font-medium text-gray-700">
                                Project Name
                            </label>
                            <input
                                id="edit-name"
                                type="text"
                                value={formData.name}
                                onChange={(e) => setFormData((prev) => ({ ...prev, name: e.target.value }))}
                                className="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none focus:border-gray-500 focus:ring-1 focus:ring-gray-500"
                            />
                            {formErrors.name && <span className="text-xs text-red-500">{formErrors.name[0]}</span>}
                        </div>

                        <div>
                            <label htmlFor="edit-description" className="mb-1 block text-sm font-medium text-gray-700">
                                Description
                            </label>
                            <textarea
                                id="edit-description"
                                rows="4"
                                value={formData.description}
                                onChange={(e) => setFormData((prev) => ({ ...prev, description: e.target.value }))}
                                className="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none focus:border-gray-500 focus:ring-1 focus:ring-gray-500"
                            />
                            {formErrors.description && (
                                <span className="text-xs text-red-500">{formErrors.description[0]}</span>
                            )}
                        </div>

                        <div>
                            <label htmlFor="edit-url" className="mb-1 block text-sm font-medium text-gray-700">
                                Project URL
                            </label>
                            <input
                                id="edit-url"
                                type="text"
                                value={formData.url}
                                onChange={(e) => setFormData((prev) => ({ ...prev, url: e.target.value }))}
                                className="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none focus:border-gray-500 focus:ring-1 focus:ring-gray-500"
                            />
                            {formErrors.url && <span className="text-xs text-red-500">{formErrors.url[0]}</span>}
                        </div>

                        <div className="mt-6 flex justify-end gap-2">
                            <button
                                type="button"
                                onClick={() => {
                                    setIsEditing(false);
                                    setFormErrors({});
                                    setFormData({
                                        name: project?.name || '',
                                        description: project?.description || '',
                                        url: project?.url || '',
                                    });
                                }}
                                className="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                            >
                                Cancel
                            </button>
                            <button
                                type="submit"
                                disabled={isSaving}
                                className="rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800 disabled:opacity-70"
                            >
                                {isSaving ? 'Saving...' : 'Save Changes'}
                            </button>
                        </div>
                    </form>
                ) : (
                    <div className="space-y-4">
                        <div>
                            <p className="text-xs font-medium uppercase tracking-wide text-gray-500">Description</p>
                            <p className="mt-1 text-sm leading-6 text-gray-700">
                                {project?.description || 'No description provided.'}
                            </p>
                        </div>

                        <div>
                            <p className="text-xs font-medium uppercase tracking-wide text-gray-500">URL</p>
                            {project?.url ? (
                                <a
                                    href={project.url}
                                    target="_blank"
                                    rel="noreferrer"
                                    className="mt-1 inline-block break-all text-sm font-medium text-blue-600 hover:text-blue-700"
                                >
                                    {project.url}
                                </a>
                            ) : (
                                <p className="mt-1 text-sm text-gray-700">No URL provided.</p>
                            )}
                        </div>
                    </div>
                )}

                <div className="mt-6 flex justify-end">
                    {!isLoading && !isEditing && project && (
                        <div className="flex w-full justify-between">
                            <button
                                type="button"
                                    onClick={() => setIsDeleteConfirmOpen(true)}
                                disabled={isDeleting}
                                className="rounded-lg border border-red-300 px-4 py-2 text-sm font-medium text-red-600 hover:bg-red-50 disabled:opacity-70"
                            >
                                    Delete
                            </button>

                            <div className="flex gap-2">
                                <button
                                    type="button"
                                    onClick={() => setIsEditing(true)}
                                    className="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                                >
                                    Edit
                                </button>
                                <button
                                    type="button"
                                    onClick={closeModal}
                                    className="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                                >
                                    Close
                                </button>
                            </div>
                        </div>
                    )}
                </div>
            </div>

            {isDeleteConfirmOpen && (
                <div className="fixed inset-0 z-[60] flex items-center justify-center bg-black/40 p-4">
                    <div className="w-full max-w-md rounded-xl bg-white p-6 shadow-xl">
                        <h3 className="text-lg font-semibold text-gray-900">Delete Project</h3>
                        <p className="mt-2 text-sm text-gray-600">
                            Are you sure you want to delete <span className="font-medium">{project?.name}</span>? This
                            action cannot be undone.
                        </p>

                        <div className="mt-6 flex justify-end gap-2">
                            <button
                                type="button"
                                onClick={() => setIsDeleteConfirmOpen(false)}
                                className="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                            >
                                Cancel
                            </button>
                            <button
                                type="button"
                                onClick={handleDelete}
                                disabled={isDeleting}
                                className="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700 disabled:opacity-70"
                            >
                                {isDeleting ? 'Deleting...' : 'Yes, Delete'}
                            </button>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
}
