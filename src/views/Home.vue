<template>
  <div>
    <main>
      <div>
        <section v-for="category in mainCategories" :key="category.id">
          <Card :category="category" />
        </section>
      </div>
    </main>
    <AppFooter />
  </div>
</template>

<script>
import AppFooter from '@/components/AppFooter'
import Card from '@/components/Card'
import { mapGetters } from 'vuex'

export default {
  name: 'Home',
  components: { AppFooter, Card },
  computed: {
    ...mapGetters(['mainCategories']),
  },
}
</script>

<style scoped>
div {
  display: flex;
  flex-direction: column;
  width: 100%;
}
section {
  margin: 1rem 1rem 2rem 1rem;
}
</style>