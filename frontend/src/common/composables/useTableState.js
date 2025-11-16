import { ref, computed, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useApi } from './useApi.js'

/**
 * @typedef {Object} TableStateOptions
 * @property {string} apiEndpoint - URL API эндпоинта (e.g., '/users')
 * @property {Object} [defaultFilters] - Фильтры по умолчанию
 * @property {number} [defaultPerPage] - Строк на странице по умолчанию
 */

export function useTableState(options) {
    const {
        apiEndpoint,
        defaultFilters = {},
        defaultPerPage = 15
    } = options

    const route = useRoute()
    const router = useRouter()
    const { request } = useApi()

    const data = ref([])
    const loading = ref(false)
    const error = ref(null)
    const paginationMeta = ref(null)

    // Читаем из URL
    const page = computed(() => parseInt(route.query.page) || 1)
    const perPage = computed(() => parseInt(route.query.per_page) || defaultPerPage)
    const sortBy = computed(() => route.query.sort_by || 'id')
    const sortOrder = computed(() => route.query.sort_order || 'asc')

    // Собираем все фильтры из URL
    const filters = computed(() => {
        const urlFilters = {}
        Object.keys(route.query).forEach(key => {
            if (!['page', 'per_page', 'sort_by', 'sort_order'].includes(key)) {
                urlFilters[key] = route.query[key]
            }
        })
        return urlFilters
    })

    // Загружаем данные
    const fetchData = async () => {
        try {
            loading.value = true
            error.value = null

            const params = {
                page: page.value,
                per_page: perPage.value,
                sort_by: sortBy.value,
                sort_order: sortOrder.value,
                ...filters.value
            }

            const response = await request('get', apiEndpoint, { params })

            // Laravel Paginator response
            data.value = response.data
            paginationMeta.value = response.meta
        } catch (err) {
            error.value = err.message
            console.error('Table fetch error:', err)
        } finally {
            loading.value = false
        }
    }

    // Обновляем URL и загружаем данные
    const updateFilters = (newFilters) => {
        router.push({
            query: {
                ...route.query,
                ...newFilters,
                page: 1  // сбрасываем на первую страницу при фильтрации
            }
        })
    }

    const updatePagination = (newPage, newPerPage) => {
        router.push({
            query: {
                ...route.query,
                page: newPage,
                per_page: newPerPage
            }
        })
    }

    const updateSort = (field, order) => {
        router.push({
            query: {
                ...route.query,
                sort_by: field,
                sort_order: order,
                page: 1
            }
        })
    }

    // Следим за изменением URL
    watch(
        () => route.query,
        () => fetchData(),
        { deep: true }
    )

    // Загружаем при первом входе
    fetchData()

    return {
        data,
        loading,
        error,
        paginationMeta,
        page,
        perPage,
        sortBy,
        sortOrder,
        filters,
        updateFilters,
        updatePagination,
        updateSort,
        fetchData
    }
}
