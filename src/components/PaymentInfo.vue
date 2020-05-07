<template>
  <div>
      <div class="callout alert">
          {{response.error}}
      </div>
      <div class="row">
          <div class="column small-8">
              Simple tranzakció azonosító
          </div>
          <div class="column small-4">
              {{response.simpleTransactionId}}
          </div>
      </div>
      <div class="row">
          <div class="column small-8">
              Rendelési azonosító
          </div>
          <div class="column small-4">
              {{response.orderId}}
          </div>
      </div>
      <div class="row">
          <div class="column small-8">
              Simple tranzakció dátuma
          </div>
          <div class="column small-4">
              {{response.date}}
          </div>
      </div>

      TODO successful Payment
      TODO errors
      TODO timeout
      TODO IPN
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
        axios.get(process.env.VUE_APP_API_URL + '?' + this.urlParams)
            .then(response => this.response = response.data)
            .catch(error => {
                console.log(error)
            })
    },
}
</script>

<style>

</style>