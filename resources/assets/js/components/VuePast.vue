<template>
    <!-- Begin Component -->
    <div class="component-container" :class="computedClasses.componentClasses">
        <!-- Begin Table -->
        <table :class="computedClasses.tableClasses">
            <!-- Begin Table Head -->
            <thead v-if="pastConfig.hasHeader">
                <!-- Search Component Row -->
                <tr v-if="pastConfig.hasSearch">
                    <th :colspan="columnCount">
                        <search-bar :axios-search-request-config="axiosSearchRequestConfig" v-model="searchResults"></search-bar>
                    </th>
                </tr>
                <!-- End Search Component Row -->
                <!-- Begin Table Column Titles Row -->
                <tr>
                    <!-- Begin Table Column Titles -->
                    <th v-for="(column, cidx) in pastConfig.headerColumns" :key="cidx" :class="pastConfig.headerClasses">
                        <a v-if="column.sortable" href="#" @click="sortData(column.name)">{{ column.label }}
                          <i :class="sortClass(column)"></i>
                        </a>
                        <span v-else href="#" @click="sortData(column.name)">{{ column.label }}</span>
                    </th>
                    <!-- End Table Column Titles -->
                    <!-- Begin Add/Delete/Edit Buttons Table Column -->
                    <th v-if="pastConfig.canAdd || pastConfig.canDelete || pastConfig.canEdit" style="width: 105px; text-align: right;">
                        <button v-if="pastConfig.canAdd" class="btn btn-default add" title="New" @click="$emit('addRow')" type="button" style="width: 87px;">
                            <i class="fa fa-fw fa-plus"></i> New
                        </button>
                    </th>
                    <!-- End Add/Delete/Edit Buttons Table Column -->
                </tr>
                <!-- End Table Column Titles Row -->
            </thead>
            <!-- End Table Head -->
            <!-- Begin Table Body -->
            <tbody>
            <!-- Begin Data Rows -->
            <tr v-if="true" v-for="(row, ridx) in data" :key="ridx">
                <!-- Begin Data Columns -->
                <td v-for="(rel, ridx) in pastConfig.headerColumns" :key="ridx" :class="pastConfig.tableDataClasses">
                    <!-- When the field of a row is an array, format it as an list. -->
                    <ul v-if="typeof row[rel.name] == 'object'">
                        <li v-for="(item, iidx) in row[rel.name]" :key="iidx">{{ item }}</li>
                    </ul>
                    <!-- Or else, display it in a span tag -->
                    <span v-else>{{ row[rel.name] }}</span>
                </td>
                <!-- End Data Columns -->
                <!-- Begin Delete/Edit buttons -->
                <td v-if="pastConfig.canDelete || pastConfig.canEdit" :class="pastConfig.tableDataClasses">
                    <!-- Begin Button Group Div -->
                    <div class="btn-group" style="width: auto;">
                        <!-- Edit Button -->
                        <button v-if="pastConfig.canEdit" @click="$emit('editRow', ridx)" class="btn btn-default edit" title="Edit" type="button">
                            <i class="fa fa-fw" :class="{'fa-refresh fa-spin': isDisabled(ridx), 'fa-pencil': !isDisabled(ridx)}" ></i>
                        </button>
                        <!-- End Edit Button -->
                        <!-- Begin Delete Button -->
                        <button v-if="pastConfig.canDelete" @click="$emit('deleteRow', ridx)" class="btn btn-default remove" title="Delete group" type="button">
                            <i class="fa fa-fw" :class="{'fa-refresh fa-spin': isDisabled(ridx), 'fa-trash': !isDisabled(ridx)}"></i>
                        </button>
                        <!-- End Delete Button -->
                    </div>
                    <!-- End Button Group Div -->
                </td>
                <!-- End Delete/Edit Buttons -->
            </tr>
            <!-- End Data Rows -->
            </tbody>
            <!-- End Table Body -->
            <!-- Begin Table Footer -->
            <tfoot>
            <!-- Begin Pager Row -->
            <tr>
                <!-- Begin Pager Table Data -->
                <td :colspan="columnCount">
                    <v-page :setting="pastConfig.pagerSettings" @page-change="pageChange"></v-page>
                </td>
                <!-- End Pager Table Data -->
            </tr>
            <!-- End Pager Row -->
            </tfoot>
            <!-- End Table Footer -->
        </table>
    </div>
</template>

<script>
import SearchBar from "./SearchBar";
import UserModal from "./UserModal"

export default {
    name: 'Users',
    components: {SearchBar},
    props: {
        pastConfig: {
            type: Object,
            required: true,
            default: function () {
                return {
                    canAdd: true,
                    canDelete: true,
                    canEdit: true,
                    componentClasses: [],
                    defaultSort: '',
                    defaultOrder: '',
                    hasFooter: true,
                    hasHeader: true,
                    hasPager: true,
                    hasSearch: true,
                    headerClasses: [],
                    headerColumns: [],
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
                    ],
                    tableFooterClasses: [],
                    tableHeaderClasses: []
                }
            }
        },
        pagination: {
            type: Object,
            required: false
        },
        axiosSearchRequestConfig: {
            type: Object,
            required: false
        },
        value: {}
    },
    data: function () {
        return {
            currentOrder: '',
            currentSort: '',
            error: {},
            filters: [
                {
                    name: 'user_group_ids',
                    label: 'User Group(s)',
                    type: 'select',
                    data: null
                }
            ],
            pageInfo: {},
            searchResults: {}
        }
    },
    computed: {
        columnCount: function () {
            let numColumns = (this.pastConfig.headerColumns != 'undefined') ? this.pastConfig.headerColumns.length : 1
            return (this.pastConfig.canAdd || this.pastConfig.canDelete || this.pastConfig.canEdit) ? numColumns += 1 : numColumns
        },
        computedClasses: function () {
            let classes = {}

            try {
                classes.componentClasses = this.pastConfig.componentClasses.join(' ')
            } catch (error) {
                classes.componentClasses = ''
            }

            try {
                classes.headerClasses = this.pastConfig.headerClasses.join(' ')
            } catch (error) {
                classes.headerClasses = ''
            }

            try {
                classes.tableClasses = this.pastConfig.tableClasses.join(' ')
            } catch (error) {
                classes.tableClasses = ''
            }

            try {
                classes.tableDataClasses = this.pastConfig.tableDataClasses.join(' ')
            } catch (error) {
                classes.tableDataClasses = ''
            }

            try {
                classes.tableFooterClasses = this.pastConfig.tableFooterClasses.join(' ')
            } catch (error) {
                classes.tableFooterClasses = ''
            }

            try {
                classes.tableHeaderClasses = this.pastConfig.tableHeaderClasses.join(' ')
            } catch (error) {
                classes.tableHeaderClasses = ''
            }

            return classes
        },
        data: {
            get: function () {
                try {
                    if(this.searchResults.hasOwnProperty('data')) {
                        this.pastConfig.pagerSettings.totalRow = this.searchResults.total
                        return this.searchResults.data
                    } else {
                        this.pastConfig.pagerSettings.totalRow = this.value.total
                        return this.value.data
                    }

                } catch (e) {

                }
            },
            set: function(newValue) {
                this.data = newValue
            }
        },
        headerSortClasses: function () {

        }
    },
    watch: {
        // TODO: Do we need this?
        pastConfig: function (newValue, oldValue) {

            try {
                newValue.headerColumns.forEach( function (element, index, arr) {
                    if (!this[index].hasOwnProperty('orderBy')) {

                    }
                }, newValue.headerColumns)

                if (this.currentSort === '' || this.currentOrder === '') {

                }

                if(!newValue.hasOwnProperty('canAdd')) {
                    this.$set(this.pastConfig, 'canAdd', true)
                }

                if(!newValue.hasOwnProperty('canEdit')) {
                    this.$set(this.pastConfig, 'canEdit', true)
                }

                if(!newValue.hasOwnProperty('canDelete')) {
                    this.$set(this.pastConfig, 'canDelete', true)
                }

                if(!newValue.hasOwnProperty('componentClasses')) {
                    this.$set(this.pastConfig, 'componentClasses', [])
                }

                if(!newValue.hasOwnProperty('currentSort')) {
                    this.$set(this.pastConfig, 'currentSort', '')
                }

                if(!newValue.hasOwnProperty('currentOrder')) {
                    this.$set(this.pastConfig, 'currentOrder', '')
                }

                if(!newValue.hasOwnProperty('hasFooter')) {
                    this.$set(this.pastConfig, 'hasFooter', true)
                }

                if(!newValue.hasOwnProperty('hasHeader')) {
                    this.$set(this.pastConfig, 'hasHeader', true)
                }

                if(!newValue.hasOwnProperty('hasPager')) {
                    this.$set(this.pastConfig, 'hasPager', true)
                }

                if(!newValue.hasOwnProperty('hasSearch')) {
                    this.$set(this.pastConfig, 'hasSearch', true)
                }

                if(!newValue.hasOwnProperty('headerClasses')) {
                    this.$set(this.pastConfig, 'headerClasses', [])
                }

                if(!newValue.hasOwnProperty('headerColumns')) {
                    this.$set(this.pastConfig, 'headerColumns', [])
                }

                if(!newValue.hasOwnProperty('pagerSettings')) {
                    this.$set(this.pastConfig, 'pagerSettings', {
                        totalRow: 0,
                        language: 'en',
                        pageSizeMenu: [15, 25, 50, 100],
                        info: true,
                        align: 'center'
                    })
                }

                if(!newValue.hasOwnProperty('tableClasses')) {
                    this.$set(this.pastConfig, 'tableClasses', [
                        'table-responsive',
                        'table-striped',
                        'form-rows',
                        'table table-bordered',
                        'table-content'
                    ])
                }

                if(!newValue.hasOwnProperty('tableDataClasses')) {
                    this.$set(this.pastConfig, 'tableDataClasses', [
                        'table-data-max',
                        'table-data'
                    ])
                }

                if(!newValue.hasOwnProperty('tableFooterClasses')) {
                    this.$set(this.pastConfig, 'tableFooterClasses', [])
                }

                if(!newValue.hasOwnProperty('tableHeaderClasses')) {
                    this.$set(this.pastConfig, 'tableHeaderClasses', [])
                }

            } catch (e) {

            }
        },
    },
    created: function () {

    },
    mounted: function() {
        this.$on('clearSearch', this.clearSearchResults)
        this.$on('search', this.$emit('search'))
    },
    methods: {
        addHeaderComponentClasses: function () {
            // TODO: sorting classes
            return this.headerClasses
        },
        clearSearchResults: function () {
            this.$parent.$emit('clearSearch')
            this.searchResults = {}
        },
        isDisabled: function(index) {
            try {
                return this.users[index].isSaving || this.groups[index].isDeleting
            } catch (e) {
                return false;
            }
        },
        pageChange: function (pInfo) {
            this.$emit('pageChanged', pInfo)
        },
        sortClass: function (column) {

            let classes = 'fa fa-fw '

            if (column.name === this.currentSort) {

                if (this.currentOrder === 'DESC') {
                    classes += 'fa-angle-double-up'
                }

                if (this.currentOrder === 'ASC'){
                    classes += 'fa-angle-double-down'
                }
            }

            return classes

        },
        sortData: function (sort, order) {

            if(this.currentSort === sort ) {

                if(this.currentOrder === 'ASC') {
                    this.currentOrder = 'DESC'
                } else {
                    this.currentOrder = 'ASC'
                }

            } else {
                this.currentOrder = 'ASC'
            }

            this.currentSort = sort

            this.$emit('sortData', {sort: this.currentSort, order: this.currentOrder})
        }
    }

}
</script>

<style scoped>
table thead tr th i.fa.fa-fw {
    float: right;
}

.table-head {
    overflow: hidden;
    position: relative;
    border: 0px;
    width: 100%;
}

.table-head-inner {
    box-sizing: content-box;
}
</style>