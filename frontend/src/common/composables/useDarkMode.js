import { ref, watch } from 'vue'

const isDark = ref(false)

export function useDarkMode() {
  const initializeTheme = () => {
    // Проверяем сохраненную тему
    const savedTheme = localStorage.getItem('darkMode')
    if (savedTheme) {
      isDark.value = JSON.parse(savedTheme)
    } else {
      // Используем системную тему по умолчанию
      isDark.value = window.matchMedia('(prefers-color-scheme: dark)').matches
    }
    
    // Применяем тему
    applyTheme()
  }

  const applyTheme = () => {
    if (isDark.value) {
      document.documentElement.classList.add('dark')
      document.body.classList.add('dark')
    } else {
      document.documentElement.classList.remove('dark')
      document.body.classList.remove('dark')
    }
  }

  const toggleDarkMode = () => {
    isDark.value = !isDark.value
  }

  // Слушаем изменения и сохраняем в localStorage
  watch(isDark, (newValue) => {
    localStorage.setItem('darkMode', JSON.stringify(newValue))
    applyTheme()
  })

  // Инициализируем при первом использовании
  if (!document.documentElement.dataset.themeInitialized) {
    initializeTheme()
    document.documentElement.dataset.themeInitialized = 'true'
  }

  return {
    isDark,
    toggleDarkMode,
    initializeTheme
  }
}