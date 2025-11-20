<template>
  <div class="w-full">
    <DataTable
        :value="rows"
        :loading="loading"
        :paginator="true"
        :rows="rowsPerPage"
        :totalRecords="total"
        :rowsPerPageOptions="rowsPerPageOptions"
        paginatorTemplate="RowsPerPageDropdown FirstPageLink PrevPageLink CurrentPageReport NextPageLink LastPageLink"
        currentPageReportTemplate="{first} до {last} из {totalRecords}"
        tableStyle="min-width: 50rem"
        responsiveLayout="scroll"
        @page="onPage"
    >
      <!-- Динамические колонки -->
      <Column
          v-for="col of columns"
          :key="col.field"
          :field="col.field"
          :header="col.header"
      >
        <template #body="{ data: rowData }">
          <!-- Используем slot если указан, иначе дефолт рендер -->
          <slot
              v-if="col.slot"
              :name="col.slot"
              :row="rowData"
          >
            {{ rowData[col.field] }}
          </slot>

          <!-- Дефолтный рендер если нет слота -->
          <template v-else>
            {{ rowData[col.field] }}
          </template>
        </template>
      </Column>

      <!-- Колонка действий (если есть actions) -->
      <Column
          v-if="actions && actions.length > 0"
          header="Действия"
          style="width: 10%"
          bodyStyle="text-align:center"
      >
        <template #body="{ data: rowData }">
          <div class="flex items-center justify-center gap-2">
            <Button
                v-for="action of actions"
                :key="action.label"
                :icon="action.icon"
                :severity="action.severity || 'secondary'"
                text
                rounded
                @click="action.callback(rowData)"
                :title="action.label"
            />
          </div>
        </template>
      </Column>

      <!-- Empty state -->
      <template #empty>
        <div class="py-8 text-center text-slate-500 font-medium">
          Нет данных
        </div>
      </template>
    </DataTable>
  </div>
</template>

<script setup>
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Button from 'primevue/button';
import {usePageData} from "@/common/composables/usePageData.js";
import {computed, ref, watch} from "vue";

const props = defineProps({
  store: {
    type: Object,
    required: true
  },

  // Описание колонок
  // { field: 'name', header: 'Имя', slot?: 'name' }
  columns: {
    type: Array,
    required: true,
    validator: (cols) => {
      return cols.every(col => col.field && col.header);
    }
  },

  // Action-кнопки
  // { label: 'Редактировать', icon: 'pi pi-pencil', callback: (row) => {} }
  actions: {
    type: Array,
    default: () => [],
    validator: (acts) => {
      return acts.every(act => act.label && act.icon && typeof act.callback === 'function');
    }
  },

  rowsPerPageOptions: {
    type: Array,
    default: () => [10, 25, 50]
  }
});

const rows = computed(() =>
    Array.isArray(data?.value?.data) ? data.value.data : (Array.isArray(data.value) ? data.value : [])
)

// Метаданные пагинации из стора
const meta = computed(() => props.store.meta);

// Общее количество записей
const total = computed(() => meta.value?.total ?? 0);

// Размер страницы
const rowsPerPage = ref(meta.value?.per_page ?? props.rowsPerPageOptions[0]);

// Обновляем когда меняется мета
watch(meta, (m) => {
  if (m) {
    rowsPerPage.value = m.per_page ?? 10;
  }
});

// Обработчик смены страницы
const onPage = async (e) => {
  // e.page - это номер страницы (0-based)
  // Laravel ожидает 1-based, поэтому +1
  const nextPage = (e.page ?? 0) + 1;
  const perPage = e.rows ?? rowsPerPage.value;

  await props.store.index({
    page: nextPage,
    per_page: perPage
  });
};

// Composable для управления данными
const {data, loading} = usePageData(props.store);
</script>
