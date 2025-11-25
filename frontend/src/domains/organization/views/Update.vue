<script setup>
import {onMounted, ref} from 'vue'
import {useRouter} from 'vue-router'
import {useVuelidate} from '@vuelidate/core'
import {useOrganizationStore} from '@/domains/organization/stores/organization.js'
import {Organization} from '@/domains/organization/models/Organization.js'
import {useErrorHandler} from '@/common/composables/useErrorHandler.js'
import {ORGANIZATION_TYPE_OPTIONS} from '@/domains/organization/constants/organizationTypes.js'

const router = useRouter()
const store = useOrganizationStore()
const {handleError} = useErrorHandler()

const form = ref(new Organization())
const loading = ref(false)
const isSubmitting = ref(false)
const formId = 'organization-form'

const $v = useVuelidate(Organization.validationRules(), form)

onMounted(async () => {
  loading.value = true

  try {
    const data = await store.fetch()
    form.value = new Organization(data)
  } catch (error) {
    handleError(error, 'Ошибка при загрузке организации')
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
    await store.update(form.value.toApiFormat())
  } finally {
    isSubmitting.value = false
  }
}

const handleSuccess = () => {
  router.push({name: 'organization-show'})
}
</script>

<template>
  <div>
    <TitleBlock title="Редактирование организации" :back-to="{name: 'organization-show'}">
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
          <FormInput
              v-model="form"
              name="name"
              label="Название"
              required
              placeholder="Введите название"
              :validation="$v"
          />

          <FormSelect
              v-model="form"
              name="type"
              label="Тип организации"
              :options="ORGANIZATION_TYPE_OPTIONS"
              required
              placeholder="Выберите тип"
              :validation="$v"
          />

          <FormInput
              v-model="form"
              name="subscription_plan"
              label="Тариф"
              placeholder="Название тарифа"
              :validation="$v"
          />

          <FormInput
              v-model="form"
              name="domain"
              label="Домен"
              disabled
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
          <FormInput
              v-model="form"
              name="inn"
              label="ИНН"
              placeholder="Введите ИНН"
              :validation="$v"
          />

          <FormInput
              v-model="form"
              name="kpp"
              label="КПП"
              placeholder="Введите КПП"
              :validation="$v"
          />

          <FormInput
              v-model="form"
              name="ogrn"
              label="ОГРН"
              placeholder="Введите ОГРН"
              :validation="$v"
          />

          <FormInput
              v-model="form"
              name="legal_address"
              label="Юридический адрес"
              placeholder="Укажите юридический адрес"
              :validation="$v"
          />

          <FormInput
              v-model="form"
              name="actual_address"
              label="Фактический адрес"
              placeholder="Укажите фактический адрес"
              :validation="$v"
          />

          <FormInput
              v-model="form"
              name="director_name"
              label="Руководитель"
              placeholder="ФИО руководителя"
              :validation="$v"
          />

          <FormInput
              v-model="form"
              name="license_number"
              label="Номер лицензии"
              placeholder="Введите номер лицензии"
              :validation="$v"
          />

          <FormInput
              v-model="form"
              name="license_issued_at"
              label="Дата выдачи лицензии"
              type="date"
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
