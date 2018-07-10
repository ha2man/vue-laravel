<template>
  <div id="app">
    <router-view />
  </div>
</template>

<script>
export default {
  created: function() {
    this.$http.interceptors.response.use(undefined, function(err) {
      return new Promise(function() {
        if (err.status === 401 && err.config && !err.config.__isRetryRequest) {
          this.$store.dispatch("main/logout");
        }
        throw err;
      });
    });
  }
};
</script>
