<template>
    <div>
        <div class="modal-header">
        <h3>{{ title }}</h3>
    </div>
        <div class="modal-body">
            <div class="row form-inline form-group">
                <div class="col-lg-12">
                    <label class="control-label col-sm-2">User Group Name</label>
                    <input type="text" class="col-sm-10" v-model="modalGroup.name" required />
                </div>
            </div>

            <div class="row form-inline form-group">
                <div class="col-lg-12">
                    <label class="control-label col-sm-2">Description</label>
                    <input type="text" class="col-sm-10" v-model="modalGroup.description" required />
                </div>
            </div>

            <div class="row form-group-lg form-group">
                <div class="col-lg-12">
                    <h3 class="list-group-item-heading">Permissions</h3>
                </div>
                <div class="list-group scrollable">
                    <div class="list-group-item" v-for="permission in permissions" :key="permission.id">
                        <label class="list-group-item-text" :for="'permission_' + permission.id" :title="permission.description">
                            <input type="checkbox" name="permission_ids" :id="'permission_' + permission.id" :value="permission.id" v-model="selectedPermissions"> {{ permission.name }}
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <div class="row">
                <div class="col-sm-6 col-sm-offset-6 text-right">
                    <div class="btn-group">
                        <button @click="$emit('close')" type="button" class="btn btn-default" :disabled="modalGroup.isSaving">
                            <i class="fa fa-fw fa-undo"></i>Go Back
                        </button>
                        <button @click="saveGroup" type="button" class="btn btn-default" :disabled="modalGroup.isSaving">
                            <i class="fa fa-fw" :class="{ 'fa-refresh fa-spin': modalGroup.isSaving, 'fa-save': !modalGroup.isSaving }"></i>
                            <span v-show="!modalGroup.isSaving">Save</span><span v-if="modalGroup.isSaving">Saving</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'UserGroupModal',
        data: function () {

            return {
                modalGroup: Vue.util.extend({}, this.initialGroup),
                permissions: null,
                title: 'Add New User Group',
                users: null,
                selectedPermissions: [],
                selectedUsers: []
            }
        },
        props: {
            initialGroup: {
                type: Object,
                required: true
            },
        },
        created: function () {

            this.getPermissions()
        },
        methods: {
            getPermissions: function () {
                window.axios.get('/api/permission').then( (response) => {
                    console.log(response.data.data)
                    this.permissions = response.data.data
                }).catch( function (reason) {
                    console.log(reason)
                })
            },
            saveGroup: function() {

                this.modalGroup.isSaving = true

                if(this.modalGroup.id == null) {
                    window.axios.post('api/usergroup', {
                        description: this.modalGroup.description,
                        name: this.modalGroup.name,
                        permission_ids: this.selectedPermissions
                    }).then( (resposne) => {
                        this.successfulSave()
                    }).catch( (reason) => {
                       // TODO: Handle error
                    })
                } else {
                    window.axios.put('api/usergroup/' + this.modalGroup.id, {
                        description: this.modalGroup.description,
                        name: this.modalGroup.name,
                        permission_ids: this.selectedPermissions
                    }).then( (response) => {
                        console.log(response.data)
                        this.successfulSave()
                    }).catch( (reason) => {
                        // TODO: Handle error
                        console.log(reason)
                    })
                }
            },
            successfulSave: function() {
                console.log(this)
                this.modalGroup.name = ''
                this.modalGroup.description = ''
                this.selectedPermissions = []
                this.modalGroup.isSaving = false
                this.$parent.$parent.$parent.$emit('refreshUserGroups')
                this.$emit('close')
            }
        }
    }
</script>

<style scoped>
.v--modal-overlay {
    z-index: 1000;
}

.list-group-item {
    display: table-cell;
    border: none;
    float: left;
    width: 20%;
}

</style>