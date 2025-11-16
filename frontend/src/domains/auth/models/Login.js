import {email, helpers, minLength, required} from '@vuelidate/validators';
import {BaseModel} from "@/core/models/BaseModel.js";

export class Login extends BaseModel {
    constructor(data = {}) {
        data = data || {};
        super(data);

        this.email = data.email ?? '';
        this.password = data.password ?? '';
        this.device_fingerprint = data.device_fingerprint ?? '';
    }

    toApiFormat() {
        return super.toApiFormat();
    }

    static validationRules() {
        return {
            email: {
                required: helpers.withMessage('Введите email', required),
                email: helpers.withMessage('Неверный формат email', email)
            },
            password: {
                required: helpers.withMessage('Пароль обязателен для заполнения', required),
                minLength: helpers.withMessage('Минимум 1 символ', minLength(1))
            }
        };
    }
}
