<script setup>
import {onMounted, ref} from 'vue'
import {useRoute, useRouter} from 'vue-router'
import {useVuelidate} from '@vuelidate/core'
import {useDepartmentsStore} from '@/domains/departments/stores/departments.js'
import {Department} from '@/domains/departments/models/Department.js'
import {useErrorHandler} from "@/common/composables/useErrorHandler.js";
import DepartmentForm from '@/domains/departments/components/DepartmentForm.vue'

const {handleError} = useErrorHandler();
const route = useRoute()
const router = useRouter()
const store = useDepartmentsStore()

const loading = ref(false)
const form = ref(new Department())

const $v = useVuelidate(Department.validationRules(), form)

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

  await store.update(route.params.uuid, form.value.toApiFormat())
}

const handleSuccess = () => {
  router.push({name: 'department-show', params: {uuid: route.params.uuid}})
}
</script>

<template>
  <div>
    <TitleBlock title="Редактирование офиса" :back-to="true" />

    <DepartmentForm
        :form="form"
        :validator="$v"
        :submit="handleFormSubmit"
        :loading="loading"
        @success="handleSuccess"
    />
  </div>
</template>
