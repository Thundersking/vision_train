<template>
  <header
    class="sticky top-0 z-50 flex w-full border-gray-200 bg-white lg:border-b dark:border-gray-800 dark:bg-gray-900"
  >
    <div class="flex grow flex-col items-center justify-between lg:flex-row lg:px-6">
      <div
        class="flex w-full items-center justify-between gap-2 border-b border-gray-200 px-3 py-3 sm:gap-4 lg:justify-normal lg:border-b-0 lg:px-0 lg:py-4 dark:border-gray-800"
      >
        <!-- Hamburger Toggle BTN -->
        <button
          :class="themeStore.sidebarToggle ? 'bg-gray-100 dark:bg-gray-800' : ''"
          class="z-50 flex h-10 w-10 items-center justify-center rounded-lg border-gray-200 text-gray-500 lg:hidden dark:border-gray-800 dark:text-gray-400"
          @click="themeStore.toggleSidebar"
        >
          <!-- Единая иконка для всех экранов -->
          <i
            v-if="!themeStore.sidebarToggle"
            class="pi pi-bars text-current"
          ></i>
          <i
            v-else
            class="pi pi-times text-current"
          ></i>
        </button>
        <!-- Hamburger Toggle BTN -->

        <router-link to="/dashboard" class="lg:hidden">
          Тренажер зрения
        </router-link>

        <!-- Application nav menu button -->
        <button
          class="z-50 flex h-10 w-10 items-center justify-center rounded-lg text-gray-700 hover:bg-gray-100 lg:hidden dark:text-gray-400 dark:hover:bg-gray-800"
          :class="menuToggle ? 'bg-gray-100 dark:bg-gray-800' : ''"
          @click="toggleMenu"
        >
          <i class="pi pi-ellipsis-h text-current"></i>
        </button>
        <!-- Application nav menu button -->

        <div class="hidden lg:block">
          <form @submit.prevent="handleSearch">
            <div class="relative">
              <span class="absolute top-1/2 left-4 -translate-y-1/2">
                <i class="pi pi-search text-gray-500 dark:text-gray-400"></i>
              </span>
              <input
                v-model="searchQuery"
                type="text"
                placeholder="Поиск или введите команду..."
                class="dark:bg-dark-900 shadow-sm focus:border-sky-300 focus:ring-sky-500/10 dark:focus:border-sky-800 h-11 w-full rounded-lg border border-gray-200 bg-transparent py-2.5 pr-14 pl-12 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden xl:w-[430px] dark:border-gray-800 dark:bg-gray-900 dark:bg-white/[0.03] dark:text-white/90 dark:placeholder:text-white/30"
              />

              <button
                type="submit"
                class="absolute top-1/2 right-2.5 inline-flex -translate-y-1/2 items-center gap-0.5 rounded-lg border border-gray-200 bg-gray-50 px-[7px] py-[4.5px] text-xs -tracking-[0.2px] text-gray-500 dark:border-gray-800 dark:bg-white/[0.03] dark:text-gray-400"
              >
                <span>⌘</span>
                <span>K</span>
              </button>
            </div>
          </form>
        </div>
      </div>

      <div
        :class="menuToggle ? 'flex' : 'hidden'"
        class="shadow-md w-full items-center justify-between gap-4 px-5 py-4 lg:flex lg:justify-end lg:px-0 lg:shadow-none"
      >
        <div class="2xsm:gap-3 flex items-center gap-2">
          <!-- Dark Mode Toggler -->
          <button
            class="hover:text-dark-900 relative flex h-11 w-11 items-center justify-center rounded-full border border-gray-200 bg-white text-gray-500 transition-colors hover:bg-gray-100 hover:text-gray-700 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-white"
            @click="themeStore.toggleDarkMode"
          >
            <i v-if="themeStore.darkMode" class="pi pi-sun text-current"></i>
            <i v-else class="pi pi-moon text-current"></i>
          </button>
          <!-- Dark Mode Toggler -->

          <!-- Notification Menu Area -->
          <div class="relative">
            <button
              class="notification-button hover:text-dark-900 relative flex h-11 w-11 items-center justify-center rounded-full border border-gray-200 bg-white text-gray-500 transition-colors hover:bg-gray-100 hover:text-gray-700 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-white"
              @click="toggleNotifications"
            >
              <span
                v-if="hasNotifications"
                class="absolute top-0.5 right-0 z-1 h-2 w-2 rounded-full bg-orange-400"
              >
                <span class="absolute -z-1 inline-flex h-full w-full animate-ping rounded-full bg-orange-400 opacity-75"></span>
              </span>
              <i class="pi pi-bell text-current"></i>
            </button>

            <!-- Notifications Dropdown -->
            <div
              v-if="notificationsOpen"
              class="shadow-lg dark:bg-gray-dark absolute -right-[240px] mt-[17px] flex h-[480px] w-[350px] flex-col rounded-2xl border border-gray-200 bg-white p-3 sm:w-[361px] lg:right-0 dark:border-gray-800"
            >
              <div class="mb-3 flex items-center justify-between border-b border-gray-100 pb-3 dark:border-gray-800">
                <h5 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                  Уведомления
                </h5>
                <button @click="closeNotifications" class="text-gray-500 dark:text-gray-400">
                  <i class="pi pi-times text-current"></i>
                </button>
              </div>
              
              <div class="custom-scrollbar flex h-auto flex-col overflow-y-auto">
                <div v-if="notifications.length === 0" class="text-center text-gray-500 py-8">
                  Нет новых уведомлений
                </div>
                <div v-else>
                  <!-- Notification items would go here -->
                  <div class="text-center text-gray-500 py-8">
                    {{ notifications.length }} уведомлений
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- Notification Menu Area -->
        </div>

        <!-- User Area -->
        <div class="relative">
          <button
            @click="toggleUserMenu"
            class="user-button flex items-center text-gray-700 dark:text-gray-400"
          >
            <span class="mr-3 h-11 w-11 overflow-hidden rounded-full">
              <img src="/src/assets/images/user/default-avatar.jpg" alt="User" />
            </span>

            <span class="text-sm mr-1 block font-medium">{{ currentUser.name || 'Пользователь' }}</span>

            <i
              :class="['pi pi-chevron-down text-gray-500 dark:text-gray-400', userMenuOpen && 'rotate-180']"
            ></i>
          </button>

          <!-- User Dropdown -->
          <div
            v-if="userMenuOpen"
            class="shadow-lg dark:bg-gray-dark absolute right-0 mt-[17px] flex w-[260px] flex-col rounded-2xl border border-gray-200 bg-white p-3 dark:border-gray-800"
          >
            <div>
              <span class="text-sm block font-medium text-gray-700 dark:text-gray-400">
                {{ currentUser.name || 'Пользователь' }}
              </span>
              <span class="text-xs mt-0.5 block text-gray-500 dark:text-gray-400">
                {{ currentUser.email || 'email@example.com' }}
              </span>
            </div>

            <ul class="flex flex-col gap-1 border-b border-gray-200 pt-4 pb-3 dark:border-gray-800">
              <li>
                <router-link
                  to="/profile"
                  class="group text-sm flex items-center gap-3 rounded-lg px-3 py-2 font-medium text-gray-700 hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-gray-300"
                >
                  <i class="pi pi-user text-gray-500 group-hover:text-gray-700 dark:text-gray-400 dark:group-hover:text-gray-300"></i>
                  Профиль
                </router-link>
              </li>
              <li>
                <router-link
                  to="/settings"
                  class="group text-sm flex items-center gap-3 rounded-lg px-3 py-2 font-medium text-gray-700 hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-gray-300"
                >
                  <i class="pi pi-cog text-gray-500 group-hover:text-gray-700 dark:text-gray-400 dark:group-hover:text-gray-300"></i>
                  Настройки
                </router-link>
              </li>
            </ul>

            <button
              @click="logout"
              class="group text-sm mt-3 flex items-center gap-3 rounded-lg px-3 py-2 font-medium text-gray-700 hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-gray-300"
            >
              <i class="pi pi-sign-out text-gray-500 group-hover:text-gray-700 dark:group-hover:text-gray-300"></i>
              Выйти
            </button>
          </div>
        </div>
        <!-- User Area -->
      </div>
    </div>
  </header>
</template>

<script>
import { useThemeStore } from '@/common/stores/theme.js'

export default {
  name: 'Header',
  setup() {
    const themeStore = useThemeStore()
    return { themeStore }
  },
  data() {
    return {
      menuToggle: false,
      searchQuery: '',
      notificationsOpen: false,
      userMenuOpen: false,
      notifications: [],
      currentUser: {
        name: 'Администратор',
        email: 'admin@visiontrain.com'
      }
    }
  },
  computed: {
    hasNotifications() {
      return this.notifications.length > 0
    }
  },
  methods: {
    toggleMenu() {
      this.menuToggle = !this.menuToggle
    },

    toggleNotifications() {
      this.notificationsOpen = !this.notificationsOpen
      this.userMenuOpen = false
    },

    closeNotifications() {
      this.notificationsOpen = false
    },

    toggleUserMenu() {
      this.userMenuOpen = !this.userMenuOpen
      this.notificationsOpen = false
    },

    handleSearch() {
      if (this.searchQuery.trim()) {
        console.log('Searching for:', this.searchQuery)
        // Implement search functionality
      }
    },

    logout() {
      // Implement logout functionality
      console.log('Logging out...')
      this.$router.push('/login')
    },

    // Close dropdowns when clicking outside
    handleClickOutside(event) {
      const target = event.target
      const notificationButton = target.closest('.notification-button')
      const userButton = target.closest('.user-button')

      if (!notificationButton && this.notificationsOpen) {
        this.notificationsOpen = false
      }

      if (!userButton && this.userMenuOpen) {
        this.userMenuOpen = false
      }
    }
  },

  mounted() {
    document.addEventListener('click', this.handleClickOutside)
  },

  beforeUnmount() {
    document.removeEventListener('click', this.handleClickOutside)
  }
}
</script>

<style scoped>
/* Additional styles if needed */
</style>