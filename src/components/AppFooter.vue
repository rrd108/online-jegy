<template>
  <footer @click="l">
    <div id="menu-icons">
      <router-link to="/"
        ><img src="../assets/logo.svg" alt="logo"
      /></router-link>
      <span @click="$store.commit('showMenuToggle')">
        <font-awesome-icon icon="bars" />
      </span>
    </div>
    <Cart />
  </footer>
</template>

<script>
import Cart from '@/components/Cart'

export default {
  name: 'AppFooter',
  components: { Cart },
  methods: {
    l(event) {
      console.log(event.target)
    },
  },
}
</script>

<style scoped>
footer {
  position: fixed;
  bottom: 0;
  background-color: #fff;
  box-sizing: border-box;
  width: 100%;
  z-index: 1;
}
#menu-icons {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin: 0.5rem 1rem -0.5rem 1rem;
}
img {
  height: 2.5rem;
}
</style>