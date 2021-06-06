import Vue from 'vue'
import App from './components/admin_about'
import VueMaterial from 'vue-material'
Vue.use(VueMaterial)
new Vue({
    el: '#admin_about',
    render: h => h(App)
});