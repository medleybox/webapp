import Vue from 'vue'
import App from './components/admin_users'
import VueMaterial from 'vue-material'
Vue.use(VueMaterial)
new Vue({
    el: '#admin_users',
    render: h => h(App)
});