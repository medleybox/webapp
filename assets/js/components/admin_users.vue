<template>
    <admin>
        <md-table md-card>
            <md-table-row>
                <md-table-head md-numeric>ID</md-table-head>
                <md-table-head>Active</md-table-head>
                <md-table-head>Username</md-table-head>
                <md-table-head>Email</md-table-head>
                <md-table-head></md-table-head>
            </md-table-row>

            <md-table-row v-for="user in users"
                v-bind:data="user"
                v-bind:key="user.id">
                <md-table-cell md-numeric>{{user.id}}</md-table-cell>
                <md-table-cell>
                    <md-icon v-if="user.active === true">check_circle</md-icon>
                    <md-icon v-else>cancel</md-icon>
                </md-table-cell>
                <md-table-cell>{{user.username}}</md-table-cell>
                <md-table-cell>{{user.email}}</md-table-cell>
                <md-table-cell>
                    <admin-users-dialog v-bind:id="user.id"></admin-users-dialog>
                </md-table-cell>
            </md-table-row>
        </md-table>
    </admin>
</template>

<style lang="scss" scoped>

</style>

<script>
import adminApp from './admin'
import adminUsersDialog from './admin_users_dialog'
import { EventBus } from '../event-bus.js';

export default {
    name: "admin_users",
    created: function () {
        let _this = this;
        _this.fetchData();
        EventBus.$on('admin_user-fetchData', function(e) {
            _this.fetchData();
        });
    },
    data: function () {
        return {
            users: {}
        };
    },
    methods: {
        fetchData() {
            fetch('/admin/users/json', {
                method: 'GET',
                credentials: 'same-origin',
            }).then((response) => {
                return response.json();
            }).then((json) => {
                this.users = json.users;
            });
        }
    },
    components: {
        'admin': adminApp,
        'admin-users-dialog': adminUsersDialog
    }
}
</script>