<template>
  <!-- Hero / Two-column -->
  <div class="lg:min-h-screen flex items-center justify-center p-6">
    <div class="grid lg:grid-cols-2 items-center gap-10 max-w-6xl max-lg:max-w-lg w-full">
      <!-- Lado esquerdo: headline e copy -->
      <div>
        <h1 class="lg:text-5xl text-4xl font-bold text-slate-900 !leading-tight">
          Faça seu Login
        </h1>
        <p class="text-[15px] mt-6 text-slate-600 leading-relaxed">
          Adentre na plataforma e descubra os mais diversos prompts,todos voltados para seus estudos e trabalhos.
        </p>

        <p class="text-[15px] mt-6 lg:mt-12 text-slate-600">
          Não tem uma conta ?
          <RouterLink
              to="/cadastro"
          class="text-blue-600 font-medium hover:underline ml-1"
          >
          Registre-se aqui
          </RouterLink>
        </p>
      </div>

      <!-- Lado direito: formulário -->
      <form class="max-w-md lg:ml-auto w-full" @submit.prevent="login" novalidate>
        <h2 class="text-slate-900 text-3xl font-semibold mb-8 flex items-center gap-2">
          Preencha os campos
        </h2>

        <!-- Caixa de erro -->
        <div
            v-if="errorMsg"
            class="mb-6 rounded-xl border-2 border-red-500 bg-red-50 text-red-700 px-4 py-3"
        >
          <i class="bi bi-exclamation-triangle-fill mr-2"></i>{{ errorMsg }}
        </div>

        <div class="space-y-6">
          <!-- Email -->
          <div>
            <label class="text-sm text-slate-900 font-medium mb-2 block">
              Email
            </label>
            <div class="relative">
              <i class="bi bi-envelope absolute left-3 top-1/2 -translate-y-1/2"></i>
              <input
                  v-model.trim="email"
                  type="email"
                  required
                  class="bg-slate-100 w-full text-sm text-slate-900 pl-10 pr-4 py-3 rounded-md outline-0 border border-gray-200 focus:border-blue-600 focus:bg-transparent"
                  placeholder="Enter Email"
                  autocomplete="email"
              />
            </div>
          </div>

          <!-- Password -->
          <div>
            <label class="text-sm text-slate-900 font-medium mb-2 block">
              Senha
            </label>
            <div class="relative">
              <i class="bi bi-lock-fill absolute left-3 top-1/2 -translate-y-1/2"></i>
              <input
                  v-model="password"
                  :type="showPwd ? 'text' : 'password'"
                  required
                  class="bg-slate-100 w-full text-sm text-slate-900 pl-10 pr-12 py-3 rounded-md outline-0 border border-gray-200 focus:border-blue-600 focus:bg-transparent"
                  placeholder="Enter Password"
                  autocomplete="current-password"
              />
              <button
                  type="button"
                  class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-600 hover:text-slate-900"
                  @click="showPwd = !showPwd"
                  aria-label="Mostrar/ocultar senha"
              >
                <i :class="showPwd ? 'bi bi-eye-slash' : 'bi bi-eye'"></i>
              </button>
            </div>
          </div>

          <!-- Remember + Forgot -->
          <div class="flex flex-wrap items-center justify-between gap-4">
            <label class="flex items-center">
              <input
                  v-model="remember"
                  type="checkbox"
                  class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-slate-300 rounded"
              />
              <span class="ml-3 block text-sm text-slate-900">Lembre-se de mim</span>
            </label>

            <RouterLink
                to="/forgot-password"
                class="text-sm text-blue-600 hover:text-blue-500 font-medium"
            >
              Esqueceu sua senha ?
            </RouterLink>
          </div>
        </div>

        <!-- Botão -->
        <div class="!mt-12">
          <button
              :disabled="isLoading"
              type="submit"
              class="w-full shadow-xl py-2.5 px-4 text-[15px] font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none cursor-pointer disabled:opacity-60 disabled:cursor-not-allowed flex items-center justify-center gap-2"
          >
            <i v-if="isLoading" class="bi bi-arrow-repeat animate-spin"></i>
            <span v-if="!isLoading">Log in</span>
            <span v-else>Signing in...</span>
          </button>
        </div>

        <!-- Divider -->
        <div class="my-6 flex items-center gap-4">
          <hr class="w-full border-slate-300" />
          <p class="text-sm text-slate-900 text-center">or</p>
          <hr class="w-full border-slate-300" />
        </div>

        <!-- Social buttons (placeholder) -->
        <div class="space-x-6 flex justify-center">
          <button type="button" class="border-0 outline-0 cursor-pointer" title="Sign in with Google">
            <i class="bi bi-google text-2xl"></i>
          </button>
          <button type="button" class="border-0 outline-0 cursor-pointer" title="Sign in with Facebook">
            <i class="bi bi-facebook text-2xl"></i>
          </button>
          <button type="button" class="border-0 outline-0 cursor-pointer" title="Sign in with Apple">
            <i class="bi bi-apple text-2xl"></i>
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import { api, ensureCsrfCookie } from '@/lib/api'

export default {
  name: 'LoginForm',
  data() {
    return {
      email: '',
      password: '',
      remember: false,
      isLoading: false,
      errorMsg: '',
      showPwd: false
    }
  },
  methods: {
    async login () {
      this.errorMsg = ''
      if (!this.email || !this.password) {
        this.errorMsg = 'Informe e-mail e senha.'
        return
      }

      this.isLoading = true
      try {
        // 1) CSRF antes do POST /login
        await ensureCsrfCookie()

        // 2) cria sessão (guard 'web')
        await api.post('/login', {
          email: this.email,
          password: this.password,
          remember: this.remember
        })

        // 3) (opcional) confirma usuário autenticado
        await api.get('/me')

        // 4) redireciona
        const redirectTo = this.$route?.query?.redirect || '/home'
        this.$router.replace(redirectTo)
      } catch (e) {
        this.errorMsg =
            e?.response?.data?.message ||
            e?.response?.data?.errors?.email?.[0] ||
            (e?.response?.status === 419
                ? 'Sessão expirada/CSRF inválido. Recarregue a página e tente novamente.'
                : 'Falha no login. Verifique suas credenciais.')
      } finally {
        this.isLoading = false
      }
    }
  }
}
</script>
