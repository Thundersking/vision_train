<script setup>
import {ref} from 'vue'
import {useRouter} from 'vue-router'
import {useVuelidate} from '@vuelidate/core'
import {useDepartmentsStore} from '@/domains/departments/stores/departments.js'
import {Department} from '@/domains/departments/models/Department.js'
import DepartmentForm from '@/domains/departments/components/DepartmentForm.vue'

const router = useRouter()
const store = useDepartmentsStore()

const form = ref(new Department())
const loading = ref(false)

const $v = useVuelidate(Department.validationRules(), form)

const handleFormSubmit = async () => {
  $v.value.$touch()

  if ($v.value.$invalid) {
    throw new Error('Форма содержит ошибки')
  }

  await store.create(form.value.toApiFormat())
}

const handleSuccess = () => {
  router.push({name: 'departments'})
}
</script>

<template>
  <div>
    <TitleBlock title="Создание офиса" :back-to="{name: 'departments'}" />

    <DepartmentForm
        :form="form"
        :validator="$v"
        :submit="handleFormSubmit"
        :loading="loading"
        @success="handleSuccess"
    />
  </div>
</template>
