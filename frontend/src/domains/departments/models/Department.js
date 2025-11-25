import {email, helpers, numeric, required} from '@vuelidate/validators';
import {BaseModel} from "@/core/models/BaseModel.js";
import {DEFAULT_TIMEZONE_MINUTES} from "@/common/constants/timezones.js";

export class Department extends BaseModel {
    constructor(data = {}) {
        data = data || {};
        super(data);

        this.name = data.name ?? '';
        this.email = data.email ?? '';
        this.phone = data.phone ?? '';
        this.address = data.address ?? '';
        this.utc_offset_minutes = data.utc_offset_minutes ?? DEFAULT_TIMEZONE_MINUTES;
        this.is_active = data.is_active ?? true;
        this.created_at = data.created_at ?? null;
        this.updated_at = data.updated_at ?? null;
        this.timezone_display = data.timezone_display ?? '';
    }

    toApiFormat() {
        const data = super.toApiFormat();
        delete data.created_at;
        delete data.updated_at;
        delete data.timezone_display;
        delete data.id;
        return data;
    }

    static validationRules() {
        return {
            name: {
                required: helpers.withMessage('Название обязательно для заполнения', required)
            },
            utc_offset_minutes: {
                required: helpers.withMessage('Часовой пояс обязателен', required),
                numeric: helpers.withMessage('Значение должно быть числом', numeric)
            },
            email: {
                email: helpers.withMessage('Неверный формат email', email)
            },
            is_active: {
                required: helpers.withMessage('Необходимо указать статус', required)
            }
        };
    }
}
