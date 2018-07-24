<template>
        <div v-on:refreshUserGroups="getData">
            <table class="table-responsive table-striped form-rows table table-bordered table-content">
                <thead>
                <tr role="row">
                    <th scope="col" role="columnheader" aria-sort="ascending">Group Name</th>
                    <th style="width: 150px; text-align: right;"><button class="btn btn-default add" title="New User Group" @click="addGroup()" type="button"><i class="fa fa-fw fa-plus"></i> New</button></th>
                </tr>
                </thead>
                <tbody class="scrollable">
                <tr v-if="groups == null">
                    <td class="loader text-center" colspan="3"><i class="fa fa-refresh fa-spin fa-2x"></i></td>
                </tr>
                <tr v-else v-for="(group, index) in groups" :key="group.id" class="table-row">
                    <td class="table-data-max table-data"><input type="text" class="form-control" v-model="group.name" :disabled="isDisabled(index)"/></td>
                    <td class="table-data">
                        <div class="btn-group" style="width: auto;">
                            <button class="btn btn-default edit" title="Edit group" @click="editGroup(index)" type="button" :disabled="isDisabled(index)">
                                <i class="fa fa-fw" :class="{'fa-refresh fa-spin': group.isSaving, 'fa-floppy-o': !group.isSaving}" ></i>
                            </button>
                            <button class="btn btn-default" title="Edit Permissions" @click="editPermissions(group)" type="button" :disabled="isDisabled(index)">
                                <i class="fa fa-fw fa-key"></i>
                            </button>
                            <button title="Delete group" @click="deleteGroup(index)" type="button" :disabled="isDisabled(index)" class="btn btn-default remove">
                                <i class="fa fa-fw" :class="{'fa-refresh fa-spin': group.isDeleting, 'fa-trash': !group.isDeleting}"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                </tbody>
                <tfoot>
                <tr>
                    <td colspan="3">
                        <modals-container />
                    </td>
                </tr>
                </tfoot>
            </table>
        </div>
</template>

<script>
    import UserGroupModal from './UserGroupModal'
    import ErrorResponse from './ErrorResponse'

    export default {
        name: "UserGroups",
        components: {UserGroupModal, ErrorResponse},
        created: function() {
            if(this.token) {
                this.getData()
            }
        },
        mounted: function () {
            console.log(this)
            this.$on('refreshUserGroups', function (event) {
                this.getData()
            })
        },
        props: {
            token: {
                type: String,
                required: true
            }
        },
        data: function () {

            return {
                addingNewGroup: false,
                group: {
                    created_at: '',
                    description: '',
                    id: null,
                    isDeleting: false,
                    isSaving: false,
                    permission_ids: [],
                    updated_at: '',
                    user_ids: []
                },
                groups: null,
                modalOptions: {
                    classes: ['v--modal', 'user-group-modal'],
                    adaptive: false,
                    scrollable: true,
                    height: 'auto',
                    width: '80%'
                },
                permissions: null,
                users: null
            }
        },
        computed: {

        },
        watch: {
            /*
             * We're watching the token property because this.token is not assigned in created() or mounted() hooks.
             * Once we see the value has changed we call the getData() method.
             */
            token: function(newValue, oldValue) {

                this.getData()

            }
        },
        methods: {
            addGroup: function() {
                this.$modal.show(UserGroupModal, { initialGroup: this.group }, this.modalOptions)
            },
            deleteGroup: function(index) {
                this.groups[index].isDeleting = true
                window.axios.delete('/api/usergroup/' + this.groups[index].id, {
                    validateStatus: function (status) {
                        return status == 410
                    }
                }).then( (response) => {

                    this.getData()

                }).catch( (error) => {
                    // TODO: Handle Error
                })
            },
            editGroup: function(index) {
                this.groups[index].isSaving = true
                window.axios.patch('/api/usergroup/' + this.groups[index].id, {
                    name: this.groups[index].name,
                    description: this.groups[index].description,
                    permission_ids: this.groups[index].permission_ids
                }).then( (response) => {

                    this.getData()

                }).catch( (error) => {
                    console.log(error)
                })
            },
            editPermissions: function(group) {

                this.$modal.show(UserGroupModal, { initialGroup: group}, this.modalOptions)

            },
            getData: function () {

                window.axios.get('/api/usergroup').then( (response) => {
                    this.groups = response.data.data
                }).catch(function (error) {
                    console.log(error)
                })
            },

            getUsers: function () {
                window.axios.get('api/user').then( (response) => {
                    this.users = response.data.data
                }).catch( function (error) {
                    console.log(error)
                })
            },
            isDisabled: function(index) {
                return this.groups[index].isSaving && this.groups[index].isDeleting
            },

        }
    }
</script>

<style scoped>

</style>