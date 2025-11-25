<script setup>
import {onMounted, ref} from 'vue'
import {useRoute} from 'vue-router'
import {useDepartmentsStore} from '@/domains/departments/stores/departments.js'
import {useErrorHandler} from "@/common/composables/useErrorHandler.js";
import {formatTimezoneOffset} from "@/common/utils/timezone.js";

const {handleError} = useErrorHandler();
const route = useRoute()
const store = useDepartmentsStore()

const department = ref(null)
const loading = ref(false)

onMounted(async () => {
  loading.value = true

  try {
    department.value = await store.show(route.params.uuid)
  } catch (err) {
    handleError(err, 'Ошибка при загрузке офиса')
  } finally {
    loading.value = false
  }
})
</script>

<template>
  <div>
    <TitleBlock title="Просмотр офиса" :back-to="{name: 'departments'}">
      <template #actions>
        <Button
            label="Редактировать"
            icon="pi pi-pencil"
            @click="$router.push({name: 'department-update', params: {uuid: route.params.uuid}})"
        />
      </template>
    </TitleBlock>

    <Card :loading="loading">
      <div v-if="department" class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="space-y-4">
          <FieldDisplay label="Название" :value="department.name" />
          <FieldDisplay label="Email" :value="department.email" />
          <FieldDisplay label="Телефон" :value="department.phone" />
        </div>

        <div class="space-y-4">
          <FieldDisplay v-if="department.address" label="Адрес" :value="department.address" />
          <FieldDisplay
              label="Часовой пояс"
              :value="department.timezone_display || formatTimezoneOffset(department.utc_offset_minutes)"
          />
          <FieldDisplay
              label="Статус"
              type="tag"
              :value="department.is_active ? 'Активен' : 'Неактивен'"
              :tag-severity="department.is_active ? 'success' : 'danger'"
          />
        </div>
      </div>

      <div v-if="department && (department.created_at || department.updated_at)" class="mt-6 pt-6 border-t border-gray-200">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <FieldDisplay v-if="department.created_at" label="Дата создания" type="date" :value="department.created_at" />
          <FieldDisplay v-if="department.updated_at" label="Дата обновления" type="date" :value="department.updated_at" />
        </div>
      </div>

      <div v-if="!department && !loading" class="text-center py-8">
        <i class="pi pi-exclamation-triangle text-2xl text-orange-500"></i>
        <p class="mt-2 text-gray-600">Офис не найден</p>
      </div>
    </Card>
  </div>
</template>
