import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex)

import axios from 'axios'

export default new Vuex.Store({
  state: {
    categories: [],
    open: 0,
  },
  mutations: {
    setCategories: (state, categories) => state.categories = categories,
    setOpen: (state, open) => state.open = open
  },
  actions: {
    getCategories: ({ commit }) => {
      axios
        .get(`${process.env.VUE_APP_API_URL}?categories`)
        .then(response => commit('setCategories', response.data))
        .catch(err => console.error(err))
    },
  },
  modules: {}
})
