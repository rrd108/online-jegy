<template>
  <footer>
    <div>
      <img src="../assets/logo.svg" alt="logo" />
      <font-awesome-icon icon="bars" />
    </div>
    <Cart />
  </footer>
</template>

<script>
import Cart from '@/components/Cart'
export default {
  name: 'AppFooter',
  components: { Cart },
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
  height: 10vh;
  padding: 0 1rem;
}
img {
  height: 2.5rem;
}
</style>