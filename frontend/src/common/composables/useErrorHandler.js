import {useToast} from 'vue-toastification';

export function useErrorHandler() {
    const toast = useToast();
    const handleError = (err, defaultMessage = 'Ошибка при выполнении операции') => {
        const status = err.response?.status;
        const data = err.response?.data;

        switch (status) {
            case 400:
                toast.error(data?.message || 'Неверный запрос');
                break;

            case 403:
                toast.warning('У вас нет доступа к этому ресурсу');
                break;

            case 404:
                toast.error('Ресурс не найден');
                break;

            case 422:
                // Валидация - часто есть поля с ошибками
                if (data?.errors) {
                    const firstError = Object.values(data.errors)[0]?.[0];
                    toast.error(firstError || 'Ошибка валидации');
                } else {
                    toast.error(data?.message || 'Ошибка валидации');
                }
                break;

            case 429:
                toast.warning('Слишком много запросов. Попробуйте позже');
                break;

            case 500:
            case 502:
            case 503:
                toast.error('Ошибка сервера. Попробуйте позже');
                break;

            default:
                if (data?.message) {
                    toast.error(data.message);
                } else if (err.message) {
                    toast.error(err.message);
                } else {
                    toast.error(defaultMessage);
                }
        }
    };

    return { handleError };
}
