<script setup>
import {ref} from 'vue'
import {useRouter} from 'vue-router'
import {useVuelidate} from '@vuelidate/core'
import {useUserStore} from '@/domains/users/stores/user.js'
import {User} from '@/domains/users/models/User.js'

const router = useRouter()
const store = useUserStore()

const form = ref(new User())
const isSubmitting = ref(false)
const formId = 'user-form-create'

const $v = useVuelidate(User.validationRules(), form)

const handleFormSubmit = async () => {
  $v.value.$touch()

  if ($v.value.$invalid) {
    throw new Error('Форма содержит ошибки')
  }

  isSubmitting.value = true

  try {
    await store.create(form.value.toApiFormat())
  } finally {
    isSubmitting.value = false
  }
}

const handleSuccess = () => {
  router.push({name: 'users'})
}
</script>

<template>
  <div>
    <TitleBlock title="Создание" :back-to="{name: 'users'}">
      <template #actions>
        <Button
            :form="formId"
            type="submit"
            label="Сохранить"
            icon="pi pi-check"
            :loading="isSubmitting"
        />
      </template>
    </TitleBlock>

    <Card>
      <BaseForm
          :id="formId"
          :submit="handleFormSubmit"
          :validator="$v"
          @success="handleSuccess"
      >
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <FormInput
              v-model="form"
              name="last_name"
              label="Фамилия"
              required
              placeholder="Введите фамилию"
              :validation="$v"
          />

          <FormInput
              v-model="form"
              name="first_name"
              label="Имя"
              required
              placeholder="Введите имя"
              :validation="$v"
          />

          <FormInput
              v-model="form"
              name="email"
              label="Email"
              type="email"
              required
              placeholder="Введите email"
              :validation="$v"
          />

          <FormInput
              v-model="form"
              name="middle_name"
              label="Отчество"
              placeholder="Введите отчество"
              :validation="$v"
          />

          <FormInput
              v-model="form"
              name="phone"
              label="Телефон"
              type="tel"
              placeholder="Введите телефон"
              :validation="$v"
          />

          <FormSwitch
              v-model="form"
              name="is_active"
              label="Статус"
              required
              :validation="$v"
          />
        </div>
      </BaseForm>
    </Card>
  </div>
</template>
