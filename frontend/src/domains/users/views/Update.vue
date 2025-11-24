<script setup>
import {onMounted, ref} from 'vue'
import {useRoute, useRouter} from 'vue-router'
import {useVuelidate} from '@vuelidate/core'
import {useUserStore} from '@/domains/users/stores/user.js'
import {useErrorHandler} from "@/common/composables/useErrorHandler.js";
import {User} from '@/domains/users/models/User.js'
import UserForm from '@/domains/users/components/UserForm.vue'

const {handleError} = useErrorHandler();

const route = useRoute()
const router = useRouter()
const store = useUserStore()

const loading = ref(false)

const form = ref(new User())

const $v = useVuelidate(User.validationRules(), form)

onMounted(async () => {
  loading.value = true;

  try {
    const userData = await store.show(route.params.uuid);
    form.value = new User(userData);
  } catch (err) {
    handleError(err, 'Ошибка при загрузке пользователя');
  } finally {
    loading.value = false;
  }
});

const handleFormSubmit = async () => {
  $v.value.$touch();
  
  if ($v.value.$invalid) {
    throw new Error('Форма содержит ошибки');
  }
  
  await store.update(route.params.uuid, form.value.toApiFormat());
};

const handleSuccess = () => {
  router.push({name: 'user-show', params: {uuid: route.params.uuid}});
};

</script>

<template>
  <div>
    <TitleBlock title="Редактирование" :back-to="true" />

    <UserForm 
        :form="form"
        :validator="$v"
        :submit="handleFormSubmit"
        :loading="loading"
        @success="handleSuccess"
    />
  </div>
</template>
