import { ref, computed } from 'vue'

const sidebarToggle = ref(false)
const stickyMenu = ref(false)
const scrollTop = ref(false)

export function useSidebar() {
  const toggleSidebar = () => {
    sidebarToggle.value = !sidebarToggle.value
  }

  const closeSidebar = () => {
    sidebarToggle.value = false
  }

  const openSidebar = () => {
    sidebarToggle.value = true
  }

  const setStickyMenu = (value) => {
    stickyMenu.value = value
  }

  const setScrollTop = (value) => {
    scrollTop.value = value
  }

  // Проверяем, свернут ли sidebar на больших экранах
  const isCollapsed = computed(() => sidebarToggle.value)

  // CSS классы для sidebar
  const sidebarClasses = computed(() => ({
    'translate-x-0': sidebarToggle.value,
    '-translate-x-full': !sidebarToggle.value,
    'lg:w-[90px]': sidebarToggle.value, // свернутое состояние на больших экранах
    'lg:translate-x-0': true, // всегда видим на больших экранах
  }))

  // Обработка скролла для sticky header
  const handleScroll = () => {
    const scrollPosition = window.scrollY
    setScrollTop(scrollPosition > 0)
    setStickyMenu(scrollPosition > 100)
  }

  // Инициализация обработчиков
  const initializeSidebar = () => {
    // Добавляем обработчик скролла
    window.addEventListener('scroll', handleScroll)
    
    // Закрываем sidebar при клике вне его на мобильных
    const handleClickOutside = (event) => {
      if (window.innerWidth < 1024 && sidebarToggle.value) {
        const sidebar = document.querySelector('.sidebar')
        if (sidebar && !sidebar.contains(event.target)) {
          closeSidebar()
        }
      }
    }
    
    document.addEventListener('click', handleClickOutside)
    
    // Обработка изменения размера окна
    const handleResize = () => {
      if (window.innerWidth >= 1024) {
        // На больших экранах sidebar должен быть всегда видимым
        // но может быть свернутым
      } else {
        // На мобильных устройствах закрываем sidebar
        closeSidebar()
      }
    }
    
    window.addEventListener('resize', handleResize)
    
    // Cleanup функция
    return () => {
      window.removeEventListener('scroll', handleScroll)
      document.removeEventListener('click', handleClickOutside)
      window.removeEventListener('resize', handleResize)
    }
  }

  return {
    sidebarToggle,
    stickyMenu,
    scrollTop,
    isCollapsed,
    sidebarClasses,
    toggleSidebar,
    closeSidebar,
    openSidebar,
    setStickyMenu,
    setScrollTop,
    initializeSidebar
  }
}