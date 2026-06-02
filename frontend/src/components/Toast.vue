<template>
  <teleport to="body">
    <div class="fixed top-4 right-4 z-50 flex flex-col gap-2 w-80 max-w-[calc(100vw-2rem)]" aria-live="polite">
      <transition-group name="toast" tag="div" class="flex flex-col gap-2">
        <div
          v-for="toast in toasts"
          :key="toast.id"
          class="flex items-start gap-3 p-4 cursor-pointer border-l-2"
          :class="toastClass(toast.type)"
          @click="remove(toast.id)"
          role="alert"
        >
          <div class="w-1.5 h-1.5 rounded-full mt-1.5 flex-shrink-0" :class="dotClass(toast.type)"></div>
          <p class="text-sm font-medium leading-snug flex-1">{{ toast.message }}</p>
          <button class="text-xs opacity-40 hover:opacity-100 transition-opacity leading-none font-bold" aria-label="Fermer">×</button>
        </div>
      </transition-group>
    </div>
  </teleport>
</template>

<script setup>
import { useToast } from '@/composables/useToast'

const { toasts, remove } = useToast()

function toastClass(type) {
  return {
    success: 'bg-dark-800 border-emerald-500 text-emerald-100',
    error: 'bg-dark-800 border-red-500 text-red-100',
    info: 'bg-dark-800 border-orange-500 text-orange-100',
  }[type] || 'bg-dark-800 border-orange-500 text-white'
}

function dotClass(type) {
  return {
    success: 'bg-emerald-500',
    error: 'bg-red-500',
    info: 'bg-orange-500',
  }[type] || 'bg-orange-500'
}
</script>

<style scoped>
.toast-enter-active, .toast-leave-active { transition: all 0.25s ease; }
.toast-enter-from { opacity: 0; transform: translateX(100%); }
.toast-leave-to { opacity: 0; transform: translateX(100%); }
</style>
