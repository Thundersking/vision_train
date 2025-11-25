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
              name="name"
              label="Название"
              required
              placeholder="Введите название офиса"
              :validation="validator"
          />

          <FormInput
              v-model="form"
              name="email"
              label="Email"
              type="email"
              placeholder="Введите email"
              :validation="validator"
          />

          <FormInput
              v-model="form"
              name="phone"
              label="Телефон"
              placeholder="Введите телефон"
              :validation="validator"
          />
        </div>

        <div class="space-y-4">
          <FormSelect
              v-model="form"
              name="utc_offset_minutes"
              label="Часовой пояс"
              :options="timezoneOptions"
              placeholder="Выберите часовой пояс"
              required
              :validation="validator"
          />

          <FormInput
              v-model="form"
              name="address"
              label="Адрес"
              placeholder="Укажите адрес"
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
                label="Сохранить"
                icon="pi pi-check"
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
import {TIMEZONE_OPTIONS} from "@/common/constants/timezones.js";

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
  }
})

const emit = defineEmits(['success', 'error'])

const {form, validator, submit, loading} = toRefs(props)

const timezoneOptions = TIMEZONE_OPTIONS

const onSuccess = (payload) => {
  emit('success', payload)
}

const onError = (error) => {
  emit('error', error)
}
</script>
