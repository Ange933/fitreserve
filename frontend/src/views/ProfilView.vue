<template>
  <div class="page-container max-w-2xl mx-auto">

    <div class="mb-10 animate-fade-in">
      <p class="label-tag text-orange-500 mb-2">Espace membre</p>
      <h1 class="section-title text-white">Mon <span class="text-gradient">profil</span></h1>
    </div>

    <!-- Carte identité -->
    <div class="glass-card p-6 mb-6 flex items-center gap-4">
      <div class="w-14 h-14 bg-orange-500/20 border border-orange-500/30 flex items-center justify-center font-display font-black text-orange-500 text-xl flex-shrink-0">
        {{ initials }}
      </div>
      <div>
        <p class="font-bold text-white text-lg">{{ auth.fullName }}</p>
        <p class="text-zinc-500 text-sm">{{ auth.user?.email }}</p>
        <p v-if="auth.user?.telephone" class="text-zinc-500 text-sm">{{ auth.user?.telephone }}</p>
        <p class="text-zinc-600 text-xs mt-1">Membre depuis {{ memberSince }}</p>
      </div>
    </div>

    <!-- Formulaire modification -->
    <div class="glass-card p-6 md:p-8">
      <h2 class="font-bold text-white text-base uppercase tracking-wide mb-6 border-b border-dark-600 pb-4">
        Modifier mes informations
      </h2>

      <form @submit.prevent="handleSave" class="space-y-5" novalidate>

        <div class="grid grid-cols-2 gap-4">
          <div class="space-y-1.5">
            <label for="prenom" class="label-tag">Prénom</label>
            <input id="prenom" v-model="form.prenom" type="text" autocomplete="given-name" class="input-field" :class="{ 'border-red-500': errors.prenom }" required aria-required="true" aria-describedby="prenom-error" />
            <p id="prenom-error" v-if="errors.prenom" class="text-red-400 text-xs" role="alert">{{ errors.prenom }}</p>
          </div>
          <div class="space-y-1.5">
            <label for="nom" class="label-tag">Nom</label>
            <input id="nom" v-model="form.nom" type="text" autocomplete="family-name" class="input-field" :class="{ 'border-red-500': errors.nom }" required aria-required="true" aria-describedby="nom-error" />
            <p id="nom-error" v-if="errors.nom" class="text-red-400 text-xs" role="alert">{{ errors.nom }}</p>
          </div>
        </div>

        <div class="space-y-1.5">
          <label for="telephone" class="label-tag">Téléphone</label>
          <input id="telephone" v-model="form.telephone" type="tel" autocomplete="tel" placeholder="06 12 34 56 78" class="input-field" :class="{ 'border-red-500': errors.telephone }" aria-describedby="telephone-error" />
          <p id="telephone-error" v-if="errors.telephone" class="text-red-400 text-xs" role="alert">{{ errors.telephone }}</p>
        </div>

        <div class="border-t border-dark-600 pt-5">
          <p class="label-tag mb-4">Changer le mot de passe <span class="text-zinc-700 normal-case font-normal">(laisser vide pour ne pas changer)</span></p>

          <div class="space-y-4">
            <div class="space-y-1.5">
              <label for="password" class="label-tag">Nouveau mot de passe</label>
              <div class="relative">
                <input id="password" v-model="form.password" :type="showPwd ? 'text' : 'password'" autocomplete="new-password" placeholder="Laisser vide pour ne pas changer" class="input-field pr-12" :class="{ 'border-red-500': errors.password }" aria-describedby="password-error" />
                <button type="button" @click="showPwd = !showPwd" :aria-label="showPwd ? 'Masquer le mot de passe' : 'Afficher le mot de passe'" class="absolute right-3 top-1/2 -translate-y-1/2 text-zinc-600 hover:text-zinc-300 transition-colors">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                  </svg>
                </button>
              </div>
              <p id="password-error" v-if="errors.password" class="text-red-400 text-xs" role="alert">{{ errors.password }}</p>
            </div>

            <div class="space-y-1.5">
              <label for="confirmPassword" class="label-tag">Confirmer le mot de passe</label>
              <div class="relative">
                <input id="confirmPassword" v-model="form.confirmPassword" :type="showConfirmPwd ? 'text' : 'password'" autocomplete="new-password" placeholder="••••••••" class="input-field pr-12" :class="{ 'border-red-500': errors.confirmPassword }" aria-describedby="confirmPassword-error" />
                <button type="button" @click="showConfirmPwd = !showConfirmPwd" :aria-label="showConfirmPwd ? 'Masquer la confirmation' : 'Afficher la confirmation'" class="absolute right-3 top-1/2 -translate-y-1/2 text-zinc-600 hover:text-zinc-300 transition-colors">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                  </svg>
                </button>
              </div>
              <p id="confirmPassword-error" v-if="errors.confirmPassword" class="text-red-400 text-xs" role="alert">{{ errors.confirmPassword }}</p>
            </div>
          </div>
        </div>

        <div v-if="errors.global" class="p-4 border-l-2 border-red-500 bg-red-500/5 text-red-300 text-sm font-medium" role="alert">
          {{ errors.global }}
        </div>

        <div class="flex gap-3 pt-2">
          <button type="submit" class="btn-primary flex-1 py-3.5" :disabled="saving">
            <LoadingSpinner v-if="saving" size="sm" />
            <span>Enregistrer</span>
          </button>
          <router-link to="/reservations" class="btn-ghost flex-1 py-3.5 text-center text-sm">Annuler</router-link>
        </div>
      </form>
    </div>

  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { format } from 'date-fns'
import { fr } from 'date-fns/locale'
import { useAuthStore } from '@/stores/auth'
import { authService } from '@/services/api'
import { useToast } from '@/composables/useToast'
import LoadingSpinner from '@/components/LoadingSpinner.vue'

const auth = useAuthStore()
const toast = useToast()
const saving = ref(false)
const showPwd = ref(false)
const showConfirmPwd = ref(false)

const form = reactive({ prenom: '', nom: '', telephone: '', password: '', confirmPassword: '' })
const errors = reactive({ prenom: '', nom: '', telephone: '', password: '', confirmPassword: '', global: '' })

const initials = computed(() => {
  const p = auth.user?.prenom?.[0] || ''
  const n = auth.user?.nom?.[0] || ''
  return (p + n).toUpperCase()
})

const memberSince = computed(() => {
  if (!auth.user?.createdAt) return ''
  return format(new Date(auth.user.createdAt), 'd MMMM yyyy', { locale: fr })
})

function validate() {
  Object.keys(errors).forEach(k => errors[k] = '')
  let valid = true
  if (!form.prenom.trim()) { errors.prenom = 'Requis.'; valid = false }
  if (!form.nom.trim()) { errors.nom = 'Requis.'; valid = false }
  if (form.telephone && !/^(\+33|0)[1-9](\d{2}){4}$/.test(form.telephone.replace(/\s/g, ''))) {
    errors.telephone = 'Numéro invalide (ex: 06 12 34 56 78).'; valid = false
  }
  if (form.password) {
    if (form.password.length < 8) { errors.password = 'Minimum 8 caractères.'; valid = false }
    else if (!/^(?=.*[A-Z])(?=.*\d).+$/.test(form.password)) { errors.password = 'Doit contenir une majuscule et un chiffre.'; valid = false }
    if (form.password !== form.confirmPassword) { errors.confirmPassword = 'Les mots de passe ne correspondent pas.'; valid = false }
  }
  return valid
}

async function handleSave() {
  if (!validate()) return
  saving.value = true
  try {
    const payload = {
      nom: form.nom,
      prenom: form.prenom,
      telephone: form.telephone || null,
    }
    if (form.password) payload.password = form.password

    const { data } = await authService.updateMe(payload)
    auth.user = data.user
    toast.success('Profil mis à jour.')
    form.password = ''
    form.confirmPassword = ''
  } catch (e) {
    const d = e.response?.data
    if (d?.errors) Object.assign(errors, d.errors)
    else errors.global = d?.message || 'Erreur lors de la mise à jour.'
  } finally {
    saving.value = false
  }
}

onMounted(() => {
  if (auth.user) {
    form.prenom = auth.user.prenom || ''
    form.nom = auth.user.nom || ''
    form.telephone = auth.user.telephone || ''
  }
})
</script>
