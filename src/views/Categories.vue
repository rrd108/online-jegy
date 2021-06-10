<template>
  <div class="content">
    <CategoryNavigation :category="category" />

    <main>
      <Card :category="category" />
      <div class="subcategory">
        <section
          v-for="subCategory in $store.state.categories.filter(
            (subCategory) => subCategory.parent == category.id
          )"
          :key="subCategory.id"
          :class="`sp${subCategory.position}`"
        >
          <h2>{{ subCategory.name }}</h2>
          <h3>{{ subCategory.description }}</h3>
          <ul>
            <li v-for="product in products(subCategory)" :key="product.id">
              {{ product.name }}
              <add-to-cart-button :product="product" />
            </li>
          </ul>
        </section>
      </div>
    </main>

    <AppFooter />
  </div>
</template>

<script>
import AppFooter from '@/components/AppFooter'
import AddToCartButton from '@/components/AddToCartButton.vue'
import Card from '@/components/Card'
import CategoryNavigation from '@/components/CategoryNavigation'

export default {
  name: 'Categories',
  components: { AddToCartButton, AppFooter, Card, CategoryNavigation },
  computed: {
    category() {
      return this.$store.state.categories.find(
        (category) => category.id == this.$route.params.id
      )
    },
  },
  methods: {
    products(subCategory) {
      return this.$store.state.products.filter(
        (product) => product.category_id == subCategory.id
      )
    },
  },
}
</script>

<style scoped>
.content {
  display: flex;
  flex-direction: column;
  width: 100%;
}
main {
  display: flex;
  flex-direction: column;
  margin: 1rem;
}

.subcategory {
  background-color: #fff;
  padding: 2.5rem 1rem 1rem 1rem;
  border-radius: 0.5em;
  margin-top: -1rem;
  box-shadow: 0 0.25em 0.25em #483a1d66;
}
.subcategory section {
  color: #fff;
  border-radius: 0.5em;
  text-align: center;
  box-shadow: 0 0.25em 0.25em #483a1d66;
  position: relative;
  padding: 0.5rem;
  margin-bottom: 1rem;
}
.subcategory section::before {
  content: '';
  position: absolute;
  inset: 0;
  background-image: url('../assets/cicmo.svg');
  background-repeat: no-repeat;
  background-position: -4rem -4rem;
  background-size: 10rem;
  opacity: 0.35;
}
.sp1 {
  background-color: #366135;
}
.sp2 {
  background-color: #213846;
}
.sp3 {
  background-color: #e1867b;
}

h2 {
  margin: 0.4em 0;
  font-size: 1.7rem;
}
h3 {
  margin: 1em 0;
  font-size: 1rem;
  font-weight: 100;
}

ul {
  display: flex;
  flex-direction: column;
  font-size: 1.7rem;
  text-align: left;
}
</style>