<template>
    <li class="dropdown">
        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" aria-haspopup="true" v-pre>Administration</a>
        <ul class="dropdown-menu">
            <router-link v-if="hasSettingPermissions()" to="/settings" tag="li"><a>Settings</a></router-link>
            <router-link v-if="hasUserPermissions()" to="/users" tag="li"><a>User Management</a></router-link>
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
        hasSettingPermissions: function () {
            return true
        },
        hasUserPermissions: function () {
            console.log(this.$store.state.current_user)
            return true
        }
    }
}
</script>

<style scoped>

</style>