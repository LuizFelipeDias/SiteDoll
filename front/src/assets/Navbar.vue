<template>
  <nav class="bg-gray-900 text-white">
    <div class="relative px-4 py-4 md:flex md:items-center md:justify-between">
      <!-- Logo -->
      <button class="text-2xl font-bold" @click="go('Home')">Prompt</button>

      <!-- Hamburguer -->
      <button
          class="md:hidden text-white text-3xl p-2 -mr-2 absolute right-4 top-3"
          @click="open = !open"
          :aria-expanded="open.toString()"
          aria-label="Abrir/fechar menu"
      >
        <i :class="open ? 'bi bi-x-lg' : 'bi bi-list'"></i>
      </button>

      <!-- Menu -->
      <ul
          class="md:flex md:items-center gap-5 absolute left-0 top-full w-full md:w-auto bg-gray-900
                 md:static transition-transform duration-300 z-40 px-4 md:px-6 pb-4 md:pb-0"
          :class="[open ? 'translate-x-0' : '-translate-x-full md:translate-x-0']"
      >
        <li v-for="item in visibleItems" :key="item" class="my-4 md:my-0">
          <button class="text-xl hover:text-green-500 duration-300" @click="go(item)">
            {{ item }}
          </button>
        </li>
      </ul>
    </div>
  </nav>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { api } from '@/lib/api'

const router = useRouter()
const open = ref(false)

/* Itens do menu (ajuste conforme desejar) */
const authed = ref(false)
onMounted(async () => {
  try {
    await api.get('/me')     // 200 => autenticado por cookie Sanctum
    authed.value = true
  } catch {
    authed.value = false
  }
})
const visibleItems = computed(() =>
    authed.value ? ['Conteudo', 'Sobre', 'Logout'] : ['Conteudo', 'Sobre', 'Logout']
)

/* Logout (Sanctum via cookie) — igual ao layout */
async function logout () {
  try {
    await api.post('/logout')
  } catch {}
  // limpeza local opcional (se você guarda algo no front)
  localStorage.removeItem('token')
  document.cookie = 'token=; Max-Age=0; Path=/; SameSite=Lax'

  open.value = false
  router.replace({ name: 'login' })
}

/* Navegação */
async function go(item) {
  open.value = false

  switch (item) {
    case 'Home':
      router.push({ name: 'home' })
      break
    case 'Conteudo':
      router.push({ name: 'prompts' })
      break
    case 'Logout':
      await logout()
      break
  }
}
</script>
