<template>
  <div id="app">
    <div id="nav">
      <router-link to="/">Home</router-link> |
      <router-link to="/about">About</router-link>
    </div>
    <router-view/>
  </div>
</template>

<style>
@import url("https://fonts.googleapis.com/css?family=Quicksand:500");

body {
  background: url("./assets/background.png") #efdfd2 !important;
  margin: 0;
}

#app {
  font-family: "Quicksand", sans-serif;
  color: #574634;
  min-height: 100vh;
  font-size: 1.4rem;
}

#nav {
  padding: 30px;
}

#nav a {
  font-weight: bold;
  color: #2c3e50;
}

#nav a.router-link-exact-active {
  color: #42b983;
}
</style>
