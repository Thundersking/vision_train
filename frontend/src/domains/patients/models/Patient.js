import { required, helpers, email, numeric, minValue, maxValue, maxLength } from '@vuelidate/validators'
import { BaseModel } from '@/core/models/BaseModel.js'
import { GENDER_OPTIONS } from '@/domains/patients/сonstants/constants.js'

export class Patient extends BaseModel {
  constructor(data = {}) {
    super(data)

    this.first_name = data.first_name ?? ''
    this.last_name = data.last_name ?? ''
    this.middle_name = data.middle_name ?? ''
    this.email = data.email ?? ''
    this.phone = data.phone ?? ''
    this.gender = data.gender ?? 'male'
    this.hand_size_cm = data.hand_size_cm !== undefined && data.hand_size_cm !== null
      ? Number(data.hand_size_cm)
      : null
    this.birth_date = data.birth_date ? new Date(data.birth_date) : null
    this.user_id = data.user_id ?? data.doctor?.id ?? null
    this.user_uuid = data.user_uuid ?? data.doctor?.uuid ?? data.user?.uuid ?? null
    this.notes = data.notes ?? ''
    this.is_active = data.is_active ?? true

    this.full_name = data.full_name ?? ''
    this.gender_label = data.gender_label ?? ''
    this.doctor = data.doctor ?? null
    this.created_at = data.created_at ?? null
    this.updated_at = data.updated_at ?? null
  }

  toApiFormat() {
    const payload = super.toApiFormat()
    payload.birth_date = this.formatDateForApi(this.birth_date)
    payload.hand_size_cm = this.hand_size_cm !== null && this.hand_size_cm !== ''
      ? parseFloat(this.hand_size_cm)
      : null
    payload.user_id = this.user_id ?? this.doctor?.id ?? null
    payload.is_active = Boolean(this.is_active)
    return payload
  }

  static validationRules() {
    const minutesMessage = 'Размер руки должен быть числом от 5 до 30'
    return {
      first_name: {
        required: helpers.withMessage('Имя обязательно', required)
      },
      last_name: {
        required: helpers.withMessage('Фамилия обязательна', required)
      },
      email: {
        email: helpers.withMessage('Некорректный email', email)
      },
      phone: {
        required: helpers.withMessage('Телефон обязателен', required)
      },
      gender: {
        required: helpers.withMessage('Пол обязателен', required)
      },
      hand_size_cm: {
        required: helpers.withMessage('Размер руки обязателен', required),
        numeric: helpers.withMessage(minutesMessage, numeric),
        minValue: helpers.withMessage(minutesMessage, minValue(5)),
        maxValue: helpers.withMessage(minutesMessage, maxValue(30))
      },
      birth_date: {
        required: helpers.withMessage('Дата рождения обязательна', required)
      },
      user_id: {
        required: helpers.withMessage('Необходимо выбрать врача', required)
      },
      notes: {
        maxLength: helpers.withMessage('Максимум 1000 символов', maxLength(1000))
      }
    }
  }

  static genderOptions() {
    return GENDER_OPTIONS
  }
}
