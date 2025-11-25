<template>
  <div>
    <label v-if="label" :for="computedId" class="block text-sm font-medium mb-1 text-surface-900 dark:text-surface-0">
      {{ label }}
      <span v-if="required" class="text-red-500 ml-1">*</span>
    </label>

    <Dropdown
        :id="computedId"
        :modelValue="modelValue?.[name]"
        @update:modelValue="updateField"
        :options="options"
        :optionLabel="optionLabel"
        :optionValue="optionValue"
        :placeholder="placeholder"
        :disabled="disabled"
        :showClear="clearable"
        class="w-full"
        :class="{'p-invalid': hasError}"
        v-bind="$attrs"
    />

    <small class="text-red-500" v-if="hasError">
      {{ errorMessage }}
    </small>
  </div>
</template>

<script setup>
import {computed} from 'vue'
import Dropdown from 'primevue/dropdown'
import {useFormFieldErrors} from "@/common/composables/useFormFieldErrors.js";

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
  options: {
    type: Array,
    default: () => []
  },
  optionLabel: {
    type: String,
    default: 'label'
  },
  optionValue: {
    type: String,
    default: 'value'
  },
  placeholder: {
    type: String,
    default: ''
  },
  disabled: {
    type: Boolean,
    default: false
  },
  clearable: {
    type: Boolean,
    default: false
  },
  validation: {
    type: Object,
    default: () => null
  }
})

const computedId = computed(() => props.id || `form-select-${props.name}`)

const { hasError, errorMessage, clearBackendError } = useFormFieldErrors(
    props.name,
    props.validation
);

const updateField = (value) => {
  props.modelValue[props.name] = value
  clearBackendError()

  props.validation?.[props.name]?.$touch?.()
}
</script>
