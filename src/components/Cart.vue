<template>
  <div id="cart">
    <div id="cart-icon">
      <font-awesome-icon
        icon="shopping-cart"
        @click="$store.commit('showCartToggle')"
      />
      <transition name="pop">
        <span
          v-show="$store.state.cart.length"
          :key="$store.state.cart.length"
          id="cart-counter"
        >
          {{ $store.state.cart.length }}
        </span>
      </transition>
    </div>

    <transition name="expand">
      <div class="cart-content" v-show="$store.state.showCart">
        <h2><font-awesome-icon icon="shopping-cart" /> Kosár</h2>

        <ul>
          <li v-for="(product, i) in cart" :key="i + product.id" class="items">
            <font-awesome-icon icon="trash" />
            <span class="category">
              {{ product.mainCategory.name }} /
              {{ product.category.name }}
            </span>
            <span class="product">
              {{ product.pcs }} db x -
              {{ product.name }}
            </span>
            <span class="price">
              {{ (product.pcs * product.price) | toNumFormat }} Ft
            </span>
          </li>
          <li class="total">
            <span>Összesen</span>
            <span>{{ total | toNumFormat }} Ft</span>
          </li>
        </ul>
      </div>
    </transition>
  </div>
</template>

<script>
export default {
  name: 'Cart',
  computed: {
    cart() {
      return Object.values(
        this.$store.state.cart.reduce((reduced, current) => {
          if (reduced[current.id]) {
            reduced[current.id]['pcs']++
          } else {
            current.pcs = 1
            current.category = this.$store.state.categories.find(
              (category) => category.id == current.category_id
            )
            current.mainCategory = this.$store.state.categories.find(
              (category) => category.id == current.category.parent
            )
            reduced[current.id] = current
          }
          return reduced
        }, {})
      )
    },
    total() {
      return this.cart.reduce((reducer, current) => reducer + current.price * current.pcs, 0)
    },
  },
}
</script>

<style scoped>
#cart {
  display: flex;
  flex-direction: column;
}
#cart-icon {
  position: relative;
  display: flex;
  justify-content: center;
  align-items: center;
  color: #fff;
  transform: translateY(-2.5rem);
}
#cart-icon::before {
  content: '';
  position: absolute;
  background-color: #483a1d;
  border-radius: 50%;
  box-shadow: 0 0.25em 0.25em #483a1d66;
  width: 3rem;
  height: 3rem;
  z-index: -1;
}
#cart-counter {
  position: absolute;
  left: calc(50% + 0.5rem);
  top: -1rem;
  display: flex;
  justify-content: center;
  align-items: center;
  background-color: #fff;
  border-radius: 50%;
  width: 1.5rem;
  height: 1.5rem;
  color: #483a1d;
  font-weight: bold;
  box-shadow: 0 0.25em 0.25em #483a1d66;
}

.cart-content {
  width: 100%;
  padding: 0 1rem 1rem 1rem;
  box-sizing: border-box;
}
h2 {
  margin-top: 0;
}
li {
  border-radius: 0.5rem;
  margin: 0.75em 0;
  padding: 0.5em;
}
.items {
  display: grid;
  grid-template-columns: 1fr 8fr 3fr;

  background-color: #e0cc9f;
}

.category {
  grid-column: 2/4;
}
.product {
  grid-column: 2/2;
}
.price {
  grid-column: 3/3;
  text-align: right;
}
.total {
  display: flex;
  justify-content: space-between;
  background-color: #483a1d;
  color: #e0cc9f;
  font-weight: bold;
}

.pop-enter,
.pop-leave-to {
  transform: scale(0);
}
.pop-enter-to,
.pop-leave-leave {
  transform: scale(1.5);
}
.pop-enter-active,
.pop-leave-active {
  transition: transform 175ms ease;
}

.expand-enter-active,
.expand-leave-active {
  transition: all 350ms ease;
  max-height: 100vh; /* by this the cart expands nicely not just jumps up */
}
.expand-enter,
.expand-leave-to {
  max-height: 0;
  opacity: 0;
}
</style>