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

<script>
export default {
  name: 'FormInput',

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
    /**
     * Является ли поле обязательным
     */
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
  },

  emits: ['update:modelValue'],

  computed: {
    computedId() {
      return this.id || `form-input-${this.name}`;
    },

    hasError() {
      const v = this.validation;
      const key = this.name;
      if (v && key && v[key]) {
        return v[key].$error;
      }
      return false;
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
    updateField(value) {
      this.modelValue[this.name] = value;

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
