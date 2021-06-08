<template>
  <transition name="bounce">
    <aside v-show="$store.state.menuOpen">
      <ul>
        <li v-for="category in mainCategories" :key="category.id">
          {{ category.name }}
        </li>
        <li>Kosár</li>
        <li>Infók</li>
      </ul>
    </aside>
  </transition>
</template>

<script>
import { mapGetters } from 'vuex'

export default {
  name: 'Menu',
  computed: {
    ...mapGetters(['mainCategories']),
  },
}
</script>

<style scoped>
aside {
  position: fixed;
  box-sizing: border-box;
  top: 0;
  right: 0;
  width: 15rem;
  height: 93vh;
  z-index: 1;
  background: #fff;
  transform-origin: right bottom;
  transition: transform 350ms ease;
  font-size: 2rem;
  font-weight: bold;
  padding: 1em;
}
li {
  margin: .5em 0;
}

.bounce-enter,
.bounce-leave-to {
  transform: translateX(15rem);
}
.bounce-enter-to,
.bounce-leave-leave {
  transform: translateX(0);
}
.bounce-enter-active,
.bounce-leave-active {
  transition: transform 350ms ease;
}
</style>