import apiClient from '@/core/api/client.js'

class PatientExaminationService {
  index(patientUuid, params = {}) {
    return apiClient.get(`/patients/${patientUuid}/examinations`, { params })
  }

  create(patientUuid, payload) {
    return apiClient.post(`/patients/${patientUuid}/examinations`, payload)
  }
}

export const patientExaminationService = new PatientExaminationService()
