<template>
  <div>
    <nav-bar></nav-bar>

    <div class="container">
      <router-view/>
    </div>
  </div>
</template>

<script>
import Vue from 'vue'
import BootstrapVue from 'bootstrap-vue'

Vue.use(BootstrapVue);


import 'bootstrap/dist/css/bootstrap.css'
import 'bootstrap-vue/dist/bootstrap-vue.css'

import NavBar from "./components/NavBar";
import Styles from "../styles.css";

export default {
  name: 'App',
  components: {NavBar, Styles}
}
</script>
