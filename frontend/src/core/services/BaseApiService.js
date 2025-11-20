import apiClient from '../api/client.js';

export class BaseApiService {
    /**
     * @param {string} resource - Имя ресурса для API (например, 'users', 'clients')
     */
    constructor(resource) {
        this.resource = resource;
    }

    /**
     * Получение списка записей
     * @param {Object} params - Параметры запроса (фильтрация, пагинация)
     * @returns {Promise}
     */
    index(params = {}) {
        return apiClient.get(`/${this.resource}`, { params });
    }

    /**
     * Получение информации о записи
     * @param {String} id - Идентификатор записи
     * @returns {Promise}
     */
    show(id) {
        return apiClient.get(`/${this.resource}/${id}`);
    }

    /**
     * Создание новой записи
     * @param {Object} data - Данные записи
     * @returns {Promise}
     */
    create(data) {
        return apiClient.post(`/${this.resource}`, data);
    }

    /**
     * Обновление данных записи
     * @param {String} id - Идентификатор записи
     * @param {Object} data - Обновленные данные
     * @returns {Promise}
     */
    update(id, data) {
        return apiClient.put(`/${this.resource}/${id}`, data);
    }

    /**
     * Удаление записи
     * @param {String} id - Идентификатор записи
     * @returns {Promise}
     */
    delete(id) {
        return apiClient.delete(`/${this.resource}/${id}`);
    }
}