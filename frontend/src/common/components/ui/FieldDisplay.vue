<script setup>
defineProps({
  label: {
    type: String,
    required: true
  },
  value: {
    type: [String, Number, Boolean, Date],
    default: null
  },
  type: {
    type: String,
    default: 'text',
    validator: (value) => ['text', 'date', 'tag'].includes(value)
  },
  tagSeverity: {
    type: String,
    default: 'info'
  }
})
</script>

<template>
  <div>
    <label class="block text-sm font-semibold text-gray-700 mb-1">{{ label }}</label>
    
    <p v-if="type === 'text'" class="text-gray-900">
      {{ value || '-' }}
    </p>
    
    <p v-else-if="type === 'date'" class="text-gray-600">
      {{ value ? new Date(value).toLocaleString('ru-RU') : '-' }}
    </p>
    
    <Tag v-else-if="type === 'tag'" 
         :value="value" 
         :severity="tagSeverity" />
  </div>
</template>