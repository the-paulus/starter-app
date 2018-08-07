<template>
    <div class="component-container">
        <table class="table-responsive table-striped form-rows table table-bordered table-content">
            <thead>
                <tr>
                    <th :colspan="columns.length + 1"><search-bar :search-config="searchConfig" :clear-search-config="clearSearchConfig" v-model="searchResults"></search-bar></th>
                </tr>
                <tr>
                    <th v-for="column in columns">{{ column.label }}</th>
                    <th style="width: 105px; text-align: right;"><button class="btn btn-default add" title="New User Group" @click="addUser()" type="button" style="width: 87px;"><i class="fa fa-fw fa-plus"></i> New</button></th>
                </tr>
            </thead>
            <tbody>
                <tr v-if="isLoading">
                    <td class="loader text-center" :colspan="columns.length + 1"><i class="fa fa-refresh fa-spin fa-2x"></i></td>
                </tr>
                <tr v-if="users.length == 0">
                    <td class="loader text-center text-info" :colspan="columns.length + 1">No results found.</td>
                </tr>
                <tr v-for="(user, index) in users" :key="user.id" v-if="!isLoading || users.length > 0">
                    <td class="table-data-max table-data" v-for="(field, index) in columns" :key="index">
                        <span v-if="typeof user[field.name] == 'string'">{{ user[field.name] }}</span>
                        <ul v-if="typeof user[field.name] == 'object'">
                            <li v-for="(group, index) in user[field.name]" :key="index">{{ getUserGroup(group) }}</li>
                        </ul>
                    </td>
                    <td class="table-data">
                        <div class="btn-group" style="width: auto;">
                            <button class="btn btn-default edit" title="Edit group" @click="editUser(index)" type="button" :disabled="isDisabled(index)">
                                <i class="fa fa-fw" :class="{'fa-refresh fa-spin': isDisabled(index), 'fa-pencil': !isDisabled(index)}" ></i>
                            </button>
                            <button title="Delete group" @click="deleteUser(index)" type="button" :disabled="isDisabled(index)" class="btn btn-default remove">
                                <i class="fa fa-fw" :class="{'fa-refresh fa-spin': isDisabled(index), 'fa-trash': !isDisabled(index)}"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td :colspan="columns.length + 1">
                        <v-page :setting="pageSettings" @page-change="pageChange"></v-page>
                        <modals-container />
                    </td>
                </tr>
            </tfoot>
        </table>

    </div>
</template>

<script>
import SearchBar from "./SearchBar";
import UserModal from "./UserModal"

export default {
    name: 'Users',
    components: {SearchBar,UserModal},
    data: function () {
        return {
            clearSearchConfig: {
                url: '/api/user',
                method: 'get'
            },
            columns: [
                {
                    name: 'last_name',
                    label: 'Last Name',
                    sortable: true
                },
                {
                    name: 'first_name',
                    label: 'first Name',
                    sortable: true
                },
                {
                    name: 'email',
                    label: 'email',
                    sortable: true
                },
                {
                    name: 'user_group_ids',
                    label: 'User Groups'
                }
            ],
            data: {},
            error: {},
            filters: [
                {
                    name: 'user_group_ids',
                    label: 'User Group(s)',
                    type: 'select',
                    data: null
                }
            ],
            groups: [],
            isLoading: true,
            modalOptions: {},
            pagination: {
                current_page: 1,
                per_page: 15,
                order_by: 'last_name',
                sort_by: 'ASC'
            },
            pageSettings: {
                totalRow: 0,
                language: 'en',
                pageSizeMenu: [15, 25, 50, 100],
                info: true,
                align: 'center'
            },
            searchConfig: {
                url: '/api/user/search',
                method: 'post'
            },
            searchResults: {},
            user: {
                id: 0,
                last_name: '',
                first_name: '',
                email: '',
                user_group_ids: [],
                isSaving: false,
                isEditing: false,
                isDeleting: false
            },
            users: {}
        }
    },
    computed: {
    },
    watch: {
        error: function (newValue, oldValue) {

            this.$modal.show(ErrorResponse, {}, this.modalOptions)

        },
        searchResults: function(newValue, oldValue) {
            console.log('watch')
            console.log(newValue)
            this.users = newValue.data
            delete newValue.data
            this.pagination = newValue
        },
        /*
         * We're watching the token property because this.token is not assigned in created() or mounted() hooks.
         * Once we see the value has changed we call the getData() method.
         */
        token: function(newValue, oldValue) {

            this.getData()

        },
        users: function(newValue, oldValue) {
            console.log(newValue)
        }
    },
    created: function () {
        this.getData()

        // Create a new modalOptions objects to be used explicitly by this
        // component's modals. Any modifications to the this.$attrs.modalOptions
        // object will persist throughout the rest of the application, otherwise.
        this.modalOptions = Vue.util.extend({}, this.$attrs.modalOptions)

        // Override modalOptions here
        this.modalOptions.width = '40%'

        // TODO: Get a list of all the filters
        window.axios.get('/api/usergroup').then( (response) => {
            this.groups = response.data.data
            this.filters.usergroups = {
                name: 'user_group_ids',
                label: 'User Groups',
                input: 'select'
            }
        })
    },
    mounted: function() {
        this.$on('refreshUsers', this.getData)
        this.$on('clearSearch', this.getData)
    },
    methods: {
        addUser: function() {
            this.$modal.show(UserModal, { initialUser: {
                    id: null,
                    last_name: '',
                    first_name: '',
                    email: '',
                    user_group_ids: [],
                    isSaving: false,
                    isEditing: false,
                    isDeleting: false
                }
            }, this.modalOptions)

        },
        deleteUser: function(index) {
            this.groups[index].isDeleting = true
            window.axios.delete('/api/user/' + this.groups[index].id).then( (response) => {
                switch(response.status) {
                    case 401:
                    case 403:
                        this.$modal.show('dialog', {
                            title: 'Error',
                            text: 'Unable to delete:<ul><li>' + response.data.errors.join('</li><li>') + '</li></ul>'
                        })
                        break
                    case 404:
                        // If what is trying to be deleted is not found, then we can assume it has already been
                        // deleted.
                    case 410:
                    default:
                        this.getData()
                }
            }).catch( (error) => {
                this.$modal.show(ErrorResponse, {}, this.modalOptions)
            }).finally(() => {
                this.groups[index].isDeleting = false
            })
        },
        editUser: function(index) {
            this.$modal.show(UserModal, {initialUser: this.users[index]}, this.modalOptions);
        },
        getData: function () {
            this.isLoading = true
            var uri = '/api/user'
            if (this.pagination.first_page_url) {
                uri = this.pagination.first_page_url

                var p = (this.pagination.current_page) ? this.pagination.current_page : 1
                var pp = (this.pagination.per_page) ? this.pagination.per_page : this.pageSettings.pageSizeMenu[0]
                var s = (this.pagination.sort_by) ? this.pagination.sort_by : this.columns[0].name
                var so = (this.pagination.order_by) ? this.pagination.order_by : 'ASC'

                uri = uri.replace(/page=[\d]+/, 'page=' + p)
                uri = uri.replace(/perPage=[\d]+/, 'perPage=' + pp)
                uri = uri.replace(/sortBy=[\w]+/, 'sortBy=' + s)
                uri = uri.replace(/orderBy=[(ASC|DESC)]+/, 'orderBy=' + so)
            }

            window.axios.get(uri).then( (response) => {
                console.log(response)
                this.users = Vue.util.extend({}, response.data.data)
                this.pagination = Vue.util.extend({}, response.data)
                delete this.pagination.data
                this.pageSettings.totalRow = this.pagination.total
            }).catch(function (error) {
                console.log(error)
            }).finally( () => {
                this.isLoading = false
            })
        },
        getUserGroup: function (group) {

            try {
                var user_group = this.groups.find(function (element) {
                    return element.id == this
                }, group).name

                return user_group
            } catch (e) {

            }
        },
        isDisabled: function(index) {
            try {
                return this.users[index].isSaving || this.groups[index].isDeleting
            } catch (e) {
                return false;
            }
        },
        pageChange: function (pInfo) {
            // console.log(this.pagination.current_page)
            this.pagination.current_page = pInfo.pageNumber
            // console.log(this.pagination.first_page_url.replace(/page=[\d]+/, 'page=' + pInfo.pageNumber).replace(/perPage=[\d]+/, 'perPage=' + pInfo.pageSize))
            this.pagination.per_page = pInfo.pageSize
            this.getData()
        }

    }

}
</script>

<style scoped>
    /*
table thead, tbody {
    display: block;
}
tbody {
    height: 100px;
    overflow-x: hidden;
    overflow-y: auto;
}*/
</style>