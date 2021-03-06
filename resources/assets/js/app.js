
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');
window.VueRouter = require('vue-router')
window.JWT = require('jsonwebtoken')

import Users from './components/Users'
import Settings from './components/Settings'
import ErrorResponse from './components/ErrorResponse'
import VueRouter from 'vue-router'
import VModal from 'vue-js-modal'
import vPage from 'v-page'
import VueEsc from 'vue-esc'
import Vuex from 'vuex'
import BootstrapVue from 'bootstrap-vue'
import wysiwyg from "vue-wysiwyg"

Vue.use(Vuex)
Vue.use(VueRouter)
Vue.use(VModal, { dynamic: true, injectModalsContainer: true, dialog: true })
Vue.use(vPage)
Vue.use(VueEsc)
Vue.use(BootstrapVue)
Vue.use(wysiwyg, {});

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.component('administration-menu', require('./components/AdministrationMenu.vue'))
Vue.component('vue-table', require('./components/VuePast'))
Vue.component('users', require('./components/Users.vue'))
Vue.component('modals-container', require('vue-js-modal/src/ModalsContainer.vue'))
Vue.component('settings', require('./components/Settings.vue'))
Vue.component('error-component', require('./components/ErrorResponse'))

const router = new VueRouter({
    mode: 'history',
    base: __dirname + 'admin'
})

const store = new Vuex.Store({
    state: {
        current_user: {},
        errors: [],
        groups: [],
        modalOptions: {
            classes: ['v--modal'],
            adaptive: false,
            scrollable: true,
            height: 'auto',
            width: '80%'
        },
        permissions: [],
        settings: [],
        settingGroups: [],
        token: '',
        users: []
    },
    mutations: {
        setCurrentUser: function(state, payload) {
            state.current_user = payload
        },
        updateErrors: function (state, payload) {
          state.errors = payload
        },
        updateGroups: function (state, payload) {
            state.groups = payload
        },
        updatePermissions: function(state, payload) {
            state.permissions = payload
        },
        updateSettings: function (state, payload) {
            state.settings = payload
        },
        updateSettingGroups: function(state, payload) {
            state.settingGroups = payload
        },
        updateToken: function(state, payload) {
            state.token = payload
        },
        updateUsers: function (state, payload) {
            state.users = payload
        }
    },
    actions: {
        updateGroups: function ({ commit }, pagination) {
            let url = process.env.MIX_BACKEND_URL + '/usergroup'

            if (pagination != null) {
                url += '?page=' + pagination.page
                url += '&perPage=' + pagination.perPage
                url += '&sortBy=' + pagination.sortBy
                url += '&orderBy=' + pagination.orderBy
            }

            return window.axios.request({
                url: url,
                method: 'get'
            }).then( (response) => {

                commit('updateGroups', response.data)

            }).catch(function(reason) {

                commit('updateErrors', reason)

            })
        },
        updatePermissions: function ({ commit }, pagination) {

            let url = process.env.MIX_BACKEND_URL + '/permission'

            if (pagination != null) {
                url += '?page=' + pagination.page
                url += '&perPage=' + pagination.perPage
                url += '&sortBy=' + pagination.sortBy
                url += '&orderBy=' + pagination.orderBy
            }

            return window.axios.request({
                url: url,
                method: 'get'
            }).then( (response) => {

                commit('updatePermissions', response.data)

            }).catch( (reason) => {

                commit('updateErrors', reason)

            })
        },
        updateSettings: function({commit}) {

            let url = process.env.MIX_BACKEND_URL + '/setting'

            return window.axios.request({
                url: url,
                method: 'get'
            }).then( (response) => {

                commit('updateSettings', response.data)

            }).catch( (reason) => {

                commit('updateErrors', reason)

            })
        },
        updateSettingGroups: function({commit}) {
            let url = process.env.MIX_BACKEND_URL + '/settinggroup'

            return window.axios.request({
                url: url,
                method: 'get'
            }).then( (response) => {

                commit('updateSettingGroups', response.data)

            }).catch(function(reason){

                commit('updateErrors', reason)

            })
        },
        updateUsers: function ({ commit }, pagination) {

            let url = process.env.MIX_BACKEND_URL + '/user'

            if (pagination != null) {
                url += '?page=' + pagination.page
                url += '&perPage=' + pagination.perPage
                url += '&sortBy=' + pagination.sortBy
                url += '&orderBy=' + pagination.orderBy
            }

           return window.axios.request({
                url: url,
                method: 'get',
                transformResponse: [function (data) {
                    data = JSON.parse(data)
                    data.data.forEach(function (element, index, arr) {
                        this.data[index].groups.forEach( function (element, index, arr) {
                            arr[index] = element.name
                        })
                    }, data)
                    return data
                }]
            }).then( (response) => {

                commit('updateUsers', response.data)

            }).catch(function (reason) {

               commit('updateErrors', reason)

           })
        }
    }
})

Vue.filter('capitalize', function (value) {
    if(!value) return ''
    value = value.toString()
    return value.charAt(0).toUpperCase() + value.slice(1)
})

const app = new Vue({
    el: '#app',
    router,
    store,
    props: {},
    Components: {ErrorResponse},
    data: function () {

        return {
            token: ''
        }
    },
    computed: {
        errors: function () {
            return this.$store.state.errors
        }
    },
    watch: {
        errors: function (newValue, oldValue) {
            if( newValue != null ) {
                this.showErrorModal(newValue.response, newValue.error)
            }
        },
        token: function (newValue, oldValue) {

            window.axios.defaults.headers.common['Authorization'] = 'Bearer ' + this.token

        }
    },
    created: function () {

        window.axios.get('/token').then( (response) => {

            try {

                this.token = response.data.data.token
                this.$store.commit('updateToken', response.data.data.token)
                this.current_user = response.data.data.user
                this.$store.commit('setCurrentUser', response.data.data.user)

            }catch (e) {

            }

        }).catch(function (reason) {

            this.$state.commit('updateError', reason);

        }).then(() => {
            this.$router.addRoutes([
                {
                    component: Settings,
                    name: 'Settings',
                    path: '/settings',
                    props: {
                        modalOptions: this.modalOptions
                    }
                },
                {
                    component: Users,
                    name: 'User Management',
                    path: '/users',
                    props: {
                        modalOptions: this.modalOptions
                    }
                }
            ])
        }).catch(function (e) {

        })

    },
    mounted: function () {
    },
    methods: {
        showErrorModal: function (response, error) {
            this.$modal.show(ErrorResponse, {
                message: 'An error has occurred',
                response: response,
                error: error
            })
        }
    }
});
