import { useToast } from 'primevue/usetoast';

export function useNotification() {
    const toast = useToast();

    return {
        success: (message) => {
            toast.add({
                severity: 'success',
                summary: 'Успех',
                detail: message,
                life: 3000
            });
        },

        error: (message) => {
            toast.add({
                severity: 'error',
                summary: 'Ошибка',
                detail: message,
                life: 4000
            });
        },

        warning: (message) => {
            toast.add({
                severity: 'warn',
                summary: 'Предупреждение',
                detail: message,
                life: 3000
            });
        },

        info: (message) => {
            toast.add({
                severity: 'info',
                summary: 'Информация',
                detail: message,
                life: 3000
            });
        }
    };
}
