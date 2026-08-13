import { useForm } from '@inertiajs/react';

export default function AddProjectModal({ isOpen, onClose }) {
    const form = useForm({
        name: '',
        description: '',
        url: '',
    });

    const closeModal = () => {
        form.clearErrors();
        form.reset();
        onClose();
    };

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
