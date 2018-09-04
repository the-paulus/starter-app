
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');
window.VueRouter = require('vue-router')
window.JWT = require('jsonwebtoken')

import UserGroups from './components/UserGroups'
import Users from './components/Users'
import Settings from './components/Settings'
import VueRouter from 'vue-router'
import VModal from 'vue-js-modal'
import vPage from 'v-page'
import VueEsc from 'vue-esc'
import Vuex from 'vuex'
import BootstrapVue from 'bootstrap-vue'

Vue.use(Vuex)
Vue.use(VueRouter)
Vue.use(VModal, { dynamic: false, injectModalsContainer: true, dialog: true })
Vue.use(vPage)
Vue.use(VueEsc)
Vue.use(BootstrapVue)

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.component('administration-menu', require('./components/AdministrationMenu.vue'))
Vue.component('vue-table', require('./components/VuePast'))
Vue.component('user-groups', require('./components/UserGroups.vue'))
Vue.component('users', require('./components/Users.vue'))
Vue.component('modals-container', require('vue-js-modal/src/ModalsContainer.vue'))
Vue.component('search-bar', require('./components/SearchBar.vue'))
Vue.component('settings', require('./components/Settings.vue'))

const router = new VueRouter({
    mode: 'history',
    base: __dirname + 'admin'
})

const store = new Vuex.Store({
    state: {
        groups: [],
        token: '',
        users: []
    },
    mutations: {
        updateGroups: function (state, payload) {
            state.groups = payload
        },
        updateUsers: function (state, payload) {
            state.users = payload
        }
    },
    actions: {
        updateGroups: function ({ commit, state }) {

        },
        updateUsers: function ({ commit }, pagination) {
            let url = '/api/user'

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
    props: {
        modalOptions: {
            type: Object,
            required: false,
            default: function () {
                return {
                    classes: ['v--modal'],
                    adaptive: false,
                    scrollable: true,
                    height: 'auto',
                    width: '80%'
                }
            }
        },
    },
    data: function () {

        return {

            token: ''
        }
    },
    watch: {
        token: function (newValue, oldValue) {

            window.axios.defaults.headers.common['Authorization'] = 'Bearer ' + this.token

        }
    },
    created: function () {

        window.axios.get('/token').then( (response) => {

            this.token = response.data.data.token

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
        })

    },
    mounted: function () {
        this.$on('showErrorModal', this.showErrorModal)
    },
    methods: {
        showErrorModal: function (response, error) {
            this.$modal.show(ErrorResponse, {
                message: 'An error has occurred',
                response: response,
                error: error
            }, this.modalOptions)
        }
    }
});
