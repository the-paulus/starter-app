<template>
    <div class="component-container">
        <ul class="nav nav-tabs" role="tablist">
            <li title="Users" class="nav-item active">
                <a href="#users-tab" role="tab" data-toggle="tab">Users</a>
            </li>
            <li title="Users" class="nav-item">
                <a href="#groups-tab" role="tab" data-toggle="tab">Groups</a>
            </li>
        </ul>
        <div class="tab-content">
            <div role="tab" class="tab-pane active" id="users-tab">
                <vue-table id="userstable" v-model="this.$store.state.users" :past-config="userPastConfig" :axios-search-request-config="axiosUserSearchConfig" @addRow="addUser" @deleteRow="confirmDeleteUser" @editRow="editUser" @sortData="sortUsers" @pageChanged="pageChanged"></vue-table>
            </div>
            <div role="tab" class="tab-pane" id="groups-tab">
                <vue-table id="groups-table" v-model="this.$store.state.groups" :past-config="groupPastConfig" @addRow="addGroup" @deleteRow="confirmDeleteGroup" @editRow="editGroup" @sortData="sortGroups"></vue-table>
            </div>
        </div>
        <modals-container></modals-container>
        <v-dialog/>
    </div>
</template>

<script>
import UserModal from './UserModal'
import UserGroups from './UserGroups'
import UserGroupModal from './UserGroupModal'

export default {
    name: 'Users',
    components: {UserModal,UserGroups,UserGroupModal},
    data: function () {
        return {
            axiosUserSearchConfig: {
                url: '/api/user/search',
                method: 'post',
                params: {

                },
                transformResponse: [function (data) {
                    data = JSON.parse(data)
                    data.data.forEach(function (element, index, arr) {
                        this.data[index].groups.forEach( function (element, index, arr) {
                            arr[index] = element.name
                        })
                    }, data)
                    return data
                }]
            },
            groupPastConfig: {
                canAdd: true,
                canDelete: true,
                canEdit: true,
                hasFooter: false,
                hasHeader: true,
                hasSearch: false,
                headerColumns: [
                    {
                        name: 'name',
                        label: 'Group Name',
                        sortable: true
                    }
                ],
                pagerSettings: {
                    totalRow: 0,
                    language: 'en',
                    pageSizeMenu: [15, 25, 50, 100],
                    info: true,
                    align: 'center'
                },
                tableClasses: [
                    'table-responsive',
                    'table-striped',
                    'form-rows',
                    'table table-bordered',
                    'table-content'
                ],
                tableDataClasses: [
                    'table-data-max',
                    'table-data'
                ]
            },
            userPastConfig: {
                canAdd: true,
                canDelete: true,
                canEdit: true,
                hasHeader: true,
                hasSearch: true,
                headerColumns: [
                    {
                        name: 'last_name',
                        label: 'Last Name',
                        sortable: true
                    },
                    {
                        name: 'first_name',
                        label: 'First Name',
                        sortable: true
                    },
                    {
                        name: 'email',
                        label: 'Email',
                        sortable: true
                    },
                    {
                        name: 'groups',
                        label: 'User Groups',
                        sortable: false
                    }
                ],
                pagerSettings: {
                    totalRow: 0,
                    language: 'en',
                    pageSizeMenu: [15, 25, 50, 100],
                    info: true,
                    align: 'center'
                },
                tableClasses: [
                    'table-responsive',
                    'table-striped',
                    'form-rows',
                    'table table-bordered',
                    'table-content'
                ],
                tableDataClasses: [
                    'table-data-max',
                    'table-data'
                ]
            },
            error: {},
            filters: [
                {
                    name: 'user_group_ids',
                    label: 'User Group(s)',
                    type: 'select',
                    data: null
                }
            ],
            userModalOptions: {},
            pagination: {
                page: 1,
                perPage: 15,
                orderBy: 'ASC',
                sortBy: 'last_name'
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
            }
        }
    },
    computed: {
        users: function () {
            return this.$store.state.users.data
        },
        usersPagination: function () {
            let pagination = Vue.util.extend({}, this.$store.state.users)
            delete pagination.data
            return pagination
        },
        groups: function () {
            return this.$store.state.groups.data
        },
        groupsPagination: function () {
            let pagination = Vue.util.extend({}, this.$store.state.groups)
            delete pagination.data
            return pagination
        }
    },
    watch: {},
    created: function () {

        // Create a new modalOptions objects to be used explicitly by this
        // component's modals. Any modifications to the this.$attrs.modalOptions
        // object will persist throughout the rest of the application, otherwise.
        this.userModalOptions = Vue.util.extend({}, this.$store.state.modalOptions)

        // Override modalOptions here
        this.userModalOptions.width = '40%'

    },
    mounted: function() {

        this.$store.dispatch('updateGroups')
        this.$store.dispatch('updateUsers', this.pagination)

    },
    methods: {
        addGroup: function() {
            this.$modal.show(UserGroupModal, {
                initialGroup: {
                    created_at: '',
                    description: '',
                    id: null,
                    isDeleting: false,
                    isSaving: false,
                    permission_ids: [],
                    updated_at: '',
                    user_ids: []
                }
            }, this.$store.state.modalOptions)
        },
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
            }, this.userModalOptions)

        },
        confirmDeleteUser: function(index) {

            this.$modal.show('dialog', {
                title: 'Confirm',
                text: 'Are you sure you want to delete ' + this.users[index].first_name + ' ' + this.users[index].last_name + '?',
                buttons: [
                    {
                        title: 'Yes',
                        handler: () => {
                            this.$modal.hide('dialog')
                            this.deleteUser(this.$store.state.users[index].id)
                        }
                    },
                    {
                        title: 'No'
                    }
                ]
            })
        },
        confirmDeleteGroup: function(index) {

            this.$modal.show('dialog', {
                title: 'Confirm',
                text: 'Are you sure you want to delete ' + this.groups[index].name + '?',
                buttons: [
                    {
                        title: 'Yes',
                        handler: () => {
                            this.$modal.hide('dialog')
                            this.deleteGroup(this.groups[index].id)
                        }
                    },
                    {
                        title: 'No'
                    }
                ]
            })
        },
        deleteGroup: function(id) {

            window.axios.delete('/api/usergroup/' + id).then( (response) => {
                this.$store.dispatch('updateGroups')
            }).catch( (error) => {
                this.$store.commit('updateErrors', error)
            })
        },
        deleteUser: function (id) {
            window.axios.delete('/api/user/' + id).then( (response) => {
                this.$modal.show('dialog', {
                    text: 'Deleted successfully.',
                    buttons: [
                        {
                            title: 'OK'
                        }
                    ]
                })
                this.$store.dispatch('updateUsers')
            }).catch( (error) => {
                this.$store.commit('updateErrors', error)
            })
        },
        editUser: function(index) {
            this.$modal.show(UserModal, {initialUser: this.$store.state.users.data[index]}, this.userModalOptions);
        },
        editGroup: function(index) {
            this.$modal.show(UserGroupModal, {initialGroup: this.$store.state.groups.data[index]}, this.$store.state.modalOptions)
        },
        pageChanged: function (pageInfo) {
            this.pagination.page = pageInfo.pageNumber
            this.pagination.perPage = pageInfo.pageSize

            this.$store.dispatch('updateUsers', this.pagination)
        },
        sortGroups: function(args) {
            this.pagination.sortBy = args.sort
            this.pagination.orderBy = args.order

            this.$store.dispatch('updateGroups', this.pagination)
        },
        sortUsers: function (args) {
            this.pagination.sortBy = args.sort
            this.pagination.orderBy = args.order

            this.$store.dispatch('updateUsers', this.pagination)
        }
    }
}
</script>

<style scoped>
</style>