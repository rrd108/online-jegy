<template>
  <div>
    <a class="product" @click.prevent="addToCart(product)">
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
    addToCart(product) {
      this.$store.commit('addToCart', product)
    },
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
  transition: width 250ms ease;
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
  font-size: 1.7rem;
  background-color: #fff;
  border-radius: 50%;
  height: 3rem;
  width: 3rem;
  display: flex;
  justify-content: center;
  align-items: center;
  transition: transform 350ms ease;
}
.sp1 .product-button{
  color: #366135;
}
.sp2 .product-button{
  color: #213846;
}
.sp3 .product-button{
  color: #e1867b;
}

.product:active .product-button{
  transform: rotate(-45deg);
}
</style>