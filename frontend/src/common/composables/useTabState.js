import { computed, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'

/**
 * Синхронизирует активную вкладку с query-параметром (?tab=)
 * Использование:
 * ```js
 * const activeTab = ref('overview')
 * useTabState(activeTab) // создаст/прочитает ?tab=overview
 * ```
 * @param {import('vue').Ref<string|null>} activeTab - реактивный ref текущего таба
 * @param {string} [paramName='tab'] - имя query-параметра
 */
export function useTabState(activeTab, paramName = 'tab') {
  const route = useRoute()
  const router = useRouter()

  const queryTab = computed(() => route.query[paramName] ?? null)

  // Поддержка открытия страницы сразу с нужным табом (?tab=examinations)
  if (queryTab.value && queryTab.value !== activeTab.value) {
    activeTab.value = queryTab.value
  }

  // Подписываемся на изменение query (например, пользователь вернулся по истории)
  watch(queryTab, (value) => {
    if (value !== activeTab.value) {
      activeTab.value = value
    }
  })

  // Обновляем query при переключении таба
  watch(activeTab, (value) => {
    router.replace({
      query: {
        ...route.query,
        [paramName]: value || undefined,
      }
    })
  })
}
