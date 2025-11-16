export class BaseModel {
    constructor(data = {}) {
        this.id = data?.id || null;
        this.uuid = data?.uuid || null;
    }

    /**
     * Базовый метод преобразования в API формат
     * @returns {any}
     */
    toApiFormat() {
        // Создаем копию объекта без ссылки
        return {...this};
    }

    /**
     * Форматирует дату для API в формат YYYY-MM-DD
     * @param {Date|string} dateValue - Дата для форматирования
     * @returns {string|null} - Форматированная дата или null
     */
    formatDateForApi(dateValue) {
        if (!dateValue) return null;

        const date = new Date(dateValue);

        // Проверка на валидность даты
        if (isNaN(date.getTime())) return null;

        return `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}-${String(date.getDate()).padStart(2, '0')}`;
    }

    /**
     * Статический метод создания из API данных
     * @param data
     * @returns {BaseModel}
     */
    static fromApi(data) {
        return new this(data);
    }

    /**
     * Обновить объект данными из другого объекта
     * @param data
     * @returns {BaseModel}
     */
    update(data) {
        Object.assign(this, data);
        return this;
    }

    /**
     * Валидационные правила (должны быть переопределены)
     * @param instance
     * @returns {{}}
     */
    static validationRules(instance = null) {
        return {};
    }

}
