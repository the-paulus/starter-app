<template>
    <div class="component-container settings-container">
        <ul class="nav nav-tabs" role="tablist">
            <li v-for="(group, index) in groups" :key="index" :title="group.name" class="nav-item" :class="{active: group.isActive}">
                <a :href="'#' + nameToId(group)" role="tab" data-toggle="tab">{{ group.name }}</a>
            </li>
        </ul>
        <div class="tab-content">
            <div v-for="(group, gidx) in groups" :key="gidx" role="tab" class="tab-pane" :class="{active:group.isActive}" :id="nameToId(group)">
                <div v-for="(setting, sidx) in group.settings" :key="sidx" class="row col-lg-12">
                    <div v-if="setting.setting_type === 'text'" class="col-lg-12">
                        <label class="control-label col-lg-12">{{ setting.name }}</label>
                        <textarea class="form-control">{{setting.value}}</textarea>
                        <button @click="saveSetting(sidx)" type="button" class="btn btn-default form" :disabled="setting.isSaving">
                            <i class="fa fa-fw" :class="{ 'fa-refresh fa-spin': setting.isSaving, 'fa-save': !setting.isSaving }"></i>
                            <span v-show="!setting.isSaving">Save</span><span v-if="setting.isSaving">Saving</span>
                        </button>
                    </div>
                    <div v-else class="col-lg-12 form-group">
                        <label class="control-label col-lg-12">{{ setting.name }}</label>
                        <input :value="setting.value" type="text" class="form-control" />
                        <button @click="saveSetting(sidx)" type="button" class="btn btn-default " :disabled="setting.isSaving">
                            <i class="fa fa-fw" :class="{ 'fa-refresh fa-spin': setting.isSaving, 'fa-save': !setting.isSaving }"></i>
                            <span v-show="!setting.isSaving">Save</span><span v-if="setting.isSaving">Saving</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'Settings',
    data: function () {
        return {
            group: {
                description: '',
                id: null,
                name: '',
                settings: []
            },
            groups: [],
            setting: {
                description: '',
                id: null,
                isSaving: false,
                name: '',
                setting_type: null
            },
            settings: [],
        }
    },
    computed: {},
    watch: {
        groups: function (newValue, oldValue) {
            if (oldValue.length === 0) {
                this.groups.forEach(function (value, index, array) {
                    if (index === 0) {
                        value['isActive'] = true
                    } else {
                        value['isActive'] = false
                    }
                    this.$set(this.groups, index, value)
                }, this)
            }
            console.log(this.groups)
        },
        settings: function(newValue, oldValue) {
            this.settings.forEach(function (value, index, array) {
                if (!value.hasOwnProperty('isSaving')) {
                    value['isSaving'] = false
                    this.$set(this.settings, index, value)
                }
            }, this)
        },
        token: function (newValue, oldValue) {
            this.getData()
        }
    },
    created: function () {
        console.log(this.groups)
        this.getData()

    },
    mounted: function () {
        this.$on('refresh', this.getData)
    },
    methods: {
        getData: function () {
            window.axios.get('/api/settinggroup').then( (response) => {
                console.log(response)
                this.groups = response.data.data
            }).catch((error) => {
                console.log(error)
            }).finally(() => {
                // Do something...
            })
            window.axios.get('/api/setting').then( (response) => {
                console.log(response)
                this.settings = response.data.data
            })
        },
        nameToId: function (group) {
            return group.name.toLowerCase().replace(' ', '-')
        },
        setActive: function (group) {

        }
    }
}
</script>

<style scoped>

</style>