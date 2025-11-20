import { useNotification } from '@/common/composables/useNotification.js';

export function useErrorHandler() {
    const { error: showError, warning: showWarning, info: showInfo } = useNotification();

    const handleError = (err, defaultMessage = 'Ошибка при выполнении операции') => {
        const status = err.response?.status;
        const data = err.response?.data;

        switch (status) {
            case 400:
                showError(data?.message || 'Неверный запрос');
                break;

            case 403:
                showWarning('У вас нет доступа к этому ресурсу');
                break;

            case 404:
                showError('Ресурс не найден');
                break;

            case 422:
                // Валидация - часто есть поля с ошибками
                if (data?.errors) {
                    const firstError = Object.values(data.errors)[0]?.[0];
                    showError(firstError || 'Ошибка валидации');
                } else {
                    showError(data?.message || 'Ошибка валидации');
                }
                break;

            case 429:
                showWarning('Слишком много запросов. Попробуйте позже');
                break;

            case 500:
            case 502:
            case 503:
                showError('Ошибка сервера. Попробуйте позже');
                break;

            default:
                if (data?.message) {
                    showError(data.message);
                } else if (err.message) {
                    showError(err.message);
                } else {
                    showError(defaultMessage);
                }
        }
    };

    return { handleError };
}
