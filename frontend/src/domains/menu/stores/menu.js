import { defineStore } from 'pinia'

export const useMenuStore = defineStore('menu', {
  state: () => ({
    items: [],
    expandedKeys: {},
    isDarkMode: false,
    sidebarOpen: false
  }),

  getters: {
    getMenuItems: (state) => state.items,
    isExpanded: (state) => (key) => state.expandedKeys[key] || false,
    isDark: (state) => state.isDarkMode
  },

  actions: {
    setMenuItems(items) {
      this.items = items
      localStorage.setItem('menu_items', JSON.stringify(items))
    },

    toggleMenuItem(key) {
      this.expandedKeys[key] = !this.expandedKeys[key]
      localStorage.setItem('expanded_menu_keys', JSON.stringify(this.expandedKeys))
    },

    clearMenu() {
      this.items = []
      this.expandedKeys = {}
      localStorage.removeItem('menu_items')
      localStorage.removeItem('expanded_menu_keys')
    },

    setTheme(isDark) {
      this.isDarkMode = isDark
      localStorage.setItem('theme', isDark ? 'dark' : 'light')
      
      if (isDark) {
        document.documentElement.classList.add('dark')
      } else {
        document.documentElement.classList.remove('dark')
      }
    },

    toggleSidebar() {
      this.sidebarOpen = !this.sidebarOpen
    },

    closeSidebar() {
      this.sidebarOpen = false
    },

    loadFromStorage() {
      // Загружаем меню
      const menuItems = localStorage.getItem('menu_items')
      if (menuItems) {
        this.items = JSON.parse(menuItems)
      } else {
        // Тестовые данные если нет в localStorage
        this.setTestData()
      }

      // Загружаем состояние раскрытых пунктов
      const expandedKeys = localStorage.getItem('expanded_menu_keys')
      if (expandedKeys) {
        this.expandedKeys = JSON.parse(expandedKeys)
      }

      // Загружаем тему
      const theme = localStorage.getItem('theme')
      if (theme) {
        this.setTheme(theme === 'dark')
      }
      
      // Устанавливаем тестовые данные пользователя
      this.setTestUserData()
    },

    setTestData() {
      const testMenuItems = [
        {
          key: "dashboard",
          label: "Главная",
          icon: "pi pi-home",
          path: "/dashboard"
        },
        {
          key: "patients",
          label: "Пациенты",
          icon: "pi pi-users",
          path: "/patients",
          children: [
            {
              key: "patients-list",
              label: "Список",
              icon: "pi pi-list",
              path: "/patients/list"
            },
            {
              key: "patients-archive",
              label: "Архив",
              icon: "pi pi-inbox",
              path: "/patients/archive"
            }
          ]
        },
        {
          key: "exercise-types",
          label: "Типы упражнений",
          icon: "pi pi-list",
          path: "/exercise-types"
        },
        {
          key: "exercise-templates",
          label: "Шаблоны упражнений",
          icon: "pi pi-clone",
          path: "/exercise-templates"
        },
        {
          key: "reports",
          label: "Отчеты",
          icon: "pi pi-chart-bar",
          path: "/reports"
        }
      ]
      
      this.setMenuItems(testMenuItems)
    },

    setTestUserData() {
      const userData = {
        id: 1,
        name: "Михаил Смирнов",
        email: "smirnov@vision-clinic.ru",
        initials: "МС"
      }
      
      if (!localStorage.getItem('user_data')) {
        localStorage.setItem('user_data', JSON.stringify(userData))
      }
    }
  }
})
