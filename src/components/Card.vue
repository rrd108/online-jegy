<template>
  <div>
    <div
      class="card"
      :class="[
        { right: !(category.position % 2) },
        `p${category.position}`,
        { active: $store.state.open == category.id },
      ]"
      @click="$store.commit('setOpen', category.id)"
    >
      <div class="card-content">
        <h1>{{ category.name }}</h1>
        <p>{{ subCategoryNames(category.id) }}</p>
        <font-awesome-icon icon="chevron-circle-down" />
      </div>
    </div>
    <transition name="slide">
      <div class="subcategory" v-show="$store.state.open == category.id">
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
    </transition>
  </div>
</template>

<script>
import AddToCartButton from '@/components/AddToCartButton.vue'

export default {
  name: 'Card',
  props: ['category'],
  components: { AddToCartButton },
  methods: {
    products(subCategory) {
      return this.$store.state.products.filter(
        (product) => product.category_id == subCategory.id
      )
    },
    subCategoryNames(id) {
      return this.$store.state.categories
        .filter((subCategory) => subCategory.parent == id)
        .map((category) => category.name)
        .join(', ')
    },
  },
}
</script>

<style scoped>
.card {
  background-color: #614f28;
  color: #fff;
  height: 8.25rem;
  border-radius: 0.5em;
  margin-bottom: 0.25em;
  box-shadow: 0 0.25em 0.25em #483a1d66;
  position: relative;
}
.card::before,
.card::after {
  content: '';
  position: absolute;
  inset: 0;
  background-repeat: no-repeat;
  border-radius: 0.5em;
}
.card::before {
  top: -2rem;
  background-position: -5rem 0;
}
.p1.card::before {
  background-image: url('../assets/syama-gauri-250.png');
  background-position: -3rem 0;
}
.p2.card::before {
  background-image: url('../assets/hari-narayana-250.png');
  background-position: calc(100% + 4rem) 0;
}
.p3.card::before {
  background-image: url('../assets/vallabhi-250.png');
}
.card::after {
  background-image: url('../assets/cicmo.svg');
  background-position: calc(100% + 4rem) -4rem;
  background-size: 10rem;
  opacity: 0.35;
}
.card.right::after {
  background-position: -4rem -4rem;
}
.card-content {
  display: flex;
  justify-content: flex-start;
  align-items: center;
  flex-direction: column;
}
h1 {
  font-size: 3rem;
  margin: 0.2em 0 0 0;
  z-index: 1;
}
p {
  font-size: 1rem;
  margin: 0.5em 0;
  z-index: 1;
}
svg {
  font-size: 3rem;
  position: absolute;
  bottom: -1rem;
  filter: drop-shadow(0 0.1em 0.1em #483a1d66);
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
  background-position: -2rem -2rem;
  background-size: 5rem;
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
  list-style: none;
  margin: 0;
  padding: 0;
  display: flex;
  flex-direction: column;
  font-size: 1.7rem;
  text-align: left;
}

.slide-enter-active, .slide-leave-active {
  transition: transform 500ms;
  transform-origin: top;
  z-index: -1;
}
.slide-enter, .slide-leave-to {
  transform: scaleY(0);
}
</style>