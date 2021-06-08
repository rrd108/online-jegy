import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex)

import axios from 'axios'

export default new Vuex.Store({
  state: {
    cart: [],
    categories: [],
    open: 0,
    products: []
  },
  mutations: {
    addToCart : (state, product) => state.cart.push(product),
    setCategories: (state, categories) => (state.categories = categories),
    setOpen: (state, open) => (state.open = open),
    setProducts: (state, products) => (state.products = products),
  },
  actions: {
    getCategories: ({ commit }) => {
      axios
        .get(`${process.env.VUE_APP_API_URL}?categories`)
        .then(response => commit('setCategories', response.data))
        .catch(err => console.error(err))
    },
    getProducts: ({ commit }) => {
      axios
        .get(`${process.env.VUE_APP_API_URL}?products`)
        .then(response => commit('setProducts', response.data))
        .catch(err => console.error(err))
    }
  },
  modules: {}
})
