import { computed, onMounted } from 'vue';

/**
 * @param {object} storeInstance - инстанс Pinia-стора (например, useUserStore())
 */
export function usePageData(storeInstance) {
    const data = computed(() => storeInstance.data || []);
    const loading = computed(() => storeInstance.loading || false);

    onMounted(async () => {
        if (storeInstance?.index && typeof storeInstance.index === 'function') {
            await storeInstance.index();
        }
    });

    return { data, loading };
}