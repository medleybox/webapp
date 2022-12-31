<template>
  <div class="page-container">
    <md-app md-waterfall md-mode="fixed-last">
      <md-app-toolbar class="md-large md-dense md-primary">
        <div class="md-toolbar-row">
          <div class="md-toolbar-section-start">
            <md-button class="md-icon-button" @click="menuVisible = !menuVisible">
              <md-icon>menu</md-icon>
            </md-button>

            <span class="md-title">{{appTitle}}</span>
          </div>

          <!--div class="md-toolbar-section-end">
            <md-button class="md-icon-button">
              <md-icon>more_vert</md-icon>
            </md-button>
          </div-->
        </div>

        <!--div class="md-toolbar-row">
          <md-tabs class="md-primary">
            <md-tab id="tab-home" md-label="Home"></md-tab>
            <md-tab id="tab-pages" md-label="Pages"></md-tab>
            <md-tab id="tab-posts" md-label="Posts"></md-tab>
            <md-tab id="tab-favorites" md-label="Favorites"></md-tab>
          </md-tabs>
        </div-->
      </md-app-toolbar>

      <md-app-drawer :md-active.sync="menuVisible">
        <md-toolbar class="md-transparent" md-elevation="0">Navigation</md-toolbar>

        <md-list>
          <md-list-item>
            <md-icon>account_circle</md-icon>
            <a class="md-list-item-text" href="/admin/users">Users</a>
          </md-list-item>
          <md-list-item>
            <md-icon>smart_display</md-icon>
            <a class="md-list-item-text" href="/admin/media">Media</a>
          </md-list-item>
          <md-list-item>
            <md-icon>info</md-icon>
            <a class="md-list-item-text" href="/admin/about">About</a>
          </md-list-item>
          <md-list-item>
            <md-icon>exit_to_app</md-icon>
            <a class="md-list-item-text" href="/">Frontend</a>
          </md-list-item>
        </md-list>
      </md-app-drawer>

      <md-app-content>
        <slot>Default content</slot>
      </md-app-content>
    </md-app>
  </div>
</template>

<style lang="scss">
  .md-app {
    min-height: 100vh;
    border: 1px solid rgba(#000, .12);
    overflow: inherit !important;
    border-bottom: none;
  }

  .md-table-cell-container {
    min-width: 105px !important;
    padding: 6px 4px 6px 4px !important;
  }

  .md-table-head-label {
    padding-right: 4px !important;
    padding-left: 4px !important;
  }

   // Demo purposes only
  .md-drawer {
    width: 230px;
    max-width: calc(100vw - 125px);
  }

  .md-toolbar.md-app-toolbar {
    position: sticky !important;
    top: 0px !important;
    transform: translate3d(0px, 0px, 0px) !important;
  }

  .md-app-scroller {
    margin-top: 5px !important;
  }
</style>

<script>
export default {
  name: "admin",
  created: function () {
    //console.log('created_admin');
    this.fetchData();
  },
  methods: {
    fetchData() {
      //console.log('fetchData_admin');
    }
  },
  computed: {
    appTitle: function () {
      const el = document.querySelector('.medleybox-admin');
      const data = el.dataset;
      return data.title;
    }
  },
  data: () => ({
    menuVisible: false
  })
}
</script>