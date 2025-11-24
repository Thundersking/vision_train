<template>
  <form @submit.prevent="handleSubmit" class="space-y-4">
    <slot></slot>

    <div v-if="backendErrors.length" class="bg-red-50 p-3 rounded-md border border-red-200">
      <ul class="text-red-600 text-sm">
        <li v-for="(error, index) in backendErrors" :key="index" class="ml-4 list-disc">
          {{ error }}
        </li>
      </ul>
    </div>

    <slot name="actions" :loading="loading"></slot>
  </form>
</template>

<script setup>
import { ref, computed, provide } from 'vue';

const props = defineProps({
  submit: {
    type: Function,
    required: true
  },
  validator: {
    type: Object,
    default: null
  }
});

const emit = defineEmits(['success', 'error']);

const loading = ref(false);
const fieldErrors = ref({});

const backendErrors = computed(() => {
  return Object.values(fieldErrors.value).flat();
});

const clearFieldError = (fieldName) => {
  if (fieldErrors.value[fieldName]) {
    delete fieldErrors.value[fieldName];
  }
};

provide('baseFormErrors', {
  fieldErrors,
  clearFieldError
});

const handleSubmit = async () => {
  loading.value = true;
  fieldErrors.value = {};

  try {
    const result = await props.submit();
    emit('success', result);
  } catch (error) {
    if (error.response?.data?.errors) {
      fieldErrors.value = error.response.data.errors;
    } else if (error.response?.data?.message) {
      fieldErrors.value = { _general: [error.response.data.message] };
    }

    emit('error', error);
  } finally {
    loading.value = false;
  }
};
</script>

