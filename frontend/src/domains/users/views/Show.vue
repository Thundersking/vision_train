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
        />
      </template>
    </TitleBlock>

    <Card :loading="loading">
      <div v-if="user" class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Фамилия</label>
            <p class="text-gray-900 font-medium">{{ user.last_name }}</p>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Имя</label>
            <p class="text-gray-900 font-medium">{{ user.first_name }}</p>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <p class="text-gray-900 font-medium">{{ user.email }}</p>
          </div>
        </div>
        <div class="space-y-4">
          <div v-if="user.middle_name">
            <label class="block text-sm font-medium text-gray-700 mb-1">Отчество</label>
            <p class="text-gray-900 font-medium">{{ user.middle_name }}</p>
          </div>
          <div v-if="user.phone">
            <label class="block text-sm font-medium text-gray-700 mb-1">Телефон</label>
            <p class="text-gray-900 font-medium">{{ user.phone }}</p>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Статус</label>
            <Tag :value="user.is_active ? 'Активный' : 'Неактивный'"
                 :severity="user.is_active ? 'success' : 'danger'"/>
          </div>
        </div>
      </div>

      <div v-if="user && (user.created_at || user.updated_at)" class="mt-6 pt-6 border-t border-gray-200">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div v-if="user.created_at">
            <label class="block text-sm font-medium text-gray-700 mb-1">Дата создания</label>
            <p class="text-gray-600">{{ new Date(user.created_at).toLocaleString('ru-RU') }}</p>
          </div>
          <div v-if="user.updated_at">
            <label class="block text-sm font-medium text-gray-700 mb-1">Дата обновления</label>
            <p class="text-gray-600">{{ new Date(user.updated_at).toLocaleString('ru-RU') }}</p>
          </div>
        </div>
      </div>

      <div v-if="!user && !loading" class="text-center py-8">
        <i class="pi pi-exclamation-triangle text-2xl text-orange-500"></i>
        <p class="mt-2 text-gray-600">Пользователь не найден</p>
      </div>
    </Card>
  </div>
</template>
