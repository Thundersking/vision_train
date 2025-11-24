<script setup>
import {onMounted, ref} from 'vue'
import {useRoute} from 'vue-router'
import {useUserStore} from '@/domains/users/stores/user.js'
import {useErrorHandler} from "@/common/composables/useErrorHandler.js";

const {handleError} = useErrorHandler();

const route = useRoute()
const store = useUserStore()

const user = ref(null)
const loading = ref(false)

onMounted(async () => {
  loading.value = true;

  try {
    user.value = await store.show(route.params.uuid);
  } catch (err) {
    handleError(err, 'Ошибка при загрузке пользователя');
  } finally {
    loading.value = false;
  }
});
</script>

<template>
  <div>
    <TitleBlock title="Просмотр" :back-to="{name: 'users'}">
      <template #actions>
        <Button
            label="Редактировать"
            icon="pi pi-pencil"
            @click="$router.push({name: 'user-update', params: {uuid: route.params.uuid}})"
        />
      </template>
    </TitleBlock>

    <Card :loading="loading">
      <div v-if="user" class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="space-y-4">
          <FieldDisplay label="Фамилия" :value="user.last_name" />
          <FieldDisplay label="Имя" :value="user.first_name" />
          <FieldDisplay label="Email" :value="user.email" />
        </div>
        <div class="space-y-4">
          <FieldDisplay v-if="user.middle_name" label="Отчество" :value="user.middle_name" />
          <FieldDisplay v-if="user.phone" label="Телефон" :value="user.phone" />
          <FieldDisplay 
            label="Статус" 
            type="tag"
            :value="user.is_active ? 'Активный' : 'Неактивный'"
            :tag-severity="user.is_active ? 'success' : 'danger'"
          />
        </div>
      </div>

      <div v-if="user && (user.created_at || user.updated_at)" class="mt-6 pt-6 border-t border-gray-200">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <FieldDisplay v-if="user.created_at" label="Дата создания" type="date" :value="user.created_at" />
          <FieldDisplay v-if="user.updated_at" label="Дата обновления" type="date" :value="user.updated_at" />
        </div>
      </div>

      <div v-if="!user && !loading" class="text-center py-8">
        <i class="pi pi-exclamation-triangle text-2xl text-orange-500"></i>
        <p class="mt-2 text-gray-600">Пользователь не найден</p>
      </div>
    </Card>
  </div>
</template>
