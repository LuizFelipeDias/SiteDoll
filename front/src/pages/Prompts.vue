<template>
  <div class="flex flex-col min-h-screen overflow-x-hidden">
    <div class="flex flex-1 relative">
      <!-- Sidebar -->
      <aside
          class="bg-gray-400 text-white w-64 p-4 absolute inset-y-0 left-0 transform transition-transform duration-200 ease-in-out z-40 md:relative md:translate-x-0"
          :class="open ? 'translate-x-0' : '-translate-x-full'"
      >
        <div class="flex items-center justify-between py-2 mb-3">
          <span class="flex items-center gap-x-2">
            <i class="bi bi-boxes hover:text-green-500 duration-300"></i>
            <span class="text-2xl font-extrabold hover:text-green-500 duration-300">Lista Conteúdo</span>
          </span>
          <button @click="toggleSidebar" class="text-2xl md:hidden" v-if="open" aria-label="Fechar menu">
            <i class="bi bi-arrow-bar-left"></i>
          </button>
        </div>

        <nav>
          <h2 class="text-lg font-bold mt-4 mb-2">Localização</h2>
          <ul class="mb-4 space-y-1">
            <li>
              <!-- corrigido: sua rota 'home' é path '/' -->
              <router-link to="/home" class="hover:text-green-500">Página inicial</router-link>
            </li>
          </ul>

          <h3 class="text-base font-semibold mt-6 mb-2">Blocos</h3>
          <ul class="space-y-1">
            <li><button class="hover:text-green-500" @click="setScope('prompts')">Prompts</button></li>
            <li><button class="hover:text-green-500" @click="setScope('categorias')">Categorias</button></li>
          </ul>
        </nav>
      </aside>

      <!-- Conteúdo principal -->
      <div class="flex flex-col flex-1 min-w-0">
        <!-- Header -->
        <header
            class="bg-white shadow-md py-3 md:py-4 transition-all duration-200 ease-in-out w-full"
            :class="open ? 'pl-[calc(16rem+1rem)] md:pl-6' : 'pl-3 md:pl-6'"
        >
          <div class="max-w-7xl mx-auto px-3 md:px-6 flex items-center gap-3">
            <!-- Botão abrir (mobile) -->
            <button @click="toggleSidebar" class="text-2xl md:hidden" v-if="!open" aria-label="Abrir menu">
              <i class="bi bi-arrow-bar-right"></i>
            </button>

            <!-- Search (filtro da tabela atual) -->
            <div class="flex-1">
              <label class="relative block group w-full max-w-sm md:max-w-lg">
                <span class="sr-only">Pesquisar</span>
                <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input
                    type="search"
                    v-model="query"
                    placeholder="Buscar (filtro da tabela atual)..."
                    class="w-full pl-9 pr-3 py-2 rounded-md border border-gray-300 bg-white placeholder:text-gray-400 text-sm
                         focus:outline-none focus:ring-2 focus:ring-green-500/40 focus:border-green-500
                         transition-[box-shadow,transform] duration-200 focus:shadow-sm focus:scale-[1.01]"
                />
              </label>
            </div>

            <!-- Perfil + dropdown -->
            <div class="ml-auto relative" ref="profileWrap">
              <button
                  @click="toggleUserMenu"
                  :aria-expanded="userMenuOpen.toString()"
                  class="flex items-center gap-2 md:gap-3 pr-1 sm:pr-0"
              >
                <div class="h-8 w-8 rounded-full bg-green-100 text-green-700 flex items-center justify-center font-semibold">
                  {{ avatarInitial }}
                </div>
                <span class="hidden sm:inline font-medium text-gray-700">{{ userName || 'Usuário' }}</span>
                <i class="bi" :class="userMenuOpen ? 'bi-caret-up-fill' : 'bi-caret-down-fill'"></i>
              </button>

              <!-- Dropdown -->
              <transition name="fade-scale">
                <div
                    v-if="userMenuOpen"
                    ref="menu"
                    class="absolute right-0 top-[calc(100%+10px)] w-56 bg-white border border-gray-200 rounded-xl shadow-xl ring-1 ring-black/5 z-50"
                >
                  <div class="absolute -top-2 right-6 h-4 w-4 bg-white rotate-45 border-t border-l border-gray-200"></div>
                  <button
                      @click="logout"
                      class="w-full text-left px-4 py-2.5 text-sm hover:bg-gray-50 flex items-center gap-2"
                  >
                    <i class="bi bi-box-arrow-right"></i>
                    <span class="font-medium">Sair</span>
                  </button>
                </div>
              </transition>
            </div>
          </div>
        </header>

        <!-- Tabela dinâmica -->
        <main
            class="flex-1 p-4 transition-all duration-200 ease-in-out w-full min-w-0"
            :class="open ? 'pl-[calc(16rem+1rem)] md:pl-6' : 'pl-3 md:pl-6'"
        >
          <Content
              :scope="scope"
              :query="query"
              :focus-id="focusId"
              @change-scope="onChangeScope"
              @focused="onFocusedDone"
          />
        </main>
      </div>
    </div>

    <Footer />
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
import { useRouter } from 'vue-router'
import { api } from '@/lib/api'
import Footer from '@/assets/Footer.vue'
import Content from './Content.vue'

const router = useRouter()

/* Sidebar */
const open = ref(false)
const toggleSidebar = () => (open.value = !open.value)

/* Estado da tabela */
const scope   = ref('prompts')
const query   = ref('')
const focusId = ref(null)
function setScope(s) { scope.value = s; focusId.value = null }

/* Perfil / usuário (Sanctum por cookie) */
const userMenuOpen = ref(false)
const userName = ref('')
const userEmail = ref('')
const avatarInitial = computed(() => (userName.value?.[0] || 'U').toUpperCase())

onMounted(async () => {
  try {
      // agora: usa SÓ /me (está no seu proxy)
      const { data } = await api.get('/me')
        userName.value  = data?.nome  || ''
        userEmail.value = data?.email || ''
      } catch {
        userName.value  = ''
        userEmail.value = ''
    // opcional: redirecionar se quiser
    // router.replace({ name: 'login', query: { redirect: '/home' } })
  }

  document.addEventListener('click', onDocClick)
  document.addEventListener('keydown', onKey)
})

const profileWrap = ref(null)

function toggleUserMenu () {
  userMenuOpen.value = !userMenuOpen.value
}

/* Fecha dropdown ao clicar fora */
function onDocClick(e) {
  if (!userMenuOpen.value) return
  const root = profileWrap.value
  if (root && !root.contains(e.target)) {
    userMenuOpen.value = false
  }
}

/* Fecha com ESC */
function onKey(e) {
  if (e.key === 'Escape') userMenuOpen.value = false
}

onMounted(async () => {
  try {
    // Se tiver rota /me no backend, pode usar api.get('/me') também
    const { data } = await api.get('/me')
    userName.value = data?.nome || data?.name || ''
  } catch {
    userName.value = ''
  }

  document.addEventListener('click', onDocClick)
  document.addEventListener('keydown', onKey)
})

onBeforeUnmount(() => {
  document.removeEventListener('click', onDocClick)
  document.removeEventListener('keydown', onKey)
})

/* Logout (Sanctum via cookie) */
async function logout () {
  try {
    await api.post('/logout')
  } catch {}
  userMenuOpen.value = false
  router.replace({ name: 'login' })
}

/* Eventos vindos do Content (FK → trocar escopo + focar) */
function onChangeScope({ scope: nextScope, id }) {
  scope.value = nextScope
  focusId.value = id ?? null
}
function onFocusedDone() {
  focusId.value = null
}
</script>

<style scoped>
.fade-scale-enter-active,
.fade-scale-leave-active {
  transition: opacity .12s ease, transform .12s ease;
  transform-origin: top right;
}
.fade-scale-enter-from,
.fade-scale-leave-to {
  opacity: 0;
  transform: scale(.98) translateY(-4px);
}
</style>