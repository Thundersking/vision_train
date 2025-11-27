import { BaseModel } from '@/core/models/BaseModel.js'
import { helpers, maxLength, required } from '@vuelidate/validators'

export class ExerciseTemplateParameter extends BaseModel {
  constructor(data = {}) {
    super(data)

    this.label = data.label ?? ''
    this.target_value = data.target_value ?? ''
    this.unit = data.unit ?? ''
  }

  toApiFormat() {
    return {
      label: this.label?.trim() || null,
      target_value: this.target_value?.trim() || null,
      unit: this.unit || null
    }
  }

  static validationRules() {
    return {
      label: {
        required: helpers.withMessage('Укажите название параметра', required),
        maxLength: helpers.withMessage('Максимум 255 символов', maxLength(255))
      },
      target_value: {
        maxLength: helpers.withMessage('Максимум 255 символов', maxLength(255))
      },
      unit: {}
    }
  }
}
