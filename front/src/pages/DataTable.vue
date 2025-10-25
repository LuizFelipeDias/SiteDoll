<!-- resources/js/layouts/DefaultLayout.vue -->
<template>
  <div class="flex flex-col min-h-screen">
    <Navbar />

    <!-- Conteúdo principal ocupa o espaço livre -->
    <div class="flex-1">
        <div class="">
            
        </div>
    </div>
    <Footer />
  </div>
</template>

<script>
import Navbar from "@/assets/Navbar.vue";
import Footer from "@/assets/Footer.vue";

export default {
  name: 'DefaultLayout',
  components: { Navbar, Footer }
}
</script>
