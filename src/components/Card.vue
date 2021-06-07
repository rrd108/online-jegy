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
    <div class="subcategory" v-show="$store.state.open == category.id">
      <h2>5422</h2>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Card',
  props: ['category'],
  methods: {
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
  height: 9rem;
  border-radius: 0.5em;
  margin-bottom: 0.25em;
  box-shadow: 0 0.25em 0.25em #483a1d66;
  position: relative;
}
.card::before,
.card::after {
  content: '';
  position: absolute;
  top: 0;
  right: 0;
  bottom: 0;
  left: 0;
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
  z-index: 1;
  display: flex;
  justify-content: flex-start;
  align-items: center;
  flex-direction: column;
}
h1 {
  font-size: 3rem;
  margin: 0.2em 0 0 0;
}
p {
  font-size: 1rem;
  margin: 0.5em 0;
}
svg {
  font-size: 3.4rem;
  position: absolute;
  bottom: -0.9rem;
  filter: drop-shadow(0 0.1em 0.1em #483a1d66);
}
</style>