// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import Vue from 'vue';
import App from './App';
import router from './router';
import Axios from 'axios';
import { library } from '@fortawesome/fontawesome-svg-core'
import { faCoffee, faSignInAlt } from '@fortawesome/free-solid-svg-icons'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import {faPlus} from "@fortawesome/free-solid-svg-icons/faPlus";
import {faSyncAlt} from "@fortawesome/free-solid-svg-icons/faSyncAlt";
import {faPaperPlane} from "@fortawesome/free-solid-svg-icons/faPaperPlane";
import {faSave} from "@fortawesome/free-solid-svg-icons/faSave";
import {faTrashAlt} from "@fortawesome/free-solid-svg-icons/faTrashAlt";
import {faUserPlus} from "@fortawesome/free-solid-svg-icons/faUserPlus";
import {faUserMinus} from "@fortawesome/free-solid-svg-icons/faUserMinus";
import {faArrowLeft} from "@fortawesome/free-solid-svg-icons/faArrowLeft";
import {faUser} from "@fortawesome/free-solid-svg-icons/faUser";
import {faLock} from "@fortawesome/free-solid-svg-icons/faLock";
import {faLockOpen} from "@fortawesome/free-solid-svg-icons/faLockOpen";

// nastaveni knihovny pro synchronizaci dat
const axios = Axios.create();
axios.defaults.baseURL = 'https://akela.mendelu.cz/~xsmerda/wa/backend/public/'; // base url for all server requests
Vue.prototype.$http = axios; // inject axios into all Vue components

// automaticke obnoveni prihlaseni, uzivatel je prihlasen na trvalo
const token = localStorage.getItem('token');
if (token !== null && token !== undefined) {
    Vue.prototype.$http.defaults.headers.common['Authorization'] = token; // bude pouzito pro vsechny pozadavky na server
}

// nastaveni knihovny na ikonky, inicializace konkretnich ikonek
library.add(faCoffee);
library.add(faSignInAlt);
library.add(faPlus);
library.add(faSyncAlt);
library.add(faPaperPlane);
library.add(faSave);
library.add(faTrashAlt);
library.add(faUserPlus);
library.add(faUserMinus);
library.add(faArrowLeft);
library.add(faUser);
library.add(faLock);
library.add(faLockOpen);

Vue.component('font-awesome-icon', FontAwesomeIcon);


Vue.config.productionTip = false;

/* eslint-disable no-new */
new Vue({
  el: '#app',
  router,
  components: { App },
  template: '<App/>'
});
