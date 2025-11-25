import {BaseModel} from '@/core/models/BaseModel.js'
import {email, helpers, required} from '@vuelidate/validators'

export class Organization extends BaseModel {
  constructor(data = {}) {
    super(data)

    this.name = data.name ?? ''
    this.domain = data.domain ?? ''
    this.type = data.type ?? ''
    this.type_label = data.type_label ?? ''
    this.subscription_plan = data.subscription_plan ?? ''
    this.email = data.email ?? ''
    this.phone = data.phone ?? ''
    this.inn = data.inn ?? ''
    this.kpp = data.kpp ?? ''
    this.ogrn = data.ogrn ?? ''
    this.legal_address = data.legal_address ?? ''
    this.actual_address = data.actual_address ?? ''
    this.director_name = data.director_name ?? ''
    this.license_number = data.license_number ?? ''
    this.license_issued_at = data.license_issued_at ?? null
    this.is_active = data.is_active ?? true
    this.created_at = data.created_at ?? null
    this.updated_at = data.updated_at ?? null
  }

  toApiFormat() {
    const data = super.toApiFormat()

    data.license_issued_at = this.formatDateForApi(this.license_issued_at)

    delete data.domain
    delete data.type_label
    delete data.created_at
    delete data.updated_at

    return data
  }

  static validationRules() {
    return {
      name: {
        required: helpers.withMessage('Название обязательно для заполнения', required)
      },
      type: {
        required: helpers.withMessage('Укажите тип организации', required)
      },
      email: {
        email: helpers.withMessage('Неверный формат email', email)
      },
      is_active: {
        required: helpers.withMessage('Укажите статус организации', required)
      }
    }
  }
}
