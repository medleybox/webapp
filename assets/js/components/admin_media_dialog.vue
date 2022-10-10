<template>
    <div>
        <md-dialog :md-active.sync="showDialog">
        <md-dialog-title>{{this.title}}</md-dialog-title>

        <md-tabs v-show="this.media != {}">
            <md-tab md-label="Danger Zone">
                <div>
                    <md-card>
                        <md-card-header>
                            <div class="md-title">Delete media</div>
                        </md-card-header>

                        <md-card-content>
                            Remove this media upload and all related data
                        </md-card-content>

                        <md-card-actions>
                            <md-button class="md-raised md-accent" @click="removeMedia()">Delete</md-button>
                        </md-card-actions>
                    </md-card>
                </div>
            </md-tab>
        </md-tabs>

        <md-dialog-actions>
            <md-button class="md-primary" @click="showDialog = false">Close</md-button>
        </md-dialog-actions>
        </md-dialog>
    </div>
</template>

<script>
import { EventBus } from '../event-bus.js';

export default {
    name: "admin_media_dialog",
    created: function () {
        let _this = this;
        EventBus.$on('admin_media_dialog-fetchData', function(uuid) {
            _this.uuid = uuid;
            _this.fetchData();
            _this.title = '';
            _this.media = {};
            _this.showDialog = true;
        });
    },
    data: () => ({
        showDialog: false,
        uuid: null,
        title: '',
        media: {}
    }),
    methods: {
        fetchData() {
            fetch(`/media-file/metadata/${this.uuid}`, {
                method: 'GET',
                credentials: 'same-origin',
            }).then((response) => {
                return response.json();
            }).then((json) => {
                this.media = json;
                this.title = json.title;
            });
        },
        removeMedia() {
            let _this = this;
            fetch(`/media-file/delete/${this.uuid}`, {
                method: 'DELETE',
                credentials: 'same-origin',
            }).then((response) => {
                return response.json();
            }).then((json) => {
                console.log(json);
                if (true === json.delete) {
                    _this.showDialog = false;
                    return true;
                }

                if (json.msg) {
                    alert(json.msg);
                }
            }).catch((e) => {
                alert('Unable to delete media');
            });
        }
    }
}
</script>