<template>
    <div>
        <b-container>
            <b-col md="12" class="mt-5 shadow">
                <b-row>
                    <b-col lg="4" class="room-left-col text-light">

                        <!-- Left menu -->
                        <div class="room-header text-white">
                            <b-row class="m-0">
                                <font-awesome-icon v-if="room.lock === true" class="mr-2 my-auto" style="color: rgba(255,255,255,0.4)" icon="lock" />
                                <h4 class="my-auto">{{room.title}}</h4>
                                <b-button v-if="room.id_users_owner === loginUser.id_users" pill v-on:click="deleteRoom" class="ml-auto" type="submit" variant="dark">
                                    <router-link :to="{ name: 'rooms' }">
                                        <font-awesome-icon class="btn-trash" icon="trash-alt" />
                                    </router-link>
                                </b-button>
                            </b-row>
                        </div>

                        <div class="user-list">
                            <h6 style="font-size: smaller">People in this room</h6>
                            <div class="user-title" style="font-size: smaller; color: cornflowerblue;">
                                {{room.name}} {{room.surname}}
                            </div>
                            <div v-for="user in usersIn">
                                <div class="user-title" style="font-size: smaller">
                                    {{user.name}} {{user.surname}}
                                </div>
                            </div>
                        </div>

                        <div class="user-menu">
                            <b-form v-if="(room.id_users === loginUser.id_users) && (room.lock === true)" @submit.prevent="addUser">
                                <b-row class="m-0 pb-2">
                                    <b-form-select style="font-size: smaller" id="user-input-1" required v-model="selected1">
                                        <template slot="first">
                                            <option :value="null" disabled>-- Add user --</option>
                                        </template>

                                        <!--Opravit aby se mohli pridavat pouze uzivatele kteri nejsou v mistnost...
                                        v-if="user.id_users !== usersIn.id_users"-->
                                        <option v-for="user in usersAll" v-bind:value="user.id_users">{{user.name}} {{user.surname}}</option>
                                    </b-form-select>
                                    <b-button pill type="submit" variant="dark" class="btn-user-plus ml-auto">
                                        <font-awesome-icon class="user-plus" icon="user-plus" /></b-button>
                                </b-row>
                            </b-form>

                            <b-form v-if="room.id_users === loginUser.id_users && (room.lock === true)" @submit.prevent="kickUser">
                                <b-row class="m-0 pb-2">
                                    <b-form-select style="font-size: smaller" id="user-input-2" required v-model="selected2">
                                        <template slot="first">
                                            <option :value="null" disabled>-- Kick user --</option>
                                        </template>
                                        <option v-if="user.id_users !== loginUser.id_users" v-for="user in usersIn" v-bind:value="user.id_users">
                                            {{user.name}} {{user.surname}}
                                        </option>
                                    </b-form-select>
                                    <b-button pill type="submit" variant="dark" class="btn-user-minus ml-auto">
                                        <font-awesome-icon class="user-minus" icon="user-minus" />
                                    </b-button>
                                </b-row>
                            </b-form>

                            <!--Tento form tu nemusi byt, ale nevim jak jinak posunou inputy naspod divu-->
                            <b-form v-if="room.id_users === loginUser.id_users && (room.lock === false)" @submit.prevent="kickUser" style="padding-top: 40px">
                                <b-row class="m-0 pb-2">
                                    <b-form-select style="font-size: smaller" id="user-input-2" required v-model="selected2">
                                        <template slot="first">
                                            <option :value="null" disabled>-- Kick user --</option>
                                        </template>
                                        <option v-if="user.id_users !== loginUser.id_users" v-for="user in usersIn" v-bind:value="user.id_users">
                                            {{user.name}} {{user.surname}}
                                        </option>
                                    </b-form-select>
                                    <b-button pill type="submit" variant="dark" class="btn-user-minus ml-auto">
                                        <font-awesome-icon class="user-minus" icon="user-minus" />
                                    </b-button>
                                </b-row>
                            </b-form>

                            <b-button v-if="room.id_users !== loginUser.id_users" pill variant="dark" class="ml-auto" style="margin-top: 80px">
                                <router-link v-on:click.native="deleteUser" :to="{ name: 'rooms'}">
                                    <font-awesome-icon class="btn-arrow-left" icon="arrow-left" />
                                </router-link>
                            </b-button>

                            <b-button v-else pill variant="dark" class="ml-auto">
                                <router-link v-on:click.native="deleteUser" :to="{ name: 'rooms'}">
                                    <font-awesome-icon class="btn-arrow-left" icon="arrow-left" />
                                </router-link>
                            </b-button>
                        </div>
                    </b-col>

                    <!-- Right menu - messages -->
                    <b-col lg="8" class="room-right-col text-dark">

                        <div class="room-body text-dark">

                            <div v-for="message in messages" style="font-size: smaller">
                                <div v-if="message.id_users_to === null">
                                    <div v-if="loginUser.id_users === message.id_users" class="message-text-right">
                                        <b>{{message.login}}:</b> {{message.message}}
                                    </div>

                                    <div v-else="room.id_users !== message.id_users" class="message-text-left">
                                        <b>{{message.login}}:</b> {{message.message}}
                                    </div>
                                </div>
                                <div v-if="message.id_users_to !== null">
                                    <div v-if="(message.id_users_from === loginUser.id_users) || (message.id_users_to === loginUser.id_users)">
                                        <div v-if="loginUser.id_users === message.id_users" class="message-text-right bg-dark text-light">
                                            <b>{{message.login}}:</b> {{message.message}}
                                        </div>

                                        <div v-else="room.id_users !== message.id_users" class="message-text-left bg-dark text-light">
                                            <b>{{message.login}}:</b> {{message.message}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Input for send messages -->
                        <div class="room-footer text-dark">
                            <b-form v-if="lock === true" @submit.prevent="sendWhisper">
                                <b-row class="m-0">
                                    <b-form-input style="font-size: smaller" id="new-message-whisper" v-model="message" placeholder="Whisper text" required></b-form-input>
                                    <b-form-select style="font-size: smaller" id="user-input-3" required v-model="selected3">
                                        <template slot="first">
                                            <option :value="null" disabled>Choose</option>
                                        </template>
                                        <option v-if="user.id_users !== loginUser.id_users" v-for="user in usersIn" v-bind:value="user.id_users">
                                            {{user.name}} {{user.surname}}
                                        </option>
                                    </b-form-select>

                                    <b-form-checkbox class="mx-auto my-auto" v-model="lock">Whisper</b-form-checkbox>
                                    <b-button pill type="submit" variant="dark" class="ml-auto">
                                        <font-awesome-icon class="btn-paper-plane" icon="paper-plane" /></b-button>
                                </b-row>
                            </b-form>

                            <b-form v-else="lock === false" @submit.prevent="sendMessage">
                                <b-row class="m-0">
                                    <b-form-input style="font-size: smaller" id="new-message" v-model="message" placeholder="Message text" required></b-form-input>
                                    <b-form-checkbox class="mx-auto my-auto" v-model="lock">Whisper</b-form-checkbox>
                                    <b-button pill type="submit" variant="dark" class="ml-auto">
                                        <font-awesome-icon class="btn-paper-plane" icon="paper-plane" /></b-button>
                                </b-row>
                            </b-form>
                        </div>

                    </b-col>
                </b-row>
            </b-col>

        </b-container>
    </div>
</template>

<script>
    export default {
        name: "Room",
        data() {
            return {
                roomId: null,
                messages: [],
                reloader: null,
                kickTimer: null,
                userId: null,
                room: [],
                usersIn: [],
                usersAll: [],
                message: "",
                selected1: null,
                selected2: null,
                selected3: null,
                loginUser: [], // logged in user
                lock: null,
                fromKick: []
            }
        },
        // po nacteni komponenty
        mounted() {
            this.loadRoom();
            this.loadUsers();
            this.allUsers();
            this.loadLoginUser();

            const roomId = this.$route.params.id;
            this.roomId = roomId;

            this.reloader = setInterval(() => {
                this.checkKick();
                this.loadMessages();
                this.loadUsers();

                if ((this.usersIn.length == 1) && (this.usersIn[0].id_users != this.room.id_users_owner)) {
                    this.updateOwner();
                }

            }, 1000); // zavola metodu pro stazeni dat kazdou vterinu

        },
        // pred zrusenim komponenty
        beforeDestroy() {
            clearInterval(this.reloader);
        },
        methods: {

            // Load all messages from room by room id
            loadMessages() {
                this.$http.get('api/auth/messages/' + this.roomId)
                    .then(response => {
                        this.messages = response.data;
                    })
            },

            // Load users in this room
            loadUsers() {
                this.$http.get('api/auth/users/' + this.$route.params.id)
                    .then(response => {
                        this.usersIn = response.data;
                    })
            },

            // Load all users
            allUsers() {
                this.$http.get('api/auth/usersAll')
                    .then(response => {
                        this.usersAll = response.data;
                    })
            },

            // Load room info
            loadRoom() {
                this.$http.get('api/auth/room/' + this.$route.params.id)
                    .then(response => {
                        this.room = response.data;
                    })
            },

            // Delete this room
            deleteRoom() {
                this.$http.post('/api/auth/delRoom', {
                    id: this.roomId
                })
                    .then(() => {
                    this.roomId = null;
                    alert("Room deleted!");
                }).catch(() => {
                    alert("Error");
                })
            },


            // Send message
            sendMessage() {
                this.$http.put('/api/auth/sendMessage', {
                    message: this.message,
                    roomId: this.$route.params.id
                })
                //     .then(() => {            // just check error
                //     alert("Message sended!");
                // }).catch(() => {
                //     alert("Error");
                // })
            },

            // Send whisper message
            sendWhisper() {
                this.$http.put('/api/auth/sendWhisper', {
                    message: this.message,
                    roomId: this.roomId,
                    userId: this.selected3
                })
                //     .then(() => {            // just check error
                //     alert("Whisper sended!");
                // }).catch(() => {
                //     alert("Error");
                // })
            },

            // Kick user from this room and set timeout on 5 minutes
            kickUser() {
                this.$http.post('/api/auth/kickUser', {
                    roomId: this.roomId,
                    userId: this.selected2
                })
                    .then(() => {
                        alert("User kicked!");
                    }).catch(() => {
                    alert("Error");
                })

                this.kickTimer = setTimeout(() => {
                    this.$http.post('/api/auth/deleteKick', {
                        roomId: this.roomId,
                        userId: this.selected2
                    })
                }, 300000); // call method to remove kick
            },


            // Delete user from room
            deleteUser() {
                this.$http.post('/api/auth/deleteUser', {
                    roomId: this.roomId,
                    userId: this.loginUser.id_users
                });

                if (this.loginUser.id_users == this.room.id_users_owner) {
                    if ((this.usersIn[0].id_users != null) && (this.usersIn[0].id_users != this.room.id_users_owner)) {
                        alert('New owner:  ' + this.usersIn[0].name + ' ' + this.usersIn[0].surname);
                        this.$http.put('/api/auth/updateRoom', {
                            roomId: this.roomId,
                            userId: this.usersIn[0].id_users
                        });
                    } else {
                        alert('New owner:  ' + this.usersIn[1].name + ' ' + this.usersIn[1].surname);
                        this.$http.put('/api/auth/updateRoom', {
                            roomId: this.roomId,
                            userId: this.usersIn[1].id_users
                        });
                    }
                }
            },

            // Set new owner of this room
            updateOwner() {
                this.$http.put('/api/auth/updateRoom', {
                    roomId: this.roomId,
                    userId: this.usersIn[0].id_users
                });
                alert('You are new owner!');
                this.$router.push({
                    name: 'room',
                    params: {
                        id: this.roomId
                    }
                });
                window.location.reload();
            },

            // Add user to this room. Just owner of this room can do that.
            addUser() {
                this.$http.post('/api/auth/addUser', {
                    roomId: this.roomId,
                    userId: this.selected1
                })
                    .then(() => {
                        alert("User added!");
                    }).catch(() => {
                    alert("Error");
                })
            },

            // Load logged in user
            loadLoginUser() {
                this.$http.get('api/auth/user')
                    .then(response => {
                        this.loginUser = response.data;
                    })
            },

            // Check, if the logged in user is not in kick_room
            checkKick() {
                this.$http.get('api/auth/fromKick/' + this.$route.params.id)
                    .then(response => {
                        this.fromKick = response.data;
                    });

                for (let i = 0; i < this.fromKick.length; i++) {
                    if (this.fromKick[i].id_users == this.loginUser.id_users) {
                        this.deleteUser(); // Pri 2. napojeni pres kick to uzivatele sice vyhodi ale nevymaze z mistnosti
                        this.$router.push({
                            name: 'rooms'
                        });
                        window.location.reload();
                        alert("You have been kicked!");
                    }
                }

            }
        }
    }
</script>

<style scoped>

</style>
