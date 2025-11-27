import { helpers, maxLength, required } from '@vuelidate/validators'
import { BaseModel } from '@/core/models/BaseModel.js'

const defaultStep = () => ({
  uid: cryptoRandom(),
  title: '',
  duration: null,
  description: '',
  hint: ''
})

const defaultParameter = () => ({
  uid: cryptoRandom(),
  key: '',
  label: '',
  target_value: '',
  unit: ''
})

const cryptoRandom = () => {
  if (typeof crypto !== 'undefined' && crypto.randomUUID) {
    return crypto.randomUUID()
  }
  return Math.random().toString(36).slice(2)
}

const parsePayload = (payload) => {
  if (!payload) return {}
  if (typeof payload === 'string') {
    try {
      return JSON.parse(payload)
    } catch (e) {
      return {}
    }
  }
  if (typeof payload === 'object' && !Array.isArray(payload)) {
    return payload
  }
  return {}
}

const normalizeSteps = (steps) => {
  if (!Array.isArray(steps) || !steps.length) {
    return [defaultStep()]
  }

  return steps.map((step) => ({
    uid: cryptoRandom(),
    title: step.title ?? '',
    duration: step.duration ?? null,
    description: step.description ?? '',
    hint: step.hint ?? ''
  }))
}

const normalizeParameters = (parameters) => {
  if (!Array.isArray(parameters) || !parameters.length) {
    return []
  }

  return parameters.map((parameter) => ({
    uid: cryptoRandom(),
    key: parameter.key ?? '',
    label: parameter.label ?? '',
    target_value: parameter.target_value ?? parameter.value ?? '',
    unit: parameter.unit ?? ''
  }))
}

const hasValidSteps = helpers.withMessage('Добавьте хотя бы один шаг с названием и длительностью', (steps) => {
  if (!Array.isArray(steps) || !steps.length) {
    return false
  }

  return steps.every((step) => {
    const hasTitle = typeof step.title === 'string' && step.title.trim().length > 0
    const duration = Number(step.duration)
    return hasTitle && Number.isFinite(duration) && duration > 0
  })
})

const hasDuration = helpers.withMessage('Укажите длительность упражнения в секундах', (value) => {
  const numeric = Number(value)
  return Number.isFinite(numeric) && numeric > 0
})

export class ExerciseTemplate extends BaseModel {
  constructor(data = {}) {
    super(data)

    this.exercise_type_id = data.exercise_type_id ?? data.type?.id ?? null
    this.exercise_type = data.type ?? null
    this.title = data.title ?? ''
    this.short_description = data.short_description ?? ''
    this.difficulty = data.difficulty ?? 'medium'
    const payload = parsePayload(data.payload ?? data.payload_json)
    const {
      steps,
      parameters,
      duration_seconds,
      duration,
      instructions,
      ...rest
    } = payload

    this.duration_seconds = duration_seconds ?? duration ?? null
    this.instructions = instructions ?? ''
    this.steps = normalizeSteps(steps)
    this.parameters = normalizeParameters(parameters)
    this.payloadMeta = rest
    this.is_active = data.is_active ?? true
  }

  toApiFormat() {
    const sanitizedSteps = this.steps
      .filter((step) => typeof step.title === 'string' && step.title.trim().length)
      .map((step, index) => ({
        order: index + 1,
        title: step.title.trim(),
        duration: step.duration ? Number(step.duration) : null,
        description: step.description?.trim() || null,
        hint: step.hint?.trim() || null
      }))

    const sanitizedParameters = this.parameters
      .filter((param) => (param.key && param.key.trim()) || (param.label && param.label.trim()))
      .map((param) => ({
        key: param.key?.trim() || null,
        label: param.label?.trim() || null,
        target_value: param.target_value ?? null,
        unit: param.unit?.trim() || null
      }))

    const durationSeconds = this.duration_seconds ? Number(this.duration_seconds) : null

    const payload = {
      ...this.payloadMeta,
      duration_seconds: durationSeconds,
      instructions: this.instructions?.trim() || null,
      parameters: sanitizedParameters,
      steps: sanitizedSteps
    }

    return {
      exercise_type_id: this.exercise_type_id,
      title: this.title,
      short_description: this.short_description || null,
      difficulty: this.difficulty || null,
      payload_json: JSON.stringify(payload),
      is_active: this.is_active
    }
  }

  static validationRules() {
    return {
      exercise_type_id: {
        required: helpers.withMessage('Укажите тип упражнения', required)
      },
      title: {
        required: helpers.withMessage('Название обязательно', required),
        maxLength: helpers.withMessage('Максимум 255 символов', maxLength(255))
      },
      duration_seconds: {
        required: helpers.withMessage('Укажите длительность упражнения', required),
        hasDuration
      },
      steps: {
        required: helpers.withMessage('Добавьте хотя бы один шаг', required),
        hasValidSteps
      }
    }
  }

  static createEmptyStep() {
    return defaultStep()
  }

  static createEmptyParameter() {
    return defaultParameter()
  }
}
