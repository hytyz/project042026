import { useCallback } from 'react';

export type CleanupFn = () => void;

export function useMobileNavigation(): CleanupFn {
    return useCallback(() => {
        // remove pointer-events from body
        document.body.style.removeProperty('pointer-events');
    }, []);
}
