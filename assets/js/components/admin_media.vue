<template>
    <admin>
        <admin-media-dialog></admin-media-dialog>
        <md-table v-if="true === this.loaded" md-card>
            <md-table-row>
                <md-table-head>Title</md-table-head>
                <md-table-head>Length</md-table-head>
                <md-table-head>Size</md-table-head>
                <md-table-head>User</md-table-head>
                <md-table-head></md-table-head>
            </md-table-row>

            <md-table-row v-for="row in media.media"
                v-bind:data="row"
                v-bind:key="row.uuid">
                <md-table-cell>
                    <span class="uuid--hover" @click="copyUuidToCB(row.uuid)">
                        <md-icon>info</md-icon>
                        <md-tooltip md-direction="right">{{row.uuid}}</md-tooltip>
                    </span>
                    {{row.title}}
                </md-table-cell>
                <md-table-cell><span v-html="showTrackSeconds(row)"></span></md-table-cell>
                <md-table-cell><span v-html="getPrettyBytes(row)"></span></md-table-cell>
                <md-table-cell>{{row.user}}</md-table-cell>
                <md-table-cell><md-button class="md-primary md-raised" @click="showEditModal(row.uuid)">Edit</md-button></md-table-cell>
            </md-table-row>

            <md-table-row v-for="row in media.vault"
                v-bind:data="row"
                v-bind:key="row.uuid">
                <md-table-cell>
                    <span class="uuid--hover" @click="copyUuidToCB(row.uuid)">
                        <md-icon class="md-accent">info</md-icon>
                        <md-tooltip md-direction="right">{{row.uuid}}</md-tooltip>
                    </span>
                    {{row.title}}
                </md-table-cell>
                <md-table-cell><span v-html="showTrackSeconds(row)"></span></md-table-cell>
                <md-table-cell>-</md-table-cell>
                <md-table-cell></md-table-cell>
                <md-table-cell><md-button disabled="true" class="md-primary md-raised" @click="forceImport(row.uuid)">Force Import</md-button></md-table-cell>
            </md-table-row>
        </md-table>
    </admin>
</template>

<style lang="scss" scoped>
    .uuid--hover > .md-icon {
        cursor: pointer;
    }
</style>

<script>
import adminApp from './admin'
import adminMediaDialog from './admin_media_dialog'
import prettyBytes from 'pretty-bytes';
import { EventBus } from '../event-bus.js';

export default {
    name: "admin_media",
    created: function () {
        let _this = this;
        _this.fetchData();
    },
    data: function () {
        return {
            loaded: false,
            media: {
                media: null,
                vault: null,
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
                this.media = json;
                this.loaded = true;
            });
        },
        copyUuidToCB(uuid) {
            try {
                navigator.clipboard.writeText(uuid);
            } catch($e) {
                alert('Cannot copy uuid');
            }
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
        },
        showEditModal(uuid) {
            EventBus.$emit('admin_media_dialog-fetchData', uuid);
        },
        forceImport(uuid) {
            const data = new FormData();
            data.append('uuid', uuid);

            let _this = this;
            fetch(`/admin/media/force-import`, {
                method: 'POST',
                credentials: 'same-origin',
                body: data
            }).then((response) => {
                return response.json();
            }).then((json) => {
                console.log(json);
            });
        },
    },
    components: {
        'admin': adminApp,
        'admin-media-dialog': adminMediaDialog
    }
}
</script>