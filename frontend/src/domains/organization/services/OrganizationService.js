import {BaseApiService} from '@/core/services/BaseApiService.js'
import apiClient from '@/core/api/client.js'

class OrganizationService extends BaseApiService {
  constructor() {
    super('organization')
  }

  fetch() {
    return apiClient.get(`/${this.resource}`)
  }

  update(data) {
    return apiClient.put(`/${this.resource}`, data)
  }
}

export const organizationService = new OrganizationService()
