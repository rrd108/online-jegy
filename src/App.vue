<template>
  <div id="app">
    <AppHeader />
    <main class="row align-center">
      <DateSelect v-if="!urlParams" />
      <PaymentInfo v-if="urlParams" :urlParams="urlParams" />
    </main>
  </div>
</template>

<script>
import AppHeader from "./components/AppHeader"
import DateSelect from "./components/DateSelect"
import PaymentInfo from "./components/PaymentInfo"

export default {
  // TODO prices hardcoded in vue and in php
  name: 'App',
  components: {
    AppHeader,
    DateSelect,
    PaymentInfo
  },
  data() {
    return {
      urlParams: window.location.href.split('?')[1],
    }
  },
}
</script>

<style>
@import url("https://fonts.googleapis.com/css?family=Quicksand:500");
@import url("./assets/foundation.min.css");

body {
  background: url("./assets/background.png") #efdfd2 !important;
}

#app {
  font-family: "Quicksand", sans-serif;
  color: #574634;
  min-height: 100vh;
  font-size: 1.4rem;
  margin: 0 2vh;
}

#app h1, #app h2, #app h3 {
  font-family: "Quicksand", sans-serif;
  font-weight: bold;
}

#app input {
  font-size: 1.5rem;
}
</style>
