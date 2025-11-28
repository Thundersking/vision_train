import {BaseApiService} from '@/core/services/BaseApiService.js'
import apiClient from '@/core/api/client.js'

class ExerciseService extends BaseApiService {
    constructor() {
        super('exercises')
    }

    indexByPatient(patientUuid, params = {}) {
        return apiClient.get(`/patients/${patientUuid}/exercises`, { params })
    }
}

export const exerciseService = new ExerciseService()

