<!-- resources/js/layouts/DefaultLayout.vue -->
<template>
  <div class="flex flex-col min-h-screen">
    <Navbar />

    <!-- Conteúdo principal ocupa o espaço livre -->
    <div class="flex-1">
      <div>
        <h2 class="text-4xl text-center font-bold my-8">Bem vindo</h2>
        <p class="text-center text-gray-700">Plataforma para estudantes explorarem o poder da IA.</p>
      </div>
      <div class="mx-auto px-4 py-8 flex justify-center items-center">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-8 w-full max-w-5xl px-4">
          <div class="bg-white rounded-xl shadow-md h-48 w-full flex flex-col items-center justify-center text-gray-900 border-2 border-gray-900 p-4 text-center overflow-hidden break-words">
            <h1 class="w-full overflow-hidden break-words text-2xl font-semibold">Plataforma Interativa</h1>
          </div>

          <div class="bg-white rounded-xl shadow-md h-48 w-full flex flex-col items-center justify-center text-gray-900 border-2 border-gray-900 p-4 text-center overflow-hidden break-words">
            <h1 class="w-full overflow-hidden break-words text-2xl font-semibold">Conteúdo Modular</h1>
          </div>
          <div class="bg-white rounded-xl shadow-md h-48 w-full flex flex-col items-center justify-center text-gray-900 border-2 border-gray-900 p-4 text-center overflow-hidden break-words">
            <h1 class="w-full overflow-hidden break-words text-2xl font-semibold">Aplicação Real</h1>
          </div>
        </div>
      </div>

    </div>

    <Footer />
  </div>
</template>

<script>
import Footer from '@/assets/Footer.vue'
import Navbar from '@/assets/Navbar.vue'


export default {
  name: 'DefaultLayout',
  components: { Navbar, Footer }
}
</script>
