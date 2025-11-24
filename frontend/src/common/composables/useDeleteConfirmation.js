import {useConfirm} from 'primevue/useconfirm';
import {useToast} from 'vue-toastification';

export function useDeleteConfirmation() {
    const confirm = useConfirm();
    const toast = useToast();

    const capitalize = (text) => {
        if (!text) return '';
        return text.charAt(0).toUpperCase() + text.slice(1);
    };

    /**
     * Универсальное удаление с подтверждением
     *
     * @param {Object} options
     * @param {Function} options.deleteFn - Функция удаления (async)
     * @param {string} [options.entityName='запись'] - Название сущности ("пользователя")
     * @param {string} [options.entityTitle] - Заголовок сущности ("Иван Иванов")
     * @param {string|null} [options.currentUserUuid] - UUID текущего пользователя
     * @param {string|null} [options.targetUuid] - UUID удаляемой записи
     * @param {boolean} [options.preventSelfDelete=true] - Проверять удаление себя
     * @param {string} [options.confirmMessage] - Кастомный текст подтверждения
     * @param {string} [options.successMessage] - Кастомный текст успеха
     * @param {Function} [options.onSuccess] - Коллбек после успешного удаления
     * @param {Function} [options.onError] - Коллбек при ошибке
     * @param {boolean} [options.toastOnSuccess=true] - Показывать тост об успехе
     * @param {boolean} [options.toastOnError=true] - Показывать тост об ошибке
     */
    const confirmDelete = async ({
                                     deleteFn,
                                     entityName = 'запись',
                                     entityTitle = '',
                                     currentUserUuid = null,
                                     targetUuid = null,
                                     preventSelfDelete = true,
                                     confirmMessage = null,
                                     successMessage = null,
                                     onSuccess = null,
                                     onError = null,
                                     toastOnSuccess = true,
                                     toastOnError = true
                                 } = {}) => {
        if (typeof deleteFn !== 'function') {
            console.warn('[useDeleteConfirmation] deleteFn is required');
            return false;
        }

        if (
            preventSelfDelete &&
            currentUserUuid &&
            targetUuid &&
            currentUserUuid === targetUuid
        ) {
            toast.warning('Вы не можете удалить себя', { timeout: 3000 });
            return false;
        }

        const entityLabel = `${entityName}${entityTitle ? ` "${entityTitle}"` : ''}`;
        const confirmationText = confirmMessage || `Вы уверены, что хотите удалить ${entityLabel}?`;
        const successText = successMessage || `${capitalize(entityName)}${entityTitle ? ` "${entityTitle}"` : ''} удалён`; // generic

        return new Promise((resolve) => {
            confirm.require({
                message: confirmationText,
                header: 'Подтверждение удаления',
                icon: 'pi pi-exclamation-triangle',
                acceptLabel: 'Удалить',
                rejectLabel: 'Отмена',
                acceptClass: 'p-button-danger',
                accept: async () => {
                    try {
                        await deleteFn();

                        if (toastOnSuccess) {
                            toast.success(successText, { timeout: 3000 });
                        }

                        if (typeof onSuccess === 'function') {
                            await onSuccess();
                        }

                        resolve(true);
                    } catch (error) {
                        console.error('Ошибка удаления:', error);

                        if (toastOnError) {
                            toast.error(
                                error.response?.data?.message || 'Не удалось удалить запись',
                                { timeout: 5000 }
                            );
                        }

                        if (typeof onError === 'function') {
                            onError(error);
                        }

                        resolve(false);
                    }
                },
                reject: () => {
                    resolve(false);
                }
            });
        });
    };

    return {
        confirmDelete
    };
}
