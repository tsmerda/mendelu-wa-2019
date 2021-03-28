<template>
  <div>
    <div id="overlay"></div>
    <b-container>

      <b-card no-body class="access-card mx-auto shadow border-0">


        <b-card-body class="text-light">

          <h3 class="text-light text-center mb-4">MEMBER LOGIN</h3>
          <b-form @submit.prevent="doLogin" class="mx-auto">

            <b-form-group class="access-input-group mx-auto">
              <b-input-group>
                <b-input-group-prepend>
                  <span class="input-group-icon"><font-awesome-icon icon="user"/></span>
                </b-input-group-prepend>
                <b-form-input class="access-input" placeholder="Login" type="text" v-model="login" required></b-form-input>
              </b-input-group>
            </b-form-group>


            <b-form-group class="access-input-group mx-auto">
              <b-input-group>
                <b-input-group-prepend>
                  <span class="input-group-icon"><font-awesome-icon icon="lock"/></span>
                </b-input-group-prepend>
                <b-form-input class="access-input" placeholder="Password" type="password" v-model="password" required></b-form-input>
              </b-input-group>
            </b-form-group>

            <b-button type="submit" block variant="light" class="access-btn my-4 mt-4 mx-auto">
              <font-awesome-icon icon="sign-in-alt"/>
              LOGIN
            </b-button>

          </b-form>
          <hr class="bg-dark">

          <p class="text-center m-0">Create an account?
            <router-link :to="{ name: 'register' }">
              Sign up
            </router-link>
          </p>
        </b-card-body>
      </b-card>

    </b-container>
  </div>
</template>

<script>
  export default {
    data() {
      return  {
        login: '',
        password: '',
      }
    },
    methods: {
      doLogin() {
        this.$http.post('api/login',
                // data posilane na server
                {
                  login: this.login,
                  password: this.password
                }
        ).then(
                // uspesne prihlaseni
                (response) => {
                  const token = response.data.token; // token prijaty ze serveru
                  console.log(token);
                  localStorage.setItem('token', token); // ulozeni tokenu do prohlizece naporad, pak je mozne ho nacitat pomoci localStorage.getItem('token') pri kazdem nacteni aplikace a nastavovat ho do axiosu viz dalsi radek
                  this.$http.defaults.headers.common['Authorization'] = response.data.token; // ulozeni docasne dovnitr axiosu, bude pouzito pro vsechny pozadavky na server, po obnoveni stranky zmizi
                  // presmerovani na jinou stranku po uspesnem loginu
                  if (this.$route.params.nextUrl !== undefined) {
                    this.$router.push({ path: this.$route.params.nextUrl });
                  } else {
                    this.$router.push({ name: 'rooms'});
                  }
                  window.location.reload();
                }
        ).catch(
                // neuspech
                (error) => {
                  console.log(error);
                  alert("Check your credentials.");
                }
        );
      }
    }
  }
</script>
