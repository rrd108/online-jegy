<script>
  import axios from 'axios'

  export default {
    name: 'Days',
    props: ['products'],
    data() {
      return {
        date: new Date().toISOString().substring(0, 10),
        days: [],
        product: {},
      }
    },
    methods: {
      addDate() {
        axios
          .post(process.env.VUE_APP_API_URL, {
            date: this.date,
            product: this.product,
          })
          .then(response => {
            console.log(response.data)
          })
          .catch(error => console.log(error))
      },
    },
  }
</script>
<template>
  <div>
    <h2>Napok</h2>
    <div class="grid">
      <select v-model="product">
        <option
          v-for="product in products"
          :key="product.product"
          :value="product.product"
        >
          {{ product.product }} {{ product.adult }}/{{ product.child }}
          {{ product.slots }} fő
        </option>
      </select>
      <input type="date" v-model="date" />
      <button class="button" @click="addDate">Ment</button>
    </div>
    <ul>
      <li v-for="day in days" :key="day">{{ day }}</li>
    </ul>
  </div>
</template>
<style scoped>
  ul {
    list-style: none;
  }
  div,
  li {
    grid-template-columns: 4fr 2fr 1fr;
  }
  input::placeholder {
    font-size: 1rem;
  }
</style>
