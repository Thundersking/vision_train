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

        this.ball_count = data.ball_count ?? null
        this.ball_size = data.ball_size ?? null
        this.target_accuracy_percent = data.target_accuracy_percent ?? null
        this.vertical_area = data.vertical_area ?? ''
        this.horizontal_area = data.horizontal_area ?? ''
        this.distance_area = data.distance_area ?? ''
        this.speed = data.speed ?? ''
    }

    toApiFormat() {
        return {
            exercise_type_id: this.exercise_type_id,
            title: this.title,
            short_description: this.short_description || null,
            difficulty: this.difficulty || null,
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
            exercise_type_id: {
                required: helpers.withMessage('Укажите тип упражнения', required)
            },
            title: {
                required: helpers.withMessage('Название обязательно', required),
                maxLength: helpers.withMessage('Максимум 255 символов', maxLength(255))
            },

            ball_count: {
                required: helpers.withMessage('Укажите количество мячей', required),
                minValue: helpers.withMessage('Минимум 1', value => value === null || value === '' || value >= 1),
                maxValue: helpers.withMessage('Максимум 50', value => value === null || value === '' || value <= 50)
            },
            ball_size: {
                required: helpers.withMessage('Укажите размер мяча', required),
                minValue: helpers.withMessage('Минимум 1', value => value === null || value === '' || value >= 1),
                maxValue: helpers.withMessage('Максимум 10', value => value === null || value === '' || value <= 10)
            },
            target_accuracy_percent: {
                required: helpers.withMessage('Укажите целевую точность', required),
                minValue: helpers.withMessage('Минимум 1%', value => value === null || value === '' || value >= 1),
                maxValue: helpers.withMessage('Максимум 100%', value => value === null || value === '' || value <= 100)
            },
            vertical_area: {
                required: helpers.withMessage('Выберите вертикальную зону', required),
                validOption: helpers.withMessage('Выберите: full, top или bottom', value => ['full', 'top', 'bottom'].includes(value))
            },
            horizontal_area: {
                required: helpers.withMessage('Выберите горизонтальную зону', required),
                validOption: helpers.withMessage('Выберите: full, left или right', value => ['full', 'left', 'right'].includes(value))
            },
            distance_area: {
                required: helpers.withMessage('Выберите зону расстояния', required),
                validOption: helpers.withMessage('Выберите: full, near, medium или far', value => ['full', 'near', 'medium', 'far'].includes(value))
            },
            speed: {
                required: helpers.withMessage('Выберите скорость', required),
                validOption: helpers.withMessage('Выберите: slow, medium или fast', value => ['slow', 'medium', 'fast'].includes(value))
            }
        }
    }
}
