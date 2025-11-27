import {helpers, maxLength, required} from '@vuelidate/validators'
import {BaseModel} from '@/core/models/BaseModel.js'


export class ExerciseTemplate extends BaseModel {
    constructor(data = {}) {
        super(data)

        this.exercise_type_id = data.exercise_type_id ?? data.type?.id ?? null
        this.title = data.title ?? ''
        this.short_description = data.short_description ?? ''
        this.difficulty = data.difficulty ?? 'medium'
        this.is_active = data.is_active ?? true
        this.instructions = data.instructions ?? ''
    }

    toApiFormat() {
        return {
            exercise_type_id: this.exercise_type_id,
            title: this.title,
            short_description: this.short_description || null,
            difficulty: this.difficulty || null,
            is_active: this.is_active,
            instructions: this.instructions || null
        }
    }

    static validationRules() {
        return {
            exercise_type_id: {
                required: helpers.withMessage('Укажите тип упражнения', required)
            },
            title: {
                required: helpers.withMessage('Название обязательно', required),
                maxLength: helpers.withMessage('Максимум 255 символов', maxLength(255))
            }
        }
    }
}
