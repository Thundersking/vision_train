<template>
  <div class="flex justify-between items-center mb-4">
    <div class="flex items-center gap-1">
      <div 
        v-if="showBackButton"
        class="cursor-pointer p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors"
        @click="handleBack"
      >
        <i class="pi pi-chevron-left text-gray-600 dark:text-gray-400"></i>
      </div>
      <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
          {{ title }}
        </h1>
        <p v-if="description" class="text-gray-600 dark:text-gray-400">
          {{ description }}
        </p>
      </div>
    </div>
    <div v-if="$slots.actions" class="flex items-center gap-3">
      <slot name="actions" />
    </div>
  </div>
</template>

<script>
export default {
  name: 'TitleBlock',
  props: {
    title: {
      type: String,
      required: true
    },
    description: {
      type: String,
      default: null
    },
    backTo: {
      type: [String, Object, Boolean],
      default: null
    }
  },
  computed: {
    showBackButton() {
      return this.backTo !== null && this.backTo !== false
    }
  },
  methods: {
    handleBack() {
      if (!this.showBackButton) {
        return
      }

      const isHistoryBack = this.backTo === true || this.backTo === 'history'

      if (isHistoryBack) {
        this.$router.back()
        return
      }

      this.$router.push(this.backTo)
    }
  }
}
</script>
