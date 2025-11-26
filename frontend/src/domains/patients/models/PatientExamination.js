import { BaseModel } from '@/core/models/BaseModel.js'
import { required, helpers, minLength, maxLength } from '@vuelidate/validators'

export class PatientExamination extends BaseModel {
  constructor(data = {}) {
    super(data)

    this.type = data.type ?? 'primary'
    this.title = data.title ?? ''
    this.description = data.description ?? ''
    this.examination_date = data.examination_date ?? null
    this.results = data.results ?? ''
    this.recommendations = data.recommendations ?? ''
    this.is_active = data.is_active ?? true
  }

  toApiFormat() {
    const payload = super.toApiFormat()
    payload.examination_date = this.examination_date
      ? new Date(this.examination_date).toISOString()
      : null
    return payload
  }

  static validationRules() {
    return {
      type: {
        required: helpers.withMessage('Тип обследования обязателен', required)
      },
      title: {
        required: helpers.withMessage('Название обязательно', required)
      },
      examination_date: {
        required: helpers.withMessage('Дата обследования обязательна', required)
      },
      results: {
        required: helpers.withMessage('Результаты обязательны', required),
        minLength: helpers.withMessage('Минимум 50 символов', minLength(50))
      },
      recommendations: {
        maxLength: helpers.withMessage('Максимум 2000 символов', maxLength(2000))
      }
    }
  }
}
