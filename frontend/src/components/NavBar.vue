<template>
    <div>
        <div v-if="isLoggedIn">
            <b-navbar toggleable="lg" style="font-size: 18px; background: rgba(0,0,0,0.5);" type="dark" >
                <b-container>
                    <b-navbar-brand style="font-size: 24px;" href="">Chat</b-navbar-brand>

                    <b-navbar-toggle target="nav-collapse"></b-navbar-toggle>

                    <b-collapse id="nav-collapse" is-nav>

                        <!-- Right aligned nav items -->
                        <b-navbar-nav class="ml-auto">

                            <!--<template slot="button-content">Uživatel</template>-->
                            <b-nav-item right :to="{name: 'rooms'}">Rooms</b-nav-item>
                            <b-nav-item right :to="{name: 'profile'}">Profile</b-nav-item>
                            <b-nav-item right v-on:click="logout" :to="{name: 'login'}">Sign out</b-nav-item>

                        </b-navbar-nav>
                    </b-collapse>
                </b-container>
            </b-navbar>
            <div id="overlay"></div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "NavBar",
        computed: {
            isLoggedIn: function () {
                const token = localStorage.getItem('token');
                return token !== null && token !== undefined;
            }
        },
        methods: {
            logout: function () {
                localStorage.removeItem('token');
                window.location.reload();
            }
        }
    }
</script>

<style scoped>

</style>
