import type { route as routeFn } from 'ziggy-js';
import '@inertiajs/core';

declare global {
    const route: typeof routeFn;
}

interface FlashMessages {
    success?: string;
    error?: string;
    warning?: string;
    info?: string;
}
  
declare module '@inertiajs/core' {
    interface PageProps {
        flash?: FlashMessages;
    }
}