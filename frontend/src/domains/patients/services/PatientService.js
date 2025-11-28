import apiClient from '@/core/api/client.js'
import { BaseApiService } from '@/core/services/BaseApiService.js'

class PatientService extends BaseApiService {
  constructor() {
    super('patients')
  }

  /**
   * Поиск пациентов для автокомплита
   * @param {string} query - поисковый запрос
   * @returns {Promise<Array>} - массив объектов {id, full_name}
   */
  async search(query) {
    const response = await apiClient.get(`/patients/search`, {
      params: {
        search: query
      }
    })
    return response.data?.data || []
  }
}

export const patientService = new PatientService()
