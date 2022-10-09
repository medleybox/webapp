import Vue from 'vue'
import App from './components/admin_media'
import VueMaterial from 'vue-material'
Vue.use(VueMaterial)
new Vue({
    el: '#admin_media',
    render: h => h(App)
});