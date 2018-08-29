<template>
    <div class="component-container">
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
    created: function() {},
    mounted: function () {
        this.$store.dispatch('updateGroups')
    },
    props: {
        token: {
            type: String,
            required: false
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
        }
    },
    computed: {
        groups: function () {
            return this.$store.state.groups.data
        }
    },
    watch: {},
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
        deleteGroup: function(index) {
            this.groups[index].isDeleting = true
            window.axios.delete('/api/usergroup/' + this.groups[index].id).then( (response) => {
                this.$store.dispatch('updateGroups')
            }).catch( (error) => {
                this.$store.commit('updateErrors', error)
            })
        },
        editGroup: function(index) {
            this.groups[index].isSaving = true
            window.axios.patch('/api/usergroup/' + this.groups[index].id, {
                name: this.groups[index].name,
                description: this.groups[index].description,
                permission_ids: this.groups[index].permission_ids
            }).then( (response) => {
                this.$store.dispatch('updateGroups')
            }).catch( (error) => {
                this.$store.commit('updateErrors', error)
            })
        },
        editPermissions: function(group) {
            this.$modal.show(UserGroupModal, { initialGroup: group}, this.$store.state.modalOptions)
        },
        isDisabled: function(index) {
            return this.groups[index].isSaving && this.groups[index].isDeleting
        },

    }
}
</script>

<style scoped>

</style>