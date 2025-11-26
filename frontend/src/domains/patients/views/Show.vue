<script setup>
import { ref } from 'vue'
import { useRoute } from 'vue-router'
import { useTabState } from '@/common/composables/useTabState.js'

const route = useRoute()

const tabs = [
  { value: 'overview', label: 'Общая информация', icon: 'pi pi-user' },
  { value: 'examinations', label: 'Журнал обследований', icon: 'pi pi-notebook' },
  { value: 'devices', label: 'Устройства', icon: 'pi pi-tablet' },
  { value: 'programs', label: 'Программы', icon: 'pi pi-clipboard' },
]

const activeTab = ref('overview')
useTabState(activeTab)
</script>

<template>
  <div class="space-y-6">
    <TitleBlock
      title="Карточка пациента"
      :description="route.params.uuid"
      :back-to="{ name: 'patients' }"
    >
      <template #actions>
        <Button
          label="Редактировать"
          icon="pi pi-pencil"
          @click="$router.push({ name: 'patient-update', params: { uuid: route.params.uuid } })"
        />
        <Button
          label="Удалить"
          icon="pi pi-trash"
          severity="danger"
          outlined
        />
      </template>
    </TitleBlock>

    <BaseTabs v-model="activeTab" :tabs="tabs">
      <template #overview>
        <Card>
          <div class="text-gray-500 dark:text-gray-400">
            Здесь будет блок с общей информацией о пациенте (ФИО, контакты, параметры).
          </div>
        </Card>
      </template>

      <template #examinations>
        <Card>
          <div class="space-y-4 text-gray-500 dark:text-gray-400">
            <p>Тут отобразим таблицу "Журнал обследований" + форму добавления записи.</p>
            <p>Данные будут подгружаться лениво при первом открытии вкладки.</p>
          </div>
        </Card>
      </template>

      <template #devices>
        <Card>
          <div class="text-gray-500 dark:text-gray-400">
            Заглушка для "Устройства" — появится позже (Этап 2).
          </div>
        </Card>
      </template>

      <template #programs>
        <Card>
          <div class="text-gray-500 dark:text-gray-400">
            Заглушка для "Назначенных программ" — реализуем на соответствующем этапе.
          </div>
        </Card>
      </template>
    </BaseTabs>
  </div>
</template>
