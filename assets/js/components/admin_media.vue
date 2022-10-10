<template>
    <admin>
        <md-table md-card>
            <md-table-row>
                <md-table-head>UUID</md-table-head>
                <md-table-head>Title</md-table-head>
                <md-table-head>Length</md-table-head>
                <md-table-head>Size</md-table-head>
                <md-table-head>User</md-table-head>
                <md-table-head></md-table-head>
            </md-table-row>

            <md-table-row v-for="row in media"
                v-bind:data="row"
                v-bind:key="row.uuid">
                <md-table-cell>{{row.uuid}}</md-table-cell>
                <md-table-cell>{{row.title}}</md-table-cell>
                <md-table-cell><span v-html="showTrackSeconds(row)"></span></md-table-cell>
                <md-table-cell><span v-html="getPrettyBytes(row)"></span></md-table-cell>
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
import prettyBytes from 'pretty-bytes';
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
        },
        getPrettyBytes(row) {
            if ("undefined" == typeof row.size || null === row.size) {
                return '0 KB';
            }
            let bytes = prettyBytes(row.size);

            return bytes;
        },
        showTrackSeconds(row) {
            if ("undefined" == typeof row.seconds || Number.isNaN(row.seconds)) {
                return '--:--:--';
            }

            return new Date(row.seconds * 1000).toISOString().substr(11, 8)
        }
    },
    components: {
        'admin': adminApp
    }
}
</script>