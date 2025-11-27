import { defineStore } from 'pinia'
import { ref, computed } from 'vue'

export const useNavigationStore = defineStore('navigation', () => {
  const selectedMenu = ref('Dashboard')

  // Структура меню приложения
  const menuItems = ref([
    {
      id: 'dashboard',
      title: 'Dashboard',
      icon: 'dashboard',
      route: '/dashboard',
      children: [
        {
          id: 'analytics',
          title: 'Аналитика',
          route: '/dashboard/analytics'
        },
        {
          id: 'overview',
          title: 'Обзор',
          route: '/dashboard/overview'
        }
      ]
    },
    {
      id: 'patients',
      title: 'Пациенты',
      icon: 'patients',
      route: '/patients'
    },
    {
      id: 'users',
      title: 'Пользователи',
      icon: 'users',
      route: '/users'
    },
    {
      id: 'departments',
      title: 'Офисы',
      icon: 'departments',
      route: '/departments'
    },
    {
      id: 'organization',
      title: 'Моя организация',
      icon: 'organization',
      route: '/organization'
    },
    {
      id: 'exercise-types',
      title: 'Типы упражнений',
      icon: 'exercise-types',
      route: '/exercise-types'
    },
    {
      id: 'exercise-templates',
      title: 'Шаблоны упражнений',
      icon: 'exercise-templates',
      route: '/exercise-templates'
    },
    {
      id: 'reports',
      title: 'Отчеты',
      icon: 'reports',
      route: '/reports',
      children: [
        {
          id: 'medical-reports',
          title: 'Медицинские отчеты',
          route: '/reports/medical'
        },
        {
          id: 'statistical-reports',
          title: 'Статистические отчеты',
          route: '/reports/statistics'
        }
      ]
    }
  ])

  // Группы меню
  const menuGroups = ref([
    {
      title: 'ОСНОВНОЕ',
      items: ['dashboard', 'patients']
    },
    {
      title: 'УПРАВЛЕНИЕ',
      items: ['users', 'departments', 'organization']
    },
    {
      title: 'КАТАЛОГИ',
      items: ['exercise-types', 'exercise-templates', 'reports']
    }
  ])

  // Вычисляемые свойства
  const menuByGroups = computed(() => {
    return menuGroups.value.map(group => ({
      ...group,
      items: group.items.map(itemId => 
        menuItems.value.find(item => item.id === itemId)
      ).filter(Boolean)
    }))
  })

  const flatMenuItems = computed(() => {
    const flat = []
    menuItems.value.forEach(item => {
      flat.push(item)
      if (item.children) {
        flat.push(...item.children)
      }
    })
    return flat
  })

  // Методы
  const setSelectedMenu = (menuId) => {
    selectedMenu.value = menuId
  }

  const getMenuByRoute = (route) => {
    return flatMenuItems.value.find(item => item.route === route)
  }

  const isMenuActive = (menuId, currentRoute) => {
    const menuItem = menuItems.value.find(item => item.id === menuId)
    if (!menuItem) return false

    // Проверяем основной маршрут
    if (currentRoute === menuItem.route) return true

    // Проверяем дочерние маршруты
    if (menuItem.children) {
      return menuItem.children.some(child => currentRoute === child.route)
    }

    return false
  }

  const isSubmenuActive = (submenuRoute, currentRoute) => {
    return currentRoute === submenuRoute
  }

  const getMenuIcon = (menuId) => {
    const iconMap = {
      dashboard: 'pi-home',
      patients: 'pi-users',
      users: 'pi-user',
      departments: 'pi-building',
      organization: 'pi-briefcase',
      'exercise-types': 'pi-list',
      'exercise-templates': 'pi-clone',
      reports: 'pi-file-text'
    }
    return iconMap[menuId] || 'pi-circle'
  }

  const updateMenuFromRoute = (route) => {
    const menuItem = getMenuByRoute(route)
    if (menuItem) {
      // Если это дочерний элемент, выбираем родительское меню
      const parentMenu = menuItems.value.find(item => 
        item.children && item.children.some(child => child.route === route)
      )
      if (parentMenu) {
        setSelectedMenu(parentMenu.title)
      } else {
        setSelectedMenu(menuItem.title)
      }
    }
  }

  return {
    selectedMenu,
    menuItems,
    menuGroups,
    menuByGroups,
    flatMenuItems,
    setSelectedMenu,
    getMenuByRoute,
    isMenuActive,
    isSubmenuActive,
    getMenuIcon,
    updateMenuFromRoute
  }
})
