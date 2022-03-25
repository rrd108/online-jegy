<script>
  import axios from 'axios'

  export default {
    name: 'Products',
    created() {
      axios
        .get(process.env.VUE_APP_API_URL + '?products')
        .then(response => (this.products = response.data))
        .catch(error => console.log(error))
    },
    data() {
      return {
        product: { product: '', adult: null, child: null, slots: null },
        products: [],
      }
    },
    methods: {
      saveProduct() {
        axios
          .post(process.env.VUE_APP_API_URL, {
            product: this.product,
          })
          .then(response => {
            this.products.push(response.data.product)
            this.product = { product: '', adult: null, child: null, slots: 0 }
          })
          .catch(error => console.log(error))
      },
    },
  }
</script>
<template>
  <div>
    <h2>Barangolások</h2>
    <div class="grid">
      <input type="text" v-model="product.product" placeholder="Túra név" />
      <input
        type="number"
        placeholder="Fő"
        v-model="product.slots"
        class="right"
      />
      <input
        type="number"
        placeholder="Felnőtt"
        v-model="product.adult"
        class="right"
      />
      <input
        type="number"
        placeholder="Diák"
        v-model="product.child"
        class="right"
      />
      <button class="button" @click="saveProduct">Ment</button>
    </div>
    <ul>
      <li v-for="product in products" :key="product" class="grid">
        <span>{{ product.product }}</span>
        <span>{{ product.slots }}</span>
        <span class="right">{{
          Intl.NumberFormat().format(product.adult)
        }}</span>
        <span class="right">{{
          Intl.NumberFormat().format(product.child)
        }}</span>
      </li>
    </ul>
  </div>
</template>
<style scoped>
  ul {
    list-style: none;
  }
  div,
  li {
    grid-template-columns: 4fr 2fr 2fr 2fr 1fr;
  }
  .right {
    text-align: right;
  }
  input::placeholder {
    font-size: 1rem;
  }
</style>
