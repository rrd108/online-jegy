<template>
  <div id="cart">
    <div id="cart-icon">
      <font-awesome-icon icon="shopping-cart" />
      <transition name="pop">
        <span v-show="$store.state.cart.length" :key="$store.state.cart.length">
          {{ $store.state.cart.length }}
        </span>
      </transition>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Cart',
}
</script>

<style scoped>
#cart {
  position: absolute;
  bottom: 3vh;
  left: calc(50% - 0.5rem);
  padding: 0;
}
#cart-icon {
  position: relative;
  display: flex;
  justify-content: center;
  align-items: center;
  color: #fff;
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
  left: 1rem;
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
</style>