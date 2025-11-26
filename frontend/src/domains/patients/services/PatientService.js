import { BaseApiService } from '@/core/services/BaseApiService.js'

class PatientService extends BaseApiService {
  constructor() {
    super('patients')
  }
}

export const patientService = new PatientService()
