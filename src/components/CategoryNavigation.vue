<template>
  <nav>
    <ul>
      <li v-for="category in mainCategories" :key="category.id">
      <router-link :to="`/csoport/${category.id}`">
        {{ category.name }}
      </router-link>
      </li>
    </ul>
  </nav>
</template>

<script>
import { mapGetters } from 'vuex'

export default {
  name: 'CategoryNavigation',
  props: ['category'],
  computed: {
    ...mapGetters(['mainCategories']),
  },
}
</script>

<style scoped>
ul {
  display: flex;
  justify-content: space-evenly;
}
a {
  background-color: #574634;
  color: #fff;
  padding: .5rem 1rem;
  border-radius: .5em;
}
.router-link-active {
  box-shadow: 0 0.25em 0.25em #483a1d66;
}
</style>