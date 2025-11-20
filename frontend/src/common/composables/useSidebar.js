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

  // Проверяем, свернут ли sidebar (только на mobile/tablet)
  const isCollapsed = computed(() => false) // на desktop всегда полный

  // CSS классы для sidebar
  const sidebarClasses = computed(() => ({
    // На mobile/tablet: показываем/скрываем сайдбар
    'translate-x-0': sidebarToggle.value,
    '-translate-x-full': !sidebarToggle.value,
    // На desktop: всегда полная ширина и видимый
    'lg:translate-x-0': true, // всегда видим на больших экранах
    'lg:w-[290px]': true, // всегда полная ширина на desktop
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
        // На desktop sidebar всегда видим и не управляется toggle
        closeSidebar() // сбрасываем состояние для mobile логики
      } else {
        // На мобильных/планшетных устройствах закрываем sidebar
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