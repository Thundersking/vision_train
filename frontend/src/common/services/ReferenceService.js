import apiClient from '@/core/api/client.js'

class ReferenceService {
  getUnits() {
    return apiClient.get('/reference/units')
  }
}

export const referenceService = new ReferenceService()
