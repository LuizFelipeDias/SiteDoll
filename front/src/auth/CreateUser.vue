<template>
  <!-- Tela cheia -->
  <div class="min-h-screen w-full bg-gray-900 lg:overflow-hidden">
    <div class="mx-auto max-w-7xl px-4 md:px-6">
      <div class="grid grid-cols-12 items-center gap-6 py-8 lg:py-6 lg:h-screen">
        <!-- Esquerda -->
        <div class="col-span-12 lg:col-span-7 xl:col-span-8">
          <div class="rounded-2xl bg-transparent shadow-md xl:pl-24">
            <div class="lg:max-w-xl">
              <h2 class="text-white font-semibold leading-tight mb-3 text-3xl lg:text-4xl">
                Criar conta
              </h2>
              <p class="text-white/70 text-lg lg:text-xl leading-relaxed mb-6">
                Cadastre-se para acessar recursos exclusivos e receber novidades.
              </p>
              <p class="text-white/70 text-lg lg:text-xl leading-relaxed">
                Universidade Estadual de Ponta Grossa, Ponta Grossa, Brasil
              </p>

              <div class="grid gap-4 my-8">
                <div class="flex items-center gap-3">
                  <i class="bi bi-telephone-fill text-white text-2xl"></i>
                  <p class="text-white font-bold">(42) 99782-8982</p>
                </div>
                <div class="flex items-center gap-3">
                  <i class="bi bi-envelope-fill text-white text-2xl"></i>
                  <p class="text-white font-bold">prompt@uepg2025.com</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Direita: formulário -->
        <div class="col-span-12 lg:col-span-5 xl:col-span-4">
          <div class="rounded-2xl shadow-md bg-transparent text-gray-700 w-full px-5 md:px-8 lg:px-8 xl:px-10 py-6 lg:py-6 lg:max-h-[86vh]">
            <!-- feedback -->
            <p v-if="success" class="mb-3 rounded-xl border-2 border-emerald-400/80 bg-emerald-500/10 px-4 py-2.5 text-emerald-200">
              Usuário criado com sucesso! Redirecionando para o login…
            </p>
            <p v-if="error" class="mb-3 rounded-xl border-2 border-red-400/80 bg-red-500/10 px-4 py-2.5 text-red-200 whitespace-pre-line">
              {{ error }}
            </p>

            <form @submit.prevent="submit" class="space-y-4">
              <!-- Nome | E-mail -->
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium mb-1 text-white">Nome</label>
                  <input v-model.trim="form.nome" type="text"
                         class="w-full bg-transparent text-white placeholder-white/60 border border-white focus:border-2 focus:outline-none rounded-md px-3 py-3"
                         placeholder="Seu nome" required />
                </div>
                <div>
                  <label class="block text-sm font-medium mb-1 text-white">E-mail</label>
                  <input v-model.trim="form.email" type="email"
                         class="w-full bg-transparent text-white placeholder-white/60 border border-white focus:border-2 focus:outline-none rounded-md px-3 py-3"
                         placeholder="seu@email.com" required />
                </div>
              </div>

              <!-- CPF | Telefone -->
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium mb-1 text-white">CPF</label>
                  <input v-model="form.cpf" @input="maskCPF" inputmode="numeric"
                         class="w-full bg-transparent text-white placeholder-white/60 border border-white focus:border-2 focus:outline-none rounded-md px-3 py-3"
                         placeholder="000.000.000-00" required />
                </div>
                <div>
                  <label class="block text-sm font-medium mb-1 text-white">Telefone</label>
                  <input v-model="form.telefone" @input="maskPhone" inputmode="numeric"
                         class="w-full bg-transparent text-white placeholder-white/60 border border-white focus:border-2 focus:outline-none rounded-md px-3 py-3"
                         placeholder="(00) 00000-0000" />
                </div>
              </div>

              <!-- Senha | Confirmar senha -->
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium mb-1 text-white">Senha</label>
                  <input v-model="form.password" type="password"
                         class="w-full bg-transparent text-white placeholder-white/60 border border-white focus:border-2 focus:outline-none rounded-md px-3 py-3"
                         placeholder="••••••••" required />
                </div>
                <div>
                  <label class="block text-sm font-medium mb-1 text-white">Confirmar senha</label>
                  <input v-model="form.password_confirmation" type="password"
                         class="w-full bg-transparent text-white placeholder-white/60 border border-white focus:border-2 focus:outline-none rounded-md px-3 py-3"
                         placeholder="••••••••" required />
                </div>
              </div>

              <!-- Aceite -->
              <div class="flex items-start gap-3 pt-1">
                <input id="agree" type="checkbox"
                       class="peer relative appearance-none w-5 h-5 border rounded-md border-white cursor-pointer transition-all checked:bg-gray-900 checked:border-gray-900" />
                <label for="agree" class="text-gray-300 font-light select-none cursor-pointer">
                  Você concorda com a
                  <a href="#" class="font-medium text-white hover:underline">Política de Privacidade</a>.
                </label>
              </div>

              <!-- Botão -->
              <button type="submit" :disabled="loading"
                      class="w-full mt-2 align-middle select-none font-sans font-bold text-center uppercase transition-all disabled:opacity-50 text-sm py-3 rounded-lg bg-white text-gray-900 shadow-md hover:shadow-lg">
                <span v-if="!loading">Criar conta</span>
                <span v-else>Criando…</span>
              </button>

              <p class="text-sm text-gray-300">
                Já tem conta?
                <router-link to="/login" class="underline text-white">Fazer login</router-link>
              </p>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { useRouter } from 'vue-router'
import { api } from '@/lib/api'

const router = useRouter()
const loading = ref(false)
const success = ref(false)
const error = ref('')

const form = reactive({
  nome: '',
  email: '',
  cpf: '',
  telefone: '',
  password: '',
  password_confirmation: ''
})

function maskCPF () {
  let v = form.cpf?.toString().replace(/\D/g, '').slice(0, 11) || ''
  v = v.replace(/(\d{3})(\d)/, '$1.$2')
  v = v.replace(/(\d{3})(\d)/, '$1.$2')
  v = v.replace(/(\d{3})(\d{1,2})$/, '$1-$2')
  form.cpf = v
}

function maskPhone () {
  let v = form.telefone?.toString().replace(/\D/g, '').slice(0, 11) || ''
  v = v.length > 10
      ? v.replace(/^(\d{2})(\d{5})(\d{4}).*/, '($1) $2-$3')
      : v.replace(/^(\d{2})(\d{4})(\d{0,4}).*/, '($1) $2-$3')
  form.telefone = v
}

function flattenLaravelErrors (errors) {
  if (!errors) return ''
  try { return Object.values(errors).map(arr => arr[0]).join('\n') } catch { return '' }
}

async function submit () {
  error.value = ''
  success.value = false

  if (form.password !== form.password_confirmation) {
    error.value = 'As senhas não conferem.'
    return
  }

  loading.value = true
  try {
    const csrf = await api.get('/sanctum/csrf-cookie')
    console.log('[signup] /sanctum/csrf-cookie status:', csrf.status)

    // ENVIA COM MÁSCARA (CPF/telefone exatamente como no input)
    const res = await api.post('/api/cadastro', {
      nome: form.nome,
      email: form.email,
      cpf: form.cpf,           // << com pontos e hífen
      telefone: form.telefone, // << com máscara opcional
      password: form.password,
    }, {
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
    })

    console.log('[signup] response:', res.status, res.data)

    success.value = true
    Object.assign(form, { nome:'', email:'', cpf:'', telefone:'', password:'', password_confirmation:'' })
    setTimeout(() => router.replace({ name: 'login' }), 500)
  } catch (e) {
    console.error('[signup] error status:', e?.response?.status)
    console.error('[signup] error data:', e?.response?.data)
    error.value =
        e?.response?.data?.message ||
        flattenLaravelErrors(e?.response?.data?.errors) ||
        (e?.response?.status === 419
            ? 'Sessão/CSRF inválido. Recarregue e tente novamente.'
            : 'Falha ao criar usuário.')
  } finally {
    loading.value = false
  }
}
</script>
