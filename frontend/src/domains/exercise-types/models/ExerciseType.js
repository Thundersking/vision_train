import { helpers, maxLength, required } from '@vuelidate/validators'
import { BaseModel } from '@/core/models/BaseModel.js'
import { EXERCISE_DIMENSIONS } from '@/domains/exercise-types/constants.js'

const jsonValidator = helpers.withMessage('Введите корректный JSON', (value) => {
  if (!value) return true
  try {
    JSON.parse(value)
    return true
  } catch (e) {
    return false
  }
})

export class ExerciseType extends BaseModel {
  constructor(data = {}) {
    super(data)

    this.name = data.name ?? ''
    this.slug = data.slug ?? ''
    this.dimension = data.dimension ?? '2d'
    this.is_customizable = data.is_customizable ?? false
    this.description = data.description ?? ''
    const metrics = data.metrics ?? data.metrics_json
    this.metrics_json = metrics ? JSON.stringify(metrics, null, 2) : ''
    this.is_active = data.is_active ?? true
  }

  toApiFormat() {
    const payload = {
      name: this.name,
      slug: this.slug,
      dimension: this.dimension,
      is_customizable: this.is_customizable,
      description: this.description || null,
      metrics_json: this.metrics_json || null,
      is_active: this.is_active
    }

    return payload
  }

  static dimensions() {
    return EXERCISE_DIMENSIONS
  }

  static validationRules() {
    return {
      name: {
        required: helpers.withMessage('Название обязательно', required),
        maxLength: helpers.withMessage('Максимум 255 символов', maxLength(255))
      },
      slug: {
        required: helpers.withMessage('Слаг обязателен', required),
        maxLength: helpers.withMessage('Максимум 255 символов', maxLength(255))
      },
      metrics_json: {
        json: jsonValidator
      }
    }
  }
}
