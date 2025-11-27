import { BaseApiService } from '@/core/services/BaseApiService.js'

class ExerciseTypeService extends BaseApiService {
  constructor() {
    super('exercise-types')
  }
}

export const exerciseTypeService = new ExerciseTypeService()
