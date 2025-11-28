<template>
  <div>
    <label v-if="label" :for="computedId" class="block text-sm font-medium mb-1 text-surface-900 dark:text-surface-0">
      {{ label }}
      <span v-if="required" class="text-red-500 ml-1">*</span>
    </label>

    <!-- Показываем chip если значение выбрано -->
    <div 
      v-if="showChip" 
      class="p-select w-full"
    >
      <span class="p-select-label flex-1">{{ displayValue }}</span>
      <button
        v-if="!disabled"
        type="button"
        @click="handleClear"
        class="p-select-dropdown"
        :aria-label="`Удалить ${label}`"
      >
        <i class="pi pi-times text-sm text-surface-600 dark:text-surface-400"></i>
      </button>
    </div>


    <!-- Показываем FormAutocomplete если значение не выбрано -->
    <FormAutocomplete
      v-else
      :id="computedId"
      :modelValue="modelValue"
      @update:modelValue="$emit('update:modelValue', $event)"
      :name="name"
      :label="''"
      :store="store"
      :optionLabel="optionLabel"
      :optionValue="optionValue"
      :placeholder="placeholder"
      :required="required"
      :disabled="disabled"
      :validation="validation"
      :debounceDelay="debounceDelay"
      :minLength="minLength"
      :forceSelection="forceSelection"
      @item-selected="handleSelect"
    />
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useFormFieldErrors } from '@/common/composables/useFormFieldErrors.js'
import FormAutocomplete from '@/common/components/form/FormAutocomplete.vue'

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
  // Название поля для отображения в chip (например, 'patient_name', 'template_name')
  displayField: {
    type: String,
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
  }
})

const computedId = computed(() => props.id || `form-autocomplete-display-${props.name}`)

const { clearBackendError } = useFormFieldErrors(
  props.name,
  props.validation
)

// Проверяем, нужно ли показывать chip
const showChip = computed(() => {
  const valueId = props.modelValue[props.name]
  const displayValue = props.modelValue[props.displayField]
  return valueId && displayValue
})

// Значение для отображения в chip
const displayValue = computed(() => {
  return props.modelValue[props.displayField] || ''
})

const handleSelect = (selected) => {
  // Сохраняем значение для отображения из выбранного объекта
  if (selected && typeof selected === 'object') {
    props.modelValue[props.displayField] = selected[props.optionLabel]
  }
}

const handleClear = () => {
  props.modelValue[props.name] = null
  props.modelValue[props.displayField] = null
  clearBackendError()
  props.validation?.[props.name]?.$touch?.()
}
</script>

