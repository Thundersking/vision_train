<script setup>
import {ref} from 'vue'
import {useRouter} from 'vue-router'
import {useVuelidate} from '@vuelidate/core'
import {useDepartmentsStore} from '@/domains/departments/stores/departments.js'
import {Department} from '@/domains/departments/models/Department.js'
import {TIMEZONE_OPTIONS} from '@/common/constants/timezones.js'

const router = useRouter()
const store = useDepartmentsStore()

const form = ref(new Department())
const formId = 'department-form-create'
const isSubmitting = ref(false)

const $v = useVuelidate(Department.validationRules(), form)

const timezoneOptions = TIMEZONE_OPTIONS

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
  router.push({name: 'departments'})
}
</script>

<template>
  <div>
    <TitleBlock title="Создание офиса" :back-to="{name: 'departments'}">
      <template #actions>
        <Button
            :form="formId"
            type="submit"
            label="Создать"
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
                name="name"
                label="Название"
                required
                placeholder="Введите название офиса"
                :validation="$v"
            />

            <FormInput
                v-model="form"
                name="email"
                label="Email"
                type="email"
                placeholder="Введите email"
                :validation="$v"
            />

            <FormInput
                v-model="form"
                name="phone"
                label="Телефон"
                placeholder="Введите телефон"
                :validation="$v"
            />

            <FormSelect
                v-model="form"
                name="utc_offset_minutes"
                label="Часовой пояс"
                :options="timezoneOptions"
                placeholder="Выберите часовой пояс"
                required
                :validation="$v"
            />

            <FormInput
                v-model="form"
                name="address"
                label="Адрес"
                placeholder="Укажите адрес"
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
