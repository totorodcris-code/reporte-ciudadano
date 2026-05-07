<template>
  <div class="relative min-h-screen bg-gradient-to-br from-gray-900 via-purple-900 to-blue-900">
    <!-- Fondo animado -->
    <div class="absolute inset-0 overflow-hidden">
      <div class="particles"></div>
    </div>

    <div class="relative z-10 flex min-h-screen">
      <!-- SIDEBAR -->
      <aside class="sticky top-0 w-64 h-screen overflow-y-auto border-r shadow-2xl bg-white/10 backdrop-blur-xl border-white/20">
        <div class="p-6">
          <div class="flex items-center gap-3 mb-6">
            <div class="flex items-center justify-center w-12 h-12 transition-transform rounded-full bg-gradient-to-br from-yellow-400 to-yellow-600 hover:scale-110">
              <span class="text-2xl">🛡️</span>
            </div>
            <h2 class="text-lg font-bold text-white">Menú Admin</h2>
          </div>
          
          <nav class="space-y-2">
            <button
              v-for="item in menuItems"
              :key="item.key"
              @click="vistaActiva = item.key"
              :class="[
                'w-full text-left px-4 py-3 rounded-xl font-medium transition-all duration-300 transform',
                vistaActiva === item.key 
                  ? 'bg-gradient-to-r from-yellow-400 via-yellow-500 to-yellow-600 text-black shadow-lg scale-105' 
                  : 'text-white hover:bg-white/20 border border-white/20 hover:scale-105'
              ]"
            >
              <span class="mr-2">{{ item.icon }}</span>{{ item.label }}
            </button>
          </nav>
        </div>
      </aside>

      <!-- CONTENIDO PRINCIPAL -->
      <div class="flex flex-col flex-1">
        <!-- HEADER -->
        <header class="sticky top-0 z-50 border-b shadow-xl bg-white/10 backdrop-blur-xl border-white/20">
          <div class="flex items-center justify-between px-6 py-4">
            <div class="flex items-center gap-4">
              <div class="flex items-center justify-center w-10 h-10 transition-transform rounded-full bg-gradient-to-br from-yellow-400 to-yellow-600 hover:scale-110">
                <span class="text-xl">🛡️</span>
              </div>
              <div>
                <h1 class="text-2xl font-bold text-white">Panel Admin</h1>
                <p class="text-xs text-gray-400">ReporteCiudadanos - Sucre, Bolivia</p>
              </div>
            </div>
            <div class="flex items-center gap-4">
              <router-link
                to="/"
                class="flex items-center gap-2 px-4 py-2 text-white transition-all rounded-lg bg-primary-500/90 hover:bg-primary-600 hover:scale-105 backdrop-blur-sm min-h-touch"
              >
                <i class="fas fa-home"></i>
                <span class="hidden lg:inline">Inicio</span>
              </router-link>
              
              <div class="items-center hidden gap-3 px-4 py-2 transition-all rounded-full md:flex bg-white/10 hover:bg-white/20">
                <div class="flex items-center justify-center w-8 h-8 text-sm font-bold text-white transition-transform rounded-full bg-gradient-to-br from-purple-400 to-pink-500 hover:scale-110">
                  {{ authStore.user?.name?.charAt(0).toUpperCase() }}
                </div>
                <div class="text-right">
                  <p class="font-semibold text-white">{{ authStore.user?.name }}</p>
                  <p class="text-xs text-yellow-400">Administrador</p>
                </div>
              </div>
              <button
                @click="logout"
                class="flex items-center gap-2 px-4 py-2 text-white transition-all rounded-lg bg-red-500/90 hover:bg-red-600 hover:scale-105 backdrop-blur-sm"
              >
                <i class="fas fa-sign-out-alt"></i>
                <span class="hidden lg:inline">Salir</span>
              </button>
            </div>
          </div>
        </header>

        <!-- CONTENIDO -->
        <main class="flex-1 px-6 py-10 text-white" v-if="!loading">
          <!-- Título -->
          <div class="mb-10 transition-all duration-500 transform hover:scale-[1.02]">
            <div class="p-8 border shadow-xl rounded-3xl bg-gradient-to-r from-purple-500/20 to-pink-500/20 backdrop-blur-xl border-white/20">
              <h2 class="mb-2 text-4xl font-bold">
                {{ tituloVista }}
              </h2>
              <p class="text-lg text-gray-300">{{ descripcionVista }}</p>
            </div>
          </div>

          <!-- Estadísticas -->
          <div v-if="vistaActiva === 'dashboard'" class="grid grid-cols-1 gap-6 mb-12 md:grid-cols-2 lg:grid-cols-4">
            <div class="relative p-6 overflow-hidden transition-all duration-300 transform border shadow-xl group bg-white/10 backdrop-blur-xl rounded-2xl hover:shadow-2xl hover:scale-105 border-white/20">
              <div class="absolute top-0 right-0 w-32 h-32 transition-opacity rounded-bl-full opacity-10 group-hover:opacity-20 bg-gradient-to-br from-yellow-400 to-transparent"></div>
              <div class="relative">
                <div class="flex items-start justify-between mb-4">
                  <div>
                    <p class="mb-1 text-sm text-gray-300">Total Reportes</p>
                    <p class="text-5xl font-extrabold text-yellow-400">{{ totalReportes }}</p>
                  </div>
                  <div class="text-4xl">📊</div>
                </div>
                <p class="text-xs text-gray-400">Todos los reportes del sistema</p>
              </div>
            </div>

            <div class="relative p-6 overflow-hidden transition-all duration-300 transform border shadow-xl group bg-white/10 backdrop-blur-xl rounded-2xl hover:shadow-2xl hover:scale-105 border-white/20">
              <div class="absolute top-0 right-0 w-32 h-32 transition-opacity rounded-bl-full opacity-10 group-hover:opacity-20 bg-gradient-to-br from-orange-400 to-transparent"></div>
              <div class="relative">
                <div class="flex items-start justify-between mb-4">
                  <div>
                    <p class="mb-1 text-sm text-gray-300">Pendientes</p>
                    <p class="text-5xl font-extrabold text-orange-400">{{ pendientes }}</p>
                  </div>
                  <div class="text-4xl">⏳</div>
                </div>
                <p class="text-xs text-gray-400">Esperando respuesta</p>
              </div>
            </div>

            <div class="relative p-6 overflow-hidden transition-all duration-300 transform border shadow-xl group bg-white/10 backdrop-blur-xl rounded-2xl hover:shadow-2xl hover:scale-105 border-white/20">
              <div class="absolute top-0 right-0 w-32 h-32 transition-opacity rounded-bl-full opacity-10 group-hover:opacity-20 bg-gradient-to-br from-green-400 to-transparent"></div>
              <div class="relative">
                <div class="flex items-start justify-between mb-4">
                  <div>
                    <p class="mb-1 text-sm text-gray-300">Terminados</p>
                    <p class="text-5xl font-extrabold text-green-400">{{ terminados }}</p>
                  </div>
                  <div class="text-4xl">✅</div>
                </div>
                <p class="text-xs text-gray-400">Completados exitosamente</p>
              </div>
            </div>

            <div class="relative p-6 overflow-hidden transition-all duration-300 transform border shadow-xl group bg-white/10 backdrop-blur-xl rounded-2xl hover:shadow-2xl hover:scale-105 border-white/20">
              <div class="absolute top-0 right-0 w-32 h-32 transition-opacity rounded-bl-full opacity-10 group-hover:opacity-20 bg-gradient-to-br from-blue-400 to-transparent"></div>
              <div class="relative">
                <div class="flex items-start justify-between mb-4">
                  <div>
                    <p class="mb-1 text-sm text-gray-300">Total Usuarios</p>
                    <p class="text-5xl font-extrabold text-blue-400">{{ totalUsuarios }}</p>
                  </div>
                  <div class="text-4xl">👥</div>
                </div>
                <p class="text-xs text-gray-400">Usuarios registrados</p>
              </div>
            </div>

            <div class="relative p-6 overflow-hidden transition-all duration-300 transform border shadow-xl group bg-white/10 backdrop-blur-xl rounded-2xl hover:shadow-2xl hover:scale-105 border-white/20">
              <div class="absolute top-0 right-0 w-32 h-32 transition-opacity rounded-bl-full opacity-10 group-hover:opacity-20 bg-gradient-to-br from-purple-400 to-transparent"></div>
              <div class="relative">
                <div class="flex items-start justify-between mb-4">
                  <div>
                    <p class="mb-1 text-sm text-gray-300">Total Votos</p>
                    <p class="text-5xl font-extrabold text-purple-400">{{ totalVotos }}</p>
                  </div>
                  <div class="text-4xl">👍</div>
                </div>
                <p class="text-xs text-gray-400">Votos en el sistema</p>
              </div>
            </div>
          </div>

          <!-- Top 3 incidencias más votadas -->
          <div v-if="vistaActiva === 'dashboard'" class="p-8 border shadow-2xl bg-white/10 backdrop-blur-xl rounded-3xl border-white/20">
            <div class="flex items-center justify-between mb-6">
              <div>
                <h3 class="text-2xl font-bold">🏆 Incidencias Más Votadas</h3>
                <p class="text-gray-400">Top 3 reportes con más votos positivos</p>
              </div>
            </div>

            <div v-if="masVotadas.length > 0" class="grid grid-cols-1 gap-6 md:grid-cols-3">
              <div
                v-for="(inc, index) in masVotadas"
                :key="inc.id"
                class="relative p-5 transition-all duration-300 border rounded-2xl bg-white/5 border-white/20 hover:bg-white/10 hover:scale-[1.02]"
              >
                <div class="absolute -top-3 -left-3 w-8 h-8 flex items-center justify-center rounded-full text-sm font-bold text-black bg-gradient-to-br from-yellow-400 to-yellow-600 shadow-lg">
                  {{ index + 1 }}
                </div>
                <div class="flex items-center gap-2 mb-3">
                  <span
                    :class="[
                      'px-2 py-0.5 rounded-full text-xs font-medium',
                      inc.estado === 'resuelto' ? 'bg-green-500/20 text-green-300' :
                      inc.estado === 'en-progreso' ? 'bg-blue-500/20 text-blue-300' :
                      'bg-orange-500/20 text-orange-300'
                    ]"
                  >
                    {{ inc.estado === 'resuelto' ? '✅' : inc.estado === 'en-progreso' ? '🔄' : '⏳' }}
                  </span>
                  <span class="text-xs text-gray-500">{{ inc.categoria }}</span>
                </div>
                <h4 class="font-bold text-white mb-1 line-clamp-1">{{ inc.titulo }}</h4>
                <p class="text-sm text-gray-400 mb-3 line-clamp-2">{{ inc.descripcion }}</p>
                <div class="flex items-center justify-between text-sm">
                  <span class="text-gray-500 truncate">{{ inc.usuario }}</span>
                  <span class="flex items-center gap-1 font-semibold text-green-400">
                    <i class="fas fa-thumbs-up"></i>
                    {{ inc.votos_positivos }}/{{ inc.votos_totales }}
                  </span>
                </div>
              </div>
            </div>
            <div v-else class="py-8 text-center text-gray-500">
              <i class="mb-2 text-3xl fas fa-vote-yea"></i>
              <p>Aún no hay votos registrados</p>
            </div>
          </div>

          <!-- VISTA: DASHBOARD -->
          <div v-if="vistaActiva === 'dashboard'" class="p-8 border shadow-2xl bg-white/10 backdrop-blur-xl rounded-3xl border-white/20">
            <h3 class="mb-4 text-2xl font-bold">📊 Resumen General</h3>
            <p class="mb-6 text-gray-300">Vista general del sistema de Reporte Ciudadanos</p>
            
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
              <div class="p-6 border rounded-xl bg-white/5 border-white/20">
                <h4 class="flex items-center gap-2 mb-3 text-lg font-semibold">
                  <i class="text-yellow-400 fas fa-chart-pie"></i>
                  Estadísticas Rápidas
                </h4>
                <div class="space-y-2 text-sm">
                  <div class="flex justify-between">
                    <span class="text-gray-400">Reportes hoy:</span>
                    <span class="font-medium text-white">{{ totalReportes }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-gray-400">Tasa de resolución:</span>
                    <span class="font-medium text-green-400">{{ Math.round((terminados / (totalReportes || 1)) * 100) }}%</span>
                  </div>
                </div>
              </div>

              <div class="p-6 border rounded-xl bg-white/5 border-white/20">
                <h4 class="flex items-center gap-2 mb-3 text-lg font-semibold">
                  <i class="text-blue-400 fas fa-info-circle"></i>
                  Información
                </h4>
                <div class="space-y-2 text-sm">
                  <div class="flex justify-between">
                    <span class="text-gray-400">Categorías:</span>
                    <span class="font-medium text-purple-400">{{ categorias.length }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-gray-400">Ubicación:</span>
                    <span class="font-medium text-green-400">Sucre, Bolivia</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- MAPA INTERACTIVO DE INCIDENCIAS -->
          <div v-if="vistaActiva === 'dashboard'" class="p-8 border shadow-2xl bg-white/10 backdrop-blur-xl rounded-3xl border-white/20">
            <div class="flex flex-col items-start justify-between gap-4 mb-6 md:flex-row md:items-center">
              <div>
                <h3 class="mb-2 text-2xl font-bold">🗺️ Mapa de Incidencias</h3>
                <p class="text-gray-300">Ubicación geográfica de todos los reportes en tiempo real</p>
              </div>
              <div class="flex gap-3">
                <button
                  @click="refreshMap"
                  class="inline-flex items-center gap-2 px-4 py-2 font-medium text-white transition-all transform rounded-lg bg-white/10 hover:bg-white/20 hover:scale-105"
                >
                  <i class="fas fa-sync-alt"></i>
                  Actualizar Mapa
                </button>
              </div>
            </div>
            
            <BasicMap
              :incidencias="reportesConUbicacion"
            />
          </div>

          <!-- VISTA: INCIDENCIAS -->
          <div v-if="vistaActiva === 'incidencias'" class="p-8 border shadow-2xl bg-white/10 backdrop-blur-xl rounded-3xl border-white/20">
            <div class="flex flex-col items-start justify-between gap-4 mb-8 md:flex-row md:items-center">
              <div>
                <h3 class="mb-2 text-2xl font-bold">📋 Gestión de Incidencias</h3>
                <p class="text-gray-300">Todas las incidencias del sistema</p>
              </div>
            </div>

            <!-- Filtro de Fecha -->
            <div class="p-4 mb-6 border rounded-xl bg-white/5 border-white/20">
              <label class="block mb-3 text-sm font-medium text-gray-300">🗓️ Filtrar por Fecha</label>
              <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                <input
                  v-model="fechaFiltro"
                  type="date"
                  class="px-4 py-2 text-white transition-all border rounded-lg bg-white/5 border-white/20 focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 focus:outline-none"
                />
                <button
                  v-if="fechaFiltro"
                  @click="fechaFiltro = ''"
                  class="px-4 py-2 text-sm font-medium text-gray-300 transition-all rounded-lg bg-white/10 hover:bg-white/20"
                >
                  <i class="mr-2 fas fa-times"></i>Limpiar Filtro
                </button>
                <span v-if="fechaFiltro" class="text-sm text-gray-400">
                  Mostrando {{ reportesFiltrados.length }} reporte(s) del {{ formatearFechaSeleccionada(fechaFiltro) }}
                </span>
              </div>
            </div>

            <div v-if="reportesFiltrados.length > 0" class="overflow-x-auto">
              <table class="w-full text-sm text-left">
                <thead class="border-b-2 border-white/20">
                  <tr>
                    <th class="px-4 py-4 font-semibold text-gray-300">ID</th>
                    <th class="px-4 py-4 font-semibold text-gray-300">Título</th>
                    <th class="px-4 py-4 font-semibold text-gray-300">Usuario</th>
                    <th class="px-4 py-4 font-semibold text-gray-300">Estado</th>
                    <th class="px-4 py-4 font-semibold text-gray-300">Fecha</th>
                    <th class="px-4 py-4 font-semibold text-right text-gray-300">Acciones</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="reporte in reportesFiltrados" :key="reporte.id" class="transition-all border-b border-white/10 hover:bg-white/5 group">
                    <td class="px-4 py-4 text-gray-300">#{{ reporte.id }}</td>
                    <td class="px-4 py-4 font-medium transition-colors group-hover:text-yellow-400">{{ reporte.titulo }}</td>
                    <td class="px-4 py-4 text-gray-400">{{ reporte.usuario }}</td>
                    <td class="px-4 py-4">
                      <select 
                        :value="reporte.estado" 
                        @change="cambiarEstadoReporte(reporte.id, $event)" 
                        :class="[
                          'px-4 py-2 rounded-full font-medium text-sm transition-all transform hover:scale-105 text-gray-900',
                          reporte.estado === 'resuelto' ? 'bg-gradient-to-r from-green-500/20 to-green-600/20 border border-green-500/30' :
                          reporte.estado === 'en-progreso' ? 'bg-gradient-to-r from-blue-500/20 to-blue-600/20 border border-blue-500/30' :
                          'bg-gradient-to-r from-orange-500/20 to-orange-600/20 border border-orange-500/30'
                        ]"
                      >
                        <option value="pendiente">⏳ Pendiente</option>
                        <option value="en-progreso">🔄 En Progreso</option>
                        <option value="resuelto">✅ Resuelto</option>
                      </select>
                    </td>
                    <td class="px-4 py-4 text-xs text-gray-300">{{ formatearFecha(reporte.fecha) }}</td>
                    <td class="px-4 py-4 text-right">
                      <div class="flex justify-end gap-2">
                        <button @click="verDetallesReporte(reporte.id)" class="p-2 text-blue-400 transition-all transform rounded-lg hover:bg-blue-500/20 hover:scale-110 hover:text-blue-300" title="Ver"><i class="fas fa-eye"></i></button>
                        <button @click="eliminarReporte(reporte.id)" class="p-2 text-red-400 transition-all transform rounded-lg hover:bg-red-500/20 hover:scale-110 hover:text-red-300" title="Eliminar"><i class="fas fa-trash"></i></button>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div v-else class="py-12 text-center text-gray-400">
              <div class="inline-block p-8 mb-4 transition-all rounded-full bg-white/10 hover:bg-white/20">
                <i class="text-6xl">📭</i>
              </div>
              No hay incidencias para mostrar
            </div>
          </div>

          <!-- VISTA: USUARIOS -->
          <div v-if="vistaActiva === 'usuarios'" class="p-8 border shadow-2xl bg-white/10 backdrop-blur-xl rounded-3xl border-white/20">
            <div class="flex flex-col items-start justify-between gap-4 mb-8 md:flex-row md:items-center">
              <div>
                <h3 class="mb-2 text-2xl font-bold">👥 Gestión de Usuarios</h3>
                <p class="text-gray-300">Usuarios registrados en el sistema</p>
              </div>
            </div>

            <div v-if="usuarios.length > 0" class="overflow-x-auto">
              <table class="w-full text-sm text-left">
                <thead class="border-b-2 border-white/20">
                  <tr>
                    <th class="px-4 py-4 font-semibold text-gray-300">ID</th>
                    <th class="px-4 py-4 font-semibold text-gray-300">Nombre</th>
                    <th class="px-4 py-4 font-semibold text-gray-300">Correo</th>
                    <th class="px-4 py-4 font-semibold text-gray-300">Rol</th>
                    <th class="px-4 py-4 font-semibold text-gray-300">Incidencias</th>
                    <th class="px-4 py-4 font-semibold text-gray-300">Registrado</th>
                    <th class="px-4 py-4 font-semibold text-right text-gray-300">Acciones</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="usuario in usuarios" :key="usuario.id" class="border-b border-white/10 hover:bg-white/5 group">
                    <td class="px-4 py-4 text-gray-300">#{{ usuario.id }}</td>
                    <td class="px-4 py-4 font-medium transition-colors group-hover:text-yellow-400">{{ usuario.nombre }}</td>
                    <td class="px-4 py-4 text-xs text-gray-400">{{ usuario.correo }}</td>
                    <td class="px-4 py-4">
                      <select 
                        :value="usuario.role" 
                        @change="cambiarRolUsuario(usuario.id, $event)"
                        :class="[
                          'px-4 py-2 rounded-full font-medium text-sm transition-all transform hover:scale-105 text-gray-900',
                          usuario.role === 'admin' ? 'bg-gradient-to-r from-purple-500/20 to-purple-600/20 border border-purple-500/30' : 'bg-gradient-to-r from-blue-500/20 to-blue-600/20 border border-blue-500/30'
                        ]"
                      >
                        <option value="usuario">👤 Usuario</option>
                        <option value="admin">🛡️ Administrador</option>
                      </select>
                    </td>
                    <td class="px-4 py-4 text-xs text-blue-300">{{ usuario.reportes }}</td>
                    <td class="px-4 py-4 text-xs text-gray-300">{{ formatearFecha(usuario.registrado) }}</td>
                    <td class="px-4 py-4 text-right">
                      <button @click="eliminarUsuario(usuario.id)" class="p-2 text-red-400 transition-all transform rounded-lg hover:bg-red-500/20 hover:scale-110 hover:text-red-300" title="Eliminar"><i class="fas fa-trash"></i></button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div v-else class="py-12 text-center text-gray-400">
              <div class="inline-block p-8 mb-4 transition-all rounded-full bg-white/10 hover:bg-white/20">
                <i class="text-6xl">👥</i>
              </div>
              No hay usuarios para mostrar
            </div>
          </div>

          <!-- VISTA: VOTOS -->
          <div v-if="vistaActiva === 'votos'" class="space-y-6">
            <div class="p-6 border shadow-xl bg-white/10 backdrop-blur-xl rounded-2xl border-white/20 inline-block">
              <div class="flex items-center gap-3">
                <div class="p-3 rounded-lg bg-blue-500/20">
                  <i class="text-xl text-blue-400 fas fa-vote-yea"></i>
                </div>
                <div>
                  <p class="text-sm text-gray-400">Total Votos</p>
                  <p class="text-2xl font-bold text-white">{{ votosStats.total_votos }}</p>
                </div>
              </div>
            </div>

            <div class="p-8 border shadow-2xl bg-white/10 backdrop-blur-xl rounded-3xl border-white/20">
              <div class="flex flex-col items-start justify-between gap-4 mb-6 md:flex-row md:items-center">
                <h3 class="text-xl font-bold">Listado de Votos</h3>
                <div class="flex gap-3">
                  <input type="date" v-model="votosFiltroFecha" @change="cargarVotosAdmin(1)"
                    class="px-3 py-2 text-sm text-white transition-all border rounded-lg bg-white/5 border-white/20 focus:ring-2 focus:ring-yellow-500 focus:outline-none" />
                </div>
              </div>

              <div v-if="votosLoading" class="flex items-center justify-center py-12">
                <div class="w-8 h-8 border-4 rounded-full border-white/20 border-t-yellow-500 animate-spin"></div>
                <span class="ml-3 text-gray-400">Cargando votos...</span>
              </div>

              <div v-else-if="votos.length > 0" class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                  <thead class="border-b-2 border-white/20">
                    <tr>
                      <th class="px-4 py-4 font-semibold text-gray-300">ID</th>
                      <th class="px-4 py-4 font-semibold text-gray-300">Usuario</th>
                      <th class="px-4 py-4 font-semibold text-gray-300">Incidencia</th>
                      <th class="px-4 py-4 font-semibold text-gray-300">Voto</th>
                      <th class="px-4 py-4 font-semibold text-gray-300">Fecha</th>
                      <th class="px-4 py-4 font-semibold text-right text-gray-300">Acciones</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="voto in votos" :key="voto.id" class="transition-all border-b border-white/10 hover:bg-white/5 group">
                      <td class="px-4 py-4 text-gray-300">#{{ voto.id }}</td>
                      <td class="px-4 py-4">
                        <div class="flex items-center gap-2">
                          <div class="flex items-center justify-center w-8 h-8 text-xs font-semibold text-white rounded-full bg-gradient-to-br from-blue-400 to-purple-500">{{ getInitials(voto.user?.name) }}</div>
                          <div>
                            <p class="font-medium text-white">{{ voto.user?.name }}</p>
                            <p class="text-xs text-gray-500">{{ voto.user?.email }}</p>
                          </div>
                        </div>
                      </td>
                      <td class="px-4 py-4">
                        <p class="text-gray-200">{{ voto.incidencia?.titulo || 'Incidencia #' + voto.incidencia_id }}</p>
                        <p class="text-xs text-gray-500">ID: {{ voto.incidencia_id }}</p>
                      </td>
                      <td class="px-4 py-4">
                        <span :class="[
                          'inline-flex items-center gap-1 px-3 py-1.5 rounded-full text-xs font-medium',
                          voto.tipo === 'positivo' ? 'bg-green-500/20 text-green-300 border border-green-500/30' : 'bg-red-500/20 text-red-300 border border-red-500/30'
                        ]">
                          <i :class="voto.tipo === 'positivo' ? 'fas fa-thumbs-up' : 'fas fa-thumbs-down'"></i>
                          {{ voto.tipo === 'positivo' ? 'Positivo' : 'Negativo' }}
                        </span>
                      </td>
                      <td class="px-4 py-4 text-xs text-gray-400">{{ formatearFecha(voto.created_at) }}</td>
                      <td class="px-4 py-4 text-right">
                        <button @click="eliminarVotoAdmin(voto.id)" class="p-2 text-red-400 transition-all transform rounded-lg hover:bg-red-500/20 hover:scale-110" title="Eliminar voto">
                          <i class="fas fa-trash"></i>
                        </button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <div v-else class="py-12 text-center text-gray-400">
                <i class="mb-4 text-5xl fas fa-vote-yea"></i>
                <p>No hay votos registrados</p>
              </div>

              <div v-if="votosPagination.last_page > 1" class="flex items-center justify-between pt-4 mt-4 border-t border-white/10">
                <span class="text-sm text-gray-400">Mostrando {{ votosPagination.from }} a {{ votosPagination.to }} de {{ votosPagination.total }}</span>
                <div class="flex gap-2">
                  <button @click="cargarVotosAdmin(votosPagination.current_page - 1)" :disabled="votosPagination.current_page <= 1"
                    class="px-4 py-2 text-sm text-white transition-all rounded-lg bg-white/10 hover:bg-white/20 disabled:opacity-30">
                    <i class="fas fa-chevron-left"></i>
                  </button>
                  <span class="px-4 py-2 text-sm text-gray-300">{{ votosPagination.current_page }} / {{ votosPagination.last_page }}</span>
                  <button @click="cargarVotosAdmin(votosPagination.current_page + 1)" :disabled="votosPagination.current_page >= votosPagination.last_page"
                    class="px-4 py-2 text-sm text-white transition-all rounded-lg bg-white/10 hover:bg-white/20 disabled:opacity-30">
                    <i class="fas fa-chevron-right"></i>
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- VISTA: CATEGORÍAS -->
          <div v-if="vistaActiva === 'categorias'" class="p-8 border shadow-2xl bg-white/10 backdrop-blur-xl rounded-3xl border-white/20">
            <div class="flex flex-col items-start justify-between gap-4 mb-8 md:flex-row md:items-center">
              <div>
                <h3 class="mb-2 text-2xl font-bold">🏷️ Gestión de Categorías</h3>
                <p class="text-gray-300">Categorías de incidencias disponibles</p>
              </div>
              <button @click="abrirModalCrearCategoria" class="inline-flex items-center gap-2 px-6 py-3 font-bold text-black transition-all transform rounded-full shadow-lg bg-gradient-to-r from-yellow-400 via-yellow-500 to-yellow-600 hover:from-yellow-500 hover:via-yellow-600 hover:to-yellow-700 hover:scale-105">
                <i class="fas fa-plus-circle"></i> Agregar Categoría
              </button>
            </div>

            <div v-if="categoriasParaMostrar.length > 0" class="overflow-x-auto">
              <table class="w-full text-sm text-left">
                <thead class="border-b-2 border-white/20">
                  <tr>
                    <th class="px-4 py-4 font-semibold text-gray-300">ID</th>
                    <th class="px-4 py-4 font-semibold text-gray-300">Nombre</th>
                    <th class="px-4 py-4 font-semibold text-gray-300">Descripción</th>
                    <th class="px-4 py-4 font-semibold text-gray-300">Incidencias</th>
                    <th class="px-4 py-4 font-semibold text-right text-gray-300">Acciones</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="categoria in categoriasParaMostrar" :key="categoria.id" class="border-b border-white/10 hover:bg-white/5 group">
                    <td class="px-4 py-4 text-gray-300">#{{ categoria.id }}</td>
                    <td class="px-4 py-4 font-medium transition-colors group-hover:text-yellow-400">{{ categoria.nombre_categoria || categoria.nombre }}</td>
                    <td class="px-4 py-4 text-xs text-gray-400">{{ categoria.descripcion }}</td>
                    <td class="px-4 py-4 text-xs text-blue-300">{{ categoria.incidencias_count }}</td>
                    <td class="px-4 py-4 text-right">
                      <div class="flex justify-end gap-2">
                        <button
                          v-if="categoria.fromBackend"
                          @click="editarCategoria(categoria.id)"
                          class="p-2 text-yellow-400 transition-all transform rounded-lg hover:bg-yellow-500/20 hover:scale-110 hover:text-yellow-300"
                          title="Editar"
                        >
                          <i class="fas fa-edit"></i>
                        </button>
                        <button
                          v-if="categoria.fromBackend"
                          @click="eliminarCategoria(categoria.id)"
                          class="p-2 text-red-400 transition-all transform rounded-lg hover:bg-red-500/20 hover:scale-110 hover:text-red-300"
                          title="Eliminar"
                        >
                          <i class="fas fa-trash"></i>
                        </button>
                        <span v-else class="px-3 py-2 text-xs font-medium text-gray-300 rounded-full bg-white/10">Sistema</span>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div v-else class="py-12 text-center text-gray-400">
              <div class="inline-block p-8 mb-4 transition-all rounded-full bg-white/10 hover:bg-white/20">
                <i class="text-6xl">🏷️</i>
              </div>
              No hay categorías para mostrar
            </div>
          </div>

        <!-- MODAL CREAR/EDITAR CATEGORÍAS -->
        <transition enter-active-class="transition duration-300" enter-from-class="opacity-0" enter-to-class="opacity-100">
          <div v-if="modalCategoria" class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm" @click="cerrarModalCategoria">
            <div class="w-full max-w-2xl p-6 mx-4 transition-all transform bg-gray-900 border shadow-2xl rounded-3xl border-white/20" @click.stop>
              <div class="flex items-center justify-between mb-6">
                <h3 class="text-2xl font-bold text-white">{{ modoEdicion ? '✏️ Editar Categoría' : '➕ Nueva Categoría' }}</h3>
                <button @click="cerrarModalCategoria" class="text-gray-400 transition hover:text-white">
                  <i class="text-2xl fas fa-times"></i>
                </button>
              </div>
              <form @submit.prevent="guardarCategoria" class="space-y-4">
                <div>
                  <label class="block mb-2 text-sm font-medium text-gray-300">Nombre</label>
                  <input
                    v-model="formaCategoria.nombre_categoria"
                    type="text"
                    class="w-full px-4 py-3 text-white transition-all border rounded-xl bg-white/5 border-white/20 focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 focus:outline-none"
                    placeholder="Ej: Infraestructura, Limpieza, Seguridad..."
                    required
                  />
                </div>
                <div>
                  <label class="block mb-2 text-sm font-medium text-gray-300">Descripción</label>
                  <textarea
                    v-model="formaCategoria.descripcion"
                    class="w-full px-4 py-3 text-white transition-all border rounded-xl bg-white/5 border-white/20 focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 focus:outline-none"
                    rows="4"
                    placeholder="Describe brevemente esta categoría..."
                    required
                  ></textarea>
                </div>
                <div class="flex justify-end gap-3 pt-4">
                  <button
                    type="button"
                    @click="cerrarModalCategoria"
                    class="px-6 py-3 text-sm font-medium text-gray-300 transition-all rounded-xl bg-white/10 hover:bg-white/20"
                  >
                    Cancelar
                  </button>
                  <button
                    type="submit"
                    class="px-6 py-3 text-sm font-medium text-black transition-all rounded-xl bg-gradient-to-r from-yellow-400 to-yellow-600 hover:from-yellow-500 hover:to-yellow-700"
                  >
                    {{ modoEdicion ? '💾 Actualizar' : '✨ Crear' }}
                  </button>
                </div>
              </form>
            </div>
          </div>
        </transition>
      </main>

        <!-- Loader -->
        <main v-else class="flex items-center justify-center flex-1 px-6 py-10 text-center text-white">
          <div class="inline-block">
            <div class="w-16 h-16 border-4 rounded-full border-white/20 border-t-yellow-500 animate-spin"></div>
            <p class="mt-4 text-gray-300">Cargando panel de administración...</p>
          </div>
        </main>
    </div>

    <!-- Toast Notifications -->
    <div class="fixed z-50 space-y-3 top-4 right-4">
      <transition-group name="toast" tag="div" class="space-y-3">
        <div 
          v-for="toast in toasts" 
          :key="toast.id"
          :class="[
            'flex items-center gap-3 px-5 py-4 rounded-xl shadow-2xl backdrop-blur-xl border transform transition-all duration-300 min-w-[300px] max-w-sm',
            toast.type === 'success' ? 'bg-green-500/20 border-green-400/30 text-green-100' :
            toast.type === 'error' ? 'bg-red-500/20 border-red-400/30 text-red-100' :
            'bg-yellow-500/20 border-yellow-400/30 text-yellow-100'
          ]"
        >
          <span class="text-xl">
            {{ toast.type === 'success' ? '✅' : toast.type === 'error' ? '❌' : '⚠️' }}
          </span>
          <span class="flex-1 text-sm font-medium whitespace-pre-line">{{ toast.message }}</span>
          <button 
            @click="removerToast(toast.id)"
            class="p-1 transition-colors rounded-lg hover:bg-white/20"
          >
            <i class="text-sm fas fa-times"></i>
          </button>
        </div>
      </transition-group>
    </div>
  </div>
  </div>
</template>

<script>
import { useAuthStore } from "../../stores/auth"
import api from "../../services/api"
import BasicMap from "../../Components/BasicMap.vue"

export default {
  name: "AdminDashboard",
  components: {
    BasicMap
  },
  data() {
    return {
      reportes: [],
      usuarios: [],
      categorias: [],
      defaultCategorias: [
        { id: 1, nombre_categoria: 'Infraestructura', descripcion: 'Baches, banquetas, guarniciones y daños en la vía pública', incidencias_count: 0 },
        { id: 2, nombre_categoria: 'Alumbrado Público', descripcion: 'Farolas fundidas, postes dañados o sin funcionamiento', incidencias_count: 0 },
        { id: 3, nombre_categoria: 'Limpieza', descripcion: 'Recolección de basura, limpieza de calles y áreas públicas', incidencias_count: 0 },
        { id: 5, nombre_categoria: 'Agua Potable', descripcion: 'Fugas, tuberías rotas o problemas con el suministro de agua', incidencias_count: 0 },
        { id: 6, nombre_categoria: 'Áreas Verdes', descripcion: 'Parques, jardines, poda de árboles y mantenimiento de zonas verdes', incidencias_count: 0 }
      ],
      loading: true,
      vistaActiva: 'dashboard',
      menuItems: [
        { key: 'dashboard', label: 'Dashboard', icon: '📊' },
        { key: 'incidencias', label: 'Incidencias', icon: '📋' },
        { key: 'usuarios', label: 'Usuarios', icon: '👥' },
        { key: 'votos', label: 'Votos', icon: '👍' },
        { key: 'categorias', label: 'Categorías', icon: '🏷️' }
      ],
      modalCategoria: false,
      toasts: [],
      toastIdCounter: 0,
      formaCategoria: {
        id: null,
        nombre_categoria: '',
        descripcion: ''
      },
      modoEdicion: false,
      fechaFiltro: '',
      votos: [],
      votosLoading: false,
      votosStats: {
        total_votos: 0,
        votos_positivos: 0,
        votos_negativos: 0,
        incidencias_con_votos: 0,
        porcentaje_positivos: 0,
        porcentaje_negativos: 0
      },
      votosPagination: {
        current_page: 1,
        last_page: 1,
        per_page: 20,
        total: 0,
        from: 0,
        to: 0
      },
      votosFiltroFecha: '',
      masVotadas: []
    }
  },
  computed: {
    authStore() { return useAuthStore() },
    totalReportes() { return this.reportes.length },
    pendientes() { return this.reportes.filter(r => r.estado === 'pendiente').length },
    resueltos() { return this.reportes.filter(r => r.estado === 'resuelto').length },
    enProgreso() { return this.reportes.filter(r => r.estado === 'en-progreso').length },
    terminados() { return this.resueltos },
    totalUsuarios() { return this.usuarios.length },
    totalVotos() { return this.votosStats.total_votos },
    reportesFiltrados() {
      if (!this.fechaFiltro) {
        return this.reportes
      }
      return this.reportes.filter(r => {
        if (!r.fecha) return false
        const d = new Date(r.fecha)
        const localDateStr = d.getFullYear() + '-' +
          String(d.getMonth() + 1).padStart(2, '0') + '-' +
          String(d.getDate()).padStart(2, '0')
        return localDateStr === this.fechaFiltro
      })
    },
    tituloVista() {
      const item = this.menuItems.find(i => i.key === this.vistaActiva)
      return item ? item.label : 'Dashboard'
    },
    categoriasParaMostrar() {
      const backendCategorias = this.categorias.filter(c => c.nombre !== 'Seguridad')
      const missingCategorias = this.defaultCategorias
        .filter(defaultCat => !backendCategorias.some(backendCat => backendCat.nombre === defaultCat.nombre_categoria))
        .map(defaultCat => ({ ...defaultCat, fromBackend: false }))

      return backendCategorias.length > 0 || missingCategorias.length > 0
        ? [...backendCategorias, ...missingCategorias]
        : this.defaultCategorias.map(defaultCat => ({ ...defaultCat, fromBackend: false }))
    },
    descripcionVista() {
      switch (this.vistaActiva) {
        case 'dashboard': return 'Vista general del sistema de Reporte Ciudadanos'
        case 'incidencias': return 'Gestiona todas las incidencias reportadas'
        case 'usuarios': return 'Administra los usuarios registrados'
        case 'votos': return 'Administra todos los votos del sistema'
        case 'categorias': return 'Gestiona las categorías de incidencias'
        default: return ''
      }
    },
    reportesConUbicacion() {
      console.log('🔍 Depurando reportesConUbicacion en Admin Dashboard')
      console.log('📊 Total reportes:', this.reportes.length)
      console.log('📊 Reportes crudos:', this.reportes)
      
      const conCoords = this.reportes.filter((reporte, index) => {
        const tieneLat = reporte.latitud && reporte.latitud !== undefined && reporte.latitud !== null
        const tieneLng = reporte.longitud && reporte.longitud !== undefined && reporte.longitud !== null
        const latValida = tieneLat && !isNaN(parseFloat(reporte.latitud))
        const lngValida = tieneLng && !isNaN(parseFloat(reporte.longitud))
        
        console.log(`📍 Reporte ${index + 1}:`, {
          id: reporte.id,
          titulo: reporte.titulo,
          latitud: reporte.latitud,
          longitud: reporte.longitud,
          tieneLat,
          tieneLng,
          latValida,
          lngValida,
          pasaFiltro: latValida && lngValida
        })
        
        return latValida && lngValida
      }).map(reporte => ({
        ...reporte,
        latitud: parseFloat(reporte.latitud),
        longitud: parseFloat(reporte.longitud)
      }))
      
      console.log('✅ Reportes con coordenadas válidas:', conCoords.length)
      console.log('📍 Reportes filtrados:', conCoords)
      
      return conCoords
    }
  },
  async mounted() {
    if (this.authStore.user?.role !== 'admin') {
      this.$router.push('/user/dashboard')
      return
    }
    await this.cargarDatos()
    await this.cargarVotosStats()
    await this.cargarMasVotadas()
  },
  watch: {
    vistaActiva(valor) {
      if (valor === 'votos') {
        this.cargarVotosAdmin()
        this.cargarVotosStats()
      }
    }
  },
  methods: {
    showToast(message, type = 'success', duration = 3000) {
      const id = ++this.toastIdCounter
      this.toasts.push({ id, message, type })
      setTimeout(() => {
        this.removerToast(id)
      }, duration)
    },
    removerToast(id) {
      this.toasts = this.toasts.filter(t => t.id !== id)
    },
    refreshMap() {
      // Forzar la recarga de datos del mapa
      this.cargarDatos()
    },
    async cargarDatos() {
      this.loading = true
      try {
        const [responseReportes, responseUsuarios, responseCategorias] = await Promise.all([
          api.get('/incidencias'),
          api.get('/usuarios'),
          api.get('/categorias')
        ])

        // Manejar ambos formatos: API V1 (con success/data) y formato antiguo
        let reportesData
        if (responseReportes.data.success && responseReportes.data.data) {
          // Nuevo formato API V1
          reportesData = responseReportes.data.data.data || responseReportes.data.data
        } else {
          // Formato antiguo
          reportesData = responseReportes.data.data || responseReportes.data
        }

        // Verificar que reportesData sea un array
        if (!Array.isArray(reportesData)) {
          console.warn('⚠️ Estructura de datos inesperada en Admin Dashboard:', responseReportes.data)
          this.reportes = []
          return
        }

        this.reportes = reportesData.map(r => ({
          id: r.id,
          titulo: r.titulo,
          descripcion: r.descripcion,
          usuario: r.user?.name || 'Desconocido',
          estado: r.estado || 'pendiente',
          fecha: r.created_at,
          latitud: r.latitud,
          longitud: r.longitud,
          direccion: r.direccion,
          categoria_id: r.categoria_id,
          imagen: r.imagen,
          user_id: r.user_id
        }))

        this.usuarios = responseUsuarios.data.map(u => ({
          id: u.id,
          nombre: u.name,
          correo: u.email,
          role: u.role || u.rol || 'usuario',
          reportes: u.incidencias_count || 0,
          registrado: u.created_at
        }))

        this.categorias = responseCategorias.data.map(c => ({
          id: c.id,
          nombre: c.nombre_categoria,
          descripcion: c.descripcion,
          incidencias_count: c.incidencias_count || 0,
          fromBackend: true
        }))

      } catch (err) {
        console.error("Error cargando datos:", err)
      } finally {
        this.loading = false
      }
    },
    formatearFecha(fecha) {
      const date = new Date(fecha)
      return date.toLocaleDateString('es-ES', { year:'numeric', month:'short', day:'numeric' })
    },
    formatearFechaSeleccionada(fechaStr) {
      if (!fechaStr) return ''
      const partes = fechaStr.split('-')
      return `${partes[2]}/${partes[1]}/${partes[0]}`
    },
    logout() {
      this.authStore.logout()
      this.$router.push("/login")
    },
    async cambiarEstadoReporte(id, e) {
      const nuevoEstado = e.target.value
      const reporte = this.reportes.find(r => r.id === id)
      
      try {
        await api.put(`/incidencias/${id}`, { estado: nuevoEstado })
        
        if(reporte) {
          reporte.estado = nuevoEstado
        }
        
        this.showToast(`Estado cambiado a "${nuevoEstado}" exitosamente`, 'success')
      } catch (err) {
        console.error("Error al cambiar estado:", err)
        this.showToast('Error al cambiar el estado del reporte', 'error')
      }
    },
    
    verDetallesReporte(id) {
     this.$router.push(`/reportes/${id}`)
    },
    
   async eliminarReporte(id) {
     if (confirm('¿Estás seguro de que deseas eliminar este reporte?')) {
         try {
         await api.delete(`/incidencias/${id}`)
         this.reportes = this.reportes.filter(r => r.id !== id)
         this.showToast('Reporte eliminado exitosamente', 'success')
         } catch (err) {
           console.error("Error al eliminar reporte:", err)
          this.showToast('Error al eliminar el reporte', 'error')
         }
      }
    },
    async eliminarUsuario(id) {
      if (confirm('¿Estás seguro de que deseas eliminar este usuario?')) {
        try {
          await api.delete(`/usuarios/${id}`)
          this.usuarios = this.usuarios.filter(u => u.id !== id)
          this.showToast('Usuario eliminado exitosamente', 'success')
        } catch (err) {
          console.error("Error al eliminar usuario:", err)
          this.showToast('Error al eliminar el usuario', 'error')
        }
      }
    },
    abrirModalCrearCategoria() {
      this.modalCategoria = true
      this.formaCategoria = {
        id: null,
        nombre_categoria: '',
        descripcion: ''
      }
      this.modoEdicion = false
    },
    cerrarModalCategoria() {
      this.modalCategoria = false
      this.formaCategoria = {
        id: null,
        nombre_categoria: '',
        descripcion: ''
      }
      this.modoEdicion = false
    },
    async guardarCategoria() {
      try {
        if (this.formaCategoria.id) {
          await api.put(`/categorias/${this.formaCategoria.id}`, this.formaCategoria)
          const categoria = this.categorias.find(c => c.id === this.formaCategoria.id)
          if (categoria) {
            categoria.nombre = this.formaCategoria.nombre_categoria
            categoria.descripcion = this.formaCategoria.descripcion
          }
          this.showToast('Categoría actualizada exitosamente', 'success')
         } else {
           const response = await api.post('/categorias', this.formaCategoria)
           this.categorias.push({
             id: response.data.id,
             nombre: response.data.nombre_categoria,
             descripcion: response.data.descripcion,
             incidencias_count: 0,
             fromBackend: true
           })
           this.showToast('Categoría creada exitosamente', 'success')
         }
         this.cerrarModalCategoria()
       } catch (err) {
         console.error("Error al guardar categoría:", err)
         if (err.response?.data?.errors) {
           const mensajes = Object.values(err.response.data.errors).flat().join('\n')
           this.showToast(`Error al guardar la categoría:\n${mensajes}`, 'error')
         } else {
           this.showToast('Error al guardar la categoría', 'error')
         }
       }
    },
    editarCategoria(id){
      const categoria = this.categorias.find(c => c.id === id)
      if (categoria) {
        this.modalCategoria = true
        this.formaCategoria = { 
          id: categoria.id,
          nombre_categoria: categoria.nombre,
          descripcion: categoria.descripcion
        }
        this.modoEdicion = true
      }
    },
    async eliminarCategoria(id){
      const categoria = this.categorias.find(c => c.id === id)
      if (!categoria) {
        return this.showToast('Esta categoría no se puede eliminar porque no existe en el servidor.', 'warning')
      }

      if (!confirm('¿Estás seguro de que deseas eliminar esta categoría?')) {
        return
      }

      try {
        await api.delete(`/categorias/${id}`)
        this.categorias = this.categorias.filter(c => c.id !== id)
        this.showToast('Categoría eliminada exitosamente', 'success')
      } catch (err) {
        console.error("Error al eliminar categoría:", err)
        if (err.response?.status === 409) {
          const mensaje = err.response.data.message || 'No se puede eliminar la categoría porque tiene incidencias asociadas'
          this.showToast(mensaje, 'warning')
        } else if (err.response?.status === 404) {
          this.showToast('No se encontró la categoría en el servidor.', 'error')
        } else {
          this.showToast('Error al eliminar la categoría', 'error')
        }
      }
    },
    getInitials(name) {
      if (!name) return 'NA'
      return name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2)
    },

    async cargarVotosAdmin(pagina = 1) {
      this.votosLoading = true
      try {
        const params = { page: pagina }
        if (this.votosFiltroFecha) {
          params.fecha = this.votosFiltroFecha
        }
        const response = await api.get('/votos', { params })
        if (response.data.success) {
          this.votos = response.data.data
          this.votosPagination = response.data.pagination
        }
      } catch (error) {
        console.error('Error cargando votos:', error)
        this.showToast('Error al cargar votos', 'error')
      } finally {
        this.votosLoading = false
      }
    },

    async cargarVotosStats() {
      try {
        const response = await api.get('/votos/estadisticas')
        if (response.data.success) {
          this.votosStats = response.data.data
        }
      } catch (error) {
        console.error('Error cargando estadísticas de votos:', error)
      }
    },

    async cargarMasVotadas() {
      try {
        const response = await api.get('/incidencias/mas-votadas')
        if (response.data.success) {
          this.masVotadas = response.data.data
        }
      } catch (error) {
        console.error('Error cargando incidencias más votadas:', error)
      }
    },

    async eliminarVotoAdmin(id) {
      if (!confirm('¿Estás seguro de eliminar este voto?')) return
      try {
        await api.delete(`/votos/${id}`)
        this.votos = this.votos.filter(v => v.id !== id)
        this.showToast('Voto eliminado exitosamente', 'success')
        await this.cargarVotosStats()
      } catch (error) {
        console.error('Error eliminando voto:', error)
        this.showToast('Error al eliminar el voto', 'error')
      }
    },

    async cambiarRolUsuario(id, e) {
      const nuevoRole = e.target.value
      try {
        await api.put(`/usuarios/${id}/role`, { role: nuevoRole, rol: nuevoRole })
        const usuario = this.usuarios.find(u => u.id === id)
        if (usuario) {
          usuario.role = nuevoRole
        }
        await this.cargarDatos()
        this.showToast(`Rol cambiado a "${nuevoRole === 'admin' ? 'Administrador' : 'Usuario'}" exitosamente`, 'success')
      } catch (err) {
        console.error("Error al cambiar rol:", err.response?.data || err)
        const mensaje = err.response?.data?.message || err.response?.data?.errors || 'Error al cambiar el rol del usuario'
        this.showToast(mensaje, 'error')
      }
    }
  }
}
</script>

<style scoped>
/* Partículas */
.particles {
  background-image: 
    radial-gradient(circle at 20% 50%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
    radial-gradient(circle at 80% 80%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
    radial-gradient(circle at 40% 20%, rgba(255, 255, 255, 0.05) 0%, transparent 50%);
  animation: float 20s ease-in-out infinite;
}

@keyframes float {
  0%, 100% { transform: translateY(0px); }
  50% { transform: translateY(-20px); }
}

@keyframes spin {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}

.animate-spin {
  animation: spin 1s linear infinite;
}

/* Glassmorphism */
.bg-white\/10 {
  backdrop-filter: blur(20px);
}

/* Table row hover */
tbody tr:hover {
  transform: scale(1.01);
  transition: all 0.3s ease;
}

/* Button effects */
button:active {
  transform: scale(0.95);
}

/* Toast transitions */
.toast-enter-active, .toast-leave-active {
  transition: all 0.3s ease;
}
.toast-enter-from {
  opacity: 0;
  transform: translateX(100%);
}
.toast-leave-to {
  opacity: 0;
  transform: translateX(100%);
}

/* Scrollbar personalizado */
::-webkit-scrollbar {
  width: 8px;
  height: 8px;
}

::-webkit-scrollbar-track {
  background: rgba(255, 255, 255, 0.1);
  border-radius: 10px;
}

::-webkit-scrollbar-thumb {
  background: rgba(255, 255, 255, 0.3);
  border-radius: 10px;
}

::-webkit-scrollbar-thumb:hover {
  background: rgba(255, 255, 255, 0.5);
}
</style>
