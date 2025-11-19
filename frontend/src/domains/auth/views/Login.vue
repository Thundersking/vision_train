<script>
import {useVuelidate} from '@vuelidate/core';
import {Login} from "@/domains/auth/models/Login.js";
import {useAuthStore} from "@/domains/auth/stores/auth.js";

export default {
  name: 'Login',
  data() {
    return {
      login: new Login(),
      loginStore: useAuthStore(),
      v$: null
    }
  },
  async created() {
    this.v$ = useVuelidate(Login.validationRules(), this.login);
  },
  methods: {
    async handleLogin() {
      const isValid = await this.v$.$validate();
      if (!isValid) return Promise.reject(new Error('Заполните обязательные поля'));

      const payload = this.login.toApiFormat();

      return await this.loginStore.login(payload);
    },

    onLoginSuccess() {
      console.log('onLoginSuccess')
      // Переход на главную страницу после успешного логина
      this.$router.push('/dashboard');
    }
  }
}
</script>

<template>
  <div class="bg-white/90 backdrop-blur-sm rounded-xl shadow-xl p-8 border border-blue-200/50 dark:bg-gray-900/90 dark:border-gray-700">

    <!-- Header -->
    <div class="mb-8">
      <h1 class="text-3xl font-bold text-gray-800 dark:text-white text-center">Авторизация</h1>
      <p class="text-blue-600 dark:text-blue-400 text-center mt-2">Введите почту и пароль</p>
    </div>

    <BaseForm :submit="handleLogin" @success="onLoginSuccess">

      <FormInput
          v-model="login"
          name="email"
          label="Логин"
          :validation="v$"
          placeholder="Введите почту"
      />

      <FormPassword
          v-model="login"
          label="Пароль"
          :validation="v$"
          name="password"
          toggleMask
      />

      <template #actions="{ loading }">
        <Button
            type="submit"
            label="Войти"
            :loading="loading"
            class="w-full mt-6"
            size="large"
        />
      </template>
    </BaseForm>
  </div>
</template>
