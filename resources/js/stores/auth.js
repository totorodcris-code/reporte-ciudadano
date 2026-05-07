//importar el defineStore para crear un store global 
import {defineStore} from 'pinia';

//importando el api configurad
import api from '../services/api';

//
//funcion helper para decodigicar el JWT
function parseJwt(token) {
    try {
        const base64Url = token.split('.')[1].replace(/-/g, '+').replace(/_/g, '/');
        return JSON.parse(atob(base64Url));
    }
    catch (error) {
        return null;
    }
}

function normalizeUser(user) {
    if (!user) return null
    if (user.role == null && user.rol != null) {
        user.role = user.rol
    }
    return user
}

export const useAuthStore = defineStore("auth" , {
    state: () => ({
        //recuperamos tken si ya existe en el navegador
        token: localStorage.getItem('token') || null,

        //informacion del usuario autenticado
        user: normalizeUser(JSON.parse(localStorage.getItem('user')) || null),
    }),
    
    getters: {
        isAdmin: (state) => state.user && state.user.role === 'admin',
        isLoggedIn: (state) => !!state.token,
    },


//acciones que modifican el estado/////////////////////

    actions: {
        //acciones que modifican el estado
        async login(credentials) {
            //peticion POST hacia /api/login
            const response = await api.post('/login', credentials);

            //guardar el token recibido del backend
            this.token = response.data.access_token;

            //guardar los datos del usuario
            this.user = normalizeUser(response.data.user);

            //persistir el token en navegador
            localStorage.setItem('token', this.token);

            //persisitir el token parael ususario
            localStorage.setItem('user', JSON.stringify(this.user));
        },

        //creacion del setToken
        setToken(token) {
            this.token = token;
            localStorage.setItem('token', token);
        },

        //obtener los datos del usuario desde la api
        async fetchUser() {
            try{
                const response = await api.get('me');
                this.user = normalizeUser(response.data);
                localStorage.setItem('user', JSON.stringify(this.user));
            }
            catch(error){
                this.logout();
            }
        },

        //lagout del usuario
        //limpia sesion frontend
        logout() {
            //eliminar datos del storage 
            this.token = null;
            this.user = null;

            //eliminar datos  del navegador
            localStorage.removeItem('token');
            localStorage.removeItem('user');

            //remover los datos del usuario
            this.user = null;
        },
        async register(credentials) {
            //peticion POST hacia /api/register
            const response = await api.post('/register', credentials);

            //guardar el token recibido del backend
            this.token = response.data.access_token;

            //guardar los datos del usuario
            this.user = response.data.user;
            
            //persistir el token en navegador
            localStorage.setItem('token', this.token);

            //persistir el usuario en navegador
            localStorage.setItem('user', JSON.stringify(this.user));
        },
        setToken(token) {
            // Método para guardar el token directamente (usado en OAuth)
            this.token = token;
            localStorage.setItem('token', token);
        }
    },
});

