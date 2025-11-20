<template>
  <div class="flex justify-between items-center">
    <div>
      <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
        {{ title }}
      </h1>
      <p v-if="description" class="text-gray-600 dark:text-gray-400">
        {{ description }}
      </p>
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
    }
  }
}
</script>