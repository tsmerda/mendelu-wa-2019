<template>
    <div>

        <b-container>
            <b-col md="10" class="mx-auto mt-5 shadow p-0">
                <div class="profile-header text-light text-center">

                    <h4 class="m-0">User {{user.name}} {{user.surname}}</h4>

                </div>
                <div class="profile-body text-light">

                    <!-- Form info about user -->
                    <b-form @submit.prevent="updateUser">
                        <b-row>
                            <b-col md="5" class="mx-auto">
                                <b-form-group label="Login">
                                    <b-form-input type="text" v-model="user.login" required></b-form-input>
                                </b-form-group>
                                <b-form-group label="Password">
                                    <b-form-input type="password" v-model="user.password"></b-form-input>
                                </b-form-group>
                                <b-form-group label="Email">
                                    <b-form-input type="email" v-model="user.email" required></b-form-input>
                                </b-form-group>
                            </b-col>
                            <b-col md="5" class="mx-auto">
                                <b-form-group label="Name">
                                    <b-form-input type="text" v-model="user.name" required></b-form-input>
                                </b-form-group>
                                <b-form-group label="Surname">
                                    <b-form-input type="text" v-model="user.surname" required></b-form-input>
                                </b-form-group>
                                <b-form-group label="Gender">
                                    <b-form-select v-model="user.gender" required>
                                        <option v-model="user.gender" value="male">male</option>
                                        <option v-model="user.gender" value="female">female</option>
                                    </b-form-select>
                                </b-form-group>
                            </b-col>
                        </b-row>
                        <div class="profile-footer text-dark">

                            <hr class="bg-dark">
                            <b-row class="m-0">
                                <b-button pill variant="light" class="mr-auto">
                                    <router-link :to="{ name: 'rooms'}">
                                        <font-awesome-icon class="btn-arrow-left" style="color: black;" icon="arrow-left" />
                                    </router-link>
                                </b-button>
                                <b-button type="submit" pill variant="light" class="ml-auto">
                                    <font-awesome-icon class="btn-save" icon="save" /> Confirm
                                </b-button>
                            </b-row>
                        </div>

                    </b-form>

                </div>
            </b-col>
        </b-container>

    </div>
</template>

<script>
    export default {
        name: "Profile.vue",
        data() {
            return {
                user: [],

            }
        },
        mounted() {
            this.loadProfile();
        },
        methods: {
            // Load user info
            loadProfile() {
                this.$http.get('api/auth/user')
                    .then(response => {
                        this.user = response.data;
                    })
            },

            // Update user info
            updateUser() {
                this.$http.put('api/auth/user', {
                    login: this.user.login,
                    password: this.user.password,
                    email: this.user.email,
                    name: this.user.name,
                    surname: this.user.surname,
                    gender: this.user.gender
                }).then(() => {
                    alert("User updated successfully!");
                }).catch(() => {
                    alert("Error");
                })
            }
        }
    }
</script>

<style scoped>

</style>
