<script>
export default {
  name: 'Login',
  data() {
    return {
      form: {
        email: '',
        password: '',
        rememberMe: false
      },
      showPassword: false,
      loading: false,
      errors: {}
    }
  },
  methods: {
    async handleLogin() {
      this.errors = {};

      // Validation
      if (!this.form.email) {
        this.errors.email = 'Email is required';
        return;
      }
      if (!this.form.password) {
        this.errors.password = 'Password is required';
        return;
      }

      this.loading = true;

      try {
        // API call
        const response = await fetch('http://localhost:9527/api/api/login', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            email: this.form.email,
            password: this.form.password
          })
        });

        if (!response.ok) {
          throw new Error('Login failed');
        }

        const data = await response.json();

        // Save token
        localStorage.setItem('token', data.token);
        if (this.form.rememberMe) {
          localStorage.setItem('email', this.form.email);
        }

        // Redirect
        this.$router.push({ name: 'dashboard' });

      } catch (error) {
        this.errors.general = 'Invalid email or password';
      } finally {
        this.loading = false;
      }
    }
  }
}
</script>

<template>
  <div class="min-h-screen bg-slate-900 flex items-center justify-center p-4">
    <div class="w-full max-w-md bg-slate-800 rounded-lg shadow-2xl p-8 border border-slate-700">

      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-white text-center">Login</h1>
        <p class="text-slate-400 text-center mt-2">Enter your credentials</p>
      </div>

      <!-- Form -->
      <form @submit.prevent="handleLogin" class="space-y-5">

        <!-- Email Field -->
        <div>
          <label for="email" class="block text-sm font-medium text-slate-300 mb-2">
            <i class="pi pi-envelope mr-2"></i>Email
          </label>
          <input
              id="email"
              v-model="form.email"
              type="email"
              placeholder="your@email.com"
              class="w-full px-4 py-3 bg-slate-700 border border-slate-600 rounded-lg text-white placeholder-slate-500 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition"
              required
          />
          <span v-if="errors.email" class="text-red-500 text-sm mt-1 block">{{ errors.email }}</span>
        </div>

        <!-- Password Field -->
        <div>
          <label for="password" class="block text-sm font-medium text-slate-300 mb-2">
            <i class="pi pi-lock mr-2"></i>Password
          </label>
          <input
              id="password"
              v-model="form.password"
              :type="showPassword ? 'text' : 'password'"
              placeholder="••••••••"
              class="w-full px-4 py-3 bg-slate-700 border border-slate-600 rounded-lg text-white placeholder-slate-500 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition pr-12"
              required
          />
          <button
              type="button"
              @click="showPassword = !showPassword"
              class="absolute right-12 mt-11 text-slate-400 hover:text-slate-300"
          >
            <i :class="showPassword ? 'pi pi-eye-slash' : 'pi pi-eye'"></i>
          </button>
          <span v-if="errors.password" class="text-red-500 text-sm mt-1 block">{{ errors.password }}</span>
        </div>

        <!-- Remember Me -->
        <div class="flex items-center">
          <input
              id="remember"
              v-model="form.rememberMe"
              type="checkbox"
              class="w-4 h-4 bg-slate-700 border border-slate-600 rounded focus:ring-2 focus:ring-blue-500/20"
          />
          <label for="remember" class="ml-2 text-sm text-slate-300">Remember me</label>
        </div>

        <!-- Submit Button -->
        <button
            type="submit"
            :disabled="loading"
            class="w-full mt-6 bg-blue-600 hover:bg-blue-700 disabled:bg-slate-600 text-white font-semibold py-3 rounded-lg transition duration-200 flex items-center justify-center gap-2"
        >
          <span v-if="!loading">Login</span>
          <span v-else class="flex items-center gap-2">
            <i class="pi pi-spin pi-spinner"></i>
            Logging in...
          </span>
        </button>

        <!-- Error Message -->
        <div v-if="errors.general" class="bg-red-900/20 border border-red-700 text-red-300 rounded-lg p-3 text-sm">
          {{ errors.general }}
        </div>
      </form>

      <!-- Footer -->
      <div class="mt-6 text-center">
        <p class="text-slate-400 text-sm">
          Don't have an account?
          <router-link to="/register" class="text-blue-400 hover:text-blue-300 font-medium">
            Sign up
          </router-link>
        </p>
      </div>

    </div>
  </div>
</template>
