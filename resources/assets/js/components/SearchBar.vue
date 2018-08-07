<template>
    <!-- Start Search Block -->
    <div :class="computedClasses.component">
        <!-- Begin search input -->
        <div :class="computedClasses.search">
            <div class="input-group">
                <input type="text" class="form-control" placeholder="Search..." v-model="searchQuery" @keypress.enter="search" v-esc="clearSearch"/>
                <span class="input-group-btn">
                    <a v-show="!didSearch" class="btn btn-default" @click="search"><i class="fa" :class="{ 'fa-search': !isSearching, 'fa-refresh fa-spin': isSearching }"></i> Search</a>
                    <a v-show="didSearch" class="btn btn-default" @click="clearSearch"><i class="fa fa-search-minus"></i> Clear</a>
                </span>
            </div>
        </div>
        <!-- End search input -->

        <!-- Begin Filter -->
        <div class="col-sm-12" :class="computedClasses.filters" v-if="filters !== 'undefined' && filters.length">
            <fieldset class="input-group collapse">
                <legend>Filters</legend>
            </fieldset>
        </div>
        <!-- End Filter -->
    </div>
    <!-- End Search Block -->
</template>

<script>
export default {
    name: 'SearchBar',
    props: {
        searchConfig: {
            type: Object,
            required: true
        },
        clearSearchConfig: {
            type: Object,
            required: false,
            default: function () {
                return this.callbackUri
            }
        },
        componentClasses: {
            type: Array,
            required: false,
            default: function () {
                return ['row']
            }
        },
        filters: {
            type: Array,
            required: false,
            default: function () {
                return []
            }
        },
        searchClasses: {
            type: Array,
            required: false,
            default: function () {
                return ['columns', 'col-sm-12']
            }
        },
        value: {
            type: Object,
            required: true,
            default: function () {
                return []
            }
        }
    },
    data: function () {
        return {
            didSearch: false,
            filterResults: [],
            isSearching: false,
            searchQuery: ''
        }
    },
    computed: {
        computedClasses: function () {
            var classes = {}

            try {
                classes.search = this.searchClasses.join(' ')
            } catch (e) {
                classes.search = ''
            }
            try {
                classes.component = this.componentClasses.join(' ')
            } catch (e) {
                    classes.component = ''
            }
            try {
                classes.filters = this.filterClasses.join(' ')
            } catch (e) {
                    classes.filters = ''
            }

            return classes
        }
    },
    methods: {
        clearSearch: function() {
            window.axios.request(this.clearSearchConfig).then((response) => {
                this.$emit('input', response.data)
            }).finally(() => {
                this.didSearch = false
            })
        },
        search: function () {
            this.isSearching = true
            this.searchConfig.data = {
                q: this.searchQuery
            }

            window.axios.request(this.searchConfig).then((response) => {
                this.$emit('input', response.data)
            }).finally(() => {
                this.isSearching = false
                this.didSearch = true
            })
        }
    }
}
</script>

<style scoped>

</style>