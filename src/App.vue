<template>
  <div id="app">
    <router-view />
  </div>
</template>

<script>
export default {
  name: 'App',
  created() {
    this.$store.dispatch('getCategories')
    this.$store.dispatch('getProducts')
  },
}
</script>


<style>
@import url('https://fonts.googleapis.com/css?family=Quicksand:500');

body {
  background-color: #efdfd2;
  margin: 0;
}

#app {
  font-family: 'Quicksand', sans-serif;
  color: #574634;
  min-height: 100vh;
}
</style>
