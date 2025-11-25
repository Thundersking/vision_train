<script setup>
import {onMounted, ref} from 'vue'
import {useRouter} from 'vue-router'
import {useOrganizationStore} from '@/domains/organization/stores/organization.js'
import {useErrorHandler} from '@/common/composables/useErrorHandler.js'

const router = useRouter()
const {handleError} = useErrorHandler()

const store = useOrganizationStore()

const organization = ref(null)
const loading = ref(false)

onMounted(async () => {
  loading.value = true

  try {
    organization.value = await store.fetch()
  } catch (error) {
    handleError(error, 'Ошибка при загрузке организации')
  } finally {
    loading.value = false
  }
})
</script>

<template>
  <div>
    <TitleBlock title="Моя организация" :back-to="false">
      <template #actions>
        <Button
            label="Редактировать"
            icon="pi pi-pencil"
            @click="router.push({name: 'organization-edit'})"
            :disabled="loading"
        />
      </template>
    </TitleBlock>

    <Card :loading="loading">
      <div v-if="organization" class="space-y-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div class="space-y-4">
            <FieldDisplay label="Название" :value="organization.name" />
            <FieldDisplay label="Тип" :value="organization.type_label" />
            <FieldDisplay label="Домен" :value="organization.domain" />
            <FieldDisplay label="Тариф" :value="organization.subscription_plan || '-'" />
          </div>

          <div class="space-y-4">
            <FieldDisplay label="Email" :value="organization.email" />
            <FieldDisplay label="Телефон" :value="organization.phone" />
            <FieldDisplay
                label="Статус"
                type="tag"
                :value="organization.is_active ? 'Активна' : 'Неактивна'"
                :tag-severity="organization.is_active ? 'success' : 'danger'"
            />
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div class="space-y-4">
            <FieldDisplay label="ИНН" :value="organization.inn" />
            <FieldDisplay label="КПП" :value="organization.kpp" />
            <FieldDisplay label="ОГРН" :value="organization.ogrn" />
          </div>

          <div class="space-y-4">
            <FieldDisplay label="Юридический адрес" :value="organization.legal_address" />
            <FieldDisplay label="Фактический адрес" :value="organization.actual_address" />
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <FieldDisplay label="Руководитель" :value="organization.director_name" />
          <FieldDisplay label="Лицензия" :value="organization.license_number" />
          <FieldDisplay
              v-if="organization.license_issued_at"
              label="Дата выдачи лицензии"
              type="date"
              :value="organization.license_issued_at"
          />
        </div>

        <div v-if="organization.created_at || organization.updated_at" class="grid grid-cols-1 md:grid-cols-2 gap-6 border-t border-gray-200 pt-6">
          <FieldDisplay v-if="organization.created_at" label="Дата создания" type="date" :value="organization.created_at" />
          <FieldDisplay v-if="organization.updated_at" label="Дата обновления" type="date" :value="organization.updated_at" />
        </div>
      </div>

      <div v-else-if="!loading" class="text-center py-8">
        <i class="pi pi-exclamation-triangle text-2xl text-orange-500"></i>
        <p class="mt-2 text-gray-600">Данные организации недоступны</p>
      </div>
    </Card>
  </div>
</template>
