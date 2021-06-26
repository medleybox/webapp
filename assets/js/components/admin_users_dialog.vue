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
        </md-tabs>

        <md-dialog-actions>
            <md-button class="md-primary" @click="showDialog = false">Close</md-button>
            <md-button class="md-primary" @click="submitForm()">Save</md-button>
        </md-dialog-actions>
        </md-dialog>

        <md-button class="md-primary md-raised" @click="showDialog = true">Edit</md-button>
    </div>
</template>

<style lang="scss" scoped>

</style>

<script>
//import adminApp from './admin'

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
            }
        }
    },
    data: () => ({
        showDialog: false,
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
            });
        },
        submitForm() {
            const data = new FormData();
            data.append('username', this.user.username);
            data.append('email', this.user.email);

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
    },
    components: {
        //'admin': adminApp,
    }
}
</script>