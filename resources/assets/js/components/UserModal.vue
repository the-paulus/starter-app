<template>
    <div>
        <div class="modal-header">
            <h3 class="modal-title">{{ title }}</h3>
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
            <div class="row">
                <div class="col-lg-12" :class="{'error': hasValidationError('first_name') }" >
                    <label class="control-label col-sm-2">First Name</label>
                    <input id="first_name" name="first_name" data-vv-as="first name" type="text" class="col-sm-10" v-model="modalUser.first_name" />

                </div>
            </div>
            <div class="row">
                <div class="col-lg-12" :class="{ 'error': hasValidationError('last_name') }">
                    <label class="control-label col-sm-2">Last Name</label>
                    <input id="last_name" name="last_name" type="text" class="col-sm-10" v-model="modalUser.last_name" />
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12" :class="{ 'error': hasValidationError('email') }">
                    <label class="control-label col-sm-2">Email</label>
                    <input id="email" name="email" type="text" class="col-sm-10" v-model="modalUser.email" />
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12" :class="{ 'error': hasValidationError('auth_type') }">
                    <label class="control-label col-sm-2">Auth Type</label>
                    <select id="auth_type" name="auth_type" class="col-sm-10" v-model="modalUser.auth_type">
                        <option v-for="type in auth_types" :key="type.id" :value="type.name">{{ type.name }}</option>
                    </select>
                </div>
            </div>
            <hr/>
            <div class="row">
                <div class="col-lg-12" :class="{ 'error': hasValidationError('user_group_ids') }">
                    <fieldset class="col-lg-12">
                        <legend class="h4">User Groups</legend>
                        <div class="list-group scrollable">
                            <div class="list-group-item" v-for="(group, index) in userGroups" :key="group.id">
                                <label class="list-group-item-text" :for="'group_' + group.id" :title="group.description">
                                    <input type="checkbox" name="user_group_ids" data-vv-as="user group" :id="'group_' + group.id" :value="group.id" v-model="selectedUserGroups"> {{ group.name }}
                                </label>
                            </div>
                        </div>
                    </fieldset>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <div class="row">
                <div class="col-sm-6 col-sm-offset-6 text-right">
                    <div class="btn-group">
                        <button @click="$emit('close')" type="button" class="btn btn-default">
                            <i class="fa fa-fw fa-undo"></i>Go Back
                        </button>
                        <button @click="saveUser()" type="button" class="btn btn-default" :disabled="modalUser.isSaving">
                            <i class="fa fa-fw" :class="{ 'fa-refresh fa-spin': modalUser.isSaving, 'fa-save': !modalUser.isSaving }"></i>
                            <span v-show="!modalUser.isSaving">Save</span><span v-if="modalUser.isSaving">Saving</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

export default {
    name: 'UserModal',
    data: function () {

        return {
            auth_types: [],
            errors: [],
            modalUser: Vue.util.extend({}, this.initialUser),
            selectedUserGroups: []
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
        userGroups: function() {
            return _.orderBy(this.$store.state.groups.data, 'name')
        },
        title: function () {
            return (this.modalUser.id) ? 'Edit User':'Add New User'
        }
    },
    props: {
        initialUser: {
            type: Object,
            required: true
        },
    },
    created: function () {
        try {
            this.selectedUserGroups = this.modalUser.user_group_ids
        } catch (error) {}
    },
    mounted: function () {
        window.axios.get('/api/user/auth_types').then( (response) => {
            let avail_auth_types = [{id: 0, name: '- Select -'}]
            this.auth_types = avail_auth_types.concat(response.data.data)
        }).catch( (error) => {
            console.log(error);
        })
    },
    methods: {
        hasValidationError: function (field) {
            if(this.errors != undefined && this.errors.hasOwnProperty('validation')) {
                return this.errors['validation'].hasOwnProperty(field)
            }
        },
        saveUser: function() {

            this.modalUser.isSaving = true
            let values = {
                last_name: this.modalUser.last_name,
                first_name: this.modalUser.first_name,
                email: this.modalUser.email,
                user_group_ids: this.selectedUserGroups,
                auth_type: this.modalUser.auth_type
            }

            if(this.modalUser.id == null) {
                window.axios.post('/api/user', values).then( (response) => {
                    if( response.status == 406 ) {
                        this.errors = response.data.errors
                    } else {
                        this.successfulSave()
                    }
                }).catch( (error) => {
                    this.$store.commit('updateErrors', error)
                }).finally( () => {
                    this.modalUser.isSaving = false
                })
            } else {
                window.axios.put('/api/user/' + this.modalUser.id, {
                    last_name: this.modalUser.last_name,
                    first_name: this.modalUser.first_name,
                    email: this.modalUser.email,
                    user_group_ids: this.selectedUserGroups
                }).then( (response) => {
                    if( response.status == 406 ) {
                        this.errors = response.data.errors
                    } else {
                        this.successfulSave()
                    }
                }).catch( (error) => {
                    this.$store.commit('updateErrors', error)
                }).finally( () => {
                    this.modalUser.isSaving = false
                })
            }
        },
        successfulSave: function() {
            this.modalUser.last_name = ''
            this.modalUser.first_name = ''
            this.modalUser.email = ''
            this.selectedUserGroups = []
            this.modalUser.isSaving = false
            this.$store.dispatch('updateUsers')
            this.$emit('close')
        }
    }
}
</script>

<style scoped>

</style>