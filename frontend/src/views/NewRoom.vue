<template>
    <div>
        <div class="rooms-title">
            <h3>Create new room</h3>
        </div>
        <div class="new-room-body">
            <b-form @submit.prevent="createRoom">
                <b-row>
                    <b-col md="8" sm="12">
                        <b-form-input id="new-room" v-model="name" placeholder="Room title" required></b-form-input>
                    </b-col>
                    <b-col md="4" sm="12" class="my-auto">
                        <b-row>
                            <b-form-checkbox class="mx-auto my-auto" v-model="lock">Private</b-form-checkbox>
                            <b-button pill type="submit" variant="dark" class="ml-auto mr-3">
                                <font-awesome-icon class="btn-plus" icon="plus" /></b-button>
                        </b-row>
                    </b-col>
                </b-row>
            </b-form>
        </div>
    </div>
</template>

<script>
    export default {
        name: "NewRoom",
        data() {
            return {
                name: "",
                lock: false

            }
        },
        methods: {

            // Create lock/unlock room
            createRoom() {
                if (this.lock === false) {
                    this.lock = 0;
                } else {
                    this.lock = 1;
                }

                this.$http.post('/api/auth/rooms', {
                    title: this.name,
                    lock: this.lock
                }).then(() => {
                    console.log(this.lock); // check in console if the room is private or not
                    alert("Room created!");
                }).catch(() => {
                    alert("Error");
                })
            }
        }
    }
</script>

<style scoped>

</style>
