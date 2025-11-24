<template>
  <div>
    <label v-if="label" :for="computedId" class="block text-sm font-medium mb-1 text-surface-900 dark:text-surface-0">{{ label }}</label>
    <Password
        :id="computedId"
        :value="modelValue?.[name]"
        @update:modelValue="updateField"
        :feedback="feedback"
        :toggleMask="toggleMask"
        :placeholder="placeholder"
        :disabled="disabled"
        :invalid="hasError"
        class="w-full"
        inputClass="w-full"
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
    required: true
  },
  label: {
    type: String,
    default: ''
  },
  placeholder: {
    type: String,
    default: ''
  },
  disabled: {
    type: Boolean,
    default: false
  },
  toggleMask: {
    type: Boolean,
    default: false
  },
  feedback: {
    type: Boolean,
    default: false
  },
  validation: {
    type: Object,
    default: () => null
  }
})

const emit = defineEmits(['update:modelValue'])

const computedId = computed(() => {
  return props.id || `form-password-${props.name}`;
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
