<script setup>
import { onMounted } from "vue"
import { useRouter, useRoute } from "vue-router"
import { useAuthStore } from "@/stores/auth"

const router = useRouter()
const route = useRoute()
const authStore = useAuthStore()

onMounted(async() => {
  const token = route.query.token

  console.log("OAuthSuccess - Token recibido:", token) // Debug

  if (token) {
    //guardar el token y hacer fetch del usuario que incluye el rol
    authStore.setToken(token)

    //esperar de forma obligatoria que el fetch se termine de leer
    await authStore.fetchUser()

    //redirigir según el rol devuelto
    if(authStore.isAdmin){
      router.push({ name: "admin.dashboard" })
    } 
    else
    {
      router.push({ name: "user.dashboard" })
    }
  } else {
    // Si no hay token, redirigir a login
    router.push({ name: "login" })
  }
})
</script>

<template>
  <div style="text-align:center; margin-top:100px;">
    <h1>Autenticación exitosa...</h1>
    <p>Redirigiendo a la aplicación...</p>
  </div>
</template>