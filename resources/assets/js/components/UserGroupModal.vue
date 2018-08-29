<template>
    <div>
        <div class="modal-header">
        <h3>{{ title }}</h3>
    </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-lg-12 error-container bg-danger" v-for="(errorType, index) in errorResponseMessages" :key="index">
                    <h4>{{ index | capitalize }} Error</h4>
                    <ul class="error-list">
                        <li class="error" v-for="message in errorResponseMessages[index]">{{ message }}</li>
                    </ul>
                </div>
            </div>
            <div class="row form-inline form-group">
                <div class="col-lg-12" :class="{ 'error': hasValidationError('name') }">
                    <label class="control-label col-sm-2">User Group Name</label>
                    <input type="text" class="col-sm-10" v-model="modalGroup.name" required />
                </div>
            </div>

            <div class="row form-inline form-group">
                <div class="col-lg-12" :class="{ 'error': hasValidationError('description') }">
                    <label class="control-label col-sm-2">Description</label>
                    <input type="text" class="col-sm-10" v-model="modalGroup.description" required />
                </div>
            </div>

            <div class="row form-group-lg form-group">
                <div class="col-lg-12" :class="{ 'error': hasValidationError('permission_ids') }">
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
    props: {
        initialGroup: {
            type: Object,
            required: true
        },
    },
    data: function () {

        return {
            errors: [],
            modalGroup: Vue.util.extend({}, this.initialGroup),
            title: 'Add New User Group',
            users: null,
            selectedPermissions: [],
            selectedUsers: []
        }
    },
    computed: {
        errorResponseMessages: function () {
            var errorTypes = {}
            var fieldErrors = []
            for(var type in this.errors) {
                if(typeof this.errors[type] == 'object') {
                    for(var field in this.errors[type]) {
                        if(typeof this.errors[type][field] == 'object') {
                            fieldErrors = fieldErrors.concat(this.errors[type][field])
                        } else {
                            fieldErrors.push(this.errors[type][field])
                        }
                        errorTypes[type] = fieldErrors
                    }
                }
            }
            return errorTypes
        },
        permissions: function () {
            return this.$store.state.permissions.data
        }
    },
    created: function () {
        try {
            console.log(this.modalGroup)
            this.selectedPermissions = this.modalGroup.permission_ids
        } catch (e) {

        }
    },
    mounted: function () {
        this.$store.dispatch('updatePermissions')
    },
    methods: {
        hasValidationError: function (field) {
            if(this.errors != undefined && this.errors.hasOwnProperty('validation')) {
                console.log(field)
                return this.errors['validation'].hasOwnProperty(field)
            }
        },
        saveGroup: function() {
            this.modalGroup.isSaving = true

            if(this.modalGroup.id == null) {
                window.axios.post('/api/usergroup', {
                    description: this.modalGroup.description,
                    name: this.modalGroup.name,
                    permission_ids: this.selectedPermissions
                }).then( (response) => {
                    if( response.status == 406 ) {
                        this.errors = response.data.errors
                    } else {
                        this.successfulSave()
                    }
                }).catch( (error) => {
                    this.$store.commit('updateErrors', error)
                })
            } else {
                window.axios.put('/api/usergroup/' + this.modalGroup.id, {
                    description: this.modalGroup.description,
                    name: this.modalGroup.name,
                    permission_ids: this.selectedPermissions
                }).then( (response) => {
                    if( response.status == 406 ) {
                        this.errors = response.data.errors
                    } else {
                        this.successfulSave()
                    }
                }).catch( (error) => {
                    this.$store.commit('updateErrors', error)
                })
            }
            this.modalGroup.isSaving = false
        },
        successfulSave: function() {
            this.modalGroup.name = ''
            this.modalGroup.description = ''
            this.selectedPermissions = []
            this.modalGroup.isSaving = false
            this.$store.dispatch('updateGroups')
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