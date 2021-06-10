<template>
  <div id="cart">
    <div id="cart-icon">
      <font-awesome-icon
        icon="shopping-cart"
        @click="$store.commit('showCartToggle')"
      />
      <transition name="pop">
        <span v-show="$store.state.cart.length" :key="$store.state.cart.length">
          {{ $store.state.cart.length }}
        </span>
      </transition>
    </div>

    <transition name="expand">
      <div class="cart-content" v-show="$store.state.showCart">
        <h2>A kosarad</h2>
        <ul>
          <li v-for="(product, i) in $store.state.cart" :key="i + product.id">
            <font-awesome-icon icon="trash" />
            {{
              $store.state.categories.find(
                (category) => category.id == product.category_id
              ).name
            }}
            {{ product.name }}
            {{ product.price }}
          </li>
        </ul>
      </div>
    </transition>
  </div>
</template>

<script>
export default {
  name: 'Cart',
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
span {
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
  padding: 1rem;
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

.expand-enter-active, .expand-leave-active {
  transition: all 350ms ease;
  max-height: 100vh;  /* by this the cart expands nicely not just jumps up */
}
.expand-enter, .expand-leave-to {
  max-height: 0;
  opacity: 0;
}

</style>