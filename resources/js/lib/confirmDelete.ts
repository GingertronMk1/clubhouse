import { router } from '@inertiajs/vue3';

export function confirmDelete(url: string, message: string): void {
    if (window.confirm(message)) {
        router.delete(url);
    }
}
