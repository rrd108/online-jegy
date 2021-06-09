<template>
  <div id="app">
    <router-view />
    <Menu />
  </div>
</template>

<script>
import Menu from '@/components/Menu'

export default {
  name: 'App',
  components: { Menu },
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

main {
  display: flex;
  min-height: 90vh;
  box-sizing: border-box;
  padding: 1.5rem 0 10vh 0; /* 10vh is coming from Footer.vue TODO use sass variables */
}

ul {
  list-style: none;
  margin: 0;
  padding: 0;
}
</style>
