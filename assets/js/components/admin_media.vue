<template>
    <admin>
        <md-table md-card>
            <md-table-row>
                <md-table-head>UUID</md-table-head>
                <md-table-head>Title</md-table-head>
                <md-table-head>Seconds</md-table-head>
                <md-table-head>Size</md-table-head>
                <md-table-head>User</md-table-head>
                <md-table-head></md-table-head>
            </md-table-row>

            <md-table-row v-for="row in media"
                v-bind:data="row"
                v-bind:key="row.uuid">
                <md-table-cell md-numeric>{{row.uuid}}</md-table-cell>
                <md-table-cell>{{row.title}}</md-table-cell>
                <md-table-cell>{{row.seconds}}</md-table-cell>
                <md-table-cell>{{row.size}}</md-table-cell>
                <md-table-cell>{{row.user}}</md-table-cell>
                <md-table-cell><md-button class="md-primary md-raised" disabled @click="true">Edit</md-button></md-table-cell>
            </md-table-row>
        </md-table>
    </admin>
</template>

<style lang="scss" scoped>
.admin_media{
    color: red;
}
</style>

<script>
import adminApp from './admin'
import { EventBus } from '../event-bus.js';

export default {
    name: "admin_media",
    created: function () {
        let _this = this;
        _this.fetchData();
        EventBus.$on('admin_user-fetchData', function(e) {
            _this.fetchData();
        });
    },
    data: function () {
        return {
            media: {
                webapp: {},
                vault: {},
            }
        };
    },
    methods: {
        fetchData() {
            fetch('/admin/media/json', {
                method: 'GET',
                credentials: 'same-origin',
            }).then((response) => {
                return response.json();
            }).then((json) => {
                this.media = json.media;
            });
        }
    },
    components: {
        'admin': adminApp
    }
}
</script>