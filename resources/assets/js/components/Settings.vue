<template>
    <div class="component-container settings-container">
        <ul class="nav nav-tabs" role="tablist">
            <li v-for="(group, index) in groups" :key="index" :title="group.name" class="nav-item" :class="{active: index === activeTab}">
                <a :href="'#' + nameToId(group)" role="tab" data-toggle="tab">{{ group.name }}</a>
            </li>
        </ul>
        <div class="tab-content">
            <div v-for="(group, gidx) in groups" :key="gidx" role="tab" class="tab-pane" :class="{active: gidx === activeTab}" :id="nameToId(group)">
                <div v-for="(setting, sidx) in group.settings" :key="sidx" class="row col-lg-12">
                    <div v-if="setting.setting_type === 'text'" class="col-lg-12">
                        <label class="control-label col-lg-12">{{ setting.name }}</label>
                        <textarea :name="'setting_' + setting.id" v-model="setting.value" class="form-control"></textarea>
                        <button @click="saveSetting(gidx, sidx)" type="button" class="btn btn-default form pull-right" :disabled="setting.isSaving">
                            <i class="fa fa-fw" :class="{ 'fa-refresh fa-spin': setting.isSaving, 'fa-save': !setting.isSaving }"></i>
                            <span v-show="!setting.isSaving">Save</span><span v-if="setting.isSaving">Saving</span>
                        </button>
                    </div>
                    <div v-else-if="setting.setting_type === 'html'" class="col-lg-12">
                        <label class="control-label col-lg-12">{{ setting.name }}</label>
                        <wysiwyg :name="'setting_' + setting.id" v-model="setting.value" style="clear:both;"></wysiwyg>
                        <button @click="saveSetting(gidx, sidx)" type="button" class="btn btn-default form pull-right" :disabled="setting.isSaving">
                            <i class="fa fa-fw" :class="{ 'fa-refresh fa-spin': setting.isSaving, 'fa-save': !setting.isSaving }"></i>
                            <span v-show="!setting.isSaving">Save</span><span v-if="setting.isSaving">Saving</span>
                        </button>
                    </div>
                    <div v-else-if="setting.setting_type === 'date' || setting.setting_type === 'email'" class="col-lg-12 form-group-textfield">
                        <label class="control-label">{{ setting.name }}</label>
                        <input :type="setting.setting_type" class="form-control" :value="setting.value"/>
                        <button @click="saveSetting(gidx, sidx)" type="button" class="btn btn-default form" :disabled="setting.isSaving">
                            <i class="fa fa-fw" :class="{ 'fa-refresh fa-spin': setting.isSaving, 'fa-save': !setting.isSaving }"></i>
                            <span v-show="!setting.isSaving">Save</span><span v-if="setting.isSaving">Saving</span>
                        </button>
                    </div>
                    <div v-else-if="setting.setting_type === 'integer' || setting.setting_type === 'number'" class="col-lg-12 form-group-textfield">
                        <label class="control-label">{{ setting.name }}</label>
                        <input type="number" class="form-control" :value="setting.value"/>
                        <button @click="saveSetting(gidx, sidx)" type="button" class="btn btn-default form" :disabled="setting.isSaving">
                            <i class="fa fa-fw" :class="{ 'fa-refresh fa-spin': setting.isSaving, 'fa-save': !setting.isSaving }"></i>
                            <span v-show="!setting.isSaving">Save</span><span v-if="setting.isSaving">Saving</span>
                        </button>
                    </div>
                    <div v-else class="col-lg-12 form-group-textfield">
                        <label class="control-label">{{ setting.name }}</label>
                        <input :value="setting.value" type="text" class="form-control" />
                        <button @click="saveSetting(gidx, sidx)" type="button" class="btn btn-default " :disabled="setting.isSaving">
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
            activeTab: 0,
            group: {
                description: '',
                id: null,
                name: '',
                settings: []
            },
            setting: {
                description: '',
                id: null,
                isSaving: false,
                name: '',
                setting_type: null
            },
        }
    },
    computed: {
        groups: function() {

            return this.$store.state.settingGroups.data
        },
        settings: function() {
            return  this.groups.settings
        }
    },
    watch: { },
    created: function () { },
    mounted: function () {
        this.$store.dispatch('updateSettingGroups')
        this.$store.dispatch('updateSettings')
    },
    methods: {
        nameToId: function (group) {
            return group.name.toLowerCase().replace(' ', '-')
        },
        saveSetting: function (gidx, sidx) {
            window.axios.put(process.env.MIX_BACKEND_URL + '/setting/' + this.groups[gidx].settings[sidx].id,
                { value: this.groups[gidx].settings[sidx].value }).then( (response) => {

            }).catch( (reason) => {

                this.$store.commit('updateErrors',reason);

            })
        }
    }
}
</script>

<style scoped>

</style>