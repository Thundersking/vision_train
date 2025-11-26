<script setup>
import { onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useVuelidate } from '@vuelidate/core'
import { usePatientStore } from '@/domains/patients/stores/patient.js'
import { useUserStore } from '@/domains/users/stores/user.js'
import { Patient } from '@/domains/patients/models/Patient.js'
import { GENDER_OPTIONS } from '@/domains/patients/сonstants/constants.js'

const router = useRouter()
const store = usePatientStore()
const userStore = useUserStore()

const formId = 'patient-form-create'
const form = ref(new Patient())
const $v = useVuelidate(Patient.validationRules(), form)

const isSubmitting = ref(false)
const doctorOptions = ref([])
const loadingDoctors = ref(false)
const genderOptions = GENDER_OPTIONS

onMounted(() => {
  fetchDoctors()
})

const fetchDoctors = async () => {
  loadingDoctors.value = true
  try {
    doctorOptions.value = await userStore.allList()
  } finally {
    loadingDoctors.value = false
  }
}

const handleSubmit = async () => {
  $v.value.$touch()
  if ($v.value.$invalid) {
    throw new Error('Заполните обязательные поля')
  }

  isSubmitting.value = true
  try {
    const payload = form.value.toApiFormat()
    const response = await store.create(payload)
    return response
  } finally {
    isSubmitting.value = false
  }
}

const handleSuccess = (response) => {
  const patientData = response?.data ?? response
  const uuid = patientData?.uuid
  if (uuid) {
    router.push({ name: 'patient-show', params: { uuid } })
  } else {
    router.push({ name: 'patients' })
  }
}
</script>

<template>
  <div class="space-y-6">
    <TitleBlock title="Создание пациента" :back-to="{ name: 'patients' }">
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

    <Card :loading="loadingDoctors">
      <BaseForm
        :id="formId"
        :submit="handleSubmit"
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
            name="middle_name"
            label="Отчество"
            placeholder="Введите отчество"
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
            required
            placeholder="Введите телефон"
            :validation="$v"
          />

          <FormSelect
            v-model="form"
            name="gender"
            label="Пол"
            :options="genderOptions"
            required
            placeholder="Выберите пол"
            :validation="$v"
          />

          <FormInput
            v-model="form"
            name="hand_size_cm"
            label="Размер руки (см)"
            type="number"
            step="0.1"
            min="5"
            max="30"
            required
            placeholder="Например, 12.5"
            :validation="$v"
          />

          <FormDateTime
            v-model="form"
            name="birth_date"
            label="Дата рождения"
            :showTime="false"
            hourFormat="24"
            required
            placeholder="Выберите дату"
            :validation="$v"
          />

          <FormSelect
            v-model="form"
            name="user_id"
            label="Закрепленный врач"
            :options="doctorOptions"
            optionLabel="name"
            optionValue="id"
            required
            placeholder="Выберите врача"
            :validation="$v"
            :disabled="loadingDoctors"
          />

          <FormSwitch
            v-model="form"
            name="is_active"
            label="Статус"
            :validation="$v"
          />
        </div>

        <FormTextarea
          v-model="form"
          name="notes"
          label="Примечания"
          rows="4"
          placeholder="Дополнительная информация"
          :validation="$v"
        />
      </BaseForm>
    </Card>
  </div>
</template>
