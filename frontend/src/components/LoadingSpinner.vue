<template>
  <div
    :class="['inline-flex items-center justify-center', fullPage ? 'fixed inset-0 bg-dark-900/80 backdrop-blur-sm z-50' : '']"
    :aria-label="label"
    role="status"
  >
    <div class="flex flex-col items-center gap-4">
      <div
        class="rounded-full border-2 border-transparent animate-spin"
        :class="sizeClasses"
        style="border-top-color: #8b5cf6; border-right-color: #ec4899;"
      ></div>
      <p v-if="label && fullPage" class="text-slate-400 text-sm font-medium">{{ label }}</p>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  size: { type: String, default: 'md' },
  fullPage: { type: Boolean, default: false },
  label: { type: String, default: 'Chargement...' },
})

const sizeClasses = computed(() => ({
  sm: 'w-5 h-5',
  md: 'w-8 h-8',
  lg: 'w-12 h-12',
  xl: 'w-16 h-16',
}[props.size] || 'w-8 h-8'))
</script>
