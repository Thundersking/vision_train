import apiClient from '@/core/api/client.js'
import { BaseApiService } from '@/core/services/BaseApiService.js'

class ExerciseTemplateService extends BaseApiService {
  constructor() {
    super('exercise-templates')
  }
}

export const exerciseTemplateService = new ExerciseTemplateService()
