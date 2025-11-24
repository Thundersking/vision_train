import { computed, inject } from 'vue';

/**
 * Composable для работы с ошибками полей формы
 * Поддерживает как frontend (Vuelidate), так и backend валидацию
 *
 * @param {string} fieldName - Имя поля (например, 'first_name')
 * @param {Object} validation - Объект валидации Vuelidate (опционально)
 * @returns {Object} Методы и состояния для работы с ошибками
 */
export function useFormFieldErrors(fieldName, validation = null) {
    // Получаем доступ к контексту формы (предоставленному через provide в BaseForm)
    const baseFormErrors = inject('baseFormErrors', null);

    /**
     * Проверка наличия backend ошибки для поля
     */
    const hasBackendError = computed(() => {
        if (!baseFormErrors?.fieldErrors?.value) return false;
        const errors = baseFormErrors.fieldErrors.value[fieldName];
        return Array.isArray(errors) && errors.length > 0;
    });

    /**
     * Проверка наличия frontend ошибки (Vuelidate)
     */
    const hasFrontendError = computed(() => {
        if (!validation || !fieldName) return false;
        const field = validation[fieldName];
        return field && field.$error === true;
    });

    /**
     * Общая проверка: есть ли ошибка (любая)
     * Приоритет: сначала проверяем frontend, затем backend
     */
    const hasError = computed(() => {
        return hasFrontendError.value || hasBackendError.value;
    });

    /**
     * Получение текста ошибки
     * Приоритет: frontend > backend
     */
    const errorMessage = computed(() => {
        // Приоритет 1: Frontend валидация
        if (hasFrontendError.value && validation[fieldName]) {
            const errors = validation[fieldName].$errors;
            if (errors && errors.length > 0) {
                return errors[0].$message || '';
            }
        }

        // Приоритет 2: Backend валидация
        if (hasBackendError.value) {
            const errors = baseFormErrors.fieldErrors.value[fieldName];
            return errors[0]; // Laravel возвращает массив строк
        }

        return '';
    });

    /**
     * Очистка backend ошибки для конкретного поля
     * Вызывается при изменении значения пользователем
     */
    const clearBackendError = () => {
        if (baseFormErrors?.clearFieldError) {
            baseFormErrors.clearFieldError(fieldName);
        }
    };

    return {
        // Основные флаги
        hasError,
        hasBackendError,
        hasFrontendError,

        // Сообщение об ошибке
        errorMessage,

        // Методы управления
        clearBackendError
    };
}
