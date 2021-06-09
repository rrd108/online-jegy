<template>
  <transition name="bounce">
    <aside
      v-show="$store.state.showMenu"
      @click="$store.commit('showMenuToggle')"
    >
      <ul>
        <li><router-link to="/">Főoldal</router-link></li>
        <li v-for="category in mainCategories" :key="category.id">
          {{ category.name }}
        </li>
        <li @click="$store.commit('showCartToggle')">Kosár</li>
        <li><router-link to="/info">Infók</router-link></li>
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
  width: 20rem;
  transform: translateX(5rem);
  height: 93vh;
  z-index: 1;
  background: #fff;
  transform-origin: right bottom;
  font-size: 2rem;
  font-weight: bold;
  padding: 1em;
}
li {
  margin: 0.5em 0;
}

a {
  text-decoration: none;
  color: #574634;
}

.bounce-enter-active {
  animation: bounce 350ms ease;
}
.bounce-leave-active {
  animation: bounce 350ms ease reverse;
}

@keyframes bounce {
  0% {
    transform: translateX(15rem);
  }
  75% {
    transform: translateX(0);
  }
  100% {
    transform: translateX(5rem);
  }
}
</style>