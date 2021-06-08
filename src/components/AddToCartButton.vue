<template>
  <div>
    <a class="product">
      <span class="product-price"> {{ formatNumber(product.price) }}</span>
      <span class="product-button">
        <font-awesome-icon icon="shopping-cart" />
      </span>
    </a>
  </div>
</template>

<script>
export default {
  name: 'AddToCartButton',
  props: ['product'],
  methods: {
    formatNumber(value) {
      return new Intl.NumberFormat('hu-HU', {
        style: 'currency',
        currency: 'HUF',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
      }).format(value)
    },
  },
}
</script>

<style scoped>
div, a {
  display: flex;
  justify-content: flex-end;
  align-items: center;
  margin: 0.3em 0;
}
.product:active {
  width: 13rem;
}
.product {
  position: relative;
  z-index: 0;
  width: 12rem;
  transition: width 350ms ease-in-out;
}

.product::before {
  content: '';
  position: absolute;
  inset: 0.2em;
  z-index: -1;
  background-color: #0005;
  border-radius: 0.5em;
  border: none;
  box-shadow: 0 0.25em 0.25em #483a1d66;
}
.product-price {
  margin-right: 1rem;
}
.product-button {
  color: #315230;
  font-size: 1.7rem;
  background-color: #fff;
  border-radius: 50%;
  height: 3rem;
  width: 3rem;
  display: flex;
  justify-content: center;
  align-items: center;
}
</style>