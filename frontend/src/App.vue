<template>
  <div id="app" class="min-h-screen bg-gray-100">
    <nav class="bg-white shadow-lg">
      <div class="max-w-7xl mx-auto px-4">
        <div class="flex justify-between h-16">
          <div class="flex items-center">
            <router-link to="/" class="text-xl font-bold text-gray-800 hover:text-gray-600 cursor-pointer">
              Cinema App
            </router-link>
          </div>
        </div>
      </div>
    </nav>
    <main class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
      <router-view />
    </main>
  </div>
</template>

<script setup>
// App component
</script>


