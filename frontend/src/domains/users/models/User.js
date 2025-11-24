import {email, helpers, required} from '@vuelidate/validators';
import {BaseModel} from "@/core/models/BaseModel.js";

export class User extends BaseModel {
    constructor(data = {}) {
        data = data || {};
        super(data);

        this.first_name = data.first_name ?? '';
        this.last_name = data.last_name ?? '';
        this.middle_name = data.middle_name ?? '';
        this.email = data.email ?? '';
        this.phone = data.phone ?? '';
        this.is_active = data.is_active ?? true;
        this.created_at = data.created_at ?? null;
        this.updated_at = data.updated_at ?? null;
    }

    toApiFormat() {
        const data = super.toApiFormat();
        // Убираем поля которые не должны отправляться в API
        delete data.created_at;
        delete data.updated_at;
        delete data.id;
        return data;
    }

    static validationRules() {
        return {
            // first_name: {
            //     required: helpers.withMessage('Имя обязательно для заполнения', required)
            // },
            last_name: {
                required: helpers.withMessage('Фамилия обязательна для заполнения', required)
            },
            email: {
                required: helpers.withMessage('Email обязателен для заполнения', required),
                email: helpers.withMessage('Неверный формат email', email)
            },
            is_active: {
                required: helpers.withMessage('Статус обязателен для заполнения', required)
            }
        };
    }
}