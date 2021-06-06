<template>
    <admin>
        <md-card>
            <md-card-header>
                <div class="md-title">Webapp</div>
            </md-card-header>

            <md-card-content v-show="data.webapp != {}">
                Symfony: <code>{{data.webapp.symfony}}</code><br/>
                PHP: <code>{{data.webapp.php}}</code><br/>
                Version: -
            </md-card-content>
        </md-card>
        <md-card>
            <md-card-header>
                <div class="md-title">Vault</div>
            </md-card-header>

            <md-card-content>
                Music: <code></code><br/>
                Thumbnails: <code></code><br/>
            </md-card-content>
        </md-card>
    </admin>
</template>

<style lang="scss" scoped>

</style>

<script>
import adminApp from './admin'

export default {
    name: "admin_about",
    created: function () {
        console.log('created');
        this.fetchData();
    },
    data: function () {
        return {
            loaded: false,
            data: {
                webapp: {}
            }
        };
    },
    methods: {
        fetchData() {
            fetch('/admin/about/json', {
                method: 'GET',
                credentials: 'same-origin',
            }).then((response) => {
                return response.json();
            }).then((json) => {
                this.data.webapp = json.webapp;
                this.loaded = true;
            });
        }
    },
    components: {
        'admin': adminApp
    }
}
</script>