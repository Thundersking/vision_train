import { ref, watch, onUnmounted } from 'vue'

/**
 * Композабл для debounce значения
 * @param {import('vue').Ref} value - реактивное значение для debounce
 * @param {number} delay - задержка в миллисекундах (по умолчанию 300ms)
 * @returns {import('vue').Ref} - debounced значение
 */
export function useDebounce(value, delay = 1000) {
  const debouncedValue = ref(value.value)

  let timeoutId = null

  const stopWatcher = watch(value, (newValue) => {
    if (timeoutId) {
      clearTimeout(timeoutId)
    }

    timeoutId = setTimeout(() => {
      debouncedValue.value = newValue
    }, delay)
  }, { immediate: true })

  onUnmounted(() => {
    if (timeoutId) {
      clearTimeout(timeoutId)
    }
    stopWatcher()
  })

  return debouncedValue
}

