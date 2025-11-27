import apiClient from '@/core/api/client.js'
import { BaseApiService } from '@/core/services/BaseApiService.js'

class ExerciseTemplateService extends BaseApiService {
  constructor() {
    super('exercise-templates')
  }

  fetchParameters(uuid) {
    return apiClient.get(`/${this.resource}/${uuid}/parameters`)
  }

  fetchSteps(uuid) {
    return apiClient.get(`/${this.resource}/${uuid}/steps`)
  }

  createParameter(uuid, payload) {
    return apiClient.post(`/${this.resource}/${uuid}/parameters`, payload)
  }

  updateParameter(parameterUuid, payload) {
    return apiClient.put(`/parameters/${parameterUuid}`, payload)
  }

  deleteParameter(parameterUuid) {
    return apiClient.delete(`/parameters/${parameterUuid}`)
  }
}

export const exerciseTemplateService = new ExerciseTemplateService()
