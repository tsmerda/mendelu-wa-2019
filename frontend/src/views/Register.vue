<template>
  <div>
    <div id="overlay"></div>
    <b-container>

      <b-card no-body class="access-card mx-auto shadow border-0">

        <b-card-body class="text-light">
          <h3 class="text-light text-center mb-4">CREATE AN ACCOUNT</h3>

          <b-form @submit.prevent="register" class="mx-auto">
            <div class="reg-input-group">
              <b-form-group label="Login">
                <b-form-input class="reg-input" type="text" v-model="login" required></b-form-input>
              </b-form-group>
              <b-form-group label="Heslo">
                <b-form-input class="reg-input" type="password" v-model="password1" required></b-form-input>
              </b-form-group>
              <b-form-group label="Heslo znovu">
                <b-form-input class="reg-input" type="password" v-model="password2" required></b-form-input>
              </b-form-group>
              <b-form-group label="Jméno">
                <b-form-input class="reg-input" type="text" v-model="name" required></b-form-input>
              </b-form-group>
              <b-form-group label="Příjmení">
                <b-form-input class="reg-input" type="text" v-model="surname" required></b-form-input>
              </b-form-group>
              <b-form-group label="Email">
                <b-form-input class="reg-input" type="email" v-model="email" required></b-form-input>
              </b-form-group>
              <b-form-group label="Pohlaví">
                <b-form-select class="reg-input" v-model="gender" required>
                  <option value="male">male</option>
                  <option value="female">female</option>
                </b-form-select>
              </b-form-group>
            </div>

            <b-button type="submit" block variant="light" class="access-btn my-4 mt-4 mx-auto">
              <font-awesome-icon icon="sign-in-alt" /> SIGN UP
            </b-button>

          </b-form>
          <hr class="bg-dark">

          <p class="text-center m-0">Do you have an account?
            <router-link :to="{ name: 'login' }">
              Log in
            </router-link>
          </p>

        </b-card-body>
      </b-card>

    </b-container>
  </div>
</template>

<script>
  export default {
    //jako data: function() {}
    data() {
      return {
        name: '',
        surname: '',
        email: '',
        password1: '',
        password2: '',
        gender: 'male',
        login: ''
      };
    },
    methods: {
      register() {
        if (this.password1 != this.password2) {
          alert('Hesla se neshoduji.');
          return;
        }
        this.$http.post('api/register', {
          name: this.name,
          surname: this.surname,
          login: this.login,
          password: this.password1,
          gender: this.gender,
          email: this.email
        }).then(() => {
          this.$router.push({
            name: 'login'
          });
        }, () => {
          alert('Chyba registrace');
        });
      }
    }
  }
</script>
