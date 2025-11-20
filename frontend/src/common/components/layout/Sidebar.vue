<template>
  <aside
    :class="sidebarClasses"
    class="sidebar fixed left-0 top-0 z-9999 flex h-screen w-[290px] flex-col overflow-y-hidden border-r border-gray-200 bg-white px-5 dark:!border-gray-800 dark:!bg-gray-900 lg:static lg:translate-x-0"
  >
    <!-- SIDEBAR HEADER -->
    <div
      class="flex items-center justify-between gap-2 pt-8 sidebar-header pb-7"
    >
      <router-link to="/dashboard" class="flex items-center">
        <span class="logo text-xl font-bold text-gray-900 dark:text-white">
          Тренажер зрения
        </span>
      </router-link>
    </div>
    <!-- SIDEBAR HEADER -->

    <div class="flex flex-col overflow-y-auto duration-300 ease-linear no-scrollbar">
      <!-- Sidebar Menu -->
      <nav>
        <!-- Menu Groups -->
        <template v-for="group in navigationStore.menuByGroups" :key="group.title">
          <div class="mb-6">
            <h3 class="mb-4 text-xs uppercase leading-[20px] text-gray-400 dark:text-gray-500">
              <span class="menu-group-title">
                {{ group.title }}
              </span>
            </h3>

            <ul class="flex flex-col gap-4">
              <!-- Menu Items -->
              <li v-for="item in group.items" :key="item.id">
                <a
                  v-if="!item.children"
                  :href="item.route"
                  @click.prevent="handleMenuClick(item)"
                  class="menu-item group"
                  :class="isMenuActive(item) ? 'menu-item-active' : 'menu-item-inactive'"
                >
                  <i
                    :class="[
                      'pi',
                      navigationStore.getMenuIcon(item.id),
                      'text-current',
                      isMenuActive(item) ? 'menu-item-icon-active' : 'menu-item-icon-inactive'
                    ]"
                  ></i>

                  <span class="menu-item-text">
                    {{ item.title }}
                  </span>
                </a>

                <!-- Menu Item with Children -->
                <template v-else>
                  <a
                    href="#"
                    @click.prevent="toggleSubmenu(item.id)"
                    class="menu-item group"
                    :class="isMenuActive(item) ? 'menu-item-active' : 'menu-item-inactive'"
                  >
                    <i
                      :class="[
                        'pi',
                        navigationStore.getMenuIcon(item.id),
                        'text-current',
                        isMenuActive(item) ? 'menu-item-icon-active' : 'menu-item-icon-inactive'
                      ]"
                    ></i>

                    <span class="menu-item-text">
                      {{ item.title }}
                    </span>

                    <i
                      :class="[
                        'pi pi-chevron-down menu-item-arrow text-current',
                        selectedSubmenu === item.id ? 'menu-item-arrow-active' : 'menu-item-arrow-inactive'
                      ]"
                    ></i>
                  </a>

                  <!-- Dropdown Menu -->
                  <div
                    class="overflow-hidden transform translate"
                    :class="selectedSubmenu === item.id ? 'block' : 'hidden'"
                  >
                    <ul
                      class="flex flex-col gap-1 mt-2 menu-dropdown pl-9"
                    >
                      <li v-for="child in item.children" :key="child.id">
                        <a
                          :href="child.route"
                          @click.prevent="handleSubmenuClick(child)"
                          class="menu-dropdown-item group"
                          :class="isSubmenuActive(child) ? 'menu-dropdown-item-active' : 'menu-dropdown-item-inactive'"
                        >
                          {{ child.title }}
                        </a>
                      </li>
                    </ul>
                  </div>
                </template>
              </li>
            </ul>
          </div>
        </template>
      </nav>
      <!-- Sidebar Menu -->
    </div>
  </aside>
</template>

<script>
import { useNavigationStore } from '@/common/stores/navigation.js'
import { useThemeStore } from '@/common/stores/theme.js'
import { useSidebar } from '@/common/composables/useSidebar.js'

export default {
  name: 'Sidebar',
  setup() {
    const navigationStore = useNavigationStore()
    const themeStore = useThemeStore()
    const { sidebarClasses, isCollapsed } = useSidebar()

    return {
      navigationStore,
      themeStore,
      sidebarClasses,
      isCollapsed
    }
  },
  data() {
    return {
      selectedSubmenu: null
    }
  },
  computed: {
    currentRoute() {
      return this.$route.path
    }
  },
  watch: {
    currentRoute: {
      handler(newRoute) {
        this.navigationStore.updateMenuFromRoute(newRoute)
        this.updateSelectedSubmenu(newRoute)
      },
      immediate: true
    }
  },
  methods: {
    handleMenuClick(item) {
      this.navigationStore.setSelectedMenu(item.title)
      this.$router.push(item.route)
    },

    handleSubmenuClick(item) {
      this.$router.push(item.route)
    },

    toggleSubmenu(menuId) {
      if (this.selectedSubmenu === menuId) {
        this.selectedSubmenu = null
      } else {
        this.selectedSubmenu = menuId
      }
    },

    updateSelectedSubmenu(route) {
      // Находим родительское меню для текущего маршрута
      const parentMenu = this.navigationStore.menuItems.find(item => 
        item.children && item.children.some(child => child.route === route)
      )
      
      if (parentMenu) {
        this.selectedSubmenu = parentMenu.id
      }
    },

    isMenuActive(item) {
      return this.navigationStore.isMenuActive(item.id, this.currentRoute)
    },

    isSubmenuActive(item) {
      return this.navigationStore.isSubmenuActive(item.route, this.currentRoute)
    }
  }
}
</script>

<style scoped>
/* Дополнительные стили при необходимости */
</style>