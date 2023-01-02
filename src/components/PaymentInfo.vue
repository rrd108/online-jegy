<template>
  <div class="column">
    <div
      v-if="response.status"
      class="callout"
      :class="response.error ? 'alert' : 'info'"
    >
      {{ response.status }}
    </div>
    <div v-if="response.error && response.error != true" class="callout info">
      {{ response.error }}
    </div>
    <div class="row">
      <div class="column small-6">
        <strong>Rendelési azonosító</strong>
      </div>
      <div class="column small-6">
        <strong>{{ response.orderId }}</strong>
      </div>
    </div>
    <div class="row callout warning" v-show="!response.error">
      A rendelési azonosítóval tudsz majd belépni a recepción, kérjük hozd
      magaddal!
    </div>
    <div class="row">
      <div class="column small-6">Simple tranzakció azonosító</div>
      <div class="column small-4">
        {{ response.simpleTransactionId }}
      </div>
    </div>
  </div>
</template>

<script>
  import axios from 'axios'

  export default {
    name: 'PaymentInfo',
    props: ['urlParams'],
    data() {
      return {
        response: '',
      }
    },
    created() {
      axios
        .get(`${import.meta.env.VITE_APP_API_URL}?${this.urlParams}`)
        .then(response => {
          this.response = response.data
          if (response.data.amount) {
            window.fbq('track', 'Purchase', {
              value: response.data.amount,
              currency: 'HUF',
            })
          }
        })
        .catch(error => console.log(error))
    },
  }
</script>

<style></style>
