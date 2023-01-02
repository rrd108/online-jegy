<template>
  <div id="app">
    <AppHeader :product="product" />
    <main class="row align-center">
      <AdminPage v-if="url == '/admin'" />
      <article v-if="url != '/admin'">
        <DateSelect
          v-if="!backrefParams"
          @productChanged="setProduct"
          :day="day"
        />
        <PaymentInfo v-if="backrefParams" :urlParams="backrefParams" />
      </article>
    </main>
  </div>
</template>

<script>
  import AdminPage from './components/AdminPage.vue'
  import AppHeader from './components/AppHeader.vue'
  import DateSelect from './components/DateSelect.vue'
  import PaymentInfo from './components/PaymentInfo.vue'

  export default {
    name: 'App',
    components: {
      AdminPage,
      AppHeader,
      DateSelect,
      PaymentInfo,
    },
    data() {
      return {
        backrefParams:
          window.location.href.split('?')[1] &&
          window.location.href.split('?')[1].search('r=') !== -1 &&
          window.location.href.split('?')[1].search('s=') !== -1
            ? window.location.href.split('?')[1]
            : null,
        day: window.location.href.split('?day=')[1] || null,
        product: {},
        url: window.location.pathname,
      }
    },
    methods: {
      setProduct(product) {
        this.product = product
      },
    },
  }
</script>

<style>
  @import url('https://fonts.googleapis.com/css?family=Quicksand');
  @import url('./assets/foundation.min.css');

  html {
    scroll-behavior: smooth;
  }

  body {
    background: url('./assets/background.png') #efdfd2 !important;
    font-family: 'Quicksand', sans-serif;
  }

  #app {
    color: #574634;
    min-height: 100vh;
    font-size: 1.4rem;
    margin: 0 2vh;
  }

  article {
    width: 100%;
  }

  #app h1,
  #app h2 {
    font-family: 'Quicksand', sans-serif;
    font-weight: bold;
    margin: 1rem 0;
  }

  input {
    font-size: 1.5rem;
  }

  .grid {
    display: grid;
  }

  .error {
    background-color: #fff;
    color: #f00;
    padding: 0.5rem;
  }
  .cp {
    cursor: pointer;
  }
  li:nth-child(even) {
    background-color: #eee;
  }
</style>
