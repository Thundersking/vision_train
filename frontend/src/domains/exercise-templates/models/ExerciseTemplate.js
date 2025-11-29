import {helpers, maxLength, required} from '@vuelidate/validators'
import {BaseModel} from '@/core/models/BaseModel.js'


export class ExerciseTemplate extends BaseModel {
    constructor(data = {}) {
        super(data)

        this.exercise_type = data.exercise_type ?? ''
        this.name = data.name ?? ''
        this.description = data.description ?? ''
        this.difficulty = data.difficulty ?? 'medium'
        this.duration_seconds = data.duration_seconds ?? null
        this.is_active = data.is_active ?? true
        this.instructions = data.instructions ?? ''

        this.ball_count = data.ball_count ?? null
        this.ball_size = data.ball_size ?? null
        this.target_accuracy_percent = data.target_accuracy_percent ?? null
        this.vertical_area = data.vertical_area ?? null
        this.horizontal_area = data.horizontal_area ?? null
        this.distance_area = data.distance_area ?? null
        this.speed = data.speed ?? null
    }

    toApiFormat() {
        return {
            exercise_type: this.exercise_type,
            name: this.name,
            description: this.description || null,
            difficulty: this.difficulty || null,
            duration_seconds: this.duration_seconds || null,
            is_active: this.is_active,
            instructions: this.instructions || null,

            ball_count: this.ball_count,
            ball_size: this.ball_size,
            target_accuracy_percent: this.target_accuracy_percent,
            vertical_area: this.vertical_area || null,
            horizontal_area: this.horizontal_area || null,
            distance_area: this.distance_area || null,
            speed: this.speed || null
        }
    }

    static validationRules() {
        return {
            exercise_type: {
                required: helpers.withMessage('Укажите тип упражнения', required)
            },
            name: {
                required: helpers.withMessage('Название обязательно', required),
                maxLength: helpers.withMessage('Максимум 255 символов', maxLength(255))
            },

            // Настройки 3D упражнения (необязательные, только для 3D типов)
            ball_count: {
                minValue: helpers.withMessage('Минимум 1', value => value === null || value === '' || value >= 1),
                maxValue: helpers.withMessage('Максимум 50', value => value === null || value === '' || value <= 50)
            },
            ball_size: {
                minValue: helpers.withMessage('Минимум 1', value => value === null || value === '' || value >= 1),
                maxValue: helpers.withMessage('Максимум 10', value => value === null || value === '' || value <= 10)
            },
            target_accuracy_percent: {
                minValue: helpers.withMessage('Минимум 1%', value => value === null || value === '' || value >= 1),
                maxValue: helpers.withMessage('Максимум 100%', value => value === null || value === '' || value <= 100)
            },
            vertical_area: {
                validOption: helpers.withMessage('Выберите: full, top или bottom', value => !value || ['full', 'top', 'bottom'].includes(value))
            },
            horizontal_area: {
                validOption: helpers.withMessage('Выберите: full, left или right', value => !value || ['full', 'left', 'right'].includes(value))
            },
            distance_area: {
                validOption: helpers.withMessage('Выберите: full, near, medium или far', value => !value || ['full', 'near', 'medium', 'far'].includes(value))
            },
            speed: {
                validOption: helpers.withMessage('Выберите: slow, medium или fast', value => !value || ['slow', 'medium', 'fast'].includes(value))
            }
        }
    }
}
