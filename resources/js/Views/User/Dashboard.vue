<template>
  <div class="relative min-h-screen bg-gradient-to-br from-gray-900 via-blue-900 to-purple-900">
    <!-- Fondo animado -->
    <div class="absolute inset-0 overflow-hidden">
      <div class="particles"></div>
    </div>

    <!-- CONTENIDO PRINCIPAL -->
    <div class="relative z-10 min-h-screen">
      <!-- NAVBAR -->
      <header class="sticky top-0 z-50 border-b shadow-xl bg-white/10 backdrop-blur-xl border-white/20">
        <div class="container flex items-center justify-between px-6 py-4 mx-auto">
          <div class="flex items-center gap-3">
            <div class="flex items-center justify-center w-10 h-10 transition-transform rounded-full bg-gradient-to-br from-yellow-400 to-yellow-600 hover:scale-110">
              <span class="text-xl">📊</span>
            </div>
            <h1 class="text-xl font-bold text-white">
              Panel de Usuario
            </h1>
          </div>

          <div class="flex items-center gap-4">
            <router-link
              to="/"
              class="flex items-center gap-2 px-4 py-2 text-white transition-all rounded-lg bg-primary-500/90 hover:bg-primary-600 hover:scale-105 backdrop-blur-sm min-h-touch"
            >
              <i class="fas fa-home"></i>
              <span class="hidden md:inline">Inicio</span>
            </router-link>
            
            <div class="items-center hidden gap-3 px-4 py-2 transition-all rounded-full cursor-pointer md:flex bg-white/10 hover:bg-white/20" @click="$router.push('/perfil')">
              <div class="flex items-center justify-center w-8 h-8 text-sm font-bold text-white transition-transform rounded-full bg-gradient-to-br from-blue-400 to-purple-500 hover:scale-110">
                {{ authStore.user?.name?.charAt(0).toUpperCase() }}
              </div>
              <span class="font-medium text-white">
                {{ authStore.user?.name }}
              </span>
            </div>

            <button
              @click="logout"
              class="flex items-center gap-2 px-4 py-2 text-white transition-all rounded-lg bg-red-500/90 hover:bg-red-600 hover:scale-105 backdrop-blur-sm"
            >
              <i class="fas fa-sign-out-alt"></i>
              <span class="hidden md:inline">Cerrar Sesión</span>
            </button>
          </div>
        </div>
      </header>

      <!-- CONTENIDO -->
      <main class="container px-6 py-10 mx-auto text-white" v-if="!loading">
        <!-- BIENVENIDA -->
        <div class="mb-10 transition-all duration-500 transform hover:scale-[1.02]">
          <div class="p-8 border shadow-xl rounded-3xl bg-gradient-to-r from-yellow-500/20 to-orange-500/20 backdrop-blur-xl border-white/20">
            <h2 class="mb-2 text-4xl font-bold">
              👋 Bienvenido, {{ authStore.user?.name }}!
            </h2>
            <p class="text-lg text-gray-300">
              Gestiona tus reportes ciudadanos y da seguimiento a cada incidencia.
            </p>
          </div>
        </div>

        <!-- CARDS ESTADÍSTICAS -->
        <div class="grid gap-6 mb-12 md:grid-cols-3">
          <div class="relative p-6 overflow-hidden transition-all duration-300 transform border shadow-xl group bg-white/10 backdrop-blur-xl rounded-2xl hover:shadow-2xl hover:scale-105 border-white/20">
            <div class="absolute top-0 right-0 w-32 h-32 transition-opacity rounded-bl-full opacity-10 group-hover:opacity-20 bg-gradient-to-br from-yellow-400 to-transparent"></div>
            <div class="relative">
              <h3 class="flex items-center gap-2 mb-2 text-lg font-semibold">
                <span class="text-2xl">📄</span> Reportes Totales
              </h3>
              <p class="mb-2 text-5xl font-extrabold text-yellow-400">{{ totalReportes }}</p>
              <p class="text-sm text-gray-300">Todos tus reportes</p>
            </div>
          </div>

          <div class="relative p-6 overflow-hidden transition-all duration-300 transform border shadow-xl group bg-white/10 backdrop-blur-xl rounded-2xl hover:shadow-2xl hover:scale-105 border-white/20">
            <div class="absolute top-0 right-0 w-32 h-32 transition-opacity rounded-bl-full opacity-10 group-hover:opacity-20 bg-gradient-to-br from-orange-400 to-transparent"></div>
            <div class="relative">
              <h3 class="flex items-center gap-2 mb-2 text-lg font-semibold">
                <span class="text-2xl">⏳</span> Pendientes
              </h3>
              <p class="mb-2 text-5xl font-extrabold text-orange-400">{{ pendientes }}</p>
              <p class="text-sm text-gray-300">En espera de respuesta</p>
            </div>
          </div>

          <div class="relative p-6 overflow-hidden transition-all duration-300 transform border shadow-xl group bg-white/10 backdrop-blur-xl rounded-2xl hover:shadow-2xl hover:scale-105 border-white/20">
            <div class="absolute top-0 right-0 w-32 h-32 transition-opacity rounded-bl-full opacity-10 group-hover:opacity-20 bg-gradient-to-br from-green-400 to-transparent"></div>
            <div class="relative">
              <h3 class="flex items-center gap-2 mb-2 text-lg font-semibold">
                <span class="text-2xl">✅</span> Resueltos
              </h3>
              <p class="mb-2 text-5xl font-extrabold text-green-400">{{ resueltos }}</p>
              <p class="text-sm text-gray-300">Completados exitosamente</p>
            </div>
          </div>
        </div>

        <!-- MAPA INTERACTIVO DE MIS REPORTES -->
        <div class="p-8 border shadow-2xl bg-white/10 backdrop-blur-xl rounded-3xl border-white/20">
          <div class="flex flex-col items-start justify-between gap-4 mb-6 md:flex-row md:items-center">
            <div>
              <h3 class="mb-2 text-2xl font-bold">🗺️ Mis Reportes en el Mapa</h3>
              <p class="text-gray-300">Ubicación geográfica de tus reportes ciudadanos</p>
            </div>
            <div class="flex gap-3">
              <button
                @click="refreshMap"
                class="inline-flex items-center gap-2 px-4 py-2 font-medium text-white transition-all transform rounded-lg bg-white/10 hover:bg-white/20 hover:scale-105"
              >
                <i class="fas fa-sync-alt"></i>
                Actualizar
              </button>
            </div>
          </div>
          
          <BasicMap
            :incidencias="reportesConUbicacion"
          />
        </div>

        <!-- SECCIÓN DE REPORTES -->
        <div class="p-8 border shadow-2xl bg-white/10 backdrop-blur-xl rounded-3xl border-white/20">
          <!-- Header -->
          <div class="flex flex-col items-start justify-between gap-4 mb-8 md:flex-row md:items-center">
            <div>
              <h3 class="mb-2 text-2xl font-bold">Mis Reportes</h3>
              <p class="text-gray-300">Gestiona y da seguimiento a tus incidencias</p>
            </div>
            <button
              @click="abrirModalCrear"
              class="inline-flex items-center gap-2 px-6 py-3 font-bold text-black transition-all transform rounded-full shadow-lg bg-gradient-to-r from-yellow-400 via-yellow-500 to-yellow-600 hover:from-yellow-500 hover:via-yellow-600 hover:to-yellow-700 whitespace-nowrap hover:scale-105 hover:shadow-yellow-500/50"
            >
              <i class="fas fa-plus-circle"></i> Crear Nuevo Reporte
            </button>
          </div>

          <!-- Filtros -->
          <div class="flex flex-wrap gap-3 mb-6">
            <button
              v-for="filtro in filtros"
              :key="filtro"
              @click="filtroActivo = filtroActivo === filtro ? null : filtro"
              :class="[
                'px-5 py-2.5 rounded-full font-medium transition-all duration-300 transform',
                filtroActivo === filtro
                  ? 'bg-gradient-to-r from-yellow-400 to-yellow-600 text-black scale-105 shadow-lg'
                  : 'bg-white/10 text-white hover:bg-white/20 border border-white/20 hover:scale-105'
              ]"
            >
              {{ filtro === 'pendiente' ? '⏳ Pendientes' : filtro === 'resuelto' ? '✅ Resueltos' : '📄 Todos' }}
            </button>
          </div>

          <!-- Tabla de Reportes -->
          <div v-if="reportesFiltrados.length > 0" class="overflow-x-auto">
            <table class="w-full text-left">
              <thead class="border-b-2 border-white/20">
                <tr>
                  <th class="px-4 py-3 text-left text-gray-300">Imagen</th>
                  <th class="px-4 py-3 text-left text-gray-300">Título</th>
                  <th class="px-4 py-3 text-left text-gray-300">Estado</th>
                  <th class="px-4 py-3 text-left text-gray-300">Fecha</th>
                  <th class="px-4 py-3 text-center text-gray-300">Votos</th>
                  <th class="px-4 py-3 text-right text-gray-300">Acciones</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="reporte in reportesFiltrados" :key="reporte.id" class="transition-all border-b border-white/10 hover:bg-white/5 group">
                  <td class="px-4 py-4">
                    <img 
                      :src="reporte.imagen ? `/storage/${reporte.imagen}` : '/images/no-image.png'" 
                      class="object-cover h-12 shadow-lg rounded-xl cursor-pointer hover:scale-110 transition-transform" 
                      alt="imagen"
                      @click="verImagenAmpliada(reporte.imagen)"
                      @error="handleImageError"
                    />
                  </td>
                  <td class="px-4 py-4">
                    <span class="font-medium transition-colors group-hover:text-yellow-400">{{ reporte.titulo }}</span>
                  </td>
                  <td class="px-4 py-4">
                    <span
                      :class="[
                        'px-4 py-2 rounded-full text-sm font-medium inline-block transition-all transform group-hover:scale-105',
                        reporte.estado === 'resuelto'
                          ? 'bg-gradient-to-r from-green-500/20 to-green-600/20 text-green-300 border border-green-500/30'
                          : 'bg-gradient-to-r from-orange-500/20 to-orange-600/20 text-orange-300 border border-orange-500/30'
                      ]"
                    >
                      {{ reporte.estado === 'resuelto' ? '✅ Resuelto' : '⏳ Pendiente' }}
                    </span>
                  </td>
                  <td class="px-4 py-4 text-gray-300">{{ formatearFecha(reporte.fecha) }}</td>
                  <td class="px-4 py-4 text-center">
                    <span v-if="reporte.votos" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full text-xs font-medium bg-green-500/20 text-green-300 border border-green-500/30">
                      <i class="fas fa-thumbs-up"></i>
                      {{ reporte.votos.positivos }}/{{ reporte.votos.total }}
                    </span>
                    <span v-else class="text-gray-500 text-xs">Cargando...</span>
                  </td>
                  <td class="px-4 py-4 text-right">
                    <div class="flex justify-end gap-2">
                      <button
                        @click="verDetalles(reporte.id)"
                        class="p-2 font-medium text-blue-400 transition-all transform rounded-lg hover:bg-blue-500/20 hover:scale-110 hover:text-blue-300"
                        title="Ver detalles"
                      >
                        <i class="fas fa-eye"></i>
                      </button>
                      <button
                        @click="editarReporte(reporte.id)"
                        class="p-2 font-medium text-yellow-400 transition-all transform rounded-lg hover:bg-yellow-500/20 hover:scale-110 hover:text-yellow-300"
                        title="Editar"
                      >
                        <i class="fas fa-edit"></i>
                      </button>
                      <button
                        @click="eliminarReporte(reporte.id)"
                        class="p-2 font-medium text-red-400 transition-all transform rounded-lg hover:bg-red-500/20 hover:scale-110 hover:text-red-300"
                        title="Eliminar"
                      >
                        <i class="fas fa-trash"></i>
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Estado vacío -->
          <div v-else class="py-12 text-center">
            <div class="inline-block p-8 mb-4 transition-all rounded-full bg-white/10 hover:bg-white/20">
              <i class="text-6xl">📭</i>
            </div>
            <p class="mb-4 text-lg text-gray-400">No hay reportes para mostrar</p>
            <button
              @click="abrirModalCrear"
              class="inline-flex items-center gap-2 px-6 py-3 font-medium text-yellow-400 transition-all hover:text-yellow-300 hover:scale-105"
            >
              <i class="fas fa-plus-circle"></i> Crea tu primer reporte →
            </button>
          </div>

          <!-- Paginación -->
          <div v-if="totalPages > 1" class="flex justify-center mt-8">
            <Pagination
              :current-page="currentPage"
              :total-pages="totalPages"
              @page-changed="cambiarPagina"
              :max-visible-pages="5"
              class="text-white"
            />
          </div>
        </div>

      </main>

      <!-- MODAL EDITAR REPORTE -->
      <transition enter-active-class="transition duration-300" enter-from-class="opacity-0" enter-to-class="opacity-100">
        <div v-if="modalEditar" class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm" @click="cerrarModal">
          <div class="w-full max-w-2xl max-h-[90vh] overflow-y-auto p-6 mx-4 transition-all transform bg-gray-900 rounded-3xl shadow-2xl border border-white/20" @click.stop>
            <div class="flex items-center justify-between mb-6">
              <h3 class="text-2xl font-bold text-white">✏️ Editar Reporte</h3>
              <button @click="cerrarModal" class="text-gray-400 transition hover:text-white">
                <i class="text-2xl fas fa-times"></i>
              </button>
            </div>
            <form @submit.prevent="guardarEdicion" class="space-y-5">
              <div>
                <label class="block mb-2 text-sm font-medium text-gray-300">Título del reporte</label>
                <input
                  v-model="reporteEditando.titulo"
                  type="text"
                  class="w-full px-4 py-3 text-white transition-all border rounded-xl bg-white/5 border-white/20 focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 focus:outline-none"
                  required
                />
              </div>
              <div>
                <label class="block mb-2 text-sm font-medium text-gray-300">Categoría</label>
                <select
                  v-model="reporteEditando.categoria_id"
                  class="w-full px-4 py-3 text-white transition-all border rounded-xl bg-white/5 border-white/20 focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 focus:outline-none"
                  required
                >
                  <option value="" disabled>Selecciona una categoría</option>
                  <option v-for="cat in categorias" :key="cat.id" :value="cat.id" :disabled="cat.disabled">{{ cat.nombre }}</option>
                </select>
              </div>
              <div>
                <label class="block mb-2 text-sm font-medium text-gray-300">Descripción</label>
                <textarea
                  v-model="reporteEditando.descripcion"
                  class="w-full px-4 py-3 text-white transition-all border rounded-xl bg-white/5 border-white/20 focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 focus:outline-none"
                  rows="3"
                  required
                ></textarea>
              </div>
              <div>
                <label class="block mb-2 text-sm font-medium text-gray-300">Ubicación en el mapa</label>
                <div class="relative overflow-hidden border rounded-xl border-white/20">
                  <div ref="mapEdit" class="w-full h-56"></div>
                  <button
                    type="button"
                    @click="obtenerUbicacionActual('edit')"
                    class="absolute top-3 right-3 px-3 py-2 text-sm font-medium text-black transition-all rounded-lg bg-yellow-500 hover:bg-yellow-600 shadow-lg z-[1000] flex items-center gap-2"
                  >
                    <i class="fas fa-location-crosshairs"></i> Ubicación Actual
                  </button>
                  <div v-if="reporteEditando.latitud && reporteEditando.longitud" class="absolute bottom-3 left-3 px-3 py-1.5 text-sm text-white bg-black/70 rounded-lg backdrop-blur-sm flex items-center gap-2 z-[1000]">
                    <i class="text-red-400 fas fa-map-marker-alt"></i>
                    {{ parseFloat(reporteEditando.latitud).toFixed(4) }}, {{ parseFloat(reporteEditando.longitud).toFixed(4) }}
                  </div>
                </div>
              </div>
              <div>
                <label class="block mb-2 text-sm font-medium text-gray-300">Dirección</label>
                <div class="relative">
                  <i class="absolute text-gray-400 -translate-y-1/2 left-4 top-1/2 fas fa-map-pin"></i>
                  <input
                    v-model="reporteEditando.direccion"
                    type="text"
                    class="w-full py-3 pr-4 text-white transition-all border pl-11 rounded-xl bg-white/5 border-white/20 focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 focus:outline-none"
                    required
                  />
                </div>
              </div>
              <div>
                <label class="block mb-2 text-sm font-medium text-gray-300">Fotografía</label>
                <input
                  @change="onFotoChange($event, 'edit')"
                  type="file"
                  accept="image/*"
                  class="w-full px-4 py-3 text-white transition-all border cursor-pointer rounded-xl bg-white/5 border-white/20 focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 focus:outline-none file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-yellow-500 file:text-black hover:file:bg-yellow-600"
                />
                <div v-if="reporteEditando.previewUrl" class="mt-3">
                  <img :src="reporteEditando.previewUrl" class="object-cover h-32 shadow-lg rounded-xl" alt="previsualizacion" />
                </div>
              </div>
              <div class="flex justify-end gap-3 pt-4 border-t border-white/10">
                <button
                  type="button"
                  @click="cerrarModal"
                  class="px-6 py-3 text-sm font-medium text-gray-300 transition-all rounded-xl bg-white/10 hover:bg-white/20"
                >
                  Cancelar
                </button>
                <button
                  type="submit"
                  class="px-8 py-3 text-sm font-bold text-black transition-all shadow-lg rounded-xl bg-gradient-to-r from-yellow-400 to-yellow-600 hover:from-yellow-500 hover:to-yellow-700 hover:shadow-yellow-500/30"
                >
                  <i class="mr-2 fas fa-save"></i>Guardar Cambios
                </button>
              </div>
            </form>
          </div>
        </div>
      </transition>

      <!-- MODAL CREAR REPORTE -->
      <transition enter-active-class="transition duration-300" enter-from-class="opacity-0" enter-to-class="opacity-100">
        <div v-if="modalCrear" class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm" @click="cerrarModal">
          <div class="w-full max-w-2xl max-h-[90vh] overflow-y-auto p-6 mx-4 transition-all transform bg-gray-900 rounded-3xl shadow-2xl border border-white/20" @click.stop>
            <div class="flex items-center justify-between mb-6">
              <h3 class="text-2xl font-bold text-white">📝 Crear Nuevo Reporte</h3>
              <button @click="cerrarModal" class="text-gray-400 transition hover:text-white">
                <i class="text-2xl fas fa-times"></i>
              </button>
            </div>
            <form @submit.prevent="guardarNuevoReporte" class="space-y-5">
              <div>
                <label class="block mb-2 text-sm font-medium text-gray-300">Título del reporte</label>
                <input
                  v-model="nuevoReporte.titulo"
                  type="text"
                  class="w-full px-4 py-3 text-white placeholder-gray-500 transition-all border rounded-xl bg-white/5 border-white/20 focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 focus:outline-none"
                  placeholder="Ej: Bache en calle principal"
                  required
                />
              </div>
              <div>
                <label class="block mb-2 text-sm font-medium text-gray-300">Categoría</label>
                <select
                  v-model="nuevoReporte.categoria_id"
                  class="w-full px-4 py-3 text-white transition-all border rounded-xl bg-white/5 border-white/20 focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 focus:outline-none"
                  required
                >
                  <option value="" disabled>Selecciona una categoría</option>
                  <option v-for="cat in categorias" :key="cat.id" :value="cat.id" :disabled="cat.disabled">{{ cat.nombre }}</option>
                </select>
              </div>
              <div>
                <label class="block mb-2 text-sm font-medium text-gray-300">Descripción</label>
                <textarea
                  v-model="nuevoReporte.descripcion"
                  class="w-full px-4 py-3 text-white placeholder-gray-500 transition-all border rounded-xl bg-white/5 border-white/20 focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 focus:outline-none"
                  rows="3"
                  placeholder="Describe el problema con detalle..."
                  required
                ></textarea>
              </div>
              <div>
                <label class="block mb-2 text-sm font-medium text-gray-300">Ubicación en el mapa</label>
                <div class="relative overflow-hidden border rounded-xl border-white/20">
                  <div ref="mapNew" class="w-full h-56"></div>
                  <button
                    type="button"
                    @click="obtenerUbicacionActual('new')"
                    class="absolute top-3 right-3 px-3 py-2 text-sm font-medium text-black transition-all rounded-lg bg-yellow-500 hover:bg-yellow-600 shadow-lg z-[1000] flex items-center gap-2"
                  >
                    <i class="fas fa-location-crosshairs"></i> Ubicación Actual
                  </button>
                  <div v-if="nuevoReporte.latitud && nuevoReporte.longitud" class="absolute bottom-3 left-3 px-3 py-1.5 text-sm text-white bg-black/70 rounded-lg backdrop-blur-sm flex items-center gap-2 z-[1000]">
                    <i class="text-red-400 fas fa-map-marker-alt"></i>
                    {{ parseFloat(nuevoReporte.latitud).toFixed(4) }}, {{ parseFloat(nuevoReporte.longitud).toFixed(4) }}
                  </div>
                </div>
              </div>
              <div>
                <label class="block mb-2 text-sm font-medium text-gray-300">Dirección</label>
                <div class="relative">
                  <i class="absolute text-gray-400 -translate-y-1/2 left-4 top-1/2 fas fa-map-pin"></i>
                  <input
                    v-model="nuevoReporte.direccion"
                    type="text"
                    class="w-full py-3 pr-4 text-white placeholder-gray-500 transition-all border pl-11 rounded-xl bg-white/5 border-white/20 focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 focus:outline-none"
                    placeholder="Calle, número, colonia, ciudad..."
                    required
                  />
                </div>
              </div>
              <div>
                <label class="block mb-2 text-sm font-medium text-gray-300">Fotografía (obligatoria) *</label>
                <div class="relative">
                  <input
                    @change="onFotoChange($event, 'new')"
                    type="file"
                    accept="image/*"
                    required
                    class="w-full px-4 py-3 text-white transition-all border cursor-pointer rounded-xl bg-white/5 border-white/20 focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 focus:outline-none file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-yellow-500 file:text-black hover:file:bg-yellow-600"
                  />
                </div>
                <div v-if="nuevoReporte.previewUrl" class="mt-3">
                  <img :src="nuevoReporte.previewUrl" class="object-cover h-32 shadow-lg rounded-xl" alt="previsualizacion" />
                </div>
              </div>
              <div class="flex justify-end gap-3 pt-4 border-t border-white/10">
                <button
                  type="button"
                  @click="cerrarModal"
                  class="px-6 py-3 text-sm font-medium text-gray-300 transition-all rounded-xl bg-white/10 hover:bg-white/20"
                >
                  Cancelar
                </button>
                <button
                  type="submit"
                  class="px-8 py-3 text-sm font-bold text-black transition-all shadow-lg rounded-xl bg-gradient-to-r from-yellow-400 to-yellow-600 hover:from-yellow-500 hover:to-yellow-700 hover:shadow-yellow-500/30"
                >
                  <i class="mr-2 fas fa-paper-plane"></i>Enviar Reporte
                </button>
              </div>
            </form>
          </div>
        </div>
      </transition>

      <!-- MODAL IMAGEN AMPLIADA -->
      <transition enter-active-class="transition duration-300" enter-from-class="opacity-0" enter-to-class="opacity-100">
        <div v-if="modalImagen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/90 backdrop-blur-sm" @click="cerrarModalImagen">
          <div class="relative max-w-4xl max-h-[90vh] mx-4" @click.stop>
            <button 
              @click="cerrarModalImagen" 
              class="absolute top-4 right-4 z-10 p-3 text-white transition-all bg-black/50 rounded-full hover:bg-black/70"
            >
              <i class="fas fa-times text-xl"></i>
            </button>
            <img 
              :src="imagenAmpliada" 
              class="max-w-full max-h-[80vh] object-contain rounded-lg shadow-2xl"
              alt="Imagen ampliada"
              @error="cerrarModalImagen"
            />
          </div>
        </div>
      </transition>

    </div>
  </div>
</template>

<script>
import { useAuthStore } from "../../stores/auth"
import api from "../../services/api"
import Pagination from "@ocrv/vue-tailwind-pagination"
import BasicMap from "../../Components/BasicMap.vue"

export default {
  name: "Dashboard",
  components: {
    Pagination,
    BasicMap
  },
  data() {
    return {
      reportes: [],
      categorias: [],
      defaultCategorias: [
        { id: 1, nombre_categoria: 'Infraestructura', descripcion: 'Baches, banquetas, guarniciones y daños en la vía pública' },
        { id: 2, nombre_categoria: 'Alumbrado Público', descripcion: 'Farolas fundidas, postes dañados o sin funcionamiento' },
        { id: 3, nombre_categoria: 'Limpieza', descripcion: 'Recolección de basura, limpieza de calles y áreas públicas' },
        { id: 4, nombre_categoria: 'Agua Potable', descripcion: 'Fugas, tuberías rotas o problemas con el suministro de agua' },
        { id: 5, nombre_categoria: 'Áreas Verdes', descripcion: 'Parques, jardines, poda de árboles y mantenimiento de zonas verdes' }
      ],
      totalReportes: 0,
      pendientes: 0,
      resueltos: 0,
      loading: true,
      filtroActivo: null,
      filtros: ['todos', 'pendiente', 'resuelto'],
      modalEditar: false,
      modalCrear: false,
      modalImagen: false,
      imagenAmpliada: null,
      mapEdit: null,
      mapNew: null,
      markerEdit: null,
      markerNew: null,
      currentPage: 1,
      totalPages: 1,
      reporteEditando: {
        id: null,
        titulo: '',
        descripcion: '',
        categoria_id: null,
        latitud: '',
        longitud: '',
        direccion: '',
        foto: null,
        previewUrl: ''
      },
      nuevoReporte: {
        titulo: '',
        descripcion: '',
        categoria_id: null,
        latitud: '',
        longitud: '',
        direccion: '',
        foto: null,
        previewUrl: ''
      }
    }
  },

  computed: {
    authStore() {
      return useAuthStore()
    },

    reportesFiltrados() {
      if (!this.filtroActivo || this.filtroActivo === 'todos') {
        return this.reportes
      }
      return this.reportes.filter(r => r.estado === this.filtroActivo)
    },
    reportesConUbicacion() {
      console.log('🔍 Depurando reportesConUbicacion en User Dashboard')
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
    },
    mapCenter() {
      // Calcular el centro basado en los reportes del usuario
      const reportesConUbicacion = this.reportesConUbicacion
      if (reportesConUbicacion.length === 0) {
        return { lat: -19.036, lng: -65.259 } // Sucre, Bolivia por defecto
      }
      
      // Calcular el punto medio
      const avgLat = reportesConUbicacion.reduce((sum, r) => sum + r.latitud, 0) / reportesConUbicacion.length
      const avgLng = reportesConUbicacion.reduce((sum, r) => sum + r.longitud, 0) / reportesConUbicacion.length
      
      return { lat: avgLat, lng: avgLng }
    }
  },

  async mounted() {
    if (!this.authStore.isLoggedIn) {
     this.$router.push("/login")
     return
    }
   await this.cargarDatos()

  },

  methods: {
    async cargarDatos() {
      this.loading = true

      try {
        const userId = this.authStore.user?.id

        await this.cargarCategorias()

        // Obtener solo los reportes del usuario actual usando endpoint específico
        const response = await api.get(`/mis-incidencias?page=${this.currentPage}`)
        const paginatedData = response.data

        this.reportes = paginatedData.data.map(r => ({
          id: r.id,
          titulo: r.titulo,
          descripcion: r.descripcion,
          categoria_id: r.categoria_id,
          latitud: r.latitud,
          longitud: r.longitud,
          direccion: r.direccion,
          imagen: r.imagen,
          estado: r.estado || 'pendiente',
          fecha: r.created_at,
          user_id: r.user_id,
          votos: null
        }))

        await this.cargarVotosReportes()

        this.totalPages = paginatedData.last_page || 1
        this.currentPage = paginatedData.current_page || 1

        // Calcular estadísticas basadas en los reportes del usuario
        this.totalReportes = this.reportes.length
        this.pendientes = this.reportes.filter(r => r.estado === "pendiente").length
        this.resueltos = this.reportes.filter(r => r.estado === "resuelto").length

        console.log(`Cargados ${this.reportes.length} reportes para el usuario ${userId}`)

      } catch (error) {
        console.error("Error al cargar datos:", error)
        // Si falla la petición con user_id, intentar con la petición general
        try {
          const fallbackResponse = await api.get(`/incidencias?page=${this.currentPage}`)
          const fallbackData = fallbackResponse.data
          
          const userId = this.authStore.user?.id
          this.reportes = fallbackData.data
            .filter(r => r.user_id === userId)
            .map(r => ({
              id: r.id,
              titulo: r.titulo,
              descripcion: r.descripcion,
              categoria_id: r.categoria_id,
              latitud: r.latitud,
              longitud: r.longitud,
              direccion: r.direccion,
              imagen: r.imagen,
              estado: r.estado || 'pendiente',
              fecha: r.created_at,
              user_id: r.user_id,
              votos: null
            }))

          await this.cargarVotosReportes()

          this.totalPages = 1
          this.currentPage = 1
          this.totalReportes = this.reportes.length
          this.pendientes = this.reportes.filter(r => r.estado === "pendiente").length
          this.resueltos = this.reportes.filter(r => r.estado === "resuelto").length
          
          console.log(`Usando fallback: ${this.reportes.length} reportes encontrados`)
        } catch (fallbackError) {
          console.error("Error en fallback:", fallbackError)
          this.reportes = []
          this.totalReportes = 0
          this.pendientes = 0
          this.resueltos = 0
        }
      } finally {
        this.loading = false
      }
    },

    async cargarCategorias() {
      try {
        const responseCategorias = await api.get('/categorias')
        const backendCategorias = responseCategorias.data.map(c => ({
          id: c.id,
          nombre: c.nombre_categoria || c.nombre,
          descripcion: c.descripcion,
          disabled: false
        }))

        const missingCategorias = this.defaultCategorias
          .filter(defaultCat => !backendCategorias.some(backendCat => backendCat.nombre === defaultCat.nombre_categoria))
          .map(defaultCat => ({
            id: defaultCat.id,
            nombre: defaultCat.nombre_categoria,
            descripcion: defaultCat.descripcion,
            disabled: true
          }))

        this.categorias = [...backendCategorias, ...missingCategorias].sort((a, b) => a.nombre.localeCompare(b.nombre, 'es', { sensitivity: 'base' }))

        if (!this.categorias.length) {
          this.categorias = this.defaultCategorias.map(defaultCat => ({
            id: defaultCat.id,
            nombre: defaultCat.nombre_categoria,
            descripcion: defaultCat.descripcion,
            disabled: true
          }))
        }

        if (!this.nuevoReporte.categoria_id || !this.categorias.some(cat => cat.id === this.nuevoReporte.categoria_id && !cat.disabled)) {
          this.nuevoReporte.categoria_id = this.categorias.find(cat => !cat.disabled)?.id || null
        }
      } catch (error) {
        console.error('Error al cargar categorías:', error)
      }
    },
    
    async cargarVotosReportes() {
      for (const reporte of this.reportes) {
        try {
          const response = await api.get(`/incidencias/${reporte.id}/votos`)
          if (response.data.success) {
            reporte.votos = {
              positivos: response.data.data.positivos,
              total: response.data.data.total
            }
          }
        } catch (error) {
          reporte.votos = { positivos: 0, total: 0 }
        }
      }
    },

    refreshMap() {
      this.cargarDatos()
    },
    verImagenAmpliada(imagen) {
      if (!imagen) return
      this.imagenAmpliada = `/storage/${imagen}`
      this.modalImagen = true
    },
    cerrarModalImagen() {
      this.modalImagen = false
      this.imagenAmpliada = null
    },
    handleImageError(event) {
      event.target.src = '/images/no-image.png'
    },

    getFirstEnabledCategoriaId() {
      return this.categorias.find(cat => !cat.disabled)?.id || null
    },

    formatearFecha(fecha) {
      const date = new Date(fecha)
      return date.toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      })
    },

    logout() {
      this.authStore.logout()
      this.$router.push("/login")
    },

    async abrirModalCrear() {
      await this.cargarCategorias()
      this.nuevoReporte.categoria_id = this.getFirstEnabledCategoriaId()
      this.modalCrear = true
      this.$nextTick(() => {
        this.initMap('new')
      })
    },

    verDetalles(id) {
      this.$router.push(`/reportes/${id}`)
    },

    async editarReporte(id) {
      await this.cargarCategorias()
      const reporte = this.reportes.find(r => r.id === id)
      if (reporte) {
        this.reporteEditando = { ...reporte }
        if (!this.categorias.some(cat => cat.id === this.reporteEditando.categoria_id && !cat.disabled)) {
          this.reporteEditando.categoria_id = this.getFirstEnabledCategoriaId()
        }
        this.modalEditar = true
        this.$nextTick(() => {
          this.initMap('edit')
        })
      }
    },

    async initMap(type) {
      if (typeof L === 'undefined') {
        const link = document.createElement('link')
        link.rel = 'stylesheet'
        link.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css'
        document.head.appendChild(link)
        
        const script = document.createElement('script')
        script.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js'
        script.async = true
        document.head.appendChild(script)
        script.onload = () => this.createMap(type)
      } else {
        this.createMap(type)
      }
    },

    createMap(type) {
      const mapRef = type === 'edit' ? this.$refs.mapEdit : this.$refs.mapNew
      const lat = type === 'edit' ? parseFloat(this.reporteEditando.latitud) || -19.036 : parseFloat(this.nuevoReporte.latitud) || -19.036
      const lng = type === 'edit' ? parseFloat(this.reporteEditando.longitud) || -65.259 : parseFloat(this.nuevoReporte.longitud) || -65.259

      const map = L.map(mapRef).setView([lat, lng], 15)

      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
      }).addTo(map)

      const marker = L.marker([lat, lng], { draggable: true }).addTo(map)

      if (type === 'edit') {
        this.mapEdit = map
        this.markerEdit = marker
      } else {
        this.mapNew = map
        this.markerNew = marker
      }

      marker.on('dragend', async (event) => {
        const latlng = event.target.getLatLng()
        if (type === 'edit') {
          this.reporteEditando.latitud = latlng.lat
          this.reporteEditando.longitud = latlng.lng
          this.reporteEditando.direccion = await this.obtenerDireccion(latlng.lat, latlng.lng)
        } else {
          this.nuevoReporte.latitud = latlng.lat
          this.nuevoReporte.longitud = latlng.lng
          this.nuevoReporte.direccion = await this.obtenerDireccion(latlng.lat, latlng.lng)
        }
      })

      map.on('click', async (event) => {
        const latlng = event.latlng
        marker.setLatLng(latlng)
        if (type === 'edit') {
          this.reporteEditando.latitud = latlng.lat
          this.reporteEditando.longitud = latlng.lng
          this.reporteEditando.direccion = await this.obtenerDireccion(latlng.lat, latlng.lng)
        } else {
          this.nuevoReporte.latitud = latlng.lat
          this.nuevoReporte.longitud = latlng.lng
          this.nuevoReporte.direccion = await this.obtenerDireccion(latlng.lat, latlng.lng)
        }
      })
    },

    async obtenerUbicacionActual(type) {
      if (!navigator.geolocation) {
        alert('La geolocalización no está soportada por tu navegador')
        return
      }

      const btn = event?.target?.closest('button')
      if (btn) {
        btn.disabled = true
        btn.dataset.originalText = btn.innerHTML
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>'
      }

      navigator.geolocation.getCurrentPosition(
        async (position) => {
          const lat = position.coords.latitude
          const lng = position.coords.longitude

          if (type === 'edit') {
            this.reporteEditando.latitud = lat
            this.reporteEditando.longitud = lng
            this.reporteEditando.direccion = await this.obtenerDireccion(lat, lng)
            if (this.mapEdit && this.markerEdit) {
              this.mapEdit.setView([lat, lng], 15)
              this.markerEdit.setLatLng([lat, lng])
            }
          } else {
            this.nuevoReporte.latitud = lat
            this.nuevoReporte.longitud = lng
            this.nuevoReporte.direccion = await this.obtenerDireccion(lat, lng)
            if (this.mapNew && this.markerNew) {
              this.mapNew.setView([lat, lng], 15)
              this.markerNew.setLatLng([lat, lng])
            }
          }

          if (btn) {
            btn.innerHTML = '<i class="fas fa-check"></i>'
            setTimeout(() => {
              btn.innerHTML = btn.dataset.originalText
              btn.disabled = false
            }, 1500)
          }
        },
        (error) => {
          console.error('Error al obtener ubicación:', error)
          alert('No se pudo obtener tu ubicación. Verifica los permisos.')
          if (btn) {
            btn.innerHTML = btn.dataset.originalText
            btn.disabled = false
          }
        }
      )
    },

    async obtenerDireccion(lat, lng) {
      try {
        const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1`, {
          headers: {
            'User-Agent': 'ReporteCiudadanos/1.0'
          }
        })
        const data = await response.json()
        return data.display_name || ''
      } catch (error) {
        console.error('Error al obtener dirección:', error)
        return ''
      }
    },

    async guardarEdicion() {
      try {
        const formData = new FormData()
        formData.append('titulo', this.reporteEditando.titulo)
        formData.append('descripcion', this.reporteEditando.descripcion)
        formData.append('categoria_id', this.reporteEditando.categoria_id)
        formData.append('latitud', this.reporteEditando.latitud)
        formData.append('longitud', this.reporteEditando.longitud)
        if (this.reporteEditando.foto instanceof File) {
          formData.append('imagen', this.reporteEditando.foto)
        }
        formData.append('estado', this.reporteEditando.estado)
        formData.append('_method', 'PUT')

      await api.post(`/incidencias/${this.reporteEditando.id}`, formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          }
        })

      await this.cargarDatos()
      this.cerrarModal()
      alert('✅ Reporte actualizado exitosamente')
      } catch (error) {
        console.error('Error al guardar:', error)
      alert('❌ Error al guardar los cambios')
      }
    },
    
    async guardarNuevoReporte() {
      if (!this.nuevoReporte.latitud || !this.nuevoReporte.longitud) {
        alert('❌ Debes seleccionar una ubicación en el mapa')
        return
      }
      if (!this.nuevoReporte.foto || !(this.nuevoReporte.foto instanceof File)) {
        alert('❌ Debes subir una fotografía del reporte')
        return
      }
      try {
        const formData = new FormData()
        formData.append('titulo', this.nuevoReporte.titulo)
        formData.append('descripcion', this.nuevoReporte.descripcion)
        formData.append('categoria_id', this.nuevoReporte.categoria_id)
        formData.append('latitud', this.nuevoReporte.latitud)
        formData.append('longitud', this.nuevoReporte.longitud)
        formData.append('direccion', this.nuevoReporte.direccion)
        formData.append('user_id', this.authStore.user.id)

        formData.append('imagen', this.nuevoReporte.foto)

       const response = await api.post('/incidencias', formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          }
        })

   await this.cargarDatos()
   this.cerrarModal()
   alert('✅ Reporte creado exitosamente')
      } catch (error) {
        console.error('Error al crear:', error)
     if (error.response?.data?.errors) {
          console.log('Errores de validación:', error.response.data.errors)
          const mensajes = Object.values(error.response.data.errors).flat().join('\n')
       alert(`❌ Error al crear el reporte:\n${mensajes}`)
        } else {
       alert('❌ Error al crear el reporte')
        }
      }
    },

    async eliminarReporte(id) {
      if (confirm('¿Estás seguro de que deseas eliminar este reporte?')) {
        try {
        await api.delete(`/incidencias/${id}`)
         
        await this.cargarDatos()
        alert('✅ Reporte eliminado exitosamente')
        } catch (error) {
          console.error('Error al eliminar:', error)
        alert('❌ Error al eliminar el reporte')
        }
      }
    },

    onFotoChange(e, type) {
      const file = e.target.files[0]
      if (!file) return
      const url = URL.createObjectURL(file)
      if (type === 'edit') {
        this.reporteEditando.foto = file
        this.reporteEditando.previewUrl = url
      } else {
        this.nuevoReporte.foto = file
        this.nuevoReporte.previewUrl = url
      }
    },

    cambiarPagina(page) {
      this.currentPage = page
      this.cargarDatos()
    },

    cerrarModal() {
      this.modalEditar = false
      this.modalCrear = false
      this.reporteEditando = {
        id: null,
        titulo: '',
        descripcion: '',
        categoria_id: null,
        latitud: '',
        longitud: '',
        direccion: '',
        foto: null,
        previewUrl: ''
      }
      this.nuevoReporte = {
        titulo: '',
        descripcion: '',
        categoria_id: null,
        latitud: '',
        longitud: '',
        direccion: '',
        foto: null,
        previewUrl: ''
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

main {
  animation: slideUp 0.5s ease-out;
}

@keyframes slideUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
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
