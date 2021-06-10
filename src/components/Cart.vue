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
          <li v-for="(product, i) in cart" :key="i + product.id">
            <font-awesome-icon icon="trash" />
            <span>
              {{ product.pcs }} db x -
              {{ product.name }}
            </span>
            <span> {{ product.price | toNumFormat }} Ft </span>
          </li>
        </ul>
      </div>
    </transition>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'

export default {
  name: 'Cart',
  data() {
    return {
      mainCategoriesInCart: [],
      categoriesInCart: [],
    }
  },
  computed: {
    ...mapGetters(['mainCategories']),
    cart() {
      return this.$store.state.cart.reduce((reduced, current) => {
        if (reduced[current.id]) {
          reduced[current.id]['pcs']++
        } else {
          current.pcs = 1
          current.category = this.$store.state.categories.find(
            (category) => category.id == current.category_id
          )
          if (
            !this.categoriesInCart.find((cat) => cat.id == current.category.id)
          ) {
            this.categoriesInCart.push(current.category)
          }
          current.mainCategory = this.$store.state.categories.find(
            (category) => category.id == current.category.parent
          )
          if (
            !this.mainCategoriesInCart.find(
              (cat) => cat.id == current.mainCategory.id
            )
          ) {
            this.mainCategoriesInCart.push(current.mainCategory)
          }
          reduced[current.id] = current
        }
        return reduced
      }, {})
    },
  },
  methods: {
    productsInCategory(category) {
      console.log(category)
      console.log(this.cart)
      //      return this.cart.filter(product => product.category_id == category.id)
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
  display: flex;
  justify-content: space-between;
  background-color: #e0cc9f;
  border-radius: 0.5rem;
  margin: 0.75em 0;
  padding: 0.5em;
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