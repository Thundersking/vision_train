<script setup>
import {onMounted, ref} from 'vue'
import {useRoute, useRouter} from 'vue-router'
import {useVuelidate} from '@vuelidate/core'
import {useDepartmentsStore} from '@/domains/departments/stores/departments.js'
import {Department} from '@/domains/departments/models/Department.js'
import {useErrorHandler} from '@/common/composables/useErrorHandler.js'
import {TIMEZONE_OPTIONS} from '@/common/constants/timezones.js'

const {handleError} = useErrorHandler()
const route = useRoute()
const router = useRouter()
const store = useDepartmentsStore()

const loading = ref(false)
const form = ref(new Department())
const formId = 'department-form-update'
const isSubmitting = ref(false)

const $v = useVuelidate(Department.validationRules(), form)
const timezoneOptions = TIMEZONE_OPTIONS

onMounted(async () => {
  loading.value = true

  try {
    const departmentData = await store.show(route.params.uuid)
    form.value = new Department(departmentData)
  } catch (err) {
    handleError(err, 'Ошибка при загрузке офиса')
  } finally {
    loading.value = false
  }
})

const handleFormSubmit = async () => {
  $v.value.$touch()

  if ($v.value.$invalid) {
    throw new Error('Форма содержит ошибки')
  }

  isSubmitting.value = true
  try {
    await store.update(route.params.uuid, form.value.toApiFormat())
  } finally {
    isSubmitting.value = false
  }
}

const handleSuccess = () => {
  router.push({name: 'department-show', params: {uuid: route.params.uuid}})
}
</script>

<template>
  <div>
    <TitleBlock title="Редактирование офиса" :back-to="true">
      <template #actions>
        <Button
            :form="formId"
            type="submit"
            label="Сохранить"
            icon="pi pi-check"
            :loading="isSubmitting"
            :disabled="loading"
        />
      </template>
    </TitleBlock>

    <Card :loading="loading">
      <BaseForm
          :id="formId"
          :submit="handleFormSubmit"
          :validator="$v"
          @success="handleSuccess"
      >
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div class="space-y-4">
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
          </div>

          <div class="space-y-4">
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
        </div>
      </BaseForm>
    </Card>
  </div>
</template>
