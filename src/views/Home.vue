<template>
  <main>
    <div>
      <section v-for="category in mainCategories" :key="category.id">
        <Card :category="category" />
      </section>
    </div>
    <AppFooter />
  </main>
</template>

<script>
import AppFooter from '@/components/AppFooter'
import Card from '@/components/Card'

export default {
  name: 'Home',
  components: { AppFooter, Card },
  computed: {
    mainCategories() {
      return this.$store.state.categories.filter((category) => category.parent == 0)
    },
  },
}
</script>

<style scoped>
main {
  display: flex;
  align-items: flex-end;
  height: 90vh;
}
div {
  display: flex;
  flex-direction: column;
  width: 100%;
}
section {
  margin: 1rem;
}
</style>