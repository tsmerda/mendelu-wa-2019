<template>
  <div>

    <b-container>
      <!-- Show this alert, if access denied -->
      <b-alert v-model="showDismissibleAlert" variant="danger" class="w-50 mt-2 text-center mx-auto text-dark" dismissible>
        Access denied!
      </b-alert>

      <!-- Show this alert, if the user have been kicked -->
      <b-alert v-model="showDismissibleAlertKick" variant="danger" class="w-50 mt-2 text-center mx-auto text-dark" dismissible>
        You have been kicked!
      </b-alert>

      <b-col cols="12" class="mt-5 mb-5 shadow">
        <b-row>
          <b-col md="4" class="rooms-left-col text-light">

            <!-- List of rooms -->
            <div class="rooms-title">
              <b-row class="m-0">
                <h3 class="my-auto">Rooms</h3>
              </b-row>
            </div>
            <div class="rooms-list">
              <table>
                <tr v-for="room in rooms">
                  <td class="rooms-item">

                    <!-- Access to room if lock = false -->
                    <div v-if="room.lock == false" @click="checkKick(idr = room.id_rooms)" style="cursor: pointer;">
                      <div class="text-light ml-3">
                        {{room.title}}
                      </div>

                    </div>

                    <!-- Access to room if lock = true -->
                    <div v-if="room.lock == true" @click="access(idr = room.id_rooms, owner = room.id_users_owner)" style="cursor: pointer;">
                      <div class="text-light">
                        <b-row>
                          <font-awesome-icon class="ml-2 my-auto" style="margin-right: 9px; color: rgba(255,255,255,0.4)" icon="lock" /> {{room.title}}
                        </b-row>
                      </div>
                    </div>

                  </td>
                </tr>
              </table>
            </div>
          </b-col>
          <b-col md="8" class="rooms-right-col text-dark">
            <!-- Create room -->
            <new-room></new-room>
            <!--<button v-on:click="loadRooms">Obnovit</button>-->
          </b-col>
        </b-row>
      </b-col>
    </b-container>
  </div>
</template>

<script>
import NewRoom from '@/views/NewRoom';

export default {
    components: {NewRoom},
    data() {
        return {
          reloader: null,
          rooms: [],
          roomId: null,
          user: [],
          inRoom: [],
          usersIn: [],
          showDismissibleAlert: false,
          showDismissibleAlertKick: false,
          allKicks: [],

        }
    },
    mounted() {
        this.fromKick();
        this.loadRooms();
        this.loadUser();

      this.reloader = setInterval(() => {
        this.loadRooms();
      }, 1000); // call method for download data every second
    },
    methods: {

        // Load user data
        loadUser() {
          this.$http.get('api/auth/user')
                  .then(response => {
                    this.user = response.data;
                  })
        },

        // Load room list
        loadRooms() {
            this.$http.get('api/auth/rooms')
                .then(response => {
                    this.rooms = response.data;
                });
        },

        // Load all data from room_kick
        fromKick(){
            this.$http.get('api/auth/allKicks')
                .then(response => {
                  this.allKicks = response.data;
                });
          },

        // Add user to room
        addUser(idr) {
          this.getRoom(idr);
          for (let i = 0; i < this.inRoom.length; i++) {
            if (this.inRoom[i].id_rooms == idr && this.inRoom[i].id_users == this.user.id_users){
              return false;
            }
          }
              this.$http.post('/api/auth/addUser', {
                roomId: idr,
                userId: this.user.id_users
              })
                      .then(() => {
                      }).catch(() => {
                alert("Error");
              })

        },

        // Check access of room by room id
        getRoom(idr){
        this.$http.get('api/auth/access/' + idr)
                .then(response => {
                  this.inRoom = response.data;
                });
        },

        // Check access of room for user
        access(idr, owner){
          if (owner == this.user.id_users){
            if (this.checkKick(idr) == false) {
              console.log('access allowed!');           // check access
              this.addUser(idr);
              this.$router.push({name: 'room', params: {id: idr}});
            }
          } else {
            this.getRoom(idr);


            for (let i = 0; i < this.inRoom.length; i++) {
              if (this.inRoom[i].id_users == this.user.id_users) {
                if (this.checkKick(idr) == false) {
                  console.log('access allowed!');     // check access
                  //this.addUser(idr);
                  this.$router.push({name: 'room',  params: {id: idr}});
                }
              } else {                            // chova se zvlastne, jede jen pokud je nekdo v mistnosti --> OPRAVIT
                  console.log('access denied!');    // check access
                  this.showDismissibleAlert=true;
                  // alert('Access denied');
                  // this.$router.push({ name: 'rooms' });
              }
            }
          }
        },

        // Check, if the user is kicked in room by room id
        checkKick(idr){
          this.addUser(idr);
          this.$router.push({name: 'room',  params: {id: idr}});
          // TENTO FOR NEJEDE AZ DOLE SE SPUSTI ELSE??
          for (let i = 0; i < this.allKicks.length; i++) {
            if (this.allKicks[i].id_rooms == idr && this.allKicks[i].id_users == this.user.id_users) {
                this.showDismissibleAlertKick=true;
                return false;
            } else {
                this.addUser(idr);
                this.$router.push({name: 'room',  params: {id: idr}});
              return true;
            }
          }
        },

    }
}
</script>
