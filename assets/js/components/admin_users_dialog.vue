<template>
    <div>
        <md-dialog :md-active.sync="showDialog">
        <md-dialog-title>Edit user {{this.user.username}}</md-dialog-title>

        <md-tabs v-show="this.user != {}">
            <md-tab md-label="Info">
                <div>
                    <md-field>
                        <label>Username</label>
                        <md-input v-model="user.username"></md-input>
                    </md-field>

                    <md-field>
                        <label>Email</label>
                        <md-input v-model="user.email" type="email"></md-input>
                    </md-field>
                </div>
            </md-tab>
            <md-tab md-label="Danger Zone">
                <div>
                    <md-card>
                        <md-card-header>
                            <div class="md-title">Is admin user</div>
                        </md-card-header>

                        <md-card-content>
                            Should this user have access to admin functions
                        </md-card-content>

                        <md-card-actions>
                            <md-switch v-model="isAdmin"></md-switch>
                        </md-card-actions>
                    </md-card>
                    <md-card>
                        <md-card-header>
                            <div class="md-title">Delete user</div>
                        </md-card-header>

                        <md-card-content>
                            Remove this user and all uploaded content by this user
                        </md-card-content>

                        <md-card-actions>
                            <md-button class="md-raised md-accent" @click="removeUser()">Delete</md-button>
                        </md-card-actions>
                    </md-card>
                </div>
            </md-tab>
        </md-tabs>

        <md-dialog-actions>
            <md-button class="md-primary" @click="showDialog = false">Close</md-button>
            <md-button class="md-primary" @click="submitForm()">Save</md-button>
        </md-dialog-actions>
        </md-dialog>

        <md-button class="md-primary md-raised" @click="showDialog = true">Edit</md-button>
    </div>
</template>

<script>
import { EventBus } from '../event-bus.js';

export default {
    name: "admin_users_dialog",
    props: ['id'],
    created: function () {
        //
    },
    watch: {
        showDialog: function (val) {
            if (true === val) {
                this.fetchData();
                return true;
            }

            EventBus.$emit('admin_user-fetchData');
        }
    },
    data: () => ({
        showDialog: false,
        isAdmin: false,
        user: {}
    }),
    methods: {
        fetchData() {
            fetch(`/admin/users/json/${this.id}`, {
                method: 'GET',
                credentials: 'same-origin',
            }).then((response) => {
                return response.json();
            }).then((json) => {
                this.user = json.user;
                this.isAdmin = json.user.isAdmin;
            });
        },
        removeUser() {
            let _this = this;
            fetch(`/admin/users/delete/${this.id}`, {
                method: 'DELETE',
                credentials: 'same-origin',
            }).then((response) => {
                return response.json();
            }).then((json) => {
                console.log(json);
                if (true === json.success) {
                    _this.showDialog = false;
                    return true;
                }

                if (json.msg) {
                    alert(json.msg);
                }
            }).catch((e) => {
                alert('Unable to delete user');
            });
        },
        submitForm() {
            const data = new FormData();
            data.append('username', this.user.username);
            data.append('email', this.user.email);
            data.append('isAdmin', this.isAdmin);

            let _this = this;
            fetch(`/admin/users/json/${this.id}`, {
                method: 'POST',
                credentials: 'same-origin',
                body: data
            }).then(function(response) {
                if (201 !== response.status) {
                    alert('Unable to save user');
                    return false;
                }

                return response.json()
            }).then(function(json) {
                _this.showDialog = false;
            });
        }
    }
}
</script>