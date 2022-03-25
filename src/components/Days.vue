<script>
  import axios from 'axios'

  export default {
    name: 'Days',
    props: ['products'],
    data() {
      return {
        date: new Date().toISOString().substring(0, 10),
        days: [],
        error: '',
        product: {},
        selectedProduct: '',
      }
    },
    created() {
      axios
        .get(`${process.env.VUE_APP_API_URL}?days`)
        .then(response => {
          for (const prop in response.data) {
            const d = {}
            d[prop] = response.data[prop]
            this.days.push(d)
          }
        })
        .catch(error => console.log(error))
    },
    methods: {
      addDate() {
        this.error = ''

        if (!this.date || !this.selectedProduct) {
          this.error = 'Nincs kiválasztva a dátum vagy a barangolás típusa'
          return
        }

        if (this.days.map(val => Object.keys(val)[0]).includes(this.date)) {
          this.error = 'Erre a dátumra már van barangolás'
          return
        }

        axios
          .post(process.env.VUE_APP_API_URL, {
            date: this.date,
            product: this.selectedProduct,
          })
          .then(response => {
            const d = {}
            d[response.data.date] = response.data.product
            this.days.unshift(d)
            this.selectedProduct = ''
          })
          .catch(error => console.log(error))
      },
    },
  }
</script>
<template>
  <div>
    <h2>Napok</h2>
    <p class="error" v-show="error">
      {{ error }}
    </p>
    <div class="grid">
      <input type="date" v-model="date" />
      <select v-model="selectedProduct">
        <option
          v-for="product in products"
          :key="product.product"
          :value="product.product"
        >
          {{ product.product }} {{ product.adult }}/{{ product.child }}
          {{ product.slots }} fő
        </option>
      </select>
      <button class="button" @click="addDate">Ment</button>
    </div>
    <ul>
      <li v-for="product in days" :key="product.product" class="grid">
        <span>{{ Object.keys(product)[0] }}</span>
        <span>{{ product[Object.keys(product)[0]] }}</span>
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
    grid-template-columns: 2fr 3fr 1fr;
  }
  input::placeholder {
    font-size: 1rem;
  }
</style>
