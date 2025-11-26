import {BaseApiService} from '@/core/services/BaseApiService.js'
import apiClient from '@/core/api/client.js'

class PatientExaminationService extends BaseApiService {
    constructor() {
        super('patients')
    }

    index(patientUuid, params = {}) {
        return apiClient.get(`/${this.resource}/${patientUuid}/examinations`, {params})
    }

    create(patientUuid, payload) {
        return apiClient.post(`/${this.resource}/${patientUuid}/examinations`, payload)
    }
}

export const patientExaminationService = new PatientExaminationService()
