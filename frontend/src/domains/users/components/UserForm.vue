<template>
  <Card :loading="loading">
    <BaseForm
      :submit="submit"
      :validator="validator"
      @success="onSuccess"
      @error="onError"
    >
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="space-y-4">
          <FormInput
            v-model="form"
            name="last_name"
            label="Фамилия"
            required
            placeholder="Введите фамилию"
            :validation="validator"
          />

          <FormInput
            v-model="form"
            name="first_name"
            label="Имя"
            required
            placeholder="Введите имя"
            :validation="validator"
          />

          <FormInput
            v-model="form"
            name="email"
            label="Email"
            type="email"
            required
            placeholder="Введите email"
            :validation="validator"
          />
        </div>

        <div class="space-y-4">
          <FormInput
            v-model="form"
            name="middle_name"
            label="Отчество"
            placeholder="Введите отчество"
            :validation="validator"
          />

          <FormInput
            v-model="form"
            name="phone"
            label="Телефон"
            type="tel"
            placeholder="Введите телефон"
            :validation="validator"
          />

          <FormSwitch
            v-model="form"
            name="is_active"
            label="Статус"
            required
            :validation="validator"
          />
        </div>
      </div>

      <template #actions="{ loading: formLoading }">
        <slot name="actions" :loading="formLoading">
          <div class="flex gap-2 mt-6">
            <Button
              :label="submitLabel"
              :icon="submitIcon"
              :loading="formLoading"
              type="submit"
            />
          </div>
        </slot>
      </template>
    </BaseForm>
  </Card>
</template>

<script setup>
import {toRefs} from 'vue'

const props = defineProps({
  form: {
    type: Object,
    required: true
  },
  validator: {
    type: Object,
    required: true
  },
  submit: {
    type: Function,
    required: true
  },
  loading: {
    type: Boolean,
    default: false
  },
  submitLabel: {
    type: String,
    default: 'Сохранить'
  },
  submitIcon: {
    type: String,
    default: 'pi pi-check'
  }
})

const emit = defineEmits(['success', 'error'])

const {form, validator, submit, loading, submitLabel, submitIcon} = toRefs(props)

const onSuccess = (payload) => {
  emit('success', payload)
}

const onError = (error) => {
  emit('error', error)
}
</script>
