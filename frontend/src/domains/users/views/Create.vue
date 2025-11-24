<script setup>
import {ref} from 'vue'
import {useRouter} from 'vue-router'
import {useVuelidate} from '@vuelidate/core'
import {useUserStore} from '@/domains/users/stores/user.js'
import {User} from '@/domains/users/models/User.js'
import UserForm from '@/domains/users/components/UserForm.vue'

const router = useRouter()
const store = useUserStore()

const form = ref(new User())
const loading = ref(false)

const $v = useVuelidate(User.validationRules(), form)

const handleFormSubmit = async () => {
  $v.value.$touch()
  
  if ($v.value.$invalid) {
    throw new Error('Форма содержит ошибки')
  }
  
  await store.create(form.value.toApiFormat())
}

const handleSuccess = () => {
  router.push({name: 'users'})
}
</script>

<template>
  <div>
    <TitleBlock title="Создание" :back-to="{name: 'users'}" />

    <UserForm
        :form="form"
        :validator="$v"
        :submit="handleFormSubmit"
        :loading="loading"
        submit-label="Создать"
        submit-icon="pi pi-plus"
        @success="handleSuccess"
    />
  </div>
</template>
