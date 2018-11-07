<template>
    <li v-if="showAdminMenu()" class="dropdown">
        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" aria-haspopup="true" v-pre>Administration</a>
        <ul class="dropdown-menu">
            <router-link v-if="hasPermission('update settings')" to="/settings" tag="li"><a>Settings</a></router-link>
            <router-link v-if="hasPermission('update users')" to="/users" tag="li"><a>User Management</a></router-link>
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
        current_user: function() {
            return this.$store.state.current_user
        }
    },
    methods: {
        hasPermission: function (permission) {
            try {
                return Object.values(this.current_user.user_permissions).includes(permission)
            } catch(e) { }
        },
        showAdminMenu: function() {
            try {
                var current_permissions = Object.values(this.current_user.user_permissions)
                return current_permissions.includes('update settings') || current_permissions.includes('update users') || current_permissions.includes('update user groups')
            } catch (e) { }

        }
    }
}
</script>

<style scoped>

</style>