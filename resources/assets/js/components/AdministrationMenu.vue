<template>
    <li class="dropdown">
        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" aria-haspopup="true" v-pre>Administration</a>
        <ul class="dropdown-menu">
            <router-link to="/groups" tag="li"><a>Groups</a></router-link>
            <router-link to="/users" tag="li"><a>Users</a></router-link>
        </ul>
    </li>
</template>

<script>
    export default {
        mounted: function () {

        },
        data: function () {
            return {

            }
        },
        computed: {

        },
        methods: {
            hasGroupPermissions: function () {
                console.log(document.cookie)
                return true
            },
            hasSettingPermissions: function () {
                return true
            },
            hasUserPermissions: function () {
                return true
            }
        }
    }
</script>

<style scoped>

</style>