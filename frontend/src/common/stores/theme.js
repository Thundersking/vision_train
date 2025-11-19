import { defineStore } from 'pinia'
import { ref, computed, watch } from 'vue'

export const useThemeStore = defineStore('theme', () => {
  const darkMode = ref(false)
  const stickyMenu = ref(false)
  const sidebarToggle = ref(false)
  const scrollTop = ref(false)

  // Инициализация темы
  const initializeTheme = () => {
    const savedTheme = localStorage.getItem('darkMode')
    if (savedTheme) {
      darkMode.value = JSON.parse(savedTheme)
    } else {
      darkMode.value = window.matchMedia('(prefers-color-scheme: dark)').matches
    }
    applyTheme()
  }

  // Применение темы
  const applyTheme = () => {
    const htmlElement = document.documentElement
    const bodyElement = document.body

    if (darkMode.value) {
      htmlElement.classList.add('dark')
      bodyElement.classList.add('dark')
    } else {
      htmlElement.classList.remove('dark')
      bodyElement.classList.remove('dark')
    }
  }

  // Переключение темы
  const toggleDarkMode = () => {
    darkMode.value = !darkMode.value
  }

  // Управление sidebar
  const toggleSidebar = () => {
    sidebarToggle.value = !sidebarToggle.value
  }

  const closeSidebar = () => {
    sidebarToggle.value = false
  }

  const openSidebar = () => {
    sidebarToggle.value = true
  }

  // Управление sticky menu
  const setStickyMenu = (value) => {
    stickyMenu.value = value
  }

  // Управление scroll
  const setScrollTop = (value) => {
    scrollTop.value = value
  }

  // Вычисляемые свойства
  const themeClasses = computed(() => ({
    dark: darkMode.value,
    'sticky-menu': stickyMenu.value,
    'sidebar-toggle': sidebarToggle.value,
    'scroll-top': scrollTop.value
  }))

  const bodyClasses = computed(() => ({
    'dark bg-gray-900': darkMode.value,
    'bg-gray-50': !darkMode.value
  }))

  // Watchers
  watch(darkMode, (newValue) => {
    localStorage.setItem('darkMode', JSON.stringify(newValue))
    applyTheme()
  })

  // Обработка скролла
  const handleScroll = () => {
    const scrollPosition = window.scrollY
    setScrollTop(scrollPosition > 0)
    setStickyMenu(scrollPosition > 100)
  }

  // Инициализация обработчиков
  const initializeHandlers = () => {
    // Обработчик скролла
    window.addEventListener('scroll', handleScroll)

    // Обработчик изменения размера экрана
    const handleResize = () => {
      if (window.innerWidth >= 1024) {
        // На больших экранах sidebar управляется по-другому
      } else {
        // На мобильных закрываем sidebar
        if (sidebarToggle.value) {
          closeSidebar()
        }
      }
    }

    window.addEventListener('resize', handleResize)

    // Cleanup функция
    return () => {
      window.removeEventListener('scroll', handleScroll)
      window.removeEventListener('resize', handleResize)
    }
  }

  // Настройки для Alpine.js совместимости (если нужно)
  const getAlpineData = () => ({
    darkMode: darkMode.value,
    sidebarToggle: sidebarToggle.value,
    stickyMenu: stickyMenu.value,
    scrollTop: scrollTop.value,
    loaded: true
  })

  return {
    // State
    darkMode,
    stickyMenu,
    sidebarToggle,
    scrollTop,

    // Computed
    themeClasses,
    bodyClasses,

    // Actions
    initializeTheme,
    toggleDarkMode,
    toggleSidebar,
    closeSidebar,
    openSidebar,
    setStickyMenu,
    setScrollTop,
    initializeHandlers,
    getAlpineData
  }
})