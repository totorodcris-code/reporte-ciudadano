// Importando funciones necesarias del router
import { createRouter, createWebHistory } from "vue-router"

// Importar store de autenticación
import { useAuthStore } from "@/stores/auth"

// =============================
// IMPORTAR VISTAS PÚBLICAS (Lazy Loading)
// =============================
const Home = () => import("../Views/Public/Home.vue")
const Login = () => import("../Views/Public/Login.vue")
const Register = () => import("../Views/Public/Register.vue")

// =============================
// IMPORTAR DASHBOARDS (Lazy Loading)
// =============================
const UserDashboard = () => import("../Views/User/Dashboard.vue")
const AdminDashboard = () => import("../Views/Admin/Dashboard.vue")
const OAuthSuccess = () => import("@/Views/Public/OAuthSuccess.vue")

// =============================
// IMPORTAR PÁGINAS DE INCIDENCIAS (Lazy Loading)
// =============================
const DetalleIncidencia = () => import("@/Pages/Incidencias/DetalleIncidencia.vue")
const MisIncidencias = () => import("@/Pages/Incidencias/MisIncidencias.vue")
const NuevaIncidencia = () => import("@/Pages/Incidencias/NuevaIncidencia.vue")
const PerfilUsuario = () => import("@/Pages/User/Perfil.vue")

// =============================
// IMPORTAR PÁGINAS DE ADMIN (Lazy Loading)
// =============================
const GestionUsuarios = () => import("@/Pages/Admin/GestionUsuarios.vue")
const GestionIncidencias = () => import("@/Pages/Admin/GestionIncidencias.vue")
const Analytics = () => import("@/Views/Admin/Analytics.vue")

// =============================
// IMPORTAR PÁGINAS DE VOTOS (Lazy Loading)
// =============================
const VotosDashboard = () => import("@/Components/Votos/VotosDashboard.vue")

// =============================
// DEFINICIÓN DE RUTAS
// =============================
const routes = [
  // Rutas públicas
  {
    path: '/oauth/success',
    component: OAuthSuccess,
    meta: { requiresAuth: false }
  },
  {
    path: '/',
    name: 'home',
    component: Home,
    meta: { requiresAuth: false }
  },
  {
    path: '/login',
    name: 'login',
    component: Login,
    meta: { requiresAuth: false }
  },
  {
    path: '/register',
    name: 'register',
    component: Register,
    meta: { requiresAuth: false }
  },

   //rutas privadas
  // Rutas de usuario
  {
    path: '/dashboard',
    name: 'user.dashboard',
    component: UserDashboard,
    meta: { requiresAuth: true, role: 'usuario' }
  },
  {
    path: '/reportes/:id',
    name: 'reportes.show',
    component: DetalleIncidencia,
    meta: { requiresAuth: true, role: 'usuario' }
  },
  {
    path: '/mis-reportes',
    name: 'mis-reportes',
    component: MisIncidencias,
    meta: { requiresAuth: true, role: 'usuario' }
  },
  {
    path: '/nueva-incidencia',
    name: 'nueva-incidencia',
    component: NuevaIncidencia,
    meta: { requiresAuth: true, role: 'usuario' }
  },
  {
    path: '/perfil',
    name: 'perfil',
    component: PerfilUsuario,
    meta: { requiresAuth: true, role: 'usuario' }
  },
  {
    path: '/votos',
    name: 'votos',
    component: VotosDashboard,
    meta: { requiresAuth: true, role: 'usuario' }
  },

  // Rutas de admin
  {
    path: '/admin',
    redirect: '/admin/dashboard' // Redirige al dashboard si alguien escribe /admin
  },
  {
    path: '/admin/dashboard',
    name: 'admin.dashboard',
    component: AdminDashboard,
    meta: { requiresAuth: true, requiresAdmin: true, role: 'admin' }
  },
  {
    path: '/admin/usuarios',
    name: 'admin.usuarios',
    component: GestionUsuarios,
    meta: { requiresAuth: true, requiresAdmin: true, role: 'admin' }
  },
  {
    path: '/admin/incidencias',
    name: 'admin.incidencias',
    component: GestionIncidencias,
    meta: { requiresAuth: true, requiresAdmin: true, role: 'admin' }
  },
  {
    path: '/analytics',
    name: 'analytics',
    component: Analytics,
    meta: { requiresAuth: true, requiresAdmin: true, role: 'admin' }
  },
  {
    path: '/admin/votos',
    name: 'admin.votos',
    component: VotosDashboard,
    meta: { requiresAuth: true, requiresAdmin: true, role: 'admin' }
  },

  //ruta para
  {
    path:"/pathMatch(.*)",
    redirect: "/"
  },

  // Catch-all: redirige rutas no encontradas al home
  {
    path: '/:catchAll(.*)',
    redirect: '/'
  }
]

// =============================
// CREAR ROUTER
// =============================
const router = createRouter({
  history: createWebHistory(),
  routes
})

// =============================
// GUARD DE NAVEGACIÓN GLOBAL
// =============================
router.beforeEach((to) => {
  const authStore = useAuthStore()
  const { isLoggedIn, isAdmin } = authStore

  // 🔐 Si requiere login y no está autenticado
  if (to.meta.requiresAuth && !isLoggedIn) {
    return { name: 'login' }
  }

  // 👑 Si requiere admin y no lo es
  if (to.meta.requiresAdmin && !isAdmin) {
    return { name: 'user.dashboard' }
  }

  // 🔄 Si ya está logueado y quiere ir a login/register
  if (isLoggedIn && (to.name === 'login' || to.name === 'register')) {
    return isAdmin ? { name: 'admin.dashboard' } : { name: 'user.dashboard' }
  }

  return true
})

export default router