<template>
  <div class="min-h-screen bg-dark-900">
    <NavBar />
    <main>
      <router-view v-slot="{ Component }">
        <transition name="page" mode="out-in">
          <component :is="Component" />
        </transition>
      </router-view>
    </main>
    <Toast />
  </div>
</template>

<script setup>
import NavBar from '@/components/NavBar.vue'
import Toast from '@/components/Toast.vue'
</script>
