<template>
    <div :class="computedClasses.component">
        <div :class="computedClasses.filters">
            <fieldset class="input-group collapsing">
                <legend><a data-toggle="collapse" href="#search-filters" role="button" aria-expanded="false" aria-controls="search-filters">Filters</legend>
                <div id="search-filters" class="collapse">
                    <div v-for="filter in filters" :key="filter" :class="filterContainerClasses">
                        <label class="list-group-item-text" :for="'permission_' + permission.id" :title="permission.description">
                            <input type="checkbox" :id="'permission_' + permission.id" :value="permission.id" v-model="selectedPermissions"> {{ permission.name }}
                        </label>
                    </div>
                </div>
            </fieldset>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'SearchFilter',
        props: {
            componentClasses: {
                type: Array,
                required: false,
                default: function () {
                    return ['row']
                }
            },
            filters: {
                type: Array,
                required: true,
                default: function () {
                    return []
                }
            },
            filterClasses: {
                type: Array,
                required: false,
                default: function () {
                    return ['columns', 'col-sm-12']
                }
            },
        },
        data: function () {
            return {

            }
        },
        methods: {
            filterContainerClasses: function (input) {
                if( input == 'checkbox' || input == 'radio' ) {
                    return 'list-group-item'
                }
            }
        }
    }
</script>

<style scoped>

</style>