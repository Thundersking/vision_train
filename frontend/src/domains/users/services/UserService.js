import {BaseApiService} from "@/core/services/BaseApiService.js";
import apiClient from "@/core/api/client.js";

/**
 * Сервис для работы с пользователями
 */
class UserService extends BaseApiService {
    constructor() {
        super('users');
    }

    /**
     * Получение данных текущего пользователя
     * @returns {Promise}
     */
    getMe() {
        return apiClient.get(`/${this.resource}/me`);
    }
}

export const userService = new UserService();
