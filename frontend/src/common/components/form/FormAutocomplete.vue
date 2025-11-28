<template>
  <div>
    <label v-if="label" :for="computedId" class="block text-sm font-medium mb-1 text-surface-900 dark:text-surface-0">
      {{ label }}
      <span v-if="required" class="text-red-500 ml-1">*</span>
    </label>

    <AutoComplete
      :id="computedId"
      v-model="selectedItem"
      :suggestions="suggestions"
      @complete="handleSearch"
      @item-select="handleSelect"
      @clear="handleClear"
      :optionLabel="optionLabel"
      :placeholder="placeholder"
      :disabled="disabled"
      :loading="loading"
      :forceSelection="forceSelection"
      :minLength="minLength"
      class="w-full"
      :inputClass="{'p-invalid': hasError}"
      fluid
    />

    <small class="text-red-500" v-if="hasError">
      {{ errorMessage }}
    </small>
  </div>
</template>

<script setup>
import { computed, watch, onMounted } from 'vue'
import { useFormFieldErrors } from '@/common/composables/useFormFieldErrors.js'
import { useAutocomplete } from '@/common/composables/useAutocomplete.js'

const props = defineProps({
  id: {
    type: String,
    default: null
  },
  modelValue: {
    type: Object,
    required: true
  },
  name: {
    type: String,
    required: true
  },
  label: {
    type: String,
    default: ''
  },
  required: {
    type: Boolean,
    default: false
  },
  placeholder: {
    type: String,
    default: 'Начните вводить...'
  },
  disabled: {
    type: Boolean,
    default: false
  },
  validation: {
    type: Object,
    default: () => null
  },
  // Store instance (объект с методом search)
  store: {
    type: Object,
    required: true
  },
  // Настройки автокомплита
  optionLabel: {
    type: String,
    default: 'name'
  },
  optionValue: {
    type: String,
    default: 'id'
  },
  debounceDelay: {
    type: Number,
    default: 300
  },
  minLength: {
    type: Number,
    default: 2
  },
  forceSelection: {
    type: Boolean,
    default: false
  },
  // Начальное значение (объект)
  initialValue: {
    type: Object,
    default: null
  }
})

const computedId = computed(() => props.id || `form-autocomplete-${props.name}`)

const { hasError, errorMessage, clearBackendError } = useFormFieldErrors(
  props.name,
  props.validation
)

const {
  query,
  suggestions,
  loading,
  selectedItem,
  search,
  reset
} = useAutocomplete(props.store, {
  debounceDelay: props.debounceDelay,
  minLength: props.minLength,
  optionLabel: props.optionLabel,
  optionValue: props.optionValue
})

// Загружаем начальное значение если есть
onMounted(() => {
  if (props.initialValue) {
    selectedItem.value = props.initialValue
  }
})

const handleSearch = (event) => {
  query.value = event.query
  search(event)
}

const handleSelect = (event) => {
  const selected = event.value
  selectedItem.value = selected
  
  // Сохраняем значение в форму
  const value = typeof selected === 'object' 
    ? selected[props.optionValue] 
    : selected
  
  props.modelValue[props.name] = value
  clearBackendError()
  props.validation?.[props.name]?.$touch?.()
}

const handleClear = () => {
  reset()
  props.modelValue[props.name] = null
  clearBackendError()
  props.validation?.[props.name]?.$touch?.()
}

// Синхронизация с внешними изменениями
watch(() => props.modelValue[props.name], (newValue) => {
  if (!newValue && selectedItem.value) {
    reset()
  }
})
</script>

