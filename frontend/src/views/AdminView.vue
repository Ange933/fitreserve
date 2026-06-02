<template>
  <div class="page-container max-w-7xl mx-auto">

    <!-- Header -->
    <div class="mb-8 animate-fade-in">
      <p class="label-tag text-orange-500 mb-2">Espace Coach</p>
      <h1 class="section-title text-white">Tableau de bord</h1>
      <p class="text-zinc-500 mt-2">Bienvenue, Marc. Voici votre planning et vos clients.</p>
    </div>
    <button @click="refreshAll" class="btn-ghost text-xs py-2 px-4 mb-8" :disabled="loadingSeances || loadingRes">
      <svg class="w-3.5 h-3.5" :class="(loadingSeances || loadingRes) ? 'animate-spin' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
      </svg>
      Rafraîchir
    </button>

    <!-- Stats -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-8">
      <div v-for="stat in stats" :key="stat.label" class="glass-card p-5 border-t-2 border-orange-500">
        <p class="font-display font-black text-3xl text-white">{{ stat.value }}</p>
        <p class="label-tag mt-1">{{ stat.label }}</p>
      </div>
    </div>

    <!-- Tabs -->
    <div class="flex gap-1 p-1 rounded-sm mb-8 w-fit bg-dark-800 border border-dark-600">
      <button
        v-for="tab in tabs"
        :key="tab.id"
        @click="activeTab = tab.id"
        class="px-5 py-2.5 text-xs font-bold uppercase tracking-widest transition-all duration-200"
        :class="activeTab === tab.id
          ? 'bg-orange-500 text-white'
          : 'text-zinc-500 hover:text-white'"
      >
        {{ tab.label }}
      </button>
    </div>

    <!-- ═══ TAB : PLANNING ═══ -->
    <div v-if="activeTab === 'planning'">

      <div class="flex flex-wrap items-center gap-3 mb-6">
        <p class="label-tag flex-1">{{ allReservations.filter(r => r.statut !== 'annulee').length }} réservations actives</p>
        <button @click="showVacationModal = true" class="btn-ghost text-xs py-2 px-4">Période de congé</button>
        <button @click="openCreate()" class="btn-primary text-xs py-2 px-4">+ Nouveau créneau</button>
      </div>

      <div v-if="loadingRes" class="space-y-2">
        <div v-for="i in 5" :key="i" class="skeleton h-16"></div>
      </div>

      <!-- Réservations groupées par jour -->
      <div v-else class="space-y-8">

        <!-- Réservations confirmées -->
        <div>
          <p class="label-tag text-orange-500 mb-3">Créneaux réservés</p>
          <div v-if="activeReservations.length === 0" class="glass-card p-6 text-center text-zinc-600 text-sm">
            Aucune réservation active
          </div>
          <div v-else class="space-y-6">
            <div v-for="(group, date) in groupedReservations" :key="date">
              <p class="label-tag text-zinc-500 border-b border-dark-700 pb-2 mb-2">{{ formatDayLabel(date) }}</p>
              <div class="space-y-1">
                <div
                  v-for="res in group"
                  :key="res.id"
                  class="flex items-center gap-4 p-4 bg-dark-800 border border-dark-600 hover:border-orange-500/30 transition-colors"
                >
                  <!-- Heure -->
                  <div class="w-14 text-center flex-shrink-0">
                    <p class="font-display font-black text-orange-500 text-lg">{{ formatTime(res.seance.dateHeure) }}</p>
                  </div>
                  <div class="w-px h-10 bg-dark-600 flex-shrink-0"></div>
                  <!-- Client -->
                  <div class="flex items-center gap-3 flex-1 min-w-0">
                    <div class="w-8 h-8 bg-orange-500/20 border border-orange-500/30 flex items-center justify-center flex-shrink-0 font-bold text-orange-500 text-xs">
                      {{ res.user.prenom[0] }}{{ res.user.nom[0] }}
                    </div>
                    <div>
                      <p class="font-bold text-white text-sm">{{ res.user.prenom }} {{ res.user.nom }}</p>
                      <p class="text-zinc-500 text-xs">{{ res.user.email }}</p>
                    </div>
                  </div>
                  <!-- Durée -->
                  <p class="text-zinc-600 text-xs hidden md:block">{{ res.seance.duree }} min</p>
                  <!-- Badge -->
                  <span class="badge-green badge text-xs flex-shrink-0">Confirmé</span>
                  <!-- Annuler -->
                  <button @click="cancelAdminRes(res.id)" class="text-xs text-red-600 hover:text-red-400 transition-colors border border-dark-700 hover:border-red-600 px-2 py-1 flex-shrink-0">
                    Annuler
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Créneaux libres -->
        <div>
          <p class="label-tag text-zinc-600 mb-3">Créneaux libres</p>
          <div class="space-y-1">
            <div
              v-for="seance in freeSeances"
              :key="seance.id"
              class="flex items-center gap-4 p-3 border transition-all duration-200 cursor-pointer"
              :class="seance.disponible ? 'bg-dark-800/50 border-dark-700 hover:border-orange-500/30' : 'bg-red-950/20 border-red-900/40'"
              @click="handleToggle(seance)"
              :title="seance.disponible ? 'Cliquer pour bloquer' : 'Cliquer pour débloquer'"
            >
              <div class="w-14 text-center flex-shrink-0">
                <p class="text-zinc-500 text-sm font-bold">{{ formatTime(seance.dateHeure) }}</p>
                <p class="text-zinc-700 text-xs">{{ formatShortDate(seance.dateHeure) }}</p>
              </div>
              <div class="flex-1 flex items-center gap-2">
                <div class="w-2 h-2 rounded-full flex-shrink-0" :class="seance.disponible ? 'bg-zinc-600' : 'bg-red-500'"></div>
                <span v-if="!seance.disponible" class="text-red-400 text-xs font-bold uppercase tracking-wider">Indisponible</span>
                <span v-else class="text-zinc-600 text-sm italic">Libre — cliquer pour bloquer</span>
              </div>
              <div class="flex gap-1" @click.stop>
                <button @click="openEdit(seance)" class="text-xs text-zinc-600 hover:text-white transition-colors border border-dark-700 hover:border-dark-500 px-2 py-1">Modifier</button>
                <button @click="confirmDelete(seance.id)" class="text-xs text-red-800 hover:text-red-500 transition-colors border border-dark-700 hover:border-red-800 px-2 py-1">Supprimer</button>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>

    <!-- ═══ TAB : CLIENTS ═══ -->
    <div v-if="activeTab === 'clients'">
      <div v-if="loadingRes" class="space-y-2">
        <div v-for="i in 4" :key="i" class="skeleton h-20"></div>
      </div>

      <div v-else>
        <!-- Search -->
        <input
          v-model="clientSearch"
          type="search"
          placeholder="Rechercher un client..."
          class="input-field max-w-sm text-sm mb-6"
          aria-label="Rechercher un client"
        />

        <div class="space-y-3">
          <div
            v-for="client in filteredClients"
            :key="client.id"
            class="glass-card p-5 cursor-pointer hover:border-orange-500/40 transition-all"
            @click="selectedClient = selectedClient?.id === client.id ? null : client"
          >
            <div class="flex items-center justify-between gap-4">
              <div class="flex items-center gap-4">
                <!-- Avatar initiales -->
                <div class="w-10 h-10 bg-orange-500/20 border border-orange-500/30 flex items-center justify-center flex-shrink-0 font-display font-black text-orange-500 text-sm">
                  {{ client.prenom[0] }}{{ client.nom[0] }}
                </div>
                <div>
                  <p class="font-bold text-white">{{ client.prenom }} {{ client.nom }}</p>
                  <p class="text-zinc-500 text-xs">{{ client.email }}</p>
                  <p class="text-xs font-medium" :class="client.telephone ? 'text-orange-500/80' : 'text-zinc-700 italic'">
                    {{ client.telephone || 'Téléphone non renseigné' }}
                  </p>
                  <p class="text-zinc-700 text-xs">Inscrit le {{ client.createdAt }}</p>
                </div>
              </div>
              <div class="flex items-center gap-4">
                <div class="text-right">
                  <p class="font-bold text-white text-sm">{{ clientReservationCount(client.id) }}</p>
                  <p class="label-tag">séances</p>
                </div>
                <svg class="w-4 h-4 text-zinc-600 transition-transform" :class="selectedClient?.id === client.id ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
              </div>
            </div>

            <!-- Détail client -->
            <transition name="fade">
              <div v-if="selectedClient?.id === client.id" class="mt-4 pt-4 border-t border-dark-600">
                <p class="label-tag mb-3">Historique des réservations</p>
                <div v-if="clientReservations(client.id).length === 0" class="text-zinc-600 text-sm italic">
                  Aucune réservation
                </div>
                <div v-else class="space-y-2">
                  <div
                    v-for="res in clientReservations(client.id)"
                    :key="res.id"
                    class="flex items-center justify-between text-sm py-2 border-b border-dark-700"
                  >
                    <div class="flex items-center gap-3">
                      <p class="text-white font-medium">{{ formatDateTime(res.seance.dateHeure) }}</p>
                      <p class="text-zinc-500">{{ res.seance.lieu }}</p>
                    </div>
                    <div class="flex items-center gap-3">
                      <span :class="statusBadge(res.statut).class" class="badge text-xs">{{ statusBadge(res.statut).label }}</span>
                      <button
                        v-if="res.statut !== 'annulee'"
                        @click.stop="cancelAdminRes(res.id)"
                        class="text-xs text-red-600 hover:text-red-400 transition-colors"
                      >
                        Annuler
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </transition>
          </div>
        </div>

        <p v-if="filteredClients.length === 0" class="glass-card p-10 text-center text-zinc-500 text-sm">
          Aucun client trouvé
        </p>
      </div>
    </div>

    <!-- Modal créer/modifier séance -->
    <Modal v-model="showModal" :title="editingSeance ? 'Modifier le créneau' : 'Nouveau créneau'">
      <form @submit.prevent="handleSave" class="space-y-4" novalidate>
        <div class="space-y-1.5">
          <label for="titre" class="label-tag">Titre</label>
          <input id="titre" v-model="form.titre" type="text" class="input-field" placeholder="Séance coaching" required />
        </div>
        <div class="grid grid-cols-2 gap-4">
          <div class="space-y-1.5">
            <label for="dateHeure" class="label-tag">Date & heure</label>
            <input id="dateHeure" v-model="form.dateHeure" type="datetime-local" class="input-field" required />
          </div>
          <div class="space-y-1.5">
            <label for="duree" class="label-tag">Durée (min)</label>
            <input id="duree" v-model.number="form.duree" type="number" min="30" step="15" class="input-field" />
          </div>
        </div>
        <div class="space-y-1.5">
          <label for="lieu" class="label-tag">Lieu</label>
          <input id="lieu" v-model="form.lieu" type="text" class="input-field" placeholder="Salle de sport Paris 8e" />
        </div>
        <p v-if="formError" class="text-red-400 text-xs border-l-2 border-red-500 pl-3">{{ formError }}</p>
        <div class="flex gap-3 pt-2">
          <button type="submit" class="btn-primary flex-1 py-3" :disabled="saving">
            <LoadingSpinner v-if="saving" size="sm" />
            <span>{{ editingSeance ? 'Enregistrer' : 'Créer' }}</span>
          </button>
          <button type="button" @click="showModal = false" class="btn-ghost flex-1 py-3">Annuler</button>
        </div>
      </form>
    </Modal>

    <!-- Modal vacances -->
    <Modal v-model="showVacationModal" title="Bloquer une période">
      <div class="space-y-5">
        <p class="text-zinc-500 text-sm">Tous les créneaux libres dans la période seront marqués <strong class="text-white">Indisponible</strong>. Les clients ne pourront plus les réserver. Vous pouvez les débloquer à tout moment.</p>
        <div class="grid grid-cols-2 gap-4">
          <div class="space-y-1.5">
            <label for="vacation-from" class="label-tag">Du</label>
            <input id="vacation-from" v-model="vacation.from" type="date" class="input-field text-sm" />
          </div>
          <div class="space-y-1.5">
            <label for="vacation-to" class="label-tag">Au</label>
            <input id="vacation-to" v-model="vacation.to" type="date" class="input-field text-sm" />
          </div>
        </div>
        <div v-if="vacationPreview > 0" class="p-3 border-l-2 border-orange-500 bg-orange-500/5 text-sm text-orange-300">
          {{ vacationPreview }} créneau{{ vacationPreview > 1 ? 'x' : '' }} libre{{ vacationPreview > 1 ? 's' : '' }} sera{{ vacationPreview > 1 ? 'ont' : '' }} supprimé{{ vacationPreview > 1 ? 's' : '' }}.
        </div>
        <p v-if="vacation.from && vacation.to && vacationPreview === 0" class="text-zinc-600 text-sm">Aucun créneau libre dans cette période.</p>
        <div class="flex gap-3 pt-2">
          <button @click="applyVacation" :disabled="!vacation.from || !vacation.to || vacationPreview === 0 || saving" class="btn-danger flex-1 py-3">
            <LoadingSpinner v-if="saving" size="sm" />
            Supprimer {{ vacationPreview > 0 ? vacationPreview + ' créneau' + (vacationPreview > 1 ? 'x' : '') : '' }}
          </button>
          <button @click="showVacationModal = false" class="btn-ghost flex-1 py-3">Annuler</button>
        </div>
      </div>
    </Modal>

    <!-- Modal confirm delete -->
    <Modal v-model="showDeleteModal" title="Supprimer ce créneau ?">
      <div class="space-y-4 text-center">
        <p class="text-zinc-400 text-sm">Cette action est irréversible.</p>
        <div class="flex gap-3">
          <button @click="executeDelete" class="btn-danger flex-1 py-3" :disabled="saving">
            <LoadingSpinner v-if="saving" size="sm" />
            Supprimer
          </button>
          <button @click="showDeleteModal = false" class="btn-ghost flex-1 py-3">Annuler</button>
        </div>
      </div>
    </Modal>

  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { format } from 'date-fns'
import { fr } from 'date-fns/locale'
import { useSeancesStore } from '@/stores/seances'
import { reservationsService, reservationsService as resApi } from '@/services/api'
import api from '@/services/api'
import { useToast } from '@/composables/useToast'
import Modal from '@/components/Modal.vue'
import LoadingSpinner from '@/components/LoadingSpinner.vue'

const seancesStore = useSeancesStore()
const toast = useToast()

const activeTab = ref('planning')
const loadingSeances = ref(false)
const loadingRes = ref(false)
const allReservations = ref([])
const allClients = ref([])
const showModal = ref(false)
const showDeleteModal = ref(false)
const showVacationModal = ref(false)
const vacation = reactive({ from: '', to: '' })
const editingSeance = ref(null)
const deletingSeanceId = ref(null)
const saving = ref(false)
const formError = ref('')
const selectedClient = ref(null)
const clientSearch = ref('')

const form = reactive({ titre: 'Séance coaching', description: '', dateHeure: '', duree: 60, placesMax: 1, lieu: 'Salle de sport Paris 8e' })

// ── Stats ──────────────────────────────────────────────────
const stats = computed(() => {
  const today = new Date().toDateString()
  return [
    {
      value: seancesStore.seances.length,
      label: 'Créneaux à venir',
    },
    {
      value: seancesStore.seances.filter(s => new Date(s.dateHeure).toDateString() === today).length,
      label: 'Séances aujourd\'hui',
    },
    {
      value: allReservations.value.filter(r => r.statut !== 'annulee').length,
      label: 'Réservations actives',
    },
    {
      value: allClients.value.length,
      label: 'Clients',
    },
  ]
})

// ── Tabs ───────────────────────────────────────────────────
const tabs = [
  { id: 'planning', label: 'Planning' },
  { id: 'clients', label: 'Clients' },
]

// ── Réservations actives groupées par jour ─────────────────
const activeReservations = computed(() =>
  allReservations.value
    .filter(r => r.statut !== 'annulee')
    .sort((a, b) => new Date(a.seance.dateHeure) - new Date(b.seance.dateHeure))
)

const groupedReservations = computed(() => {
  const groups = {}
  activeReservations.value.forEach(r => {
    const key = format(new Date(r.seance.dateHeure), 'yyyy-MM-dd')
    if (!groups[key]) groups[key] = []
    groups[key].push(r)
  })
  return groups
})

// ── Créneaux libres (non réservés) ────────────────────────
const bookedSeanceIds = computed(() =>
  new Set(activeReservations.value.map(r => r.seance.id))
)

const freeSeances = computed(() =>
  seancesStore.seances.filter(s => !bookedSeanceIds.value.has(s.id))
)

// ── Vacances ───────────────────────────────────────────────
const vacationPreview = computed(() => {
  if (!vacation.from || !vacation.to) return 0
  const from = new Date(vacation.from + 'T00:00:00')
  const to = new Date(vacation.to + 'T23:59:59')
  return freeSeances.value.filter(s => {
    const d = new Date(s.dateHeure)
    return d >= from && d <= to
  }).length
})

async function applyVacation() {
  if (!vacation.from || !vacation.to || vacationPreview.value === 0) return
  saving.value = true
  const from = new Date(vacation.from + 'T00:00:00')
  const to = new Date(vacation.to + 'T23:59:59')
  const toBlock = freeSeances.value.filter(s => {
    const d = new Date(s.dateHeure)
    return d >= from && d <= to && s.disponible
  })
  try {
    await Promise.all(toBlock.map(s => seancesStore.toggleDisponibilite(s.id)))
    toast.success(`${toBlock.length} créneau${toBlock.length > 1 ? 'x bloqués' : ' bloqué'}. Bon repos !`)
    showVacationModal.value = false
    vacation.from = ''
    vacation.to = ''
  } catch {
    toast.error('Erreur lors du blocage.')
  } finally {
    saving.value = false
  }
}

// ── Clients ────────────────────────────────────────────────
const filteredClients = computed(() =>
  allClients.value.filter(c => {
    const q = clientSearch.value.toLowerCase()
    return !q || c.nom?.toLowerCase().includes(q) || c.prenom?.toLowerCase().includes(q) || c.email?.toLowerCase().includes(q)
  })
)

function clientReservationCount(userId) {
  return allReservations.value.filter(r => r.user?.id === userId && r.statut !== 'annulee').length
}

function clientReservations(userId) {
  return allReservations.value
    .filter(r => r.user?.id === userId)
    .sort((a, b) => new Date(b.seance.dateHeure) - new Date(a.seance.dateHeure))
}

function getClientForSeance(seanceId) {
  const res = allReservations.value.find(r => r.seance.id === seanceId && r.statut !== 'annulee')
  return res?.user ?? null
}

// ── CRUD séances ───────────────────────────────────────────
function openCreate() {
  editingSeance.value = null
  Object.assign(form, { titre: 'Séance coaching', description: '', dateHeure: '', duree: 60, placesMax: 1, lieu: 'Salle de sport Paris 8e' })
  formError.value = ''
  showModal.value = true
}

function openEdit(seance) {
  editingSeance.value = seance
  Object.assign(form, {
    titre: seance.titre,
    description: seance.description || '',
    dateHeure: format(new Date(seance.dateHeure), "yyyy-MM-dd'T'HH:mm"),
    duree: seance.duree,
    placesMax: seance.placesMax,
    lieu: seance.lieu || '',
  })
  formError.value = ''
  showModal.value = true
}

function confirmDelete(id) {
  deletingSeanceId.value = id
  showDeleteModal.value = true
}

async function handleSave() {
  if (!form.dateHeure) { formError.value = 'La date est requise.'; return }
  saving.value = true
  try {
    const payload = { ...form, dateHeure: new Date(form.dateHeure).toISOString() }
    if (editingSeance.value) {
      await seancesStore.updateSeance(editingSeance.value.id, payload)
      toast.success('Créneau mis à jour.')
    } else {
      await seancesStore.createSeance(payload)
      toast.success('Créneau créé.')
    }
    showModal.value = false
  } catch (e) {
    formError.value = e.response?.data?.message || 'Erreur.'
  } finally {
    saving.value = false
  }
}

async function executeDelete() {
  saving.value = true
  try {
    await seancesStore.deleteSeance(deletingSeanceId.value)
    toast.success('Créneau supprimé.')
    showDeleteModal.value = false
  } catch {
    toast.error('Impossible de supprimer.')
  } finally {
    saving.value = false
  }
}

async function handleToggle(seance) {
  try {
    await seancesStore.toggleDisponibilite(seance.id)
    toast.info(seance.disponible ? 'Créneau bloqué.' : 'Créneau débloqué.')
  } catch (e) {
    toast.error('Erreur : ' + (e.response?.data?.message || e.message || 'impossible de changer la disponibilité.'))
  }
}

async function cancelAdminRes(id) {
  try {
    await reservationsService.cancel(id)
    const res = allReservations.value.find(r => r.id === id)
    if (res) res.statut = 'annulee'
    toast.info('Réservation annulée.')
  } catch {
    toast.error('Erreur.')
  }
}

// ── Formatters ─────────────────────────────────────────────
function formatTime(d) { return format(new Date(d), 'HH:mm') }
function formatShortDate(d) { return format(new Date(d), 'd MMM', { locale: fr }) }
function formatDayLabel(dateStr) { return format(new Date(dateStr + 'T00:00:00'), 'EEEE d MMMM yyyy', { locale: fr }) }
function formatDateTime(d) { return format(new Date(d), 'EEE d MMM — HH:mm', { locale: fr }) }

function statusBadge(statut) {
  return {
    confirmee: { class: 'badge-green', label: 'Confirmée' },
    annulee:   { class: 'badge-red',   label: 'Annulée' },
    en_attente:{ class: 'badge-yellow',label: 'En attente' },
  }[statut] || { class: 'badge-orange', label: statut }
}

// ── Init ───────────────────────────────────────────────────
async function refreshAll() {
  loadingSeances.value = true
  loadingRes.value = true
  await seancesStore.fetchSeances(true)
  loadingSeances.value = false
  try {
    const [resData, usersData] = await Promise.all([
      reservationsService.allReservations(),
      reservationsService.allUsers(),
    ])
    allReservations.value = resData.data
    allClients.value = usersData.data
  } finally {
    loadingRes.value = false
  }
}

onMounted(refreshAll)
</script>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.2s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
