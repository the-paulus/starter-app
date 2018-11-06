<template>
    <li><a :href="href" :class="{ active: isActive }" v-on:click.prevent @click="go"><slot></slot></a></li>
</template>

<script>
    export default {
        props: {
            href: {
                type: String,
                required: true
            }
        },
        computed: {
            isActive: function() {
                return this.href === this.$root.currentRoute;
            }
        },
        methods: {
            go: function(event) {
                event.preventDefault();
                this.$root.currentRoute = this.href
                window.history.pushState(null, routes[this.href], this.href);
                axios.get(this.href, {
                    Cookie: document.cookie
                }).then(function (response) {
                    console.log(response)
                })
            }
        }
    }
</script>

<style scoped>

</style>