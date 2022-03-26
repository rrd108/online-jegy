<script>
  import axios from 'axios'

  export default {
    name: 'Products',
    props: ['products'],
    data() {
      return {
        product: {
          product: '',
          info: '',
          adult: null,
          child: null,
          slots: null,
        },
      }
    },
    methods: {
      saveProduct() {
        const i = this.products.findIndex(
          p => p.product == this.product.product
        )
        axios
          .post(process.env.VUE_APP_API_URL, {
            product: this.product,
            i,
          })
          .then(response => {
            if (i == -1) {
              this.products.push(response.data.product)
            } else {
              this.products[i] = response.data.product
            }
            this.product = {
              product: '',
              info: '',
              adult: null,
              child: null,
              slots: null,
            }
          })
          .catch(error => console.log(error))
      },
      setProduct(product) {
        this.product = product
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
    <textarea v-model="product.info"></textarea>

    <ul>
      <li
        v-for="product in products"
        :key="product.product"
        class="grid"
        @click="setProduct(product)"
      >
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
    cursor: pointer;
  }
  .right {
    text-align: right;
  }
  input::placeholder {
    font-size: 1rem;
  }
  textarea {
    height: 17rem;
  }
</style>
