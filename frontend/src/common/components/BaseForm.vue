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

<script>
export default {
  name: 'BaseForm',

  props: {
    submit: {
      type: Function,
      required: true
    },
    validator: {
      type: Object,
      default: null
    }
  },

  emits: ['success', 'error'],

  data() {
    return {
      backendErrors: [],
      loading: false
    };
  },

  methods: {
    async handleSubmit() {
      this.loading = true;
      this.backendErrors = [];

      try {
        const result = await this.submit();
        this.$emit('success', result);
      } catch (error) {
        console.log('error', error);

        /**
         * Обработка ошибок с бэкенда
         */
        if (error.response?.data?.errors) {
          const errors = error.response.data.errors;
          this.backendErrors = Object.values(errors).flat();
        } else if (error.response?.data?.message) {
          this.backendErrors = [error.response.data.message];
        }

        this.$emit('error', error);
      } finally {
        this.loading = false;
      }
    }
  }
};
</script>
