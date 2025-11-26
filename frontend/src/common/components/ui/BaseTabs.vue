<template>
  <div class="base-tabs bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-800">
    <Tabs
      :value="activeValue"
      class="rounded-2xl"
      @update:value="handleUpdate"
    >
      <TabList class="overflow-x-auto">
        <Tab
          v-for="tab in normalizedTabs"
          :key="tab.value"
          :value="tab.value"
          :disabled="tab.disabled"
          :class="[
            'text-slate-600 dark:text-slate-300 font-medium py-3 px-1 min-w-[120px] whitespace-nowrap data-[highlight=true]:text-primary-600 data-[highlight=true]:dark:text-primary-400',
            tabClass
          ]"
        >
          <div class="flex items-center gap-2">
            <i v-if="tab.icon" :class="[tab.icon, 'text-base']" />
            <span class="font-medium">{{ tab.label }}</span>
            <span v-if="tab.badge" class="px-2 py-0.5 rounded-full bg-slate-100 dark:bg-slate-800 text-xs text-slate-600 dark:text-slate-300">{{ tab.badge }}</span>
          </div>
        </Tab>
      </TabList>

      <TabPanels>
        <TabPanel
          v-for="tab in normalizedTabs"
          :key="tab.value"
          :value="tab.value"
          :lazy="lazy || tab.lazy"
          class="rounded-b-2xl"
        >
          <div :class="contentClass">
            <template v-if="isTabLoading(tab.value)">
              <div :class="loadingWrapperClass">
                <i v-if="loadingIconClass" :class="loadingIconClass" />
                <p v-if="loadingText" class="text-sm font-medium">{{ loadingText }}</p>
              </div>
            </template>
            <template v-else>
              <slot :name="tab.value" :tab="tab">
                <div class="text-sm text-slate-500">Нет контента для вкладки</div>
              </slot>
            </template>
          </div>
        </TabPanel>
      </TabPanels>
    </Tabs>
  </div>
</template>

<script setup>
import { computed, onMounted, ref, watch } from 'vue'
import Tabs from 'primevue/tabs'
import TabList from 'primevue/tablist'
import Tab from 'primevue/tab'
import TabPanels from 'primevue/tabpanels'
import TabPanel from 'primevue/tabpanel'

const props = defineProps({
  tabs: {
    type: Array,
    required: true
  },
  modelValue: {
    type: [String, Number],
    default: null
  },
  lazy: {
    type: Boolean,
    default: true
  },
  contentClass: {
    type: String,
    default: 'p-6'
  },
  tabClass: {
    type: String,
    default: ''
  },
  loading: {
    type: [Boolean, Object],
    default: false
  },
  loadingText: {
    type: String,
    default: 'Загрузка...'
  },
  loadingIconClass: {
    type: String,
    default: 'pi pi-spin pi-spinner text-2xl text-slate-400 mb-2'
  },
  loadingWrapperClass: {
    type: String,
    default: 'flex flex-col items-center justify-center min-h-[160px] py-8 text-slate-500 dark:text-slate-400'
  }
})

const emit = defineEmits(['update:modelValue', 'change'])

const normalizedTabs = computed(() => props.tabs.filter(Boolean))

const getFallbackValue = () => normalizedTabs.value[0]?.value ?? null

const activeValue = ref(props.modelValue ?? getFallbackValue())

const syncFromModel = (value) => {
  if (value === undefined) {
    return
  }

  if (value === null) {
    activeValue.value = getFallbackValue()
    return
  }

  if (activeValue.value === value) {
    return
  }

  activeValue.value = value
}

watch(() => props.modelValue, syncFromModel)

watch(normalizedTabs, (tabs) => {
  if (!tabs.some((tab) => tab?.value === activeValue.value)) {
    const fallback = getFallbackValue()
    activeValue.value = fallback
    if (fallback !== null) {
      emit('update:modelValue', fallback)
      emit('change', fallback)
    }
  }
}, { deep: true })

onMounted(() => {
  if (props.modelValue === null && activeValue.value !== null) {
    emit('update:modelValue', activeValue.value)
    emit('change', activeValue.value)
  }
})

const isPlainObject = (value) => value && typeof value === 'object' && !Array.isArray(value)

const isTabLoading = (tabValue) => {
  if (typeof props.loading === 'boolean') {
    return props.loading
  }

  if (isPlainObject(props.loading)) {
    return Boolean(props.loading[tabValue])
  }

  return false
}

const handleUpdate = (value) => {
  if (activeValue.value === value) {
    emit('change', value)
    return
  }

  activeValue.value = value
  emit('update:modelValue', value)
  emit('change', value)
}
</script>
