<template>
    <div class="component-container">
        <vue-table id="userstable" v-model="data" :past-config="pastConfig" :axios-search-request-config="axiosSearchRequestConfig" @addRow="addUser" @deleteRow="confirmDeleteUser" @editRow="editUser" @sortData="sortData" @pageChanged="pageChanged"></vue-table>
        <modals-container></modals-container>
        <v-dialog/>
    </div>
</template>

<script>
import UserModal from './UserModal'

export default {
    name: 'Users',
    components: {UserModal},
    data: function () {
        return {
            axiosSearchRequestConfig: {
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
            pastConfig: {
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
            modalOptions: {},
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

        data: function () {

            let d = Vue.util.extend({}, this.$store.state.users)

            return d

        }
    },
    watch: {
        error: function (newValue, oldValue) {

            this.$modal.show(ErrorResponse, {}, this.modalOptions)

        }
    },
    created: function () {

        // Create a new modalOptions objects to be used explicitly by this
        // component's modals. Any modifications to the this.$attrs.modalOptions
        // object will persist throughout the rest of the application, otherwise.
        this.modalOptions = Vue.util.extend({}, this.$attrs.modalOptions)

        // Override modalOptions here
        this.modalOptions.width = '40%'

    },
    mounted: function() {

        this.$store.dispatch('updateUsers', this.pagination)

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
        confirmDeleteUser: function(index) {

            this.$modal.show('dialog', {
                title: 'Confirm',
                text: 'Are you sure you want to delete ' + this.$store.state.users.data[index].first_name + ' ' + this.$store.state.users.data[index].last_name + '?',
                buttons: [
                    {
                        title: 'Yes',
                        handler: () => {
                            this.$modal.hide('dialog')
                            this.deleteUser(this.$store.state.users.data[index].id)
                        }
                    },
                    {
                        title: 'No'
                    }
                ]
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
                this.$modal.show(ErrorResponse, {}, this.modalOptions)
            })
        },
        editUser: function(index) {
            this.$modal.show(UserModal, {initialUser: this.$store.state.users.data[index]}, this.modalOptions);
        },
        pageChanged: function (pageInfo) {
            this.pagination.page = pageInfo.pageNumber
            this.pagination.perPage = pageInfo.pageSize

            this.$store.dispatch('updateUsers', this.pagination)
        },
        sortData: function (args) {
            console.log('sortData')
            console.log(args)
            this.pagination.sortBy = args.sort
            this.pagination.orderBy = args.order

            this.$store.dispatch('updateUsers', this.pagination)
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