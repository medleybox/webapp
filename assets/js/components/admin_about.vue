<template>
    <admin>
        <div class="card--container">
            <md-card>
                <md-card-header>
                    <div class="md-title">Webapp</div>
                </md-card-header>

                <md-card-content v-show="data.webapp != {}">
                    Symfony: <code>{{data.webapp.symfony}}</code><br/>
                    PHP: <code>{{data.webapp.php}}</code><br/>
                    Version: -<br/>
                    Media Files: <code>{{data.webapp.files}}</code><br/>
                </md-card-content>
            </md-card>
        </div>
        <div class="card--container">
            <md-card>
                <md-card-header>
                    <div class="md-title">Vault</div>
                </md-card-header>

                <md-card-content>
                    <p class="float-right">
                        Music: <code>{{musicSize}}</code><br/>
                        Thumbnails: <code>{{thumbnailsSize}}</code><br/>
                    </p>
                    <p>
                        Symfony: <code>{{data.vault.symfony}}</code><br/>
                        PHP: <code>{{data.vault.php}}</code><br/>
                        AudiowaveForm: <code>{{data.vault.audiowaveform}}</code><br />
                        Version: -
                    </p>
                </md-card-content>
            </md-card>
        </div>
    </admin>
</template>

<style lang="scss" scoped>
    .card--container {
        width: 48vw;
        height: 230px;
        display: inline-flex;
        justify-content: center;
        align-items: center;
        margin-bottom: 30px;

        .md-card {
            width: 100%;
        }

        p {
            width: 45%;
            margin-bottom: 0px;
            margin-top: 0px;
        }

        .float-right {
            float: right;
            margin-top: 0px;
        }
    }
</style>

<script>
import prettyBytes from 'pretty-bytes';
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
                webapp: {},
                vault: {},
                stats: {}
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
                this.data.vault = json.vault;
            });

            fetch('/vault/api/stats', {
                method: 'GET',
                credentials: 'same-origin',
            }).then((response) => {
                return response.json();
            }).then((json) => {
                this.data.stats = json;
                this.loaded = true;
            });
        }
    },
    computed: {
        musicSize: function () {
            if (false === this.loaded) {
                return '';
            }
            return prettyBytes(this.data.stats.music);
        },
        thumbnailsSize: function () {
            if (false === this.loaded) {
                return '';
            }
            return prettyBytes(this.data.stats.thumbnails);
        }
    },
    components: {
        'admin': adminApp
    }
}
</script>