<template>
  <div>
    <label v-if="label" :for="computedId" class="block text-sm font-medium mb-1 text-surface-900 dark:text-surface-0">
      {{ label }}
      <span v-if="required" class="text-red-500 ml-1">*</span>
    </label>
    <div class="flex items-center gap-2">
      <ToggleSwitch
          :id="computedId"
          :modelValue="modelValue?.[name]"
          @update:modelValue="updateField"
          :disabled="disabled"
          :class="{'p-invalid': hasError}"
          v-bind="$attrs"
      />
    </div>
    <small class="text-red-500" v-if="hasError">
      {{ errorMessage }}
    </small>
  </div>
</template>

<script setup>
import {computed} from 'vue'

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
    required: true,
  },
  label: {
    type: String,
    default: ''
  },
  required: {
    type: Boolean,
    default: false
  },
  disabled: {
    type: Boolean,
    default: false
  },
  validation: {
    type: Object,
    default: () => null
  },
})

const emit = defineEmits(['update:modelValue'])

const computedId = computed(() => {
  return props.id || `form-switch-${props.name}`;
})

const hasError = computed(() => {
  const v = props.validation;
  const key = props.name;
  if (v && key && v[key]) {
    return v[key].$error;
  }
  return false;
})

const errorMessage = computed(() => {
  const v = props.validation;
  const key = props.name;
  if (v && key && hasError.value) {
    return v[key].$errors?.[0]?.$message || '';
  }
  return '';
})

const updateField = (value) => {
  props.modelValue[props.name] = value;
  touchField();
}

const touchField = () => {
  const v = props.validation;
  const key = props.name;
  v?.[key]?.$touch?.();
}
</script>