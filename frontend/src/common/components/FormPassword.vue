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
        :class="['w-full', {'p-invalid': hasError}]"
        inputClass="w-full"
        v-bind="$attrs"
    />
    <small class="text-red-500" v-if="hasError">
      {{ errorMessage }}
    </small>
  </div>
</template>

<script>
export default {
  name: 'FormPassword',

  props: {
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
  },

  emits: ['update:modelValue'],

  computed: {
    computedId() {
      return this.id || `form-password-${this.name}`;
    },

    hasError() {
      const v = this.validation;
      const key = this.name;
      return !!(v && key && v[key]?.$error);
    },

    errorMessage() {
      const v = this.validation;
      const key = this.name;
      if (v && key && this.hasError) {
        return v[key].$errors?.[0]?.$message || '';
      }
      return '';
    }
  },

  methods: {
    updateField(next) {
      const updated = { ...(this.modelValue || {}) };
      updated[this.name] = next;

      this.$emit('update:modelValue', updated);
      this.touchField();
    },

    touchField() {
      const v = this.validation;
      const key = this.name;
      v?.[key]?.$touch?.();
    }
  }
};
</script>
