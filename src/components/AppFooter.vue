<template>
  <footer @click="l">
    <div>
      <img src="../assets/logo.svg" alt="logo" />
      <span @click="$store.commit('menuToggle')">
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
div {
  display: flex;
  justify-content: space-between;
  align-items: center;
  min-height: 10vh;
  padding: 0 1rem;
}
img {
  height: 2.5rem;
}
</style>