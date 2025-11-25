<script setup>
import {onMounted, ref} from 'vue'
import {useRoute, useRouter} from 'vue-router'
import {useVuelidate} from '@vuelidate/core'
import {useUserStore} from '@/domains/users/stores/user.js'
import {useErrorHandler} from "@/common/composables/useErrorHandler.js";
import {User} from '@/domains/users/models/User.js'

const {handleError} = useErrorHandler();

const route = useRoute()
const router = useRouter()
const store = useUserStore()

const loading = ref(false)
const isSubmitting = ref(false)
const formId = 'user-form-update'

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
  
  isSubmitting.value = true

  try {
    await store.update(route.params.uuid, form.value.toApiFormat());
  } finally {
    isSubmitting.value = false
  }
};

const handleSuccess = () => {
  router.push({name: 'user-show', params: {uuid: route.params.uuid}});
};

</script>

<template>
  <div>
    <TitleBlock title="Редактирование" :back-to="true">
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
          </div>

          <div class="space-y-4">
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
        </div>
      </BaseForm>
    </Card>
  </div>
</template>
