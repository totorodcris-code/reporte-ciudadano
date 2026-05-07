<template>
  <!-- NAVBAR -->
  <nav
    :class="['fixed z-50 w-full transition-all duration-300 bg-white dark:bg-gray-900 shadow-md']"
    role="navigation"
    aria-label="Navegación principal"
  >
    <div class="container flex items-center justify-between px-4 mx-auto lg:px-8">
      <div class="flex items-center gap-3 cursor-pointer" @click="scrollToTop">
        <span class="text-2xl">📢</span>
        <span class="text-xl font-bold text-gray-900 dark:text-white">Reporte<br /><span class="text-primary-500">Ciudadanos</span></span>
      </div>

      <ul class="items-center hidden gap-1 text-lg font-bold lg:flex">
        <li><a href="#inicio" class="inline-block px-5 py-3 text-gray-900 transition-colors rounded-lg hover:bg-gray-200 dark:hover:bg-gray-700 min-w-touch dark:text-white">
          <span class="transition-colors hover:text-primary-600 dark:hover:text-primary-400">Inicio</span>
        </a></li>
        <li><a href="#como-funciona" class="inline-block px-5 py-3 text-gray-900 transition-colors rounded-lg hover:bg-gray-200 dark:hover:bg-gray-700 min-w-touch dark:text-white">
          <span class="transition-colors hover:text-primary-600 dark:hover:text-primary-400">¿Cómo Funciona?</span>
        </a></li>
        <li><a href="#categorias" class="inline-block px-5 py-3 text-gray-900 transition-colors rounded-lg hover:bg-gray-200 dark:hover:bg-gray-700 min-w-touch dark:text-white">
          <span class="transition-colors hover:text-primary-600 dark:hover:text-primary-400">Categorías</span>
        </a></li>
        <li><a href="#beneficios" class="inline-block px-5 py-3 text-gray-900 transition-colors rounded-lg hover:bg-gray-200 dark:hover:bg-gray-700 min-w-touch dark:text-white">
          <span class="transition-colors hover:text-primary-600 dark:hover:text-primary-400">Beneficios</span>
        </a></li>
        <li><a href="#contacto" class="inline-block px-5 py-3 text-gray-900 transition-colors rounded-lg hover:bg-gray-200 dark:hover:bg-gray-700 min-w-touch dark:text-white">
          <span class="transition-colors hover:text-primary-600 dark:hover:text-primary-400">Contacto</span>
        </a></li>
        <li class="flex items-center gap-3 border-2 border-s ps-4 ms-4 min-h-touch">
          <router-link to="/login" class="inline-flex items-center px-5 py-3 font-bold text-gray-900 transition-colors rounded-lg hover:bg-gray-200 dark:hover:bg-gray-700 min-h-touch min-w-touch dark:text-white">Iniciar Sesión</router-link>
          <router-link to="/register" class="inline-flex items-center px-6 py-3 font-bold text-white transition-all rounded-full bg-primary-500 hover:bg-primary-600 min-h-touch min-w-touch">Registrarse</router-link>
        </li>
      </ul>

      <div class="flex items-center gap-2">
        <ThemeToggle />
        <button
          class="flex items-center justify-center p-2 text-2xl text-gray-900 transition-colors rounded focus:outline-none hover:bg-gray-200 dark:hover:bg-gray-700 min-w-touch min-h-touch dark:text-white lg:hidden"
          @click="toggleMenu"
          aria-label="Menú desplegable"
        >
          <i :class="['fas transition-transform duration-300', menuOpen ? 'fa-times text-red-500' : 'fa-bars text-primary-600']"></i>
        </button>
      </div>
    </div>

    <transition name="slide">
      <ul
        v-if="menuOpen"
        class="px-4 py-4 space-y-1 bg-white border-t border-gray-200 shadow-lg dark:bg-gray-900 lg:hidden dark:border-gray-700"
      >
        <li><a href="#inicio" class="block px-4 py-3 font-bold text-gray-900 transition-colors rounded dark:text-white hover:bg-gray-200 dark:hover:bg-gray-700" @click="menuOpen = false">Inicio</a></li>
        <li><a href="#como-funciona" class="block px-4 py-3 font-bold text-gray-900 transition-colors rounded dark:text-white hover:bg-gray-200 dark:hover:bg-gray-700" @click="menuOpen = false">¿Cómo Funciona?</a></li>
        <li><a href="#categorias" class="block px-4 py-3 font-bold text-gray-900 transition-colors rounded dark:text-white hover:bg-gray-200 dark:hover:bg-gray-700" @click="menuOpen = false">Categorías</a></li>
        <li><a href="#beneficios" class="block px-4 py-3 font-bold text-gray-900 transition-colors rounded dark:text-white hover:bg-gray-200 dark:hover:bg-gray-700" @click="menuOpen = false">Beneficios</a></li>
        <li><a href="#contacto" class="block px-4 py-3 font-bold text-gray-900 transition-colors rounded dark:text-white hover:bg-gray-200 dark:hover:bg-gray-700" @click="menuOpen = false">Contacto</a></li>
        <li class="flex flex-col gap-3 pt-4 mt-2 border-t border-gray-200 dark:border-gray-700">
          <router-link to="/login" class="block py-3 font-bold text-center text-gray-900 border-2 border-gray-300 rounded dark:text-white dark:border-gray-600" @click="menuOpen = false">Iniciar Sesión</router-link>
          <router-link to="/register" class="block py-3 font-bold text-center text-white rounded bg-primary-500 hover:bg-primary-600" @click="menuOpen = false">Registrarse</router-link>
        </li>
      </ul>
    </transition>
  </nav>

  <!-- HERO section -->
  <header
    id="inicio"
    class="relative flex flex-col items-center justify-center min-h-screen bg-center bg-no-repeat bg-cover"
    :style="{
      backgroundImage: `url(https://img.youtube.com/vi/rDv1k9s6xQc/maxresdefault.jpg)`
    }"
  >
    <div
      class="absolute inset-0 z-0 bg-gradient-to-b from-gray-900/70 via-gray-900/60 to-gray-900/80"
    ></div>

    <div class="absolute rounded-full top-20 left-10 w-72 h-72 bg-primary-500/10 blur-3xl"></div>
    <div class="absolute rounded-full bottom-20 right-10 w-96 h-96 bg-blue-500/10 blur-3xl"></div>

    <div class="relative z-10 max-w-5xl px-4 mx-auto text-center text-white">
      <div class="inline-block px-5 py-2 mb-6 text-lg font-medium tracking-wider uppercase border rounded-full bg-primary-500/20 text-primary-400 border-primary-400/50">
        🏙️ Plataforma Ciudadana
      </div>
      <h1 class="mb-6 text-4xl font-extrabold leading-tight md:text-6xl lg:text-7xl text-accessible-2xl">
        Tu Voz Cuenta<br/>
        <span class="text-primary-400">Tu Reporte Importa</span>
      </h1>
      <p class="max-w-2xl mx-auto mb-10 text-xl leading-relaxed text-gray-300 text-accessible-lg">
        Plataforma ciudadana para reportar y gestionar incidencias urbanas de manera rápida, efectiva y transparente.
      </p>
      <div class="flex flex-col items-center justify-center gap-5 sm:flex-row">
        <router-link
          to="/register"
          class="inline-flex items-center gap-3 px-10 py-4 text-lg font-bold text-white transition-all group rounded-xl hover:opacity-90 min-h-touch bg-primary-500 hover:bg-primary-600"
        >
          <span>Crear mi Primer Reporte</span>
          <i class="text-xl transition-transform group-hover:translate-x-1 fas fa-arrow-right"></i>
        </router-link>
        <router-link
          to="/login"
          class="inline-flex items-center gap-3 px-10 py-4 text-lg font-bold text-white transition-all border rounded-xl backdrop-blur border-primary-400/50 bg-primary-400/10 hover:bg-primary-400/20 min-h-touch"
        >
          <i class="fas fa-sign-in-alt"></i>
          <span>Iniciar Sesión</span>
        </router-link>
      </div>
    </div>

    <div class="absolute -translate-x-1/2 bottom-8 left-1/2 animate-bounce">
      <div class="flex justify-center w-6 h-10 border-2 rounded-full border-white/50">
        <div class="w-1.5 h-3 bg-white/70 rounded-full mt-2 animate-pulse"></div>
      </div>
    </div>
  </header>

  <!-- CÓMO FUNCIONA -->
  <section class="py-24 text-center bg-gray-50 dark:bg-gray-800" id="como-funciona" aria-labelledby="como-funciona-heading">
    <div class="container px-4 mx-auto">
      <div class="max-w-3xl mx-auto mb-16">
        <h2 id="como-funciona-heading" class="mb-4 text-3xl font-bold text-gray-800 md:text-4xl lg:text-5xl text-accessible-2xl dark:text-white">
          ¿Cómo Funciona?
        </h2>
        <p class="text-xl text-gray-600 text-accessible-lg dark:text-gray-300">Reporta incidencias en 3 simples pasos</p>
      </div>

      <div class="grid gap-8 mx-auto md:grid-cols-3">
        <div class="relative p-8 transition-all duration-300 bg-white shadow-lg group rounded-2xl hover:shadow-2xl hover:-translate-y-2">
          <div class="absolute flex items-center justify-center w-16 h-16 -translate-x-1/2 shadow-lg -top-6 left-1/2 rounded-2xl min-w-touch min-h-touch" style="background-color: #071952">
            <span class="text-2xl font-bold text-white">1</span>
          </div>
          <div class="mt-4">
            <div class="flex items-center justify-center w-20 h-20 mx-auto mb-6 rounded-2xl min-h-touch min-w-touch" style="background-color: #4A90E220">
              <i class="text-4xl fas fa-edit" style="color: #4A90E2"></i>
            </div>
            <h4 class="mb-3 text-xl font-bold text-accessible-lg" style="color: #071952">Reporta</h4>
            <p class="leading-relaxed text-accessible-base" style="color: #2D3748">
              Describe la incidencia, agrega fotos y ubicación exacta del problema de forma sencilla.
            </p>
          </div>
        </div>

        <div class="relative p-8 transition-all duration-300 bg-white shadow-lg group rounded-2xl hover:shadow-2xl hover:-translate-y-2">
          <div class="absolute flex items-center justify-center w-16 h-16 -translate-x-1/2 shadow-lg -top-6 left-1/2 rounded-2xl min-w-touch min-h-touch" style="background-color: #4A90E2">
            <span class="text-2xl font-bold text-white">2</span>
          </div>
          <div class="mt-4">
            <div class="flex items-center justify-center w-20 h-20 mx-auto mb-6 rounded-2xl min-h-touch min-w-touch" style="background-color: #35A29F20">
              <i class="text-4xl fas fa-tasks" style="color: #35A29F"></i>
            </div>
            <h4 class="mb-3 text-xl font-bold text-accessible-lg" style="color: #071952">Gestiona</h4>
            <p class="leading-relaxed text-accessible-base" style="color: #2D3748">
              Las autoridades revisan tu reporte y le asignan un estado de seguimiento en tiempo real.
            </p>
          </div>
        </div>

        <div class="relative p-8 transition-all duration-300 bg-white shadow-lg group rounded-2xl hover:shadow-2xl hover:-translate-y-2">
          <div class="absolute flex items-center justify-center w-16 h-16 -translate-x-1/2 shadow-lg -top-6 left-1/2 rounded-2xl min-w-touch min-h-touch" style="background-color: #35A29F">
            <span class="text-2xl font-bold text-white">3</span>
          </div>
          <div class="mt-4">
            <div class="flex items-center justify-center w-20 h-20 mx-auto mb-6 rounded-2xl min-h-touch min-w-touch" style="background-color: #07195220">
              <i class="text-4xl fas fa-check-circle" style="color: #071952"></i>
            </div>
            <h4 class="mb-3 text-xl font-bold text-accessible-lg" style="color: #071952">Resuelve</h4>
            <p class="leading-relaxed text-accessible-base" style="color: #2D3748">
              La incidencia se resuelve y tú puedes verificar el resultado y dejar tu feedback.
            </p>
          </div>
        </div>
      </div>
    </div>
  </section>

<!-- CATEGORÍAS -->
  <section class="py-24 bg-white dark:bg-gray-900" id="categorias" aria-labelledby="categorias-heading">
    <div class="container px-4 mx-auto">
      <div class="max-w-3xl mx-auto mb-16 text-center">
        <h2 id="categorias-heading" class="mb-4 text-3xl font-bold text-gray-800 md:text-4xl lg:text-5xl text-accessible-2xl dark:text-white">
          Categorías de Reportes
        </h2>
        <p class="text-xl text-gray-600 text-accessible-lg dark:text-gray-300">Tipos de incidencias que puedes reportar en tu ciudad</p>
      </div>

      <div class="grid gap-6 mx-auto md:grid-cols-2 lg:grid-cols-3">
        <div
          v-for="categoria in categorias"
          :key="categoria.id"
          class="p-6 transition-all duration-300 bg-white border-2 border-gray-200 cursor-pointer rounded-2xl hover:shadow-2xl hover:-translate-y-1 dark:bg-gray-800 dark:border-gray-700"
        >
          <div class="flex items-center justify-center w-16 h-16 mb-4 transition-all duration-300 rounded-xl bg-primary-50 dark:bg-primary-900/30">
            <i :class="[categoria.icon, 'text-3xl text-primary-600 dark:text-primary-400']"></i>
          </div>
          <h4 class="mb-2 text-lg font-bold text-gray-800 dark:text-white">{{ categoria.nombre }}</h4>
          <p class="leading-relaxed text-gray-600 dark:text-gray-300">{{ categoria.descripcion }}</p>
        </div>
      </div>
    </div>
  </section>

  <!-- ESTADÍSTICAS -->
  <section class="py-24 text-white bg-gray-900 dark:bg-gray-950" id="estadisticas" aria-labelledby="estadisticas-heading">
    <div class="container px-4 mx-auto">
      <h2 id="estadisticas-heading" class="mb-4 text-3xl font-bold text-center md:text-4xl">
        📊 Impacto Ciudadano
      </h2>

      <div class="grid gap-8 mx-auto mb-16 md:grid-cols-2 lg:grid-cols-4">
        <div class="text-center group">
          <div class="flex items-center justify-center w-24 h-24 mx-auto mb-4 transition-all duration-300 rounded-2xl bg-primary-500/20">
            <i class="text-4xl fas fa-file-alt text-primary-400"></i>
          </div>
          <div class="mb-2 text-4xl font-bold">
            {{ estadisticas.reportes.toLocaleString() }}+
          </div>
          <div class="text-gray-400">Reportes Totales</div>
        </div>
        <div class="text-center group">
          <div class="flex items-center justify-center w-24 h-24 mx-auto mb-4 transition-all duration-300 rounded-2xl bg-green-500/20">
            <i class="text-4xl text-green-400 fas fa-check-circle"></i>
          </div>
          <div class="mb-2 text-4xl font-bold">
            {{ estadisticas.resueltos.toLocaleString() }}+
          </div>
          <div class="text-gray-400">Incidencias Resueltas</div>
        </div>
        <div class="text-center group">
          <div class="flex items-center justify-center w-24 h-24 mx-auto mb-4 transition-all duration-300 rounded-2xl bg-primary-500/20">
            <i class="text-4xl fas fa-users text-primary-400"></i>
          </div>
          <div class="mb-2 text-4xl font-bold">
            {{ estadisticas.usuarios.toLocaleString() }}+
          </div>
          <div class="text-gray-400">Ciudadanos Activos</div>
        </div>
        <div class="text-center group">
          <div class="flex items-center justify-center w-24 h-24 mx-auto mb-4 transition-all duration-300 rounded-2xl bg-green-500/20">
            <i class="text-4xl text-green-400 fas fa-folder"></i>
          </div>
          <div class="mb-2 text-4xl font-bold">
            {{ estadisticas.categorias }}+
          </div>
          <div class="text-gray-400">Categorías</div>
        </div>
      </div>

      <!-- TARJETAS DE REPORTES - 3 COLUMNAS -->
      <div class="mt-16">
        <div class="max-w-7xl mx-auto px-4">
          <div class="mb-8 text-center">
            <h2 class="text-2xl font-bold text-white">📢 Reportes Recientes</h2>
            <p class="text-gray-400">Últimas incidencias reportadas por tu comunidad</p>
          </div>
          
          <!-- Estado de carga -->
          <div v-if="loading" class="flex items-center justify-center py-12">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500 mr-4"></div>
            <p class="text-gray-400">Cargando reportes...</p>
          </div>
          
          <!-- Estado vacío -->
          <div v-else-if="reportesDestacados.length === 0" class="text-center py-12">
            <div class="inline-block p-8 mb-4 bg-white/10 rounded-full">
              <i class="text-4xl">📋</i>
            </div>
            <h3 class="text-xl font-semibold text-white mb-2">No hay reportes aún</h3>
            <p class="text-gray-400 mb-6">Sé el primero en reportar una incidencia en tu comunidad</p>
            <router-link 
              to="/register"
              class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
            >
              <i class="fas fa-plus"></i>
              Crear primer reporte
            </router-link>
          </div>
          
          <!-- Grid de reportes -->
          <div v-else>
          
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div 
              v-for="reporte in reportesDestacados" 
              :key="reporte.id"
              class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-all duration-300 hover:-translate-y-1"
            >
              <!-- Imagen principal -->
              <div v-if="reporte.imagen" class="h-48 overflow-hidden">
                <img 
                  :src="reporte.imagen" 
                  :alt="reporte.titulo"
                  class="w-full h-full object-cover"
                />
              </div>
              <div v-else class="h-48 bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                <i class="text-4xl text-gray-400 fas fa-image"></i>
              </div>

              <!-- Contenido de la tarjeta -->
              <div class="p-4">
                <!-- Header con usuario y estado -->
                <div class="flex items-center justify-between mb-3">
                  <div class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white text-xs font-semibold">
                      {{ getInitials(reporte.user?.name || 'Anónimo') }}
                    </div>
                    <div>
                      <h4 class="font-semibold text-gray-900 dark:text-white text-sm">{{ reporte.user?.name || 'Anónimo' }}</h4>
                      <p class="text-xs text-gray-500 dark:text-gray-400">{{ formatearFecha(reporte.created_at) }}</p>
                    </div>
                  </div>
                  <span 
                    :class="[
                      'px-2 py-1 text-xs font-medium rounded-full',
                      reporte.estado === 'resuelto' 
                        ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100' 
                        : reporte.estado === 'en_proceso'
                        ? 'bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100'
                        : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100'
                    ]"
                  >
                    {{ reporte.estado === 'resuelto' ? '✅' : reporte.estado === 'en_proceso' ? '🔄' : '⏳' }}
                  </span>
                </div>

                <!-- Título y descripción -->
                <h3 class="font-bold text-gray-900 dark:text-white mb-2 line-clamp-1">{{ reporte.titulo }}</h3>
                <p class="text-sm text-gray-600 dark:text-gray-300 mb-3 line-clamp-2">{{ reporte.descripcion }}</p>

                <!-- Meta información -->
                <div class="flex items-center gap-2 mb-4">
                  <span class="px-2 py-1 bg-purple-100 text-purple-800 dark:bg-purple-800 dark:text-purple-100 rounded-full text-xs">
                    {{ reporte.categoria?.nombre_categoria || 'Sin categoría' }}
                  </span>
                  <span v-if="reporte.direccion" class="text-xs text-gray-500 dark:text-gray-400">
                    <i class="fas fa-map-marker-alt mr-1"></i>{{ reporte.direccion.slice(0, 20) }}...
                  </span>
                </div>

                <!-- Sección de votación y comentarios -->
                <div class="border-t border-gray-200 dark:border-gray-700 pt-3">
                  <!-- Votación -->
                  <div class="mb-3">
                    <div v-if="reporte.id">
                      <VotacionSection 
                        :incidencia-id="reporte.id" 
                        :incidencia-user-id="reporte.user_id || reporte.user?.id"
                        :compact="true"
                      />
                    </div>
                  </div>
                  
                  <!-- Comentarios -->
                  <div class="mb-3">
                    <ComentariosSection 
                      :incidencia-id="reporte.id"
                      :compact="true"
                    />
                  </div>

                  <!-- Botón de detalles -->
                  <router-link 
                    :to="`/incidencias/${reporte.id}`"
                    class="block w-full text-center px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium"
                  >
                    Ver detalles completos
                  </router-link>
                </div>
              </div>
            </div>
          </div>

          <!-- Botón cargar más -->
            <div class="mt-8 text-center">
              <button class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                Cargar más reportes
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

<!-- BENEFICIOS -->
  <section class="py-24 bg-gray-50 dark:bg-gray-800" id="beneficios" aria-labelledby="beneficios-heading">
    <div class="container px-4 mx-auto">
      <div class="max-w-3xl mx-auto mb-16 text-center">
        <h2 id="beneficios-heading" class="mb-4 text-3xl font-bold text-gray-800 md:text-4xl lg:text-5xl text-accessible-2xl dark:text-white">
          Beneficios de Reportar
        </h2>
        <p class="text-xl text-gray-600 text-accessible-lg dark:text-gray-300">Únete y haz la diferencia en tu comunidad</p>
      </div>

      <div class="grid gap-6 mx-auto md:grid-cols-2">
        <div class="flex items-start p-6 space-x-5 transition-all duration-300 bg-white shadow-md dark:bg-gray-900 rounded-2xl hover:shadow-2xl hover:-translate-y-1">
          <div class="flex items-center justify-center flex-shrink-0 w-14 h-14 rounded-xl bg-primary-500">
            <i class="text-2xl text-white fas fa-bullhorn"></i>
          </div>
          <div>
            <h4 class="mb-2 text-lg font-bold text-gray-800 dark:text-white">Haz Escuchar Tu Voz</h4>
            <p class="leading-relaxed text-gray-600 dark:text-gray-300">Participa activamente en la mejora de tu comunidad.</p>
          </div>
        </div>

        <div class="flex items-start p-6 space-x-5 transition-all duration-300 bg-white shadow-md dark:bg-gray-900 rounded-2xl hover:shadow-2xl hover:-translate-y-1">
          <div class="flex items-center justify-center flex-shrink-0 bg-blue-500 w-14 h-14 rounded-xl">
            <i class="text-2xl text-white fas fa-eye"></i>
          </div>
          <div>
            <h4 class="mb-2 text-lg font-bold text-gray-800 dark:text-white">Seguimiento Transparente</h4>
            <p class="leading-relaxed text-gray-600 dark:text-gray-300">Visualiza el estado de tu reporte en tiempo real.</p>
          </div>
        </div>
        <div class="flex items-start p-6 space-x-5 transition-all duration-300 bg-white shadow-md dark:bg-gray-900 rounded-2xl hover:shadow-2xl hover:-translate-y-1">
          <div class="flex items-center justify-center flex-shrink-0 bg-purple-600 w-14 h-14 rounded-xl">
            <i class="text-2xl text-white fas fa-users"></i>
          </div>
          <div>
            <h4 class="mb-2 text-lg font-bold text-gray-800 dark:text-white">Comunidad Unida</h4>
            <p class="leading-relaxed text-gray-600 dark:text-gray-300">Únete a ciudadanos comprometidos con el cambio.</p>
          </div>
        </div>

        <div class="flex items-start p-6 space-x-5 transition-all duration-300 bg-white shadow-md dark:bg-gray-900 rounded-2xl hover:shadow-2xl hover:-translate-y-1">
          <div class="flex items-center justify-center flex-shrink-0 bg-green-500 w-14 h-14 rounded-xl">
            <i class="text-2xl text-white fas fa-chart-line"></i>
          </div>
          <div>
            <h4 class="mb-2 text-lg font-bold text-gray-800 dark:text-white">Mejora Continua</h4>
            <p class="leading-relaxed text-gray-600 dark:text-gray-300">Contribuye al desarrollo y mantenimiento urbano.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

<!-- CONTACTO -->
  <section class="py-24 text-white bg-gray-900 dark:bg-gray-950" id="contacto" aria-labelledby="contacto-heading">
    <div class="container px-4 mx-auto text-center">
      <div class="max-w-3xl mx-auto mb-16">
        <h2 id="contacto-heading" class="mb-4 text-3xl font-bold md:text-4xl lg:text-5xl">
          ¿Necesitas Ayuda?
        </h2>
        <p class="text-xl text-gray-400">Contáctanos para cualquier duda o sugerencia</p>
      </div>

      <div class="grid max-w-4xl gap-6 mx-auto md:grid-cols-3">
        <div class="p-8 transition-all duration-300 bg-gray-800 group rounded-2xl dark:bg-gray-900 hover:bg-gray-700">
          <div class="flex items-center justify-center w-16 h-16 mx-auto mb-5 rounded-2xl bg-primary-500/20">
            <i class="text-3xl text-primary-400">✉</i>
          </div>
          <h4 class="mb-2 text-lg font-bold">Email</h4>
          <p class="text-gray-400">contacto@reporteciudadanos.com</p>
        </div>

        <div class="p-8 transition-all duration-300 bg-gray-800 group rounded-2xl dark:bg-gray-900 hover:bg-gray-700">
          <div class="flex items-center justify-center w-16 h-16 mx-auto mb-5 rounded-2xl bg-green-500/20">
            <i class="text-3xl text-green-400 fab fa-whatsapp"></i>
          </div>
          <h4 class="mb-2 text-lg font-bold">WhatsApp</h4>
          <a
            href="https://wa.me/59163750316"
            target="_blank"
            rel="noopener noreferrer"
            class="text-gray-400 transition-colors hover:text-primary-400"
          >
            +591 63750316
          </a>
        </div>

        <div class="p-8 transition-all duration-300 bg-gray-800 group rounded-2xl dark:bg-gray-900 hover:bg-gray-700">
          <div class="flex items-center justify-center w-16 h-16 mx-auto mb-5 bg-gray-700 rounded-2xl">
            <i class="text-3xl text-white fas fa-map-marker-alt"></i>
          </div>
          <h4 class="mb-2 text-lg font-bold">Ubicación</h4>
          <p class="text-gray-400">Sucre, Bolivia</p>
        </div>
      </div>

      <div class="flex justify-center gap-6 mt-12">
        <a href="#" class="flex items-center justify-center w-12 h-12 transition-all bg-gray-800 rounded-full dark:bg-gray-900 hover:bg-gray-700 hover:scale-110">
          <i class="text-xl fab fa-facebook-f"></i>
        </a>
        <a href="#" class="flex items-center justify-center w-12 h-12 transition-all bg-gray-800 rounded-full dark:bg-gray-900 hover:bg-gray-700 hover:scale-110">
          <i class="text-xl fab fa-twitter"></i>
        </a>
        <a href="#" class="flex items-center justify-center w-12 h-12 transition-all bg-gray-800 rounded-full dark:bg-gray-900 hover:bg-gray-700 hover:scale-110">
          <i class="text-xl fab fa-instagram"></i>
        </a>
        <a href="#" class="flex items-center justify-center w-12 h-12 transition-all bg-gray-800 rounded-full dark:bg-gray-900 hover:bg-gray-700 hover:scale-110">
          <i class="text-xl fab fa-linkedin-in"></i>
        </a>
      </div>
    </div>
  </section>

  <!-- CALL TO ACTION FINAL -->
  <section class="py-20 text-center bg-gray-800 dark:bg-gray-900">
    <div class="container px-4 mx-auto">
      <h2 class="mb-4 text-3xl font-bold text-white md:text-4xl">
        ¿Listo para hacer la diferencia?
      </h2>
      <p class="max-w-xl mx-auto mb-8 text-lg text-gray-300">
        Únete a miles de ciudadanos que ya están mejorando su comunidad
      </p>
      <router-link
        to="/register"
        class="inline-flex items-center gap-2 px-10 py-4 font-bold text-white transition transform rounded-xl hover:scale-105 bg-primary-500 hover:bg-primary-600"
      >
        <i class="fas fa-rocket"></i>
        <span>Registrarme Gratis</span>
      </router-link>
    </div>
  </section>

  <!-- FOOTER -->
  <footer class="py-10 text-white bg-gray-950">
    <div class="container px-4 mx-auto">
      <div class="flex flex-col items-center justify-between gap-6 md:flex-row">
        <div class="flex flex-col items-center gap-2 md:items-start">
          <span class="text-xl font-bold text-white">Reporte<br /><span class="text-primary-500">Ciudadanos</span></span>
          <p class="text-sm text-gray-400">© 2026 Reporte Ciudadanos</p>
          <p class="text-xs text-gray-500">Todos los derechos reservados</p>
        </div>

        <div class="flex flex-wrap justify-center gap-6 text-sm">
          <a href="#" class="text-gray-400 transition-colors hover:text-primary-400">Términos</a>
          <a href="#" class="text-gray-400 transition-colors hover:text-primary-400">Privacidad</a>
          <a href="#" class="text-gray-400 transition-colors hover:text-primary-400">Soporte</a>
          <a href="#" class="text-gray-400 transition-colors hover:text-primary-400">FAQ</a>
        </div>

        <div class="text-sm text-gray-500">
          Made with ❤️ in Bolivia
        </div>
      </div>
    </div>
  </footer>
</template>

<script>
import { ref, onMounted, onUnmounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import ThemeToggle from '@/Components/ThemeToggle.vue'
import VotacionSection from '@/Components/Votacion/VotacionSection.vue'
import ComentariosSection from '@/Components/Comentarios/ComentariosSection.vue'
import { useThemeStore } from '@/stores/theme'

export default {
  components: { ThemeToggle, VotacionSection, ComentariosSection },
  setup() {
    const themeStore = useThemeStore()
    return { themeStore }
  },
  data() {
    return {
      showTop: false,
      menuOpen: false,
      scrolled: false,
      loading: false,
      reportesDestacados: [],

      categorias: [
        {
          id: 1,
          nombre: 'Infraestructura',
          descripcion: 'Baches, banquetas, guarniciones y daños en la vía pública',
          icon: 'fas fa-road'
        },
        {
          id: 2,
          nombre: 'Alumbrado Público',
          descripcion: 'Farolas fundidas, postes dañados o sin funcionamiento',
          icon: 'fas fa-lightbulb'
        },
        {
          id: 3,
          nombre: 'Limpieza',
          descripcion: 'Recolección de basura, limpieza de calles y áreas públicas',
          icon: 'fas fa-broom'
        },
        {
          id: 5,
          nombre: 'Agua Potable',
          descripcion: 'Fugas, tuberías rotas o problemas con el suministro de agua',
          icon: 'fas fa-tint'
        },
        {
          id: 6,
          nombre: 'Áreas Verdes',
          descripcion: 'Parques, jardines, poda de árboles y mantenimiento de zonas verdes',
          icon: 'fas fa-tree'
        }
      ],

      estadisticas: {
        reportes: 0,
        resueltos: 0,
        usuarios: 0,
        categorias: 0
      }
    }
  },
  mounted() {
    document.addEventListener('keydown', this.handleEscape)
    window.addEventListener('scroll', this.checkScroll)
    this.fetchEstadisticas()
    this.fetchReportesDestacados()
  },
  beforeUnmount() {
    document.removeEventListener('keydown', this.handleEscape)
    window.removeEventListener('scroll', this.checkScroll)
  },
  methods: {
    toggleMenu() {
      this.menuOpen = !this.menuOpen
    },
    handleEscape(e) {
      if (e.key === 'Escape') {
        if (this.menuOpen) this.menuOpen = false
      }
    },
    scrollToTop() {
      window.scrollTo({ top: 0, behavior: 'smooth' })
    },
    checkScroll() {
      this.scrolled = window.scrollY > 50
      this.showTop = window.scrollY > 400
    },
    async fetchEstadisticas() {
      try {
        const { useApi } = await import('@/Composables/useApi')
        const { api } = useApi()
        const response = await api.getEstadisticas()
        const data = await response.json()
        this.estadisticas = data
      } catch (error) {
        console.error('Error fetching estadisticas:', error)
        const { useToast } = await import('@/Composables/useToast')
        const toast = useToast()
        toast.error('No se pudieron cargar las estadísticas. Intente nuevamente.')
      }
    },
    async fetchReportesDestacados() {
      this.loading = true
      try {
        const { useApi } = await import('@/Composables/useApi')
        const { api } = useApi()
        const response = await api.getIncidencias()
        const data = await response.json()
        
        // Manejar ambos formatos: API V1 (con success/data) y formato antiguo
        let reportesData
        if (data.success && data.data) {
          // Nuevo formato API V1 - puede ser paginado
          reportesData = data.data.data || data.data
        } else {
          // Formato antiguo o directo
          reportesData = data.data || data
        }

        const reportesProcesados = reportesData
          .filter(r => r.id && r.id > 0) // Solo incidencias con ID válido
          .slice(0, 9) // Más reportes para el grid de 3 columnas
          .map(r => ({
            ...r,
            imagen: r.imagen_url || (r.imagen ? '/storage/' + r.imagen : null),
            // Asegurar que tengamos user_id y user
            user_id: r.user_id || r.user?.id,
            user: r.user || {
              id: r.usuario?.id,
              name: r.usuario?.nombre || r.usuario?.name || 'Anónimo'
            }
          }))
        this.reportesDestacados = reportesProcesados
      } catch (error) {
        console.error('Error fetching reportes:', error)
        const { useToast } = await import('@/Composables/useToast')
        const toast = useToast()
        toast.error('No se pudieron cargar los reportes destacados. Intente nuevamente.')
      } finally {
        this.loading = false
      }
    },
    formatearFecha(fecha) {
      const date = new Date(fecha)
      const now = new Date()
      const diffTime = Math.abs(now - date)
      const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24))
      
      if (diffDays === 0) {
        return 'Hoy'
      } else if (diffDays === 1) {
        return 'Ayer'
      } else if (diffDays < 7) {
        return `Hace ${diffDays} días`
      } else if (diffDays < 30) {
        const weeks = Math.floor(diffDays / 7)
        return `Hace ${weeks} semana${weeks > 1 ? 's' : ''}`
      } else if (diffDays < 365) {
        const months = Math.floor(diffDays / 30)
        return `Hace ${months} mes${months > 1 ? 'es' : ''}`
      } else {
        const years = Math.floor(diffDays / 365)
        return `Hace ${years} año${years > 1 ? 's' : ''}`
      }
    },
    
    getInitials(name) {
      if (!name) return '??'
      return name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2)
    }
  },
}
</script>

<style scoped>
html {
  scroll-behavior: smooth;
}

.slide-enter-active,
.slide-leave-active {
  transition: all 0.3s ease;
}

.slide-enter-from,
.slide-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}
</style>