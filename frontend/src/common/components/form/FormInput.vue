<template>
  <div>
    <label v-if="label" :for="computedId" class="block text-sm font-medium mb-1 text-surface-900 dark:text-surface-0">
      {{ label }}
      <span v-if="required" class="text-red-500 ml-1">*</span>
    </label>
    <InputText
        :id="computedId"
        :value="modelValue?.[name]"
        @update:modelValue="updateField"
        :type="type"
        :placeholder="placeholder"
        :disabled="disabled"
        :class="['w-full', {'p-invalid': hasError}]"
        v-bind="$attrs"
    />
    <small class="text-red-500" v-if="hasError">
      {{ errorMessage }}
    </small>
  </div>
</template>

<script setup>
import {computed} from 'vue'
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
    required: true,
  },
  label: {
    type: String,
    default: ''
  },
  type: {
    type: String,
    default: 'text'
  },
  required: {
    type: Boolean,
    default: false
  },
  placeholder: {
    type: String,
    default: ''
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
  return props.id || `form-input-${props.name}`;
})

const { hasError, errorMessage, clearBackendError } = useFormFieldErrors(
    props.name,
    props.validation
);

const updateField = (value) => {
  props.modelValue[props.name] = value;

  clearBackendError();

  touchField();
}

const touchField = () => {
  const v = props.validation;
  const key = props.name;
  v?.[key]?.$touch?.();
}
</script>
