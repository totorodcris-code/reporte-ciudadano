// Importar CSS
import "../css/app.css";

// Importar Vue
import { createApp } from "vue";

// Pinia
import { createPinia } from "pinia";

// Router
import router from "./Router";

// Componente raíz
import App from "./App.vue";

// Theme Store
import { useThemeStore } from "./stores/theme";

// FontAwesome - load with error handling
const loadFontAwesome = async () => {
  try {
    // Try to load FontAwesome CSS
    const link = document.createElement('link');
    link.rel = 'stylesheet';
    link.href = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css';
    link.crossorigin = 'anonymous';
    link.onerror = () => {
      console.warn('FontAwesome failed to load, icons may not display correctly');
      // Fallback: Add basic icon styles
      const fallbackStyle = document.createElement('style');
      fallbackStyle.textContent = `
        .fa, .fas, .far, .fab, .fal {
          display: inline-block;
          font-style: normal;
          font-variant: normal;
          text-rendering: auto;
          line-height: 1;
        }
        .fa::before {
          display: inline-block;
          font-style: normal;
          font-variant: normal;
          text-rendering: auto;
          -webkit-font-smoothing: antialiased;
        }
      `;
      document.head.appendChild(fallbackStyle);
    };
    document.head.appendChild(link);
  } catch (error) {
    console.error('Error loading FontAwesome:', error);
  }
};

// Lazy load AOS
const loadAOS = async () => {
  if (typeof window !== 'undefined') {
    try {
      const AOS = await import('aos');
      await import('aos/dist/aos.css');
      AOS.default.init({
          duration: 1000,
          once: true
      });
    } catch (error) {
      console.warn('AOS failed to load:', error);
    }
  }
};

const app = createApp(App);

// Store
const pinia = createPinia();

app.use(pinia);
app.use(router);

// Montar app
app.mount("#app");

// Aplicar tema guardado
const themeStore = useThemeStore()
themeStore.init()

// Cargar recursos externos con manejo de errores
loadFontAwesome();

// Iniciar animaciones de forma lazy
loadAOS();