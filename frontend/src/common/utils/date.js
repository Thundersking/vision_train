// Локаль по умолчанию для всех форматирований
const DEFAULT_LOCALE = 'ru-RU'

// Таймзона по умолчанию (можно переопределить через options)
const defaultOptions = {
  timeZone: 'UTC'
}

/**
 * Форматирует дату в виде «25 января 2025 г.»
 */
export function formatDate(value, options = {}) {
  if (!value) {
    return ''
  }

  try {
    const date = value instanceof Date ? value : new Date(value)
    return new Intl.DateTimeFormat(DEFAULT_LOCALE, {
      year: 'numeric',
      month: 'long',
      day: 'numeric',
      ...defaultOptions,
      ...options
    }).format(date)
  } catch (error) {
    console.error('formatDate error', error)
    return String(value)
  }
}

/**
 * Короткий формат «25 янв» — удобен для таблиц
 */
export function formatDateShort(value, options = {}) {
  return formatDate(value, {
    month: 'short',
    day: 'numeric',
    ...options
  })
}

/**
 * Дата и время (например, «25 января 2025 г., 14:35»)
 */
export function formatDateTime(value, options = {}) {
  if (!value) {
    return ''
  }

  try {
    const date = value instanceof Date ? value : new Date(value)
    return new Intl.DateTimeFormat(DEFAULT_LOCALE, {
      year: 'numeric',
      month: 'long',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit',
      ...defaultOptions,
      ...options
    }).format(date)
  } catch (error) {
    console.error('formatDateTime error', error)
    return String(value)
  }
}

/**
 * Относительное время: «3 часа назад», «через 2 дня» и т.п.
 */
export function formatRelative(value, baseDate = new Date()) {
  if (!value) {
    return ''
  }

  try {
    const date = value instanceof Date ? value : new Date(value)
    const diff = date.getTime() - baseDate.getTime()

    const formatter = new Intl.RelativeTimeFormat(DEFAULT_LOCALE, { numeric: 'auto' })

    const seconds = Math.round(diff / 1000)
    const minutes = Math.round(seconds / 60)
    const hours = Math.round(minutes / 60)
    const days = Math.round(hours / 24)

    if (Math.abs(seconds) < 60) {
      return formatter.format(seconds, 'second')
    }
    if (Math.abs(minutes) < 60) {
      return formatter.format(minutes, 'minute')
    }
    if (Math.abs(hours) < 24) {
      return formatter.format(hours, 'hour')
    }
    return formatter.format(days, 'day')
  } catch (error) {
    console.error('formatRelative error', error)
    return String(value)
  }
}
