import Vue from "vue";
import App from "./App.vue";
import router from "./router";
import store from "./store";
import axios from "axios";
import "./global-components";
import "./utils";
import "./libs";

// Midone Theme
import "./assets/sass/app.scss";

Vue.config.productionTip = false;

Vue.prototype.$http = axios;
Vue.prototype.$http.defaults.baseURL =
  location.protocol + "//" + location.hostname + "/api";
// "http://phpstack-553220-1826317.cloudwaysapps.com/api";
const token = localStorage.getItem("token");
if (token) {
  Vue.prototype.$http.defaults.headers.common["Authorization"] =
    "Bearer " + token;
}

import "vue-multiselect/dist/vue-multiselect.min.css";
import "./assets/sass/custom.scss";

new Vue({
  router,
  store,
  render: h => h(App)
}).$mount("#app");
