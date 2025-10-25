<template>
  <div class="w-full">
    <!-- cabeçalho -->
    <div class="mb-3 flex items-center justify-center">
      <h2 class="text-xl font-semibold">
        {{ labelByScope[props.scope] || 'Lista' }}
      </h2>
    </div>

    <!-- SCROLL HORIZONTAL -->
    <div class="overflow-x-auto">
      <table class="min-w-full table-fixed">
        <thead>
        <tr class="bg-white">
          <th
              v-for="col in visibleColumns"
              :key="col.key"
              class="px-6 py-4 text-left text-sm font-medium text-gray-500"
          >
            {{ col.label }}
          </th>
        </tr>
        </thead>

        <tbody class="divide-y divide-gray-200">
        <!-- loading skeleton -->
        <tr v-if="loading">
          <td :colspan="visibleColumns.length" class="px-6 py-6">
            <div class="space-y-2">
              <div class="animate-pulse h-4 w-1/3 bg-gray-200 rounded"></div>
              <div class="animate-pulse h-4 w-1/2 bg-gray-200 rounded"></div>
            </div>
          </td>
        </tr>

        <!-- vazio -->
        <tr v-else-if="!filteredRows.length">
          <td :colspan="visibleColumns.length" class="px-6 py-8 text-sm text-gray-500">
            Nenhum registro encontrado.
          </td>
        </tr>

        <!-- linhas -->
        <tr
            v-else
            v-for="(row, idx) in filteredRows"
            :key="row.__key || row.id || idx"
            class="hover:bg-gray-50 transition-colors"
        >
          <td
              v-for="col in visibleColumns"
              :key="col.key"
              class="px-6 py-4 text-sm text-gray-900 align-top break-words"
          >
            <!-- FKs como botão/link -->
            <button
                v-if="isForeignKey(col.key, props.scope)"
                class="inline-flex items-center gap-1 text-indigo-600 hover:underline disabled:text-gray-400"
                @click="goToRelated(col.key, row[col.key])"
                :disabled="!row[col.key]"
                type="button"
            >
              <i class="bi bi-arrow-right-square"></i>
              <span>{{ formatForeign(col.key, row) }}</span>
            </button>

            <!-- Renderização especial -->
            <template v-else>
              <template v-if="props.scope === 'prompts' && col.key === 'categoria_id'">
                {{ row.categoria?.nome ? `${row.categoria.nome} (${row.categoria_id})` : formatCell(row[col.key]) }}
              </template>
              <template v-else-if="props.scope === 'prompts' && col.key === 'tipo_id'">
                {{ row.tipo?.nome ? `${row.tipo.nome} (${row.tipo_id})` : formatCell(row[col.key]) }}
              </template>
              <template v-else-if="props.scope === 'categorias' && col.key === 'parent_id'">
                {{ row.parent?.nome ? `${row.parent.nome} (${row.parent_id})` : formatCell(row[col.key]) }}
              </template>
              <template v-else>
                {{ formatCell(row[col.key]) }}
              </template>
            </template>
          </td>
        </tr>
        </tbody>
      </table>
    </div>

    <!-- paginação (15/página) -->
    <div class="flex items-center justify-between px-4 py-3">
      <div class="text-sm text-gray-600">
        Página {{ page }} de {{ totalPages }} • {{ total }} registros
      </div>
      <div class="flex items-center gap-2">
        <button
            type="button"
            class="px-3 py-1 border rounded disabled:opacity-50"
            :disabled="page <= 1 || loading"
            @click="changePage(page - 1)"
        >
          ‹ Anterior
        </button>
        <button
            type="button"
            class="px-3 py-1 border rounded disabled:opacity-50"
            :disabled="page >= totalPages || loading"
            @click="changePage(page + 1)"
        >
          Próxima ›
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { api } from '@/lib/api'

const PER_PAGE = 15

const props = defineProps({
  scope:   { type: String, required: true },
  query:   { type: String, default: '' },
  focusId: { type: [Number, String, null], default: null },
})
const emit = defineEmits(['change-scope', 'focused'])

const loading = ref(false)
const rows = ref([])

const page = ref(1)
const totalPages = ref(1)
const total = ref(0)

/** Rótulos */
const labelByScope = {
  'prompts': 'Prompts',
  'categorias': 'Categorias',
  'tipos-prompt': 'Tipos de Prompt',
}

/** Colunas por escopo */
const columnsByScope = {
  prompts: [
    { key: 'titulo',       label: 'Título' },
    { key: 'descricao',    label: 'Descrição' },
    { key: 'corpo',        label: 'Corpo (Prompt)' },
    { key: 'categoria_id', label: 'Categoria' },
    { key: 'tipo_id',      label: 'Tipo' },
  ],
  categorias: [
    { key: 'nome',      label: 'Subcategoria' },
    { key: 'parent_id', label: 'Categoria (Pai)' },
  ],
  'tipos-prompt': [
    { key: 'nome', label: 'Tipo' },
  ],
}
const visibleColumns = computed(() => columnsByScope[props.scope] || [])

/** Endpoints */
const endpoints = {
  prompts:        { list: '/api/prompts',    one: (id) => `/api/prompts/${id}` },
  categorias:     { list: '/api/categorias', one: (id) => `/api/categorias/${id}` },
  'tipos-prompt': { list: '/api/tipos',      one: (id) => `/api/tipos/${id}` },
}

/** Filtro client-side */
const filteredRows = computed(() => {
  const q = (props.query || '').trim().toLowerCase()
  if (!q) return rows.value
  const keys = visibleColumns.value.map(c => c.key)
  return rows.value.filter(r => keys.some(k => String(r?.[k] ?? '').toLowerCase().includes(q)))
})

watch(() => props.scope, () => { page.value = 1; fetchList() }, { immediate: true })

watch(() => props.focusId, async (val) => {
  if (val == null) return
  await focusOne(props.scope, val)
  emit('focused')
})

async function fetchList () {
  loading.value = true
  rows.value = []
  try {
    const base = endpoints[props.scope]?.list
    if (!base) { loading.value = false; return }

    const { data } = await api.get(base, { params: { page: page.value, per_page: PER_PAGE } })

    const arr =
        (Array.isArray(data) && data) ||
        data?.data ||
        data?.items ||
        data?.results ||
        []

    rows.value = arr.map((r, i) => ({ __key: r.id ?? `${props.scope}-${i}`, ...r }))

    const meta = data?.meta
    if (meta) {
      page.value = meta.current_page ?? page.value
      totalPages.value = meta.last_page ?? 1
      total.value = meta.total ?? rows.value.length
    } else {
      total.value = data?.total ?? rows.value.length
      totalPages.value = Math.max(1, Math.ceil(total.value / PER_PAGE))
    }
  } catch (e) {
    console.error('fetchList error:', e)
    rows.value = []
    total.value = 0
    totalPages.value = 1
  } finally {
    loading.value = false
  }
}

async function focusOne (scope, id) {
  const ep = endpoints[scope]
  if (!ep?.one) return
  loading.value = true
  try {
    const { data } = await api.get(ep.one(id))
    const row = data?.id ? data : (data?.data?.id ? data.data : data)
    rows.value = row ? [{ __key: row.id ?? `${scope}-${id}`, ...row }] : []
    page.value = 1; totalPages.value = 1; total.value = rows.value.length
  } catch (e) {
    console.error('focusOne error:', e)
    rows.value = []; total.value = 0; totalPages.value = 1
  } finally {
    loading.value = false
  }
}

function changePage (p) {
  if (p < 1 || p > totalPages.value) return
  page.value = p
  fetchList()
}

/** FKs navegáveis */
function isForeignKey (key, scope) {
  if (scope === 'prompts')     return ['categoria_id','tipo_id'].includes(key)
  if (scope === 'categorias')  return ['parent_id'].includes(key)
  return false
}
function formatForeign (key, row) {
  if (key === 'categoria_id') return row.categoria?.nome ?? row.categoria_id ?? '-'
  if (key === 'tipo_id')      return row.tipo?.nome ?? row.tipo_id ?? '-'
  if (key === 'parent_id')    return row.parent?.nome ?? row.parent_id ?? '-'
  return row[key] ?? '-'
}
function goToRelated (key, id) {
  if (!id) return
  if (props.scope === 'prompts') {
    if (key === 'categoria_id') return emit('change-scope', { scope: 'categorias',   id })
    if (key === 'tipo_id')      return emit('change-scope', { scope: 'tipos-prompt', id })
  }
  if (props.scope === 'categorias' && key === 'parent_id') {
    return emit('change-scope', { scope: 'categorias', id })
  }
}

/** formatação simples */
function formatCell (v) {
  if (v == null || v === '') return '-'
  if (Array.isArray(v)) return v.join(', ')
  if (typeof v === 'object') return JSON.stringify(v)
  return String(v)
}
</script>
