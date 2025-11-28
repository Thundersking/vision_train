import { ref, computed, watch } from 'vue'
import { useDebounce } from './useDebounce.js'

/**
 * Композабл для работы с автокомплитом
 * @param {Function|string} storeOrSearchFn - либо store (объект с методом search), либо название store (строка), либо функция поиска
 * @param {Object} options - опции
 * @param {number} options.debounceDelay - задержка debounce в мс (по умолчанию 300)
 * @param {number} options.minLength - минимальная длина запроса для поиска (по умолчанию 2)
 * @param {string} options.optionLabel - поле для отображения (по умолчанию 'name')
 * @param {string} options.optionValue - поле для значения (по умолчанию 'id')
 * @returns {Object} - объект с реактивными значениями и методами
 */
export function useAutocomplete(storeOrSearchFn, options = {}) {
  const {
    debounceDelay = 300,
    minLength = 2,
    optionLabel = 'name',
    optionValue = 'id'
  } = options

  const query = ref('')
  const suggestions = ref([])
  const loading = ref(false)
  const selectedItem = ref(null)

  const debouncedQuery = useDebounce(query, debounceDelay)

  // Определяем функцию поиска
  const getSearchFn = () => {
    // Если передан store (объект с методом search)
    if (typeof storeOrSearchFn === 'object' && storeOrSearchFn && typeof storeOrSearchFn.search === 'function') {
      return (query) => storeOrSearchFn.search(query)
    }
    // Если передана функция
    if (typeof storeOrSearchFn === 'function') {
      return storeOrSearchFn
    }
    // Если передана строка (название store) - это обработаем в компоненте
    return null
  }

  const searchFn = getSearchFn()

  // Следим за изменением debouncedQuery и выполняем поиск
  watch(debouncedQuery, async (searchTerm) => {
    if (!searchTerm || searchTerm.length < minLength || !searchFn) {
      suggestions.value = []
      return
    }

    loading.value = true
    try {
      const results = await searchFn(searchTerm)
      suggestions.value = Array.isArray(results) ? results : (results?.data || [])
    } catch (error) {
      console.error('Autocomplete search error:', error)
      suggestions.value = []
    } finally {
      loading.value = false
    }
  })

  const search = async (event) => {
    // Обновляем query, что автоматически запустит debounced поиск
    query.value = event.query || ''
  }

  const displayValue = computed(() => {
    if (!selectedItem.value) return ''
    return typeof selectedItem.value === 'object' 
      ? selectedItem.value[optionLabel] 
      : selectedItem.value
  })

  const reset = () => {
    query.value = ''
    suggestions.value = []
    selectedItem.value = null
  }

  return {
    query,
    suggestions,
    loading,
    selectedItem,
    displayValue,
    search,
    reset
  }
}

