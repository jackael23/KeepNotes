import './bootstrap';


import { createApp } from 'vue'
import App from '../js/templates/App.vue'

// Vuetify
import 'vuetify/styles'
import { createVuetify } from 'vuetify'
import * as components from 'vuetify/components'
import * as directives from 'vuetify/directives'
import '@mdi/font/css/materialdesignicons.css'

import router from './router';

import { createPinia } from 'pinia';

const pinia = createPinia();
const vuetify = createVuetify({
    components,
    directives,
    icons: {
        defaultSet: 'mdi', 
    },
})

const app = createApp(App);

app.use(vuetify);
app.use(router);
app.use(pinia);
app.mount('#app');
