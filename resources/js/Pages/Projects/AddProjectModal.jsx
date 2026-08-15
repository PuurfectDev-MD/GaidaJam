import { useForm } from '@inertiajs/react';
import { useMemo, useState } from 'react';

export default function AddProjectModal({ isOpen, onClose, hackatimeProjects = [] }) {
    const [projectSearch, setProjectSearch] = useState('');
    const [isSelectorOpen, setIsSelectorOpen] = useState(false);

    const form = useForm({
        name: '',
        description: '',
        url: '',
        hackatime_project_ids: [],
    });

    const closeModal = () => {
        form.clearErrors();
        form.reset();
        setProjectSearch('');
        setIsSelectorOpen(false);
        onClose();
    };

    const toggleHackatimeProject = (id) => {
        const selected = form.data.hackatime_project_ids;

        if (selected.includes(id)) {
            form.setData(
                'hackatime_project_ids',
                selected.filter((item) => item !== id)
            );
            return;
        }

        form.setData('hackatime_project_ids', [...selected, id]);
    };

    const filteredHackatimeProjects = useMemo(() => {
        const q = projectSearch.trim().toLowerCase();

        if (!q) {
            return hackatimeProjects;
        }

        return hackatimeProjects.filter((project) => {
            const haystack = [project.name, project.external_id, project.url]
                .filter(Boolean)
                .join(' ')
                .toLowerCase();

            return haystack.includes(q);
        });
    }, [hackatimeProjects, projectSearch]);

    const selectedHackatimeProjects = useMemo(() => {
        const selectedIds = new Set(form.data.hackatime_project_ids);
        return hackatimeProjects.filter((project) => selectedIds.has(project.id));
    }, [hackatimeProjects, form.data.hackatime_project_ids]);

    const selectorLabel = selectedHackatimeProjects.length
        ? `${selectedHackatimeProjects.length} project${selectedHackatimeProjects.length === 1 ? '' : 's'} selected`
        : 'Select Hackatime projects';

    const submit = (e) => {
        e.preventDefault();
        form.post('/projects', {
            onSuccess: () => {
                closeModal();
            },
        });
    };

    if (!isOpen) {
        return null;
    }

    return (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
            <div className="w-full max-w-lg rounded-xl bg-white p-6 shadow-xl">
                <div className="mb-4 flex items-center justify-between">
                    <h2 className="text-lg font-semibold text-gray-900">Add Project</h2>
                    <button
                        type="button"
                        onClick={closeModal}
                        className="text-sm font-medium text-gray-500 hover:text-gray-800"
                    >
                        Close
                    </button>
                </div>

                <form onSubmit={submit} className="space-y-4">
                    <div>
                        <label htmlFor="name" className="mb-1 block text-sm font-medium text-gray-700">
                            Project Name
                        </label>
                        <input
                            type="text"
                            id="name"
                            value={form.data.name}
                            onChange={(e) => form.setData('name', e.target.value)}
                            placeholder="My Project"
                            className="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none focus:border-gray-500 focus:ring-1 focus:ring-gray-500"
                        />
                        {form.errors.name && <span className="text-xs text-red-500">{form.errors.name}</span>}
                    </div>

                    <div>
                        <label htmlFor="description" className="mb-1 block text-sm font-medium text-gray-700">
                            Description
                        </label>
                        <textarea
                            id="description"
                            rows="3"
                            value={form.data.description}
                            onChange={(e) => form.setData('description', e.target.value)}
                            placeholder="Project description..."
                            className="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none focus:border-gray-500 focus:ring-1 focus:ring-gray-500"
                        />
                        {form.errors.description && <span className="text-xs text-red-500">{form.errors.description}</span>}
                    </div>

                    <div>
                        <label htmlFor="url" className="mb-1 block text-sm font-medium text-gray-700">
                            Project URL
                        </label>
                        <input
                            type="text"
                            id="url"
                            value={form.data.url}
                            onChange={(e) => form.setData('url', e.target.value)}
                            placeholder="https://example.com"
                            className="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none focus:border-gray-500 focus:ring-1 focus:ring-gray-500"
                        />
                        {form.errors.url && <span className="text-xs text-red-500">{form.errors.url}</span>}
                    </div>

                    <div>
                        <label htmlFor="hackatime-project-search" className="mb-1 block text-sm font-medium text-gray-700">
                            Linked Hackatime Projects
                        </label>
                        <div className="space-y-3">
                            <div className="relative">
                                <button
                                    type="button"
                                    onClick={() => setIsSelectorOpen((prev) => !prev)}
                                    className="flex w-full items-center justify-between rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700"
                                >
                                    <span>{selectorLabel}</span>
                                    <span className="text-xs text-gray-500">{isSelectorOpen ? 'Close' : 'Open'}</span>
                                </button>

                                {isSelectorOpen && (
                                    <div className="absolute z-20 mt-2 w-full rounded-lg border border-gray-200 bg-white p-3 shadow-lg">
                                        <input
                                            id="hackatime-project-search"
                                            type="text"
                                            value={projectSearch}
                                            onChange={(e) => setProjectSearch(e.target.value)}
                                            placeholder="Search by project name, external ID, or URL"
                                            className="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none focus:border-gray-500 focus:ring-1 focus:ring-gray-500"
                                        />

                                        <div className="mt-3 max-h-44 space-y-2 overflow-y-auto">
                                            {filteredHackatimeProjects.length > 0 ? (
                                                filteredHackatimeProjects.map((project) => {
                                                    const isSelected = form.data.hackatime_project_ids.includes(project.id);

                                                    return (
                                                        <button
                                                            key={project.id}
                                                            type="button"
                                                            onClick={() => toggleHackatimeProject(project.id)}
                                                            className={`w-full rounded-lg border px-3 py-2 text-left text-sm transition ${
                                                                isSelected
                                                                    ? 'border-gray-900 bg-gray-900 text-white'
                                                                    : 'border-gray-200 bg-white text-gray-700 hover:border-gray-300'
                                                            }`}
                                                        >
                                                            <p className="font-medium">{project.name}</p>
                                                            {(project.external_id || project.url) && (
                                                                <p className={`mt-1 text-xs ${isSelected ? 'text-gray-200' : 'text-gray-500'}`}>
                                                                    {project.external_id || project.url}
                                                                </p>
                                                            )}
                                                        </button>
                                                    );
                                                })
                                            ) : (
                                                <p className="rounded-lg border border-dashed border-gray-300 px-3 py-4 text-center text-sm text-gray-500">
                                                    No matching Hackatime projects found.
                                                </p>
                                            )}
                                        </div>
                                    </div>
                                )}
                            </div>

                            {selectedHackatimeProjects.length > 0 && (
                                <div className="flex flex-wrap gap-2">
                                    {selectedHackatimeProjects.map((project) => (
                                        <button
                                            key={project.id}
                                            type="button"
                                            onClick={() => toggleHackatimeProject(project.id)}
                                            className="rounded-full bg-gray-900 px-3 py-1 text-xs font-medium text-white"
                                        >
                                            {project.name} ×
                                        </button>
                                    ))}
                                </div>
                            )}
                        </div>
                        {form.errors.hackatime_project_ids && (
                            <span className="text-xs text-red-500">{form.errors.hackatime_project_ids}</span>
                        )}
                        {hackatimeProjects.length === 0 && (
                            <p className="mt-1 text-xs text-gray-500">
                                No Hackatime projects found to link.
                            </p>
                        )}
                    </div>

                    <div className="flex justify-end gap-2 pt-2">
                        <button
                            type="button"
                            onClick={closeModal}
                            className="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            disabled={form.processing}
                            className="rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800 disabled:opacity-70"
                        >
                            {form.processing ? 'Adding...' : 'Add Project'}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    );
}
