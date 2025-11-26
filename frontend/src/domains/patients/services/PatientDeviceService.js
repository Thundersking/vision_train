import {BaseApiService} from '@/core/services/BaseApiService.js'
import apiClient from '@/core/api/client.js'

class PatientDeviceService extends BaseApiService {
    constructor() {
        super('patients')
    }

    index(patientUuid) {
        return apiClient.get(`/${this.resource}/${patientUuid}/devices`)
    }

    generateToken(patientUuid, payload = {}) {
        return apiClient.post(`/${this.resource}/${patientUuid}/devices/token`, payload)
    }

    delete(patientUuid, deviceUuid) {
        return apiClient.delete(`/${this.resource}/${patientUuid}/devices/${deviceUuid}`)
    }
}

export const patientDeviceService = new PatientDeviceService()
