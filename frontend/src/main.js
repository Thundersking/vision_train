import { createApp } from 'vue'
import { createPinia } from 'pinia'
import Lara from '@primeuix/themes/lara';
import PrimeVue from 'primevue/config';

import ToastService from 'primevue/toastservice'
import ConfirmationService from 'primevue/confirmationservice';
import Tooltip from 'primevue/tooltip'
import {definePreset} from "@primeuix/themes";

import '@/assets/styles.css'

import {
    Toast,
    InputText,
    Password, Button,
} from 'primevue';

import App from './App.vue'
import router from './core/router'
import './core/api/interceptors'
import BaseForm from "@/common/components/BaseForm.vue";
import FormInput from "@/common/components/FormInput.vue";
import FormPassword from "@/common/components/FormPassword.vue";


const MyCustomPreset = definePreset(Lara, {
    semantic: {
        colorScheme: {
            light: {
                surface: {
                    0: '#ffffff',
                    50: '{zinc.50}',
                    100: '{zinc.100}',
                    200: '{zinc.200}',
                    300: '{zinc.300}',
                    400: '{zinc.400}',
                    500: '{zinc.500}',
                    600: '{zinc.600}',
                    700: '{zinc.700}',
                    800: '{zinc.800}',
                    900: '{zinc.900}',
                    950: '{zinc.950}'
                },
                primary: {
                    0: '#ffffff',
                    50: '{sky.50}',
                    100: '{sky.100}',
                    200: '{sky.200}',
                    300: '{sky.300}',
                    400: '{sky.400}',
                    500: '{sky.500}',
                    600: '{sky.600}',
                    700: '{sky.700}',
                    800: '{sky.800}',
                    900: '{sky.900}',
                    950: '{sky.950}'
                }
            },
            dark: {
                surface: {
                    0: '#ffffff',
                    50: '{gray.50}',
                    100: '{gray.100}',
                    200: '{gray.200}',
                    300: '{gray.300}',
                    400: '{gray.400}',
                    500: '{gray.500}',
                    600: '{gray.600}',
                    700: '{gray.700}',
                    800: '{gray.800}',
                    900: '{gray.900}',
                    950: '{gray.950}'
                },
                primary: {
                    0: '#ffffff',
                    50: '{sky.50}',
                    100: '{sky.100}',
                    200: '{sky.200}',
                    300: '{sky.300}',
                    400: '{sky.400}',
                    500: '{sky.500}',
                    600: '{sky.600}',
                    700: '{sky.700}',
                    800: '{sky.800}',
                    900: '{sky.900}',
                    950: '{sky.950}'
                }
            }
        }
    }
});


const app = createApp(App)

app.use(createPinia())
app.use(router)

app.use(PrimeVue, {
    theme: {
        preset: MyCustomPreset,
        options: {
            darkModeSelector: '.dark'
        }
    },
    locale: {
        firstDayOfWeek: 1,
        dayNames: ['Воскресенье', 'Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота'],
        dayNamesShort: ['Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'],
        dayNamesMin: ['Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'],
        monthNames: ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'],
        monthNamesShort: ['Янв', 'Фев', 'Мар', 'Апр', 'Май', 'Июн', 'Июл', 'Авг', 'Сен', 'Окт', 'Ноя', 'Дек'],
        today: 'Сегодня',
        weekHeader: 'Нед',
        apply: 'Применить',
        clear: 'Сбросить',
        startsWith: 'Начинается с',
        contains: 'Содержит',
        notContains: 'Не содержит',
        endsWith: 'Заканчивается на',
        equals: 'Равно',
        notEquals: 'Не равно',
        accept: 'Да',
        reject: 'Нет',
        // Переводы для пустых состояний
        emptySearchMessage: 'Результаты не найдены',
        emptyMessage: 'Нет доступных вариантов',
        emptySelectionMessage: 'Элемент не выбран',
        emptyFilterMessage: 'Результаты не найдены'
    }
});

app.directive('tooltip', Tooltip)
app.use(ToastService)
app.use(ConfirmationService)

app.component('BaseForm', BaseForm);
app.component('FormInput', FormInput);
app.component('FormPassword', FormPassword);
app.component('Button', Button);
app.component('Password', Password);
app.component('InputText', InputText);
app.component('Toast', Toast);
app.mount('#app')
