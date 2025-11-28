import {helpers, maxLength, required} from '@vuelidate/validators'
import {BaseModel} from '@/core/models/BaseModel.js'

export class Exercise extends BaseModel {
    constructor(data = {}) {
        super(data)

        this.patient_id = data.patient_id ?? null
        this.patient_name = data.patient_name ?? (data.patient?.full_name ?? null)
        this.exercise_template_id = data.exercise_template_id ?? null
        this.exercise_type = data.exercise_type ?? '2d'
        
        // Настройки 3D упражнения
        this.ball_count = data.ball_count ?? null
        this.ball_size = data.ball_size ?? null
        this.target_accuracy_percent = data.target_accuracy_percent ?? null
        this.vertical_area = data.vertical_area ?? null
        this.horizontal_area = data.horizontal_area ?? null
        this.distance_area = data.distance_area ?? null
        this.speed = data.speed ?? null
        
        this.duration_seconds = data.duration_seconds ?? null
        
        // Результаты
        this.fatigue_right_eye = data.fatigue_right_eye ?? null
        this.fatigue_left_eye = data.fatigue_left_eye ?? null
        this.fatigue_head = data.fatigue_head ?? null
        this.patient_decision = data.patient_decision ?? null
        this.notes = data.notes ?? null
        
        this.started_at = data.started_at ?? null
        this.completed_at = data.completed_at ?? null
    }

    toApiFormat() {
        const data = super.toApiFormat()
        delete data.created_at
        delete data.updated_at
        delete data.id
        delete data.uuid
        delete data.patient_name // patient_name не отправляется на бэкенд, только для отображения
        return data
    }

    static validationRules() {
        return {
            patient_id: {
                required: helpers.withMessage('Пациент обязателен', required)
            },
            exercise_type: {
                required: helpers.withMessage('Тип упражнения обязателен', required)
            },
            // Настройки 3D (необязательные)
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
            },
            duration_seconds: {
                minValue: helpers.withMessage('Минимум 0', value => value === null || value === '' || value >= 0)
            },
            fatigue_right_eye: {
                minValue: helpers.withMessage('Минимум 1', value => value === null || value === '' || value >= 1),
                maxValue: helpers.withMessage('Максимум 5', value => value === null || value === '' || value <= 5)
            },
            fatigue_left_eye: {
                minValue: helpers.withMessage('Минимум 1', value => value === null || value === '' || value >= 1),
                maxValue: helpers.withMessage('Максимум 5', value => value === null || value === '' || value <= 5)
            },
            fatigue_head: {
                minValue: helpers.withMessage('Минимум 1', value => value === null || value === '' || value >= 1),
                maxValue: helpers.withMessage('Максимум 5', value => value === null || value === '' || value <= 5)
            },
            patient_decision: {
                validOption: helpers.withMessage('Выберите: continue или stop', value => !value || ['continue', 'stop'].includes(value))
            }
        }
    }
}

