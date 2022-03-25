<template>
  <div id="app">
    <AppHeader />
    <main class="row align-center">
      <AdminPage v-if="url == '/admin'" />
      <article v-if="url != '/admin'">
        <DateSelect v-if="!urlParams" />
        <PaymentInfo v-if="urlParams" :urlParams="urlParams" />
      </article>
    </main>
  </div>
</template>

<script>
  import AdminPage from './components/AdminPage'
  import AppHeader from './components/AppHeader'
  import DateSelect from './components/DateSelect'
  import PaymentInfo from './components/PaymentInfo'

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
        url: window.location.pathname,
        urlParams:
          window.location.href.split('?')[1] &&
          window.location.href.split('?')[1].search('fbclid') !== 0
            ? window.location.href.split('?')[1]
            : null,
      }
    },
  }
</script>

<style>
  @import url('https://fonts.googleapis.com/css?family=Quicksand:500');
  @import url('./assets/foundation.min.css');

  body {
    background: url('./assets/background.png') #efdfd2 !important;
  }

  #app {
    font-family: 'Quicksand', sans-serif;
    color: #574634;
    min-height: 100vh;
    font-size: 1.4rem;
    margin: 0 2vh;
  }

  h1,
  h2,
  h3 {
    font-family: 'Quicksand', sans-serif;
    font-weight: bold;
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
</style>
