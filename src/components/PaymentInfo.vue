<template>
  <div class="column">
      <div v-if="response.error" class="callout alert">
          {{response.error}}
      </div>
      <div v-if="response.status" class="callout info">
          {{response.status}}
      </div>
      <div class="row">
          <div class="column small-6">
              <strong>Rendelési azonosító</strong>
          </div>
          <div class="column small-6">
              <strong>{{response.orderId}}</strong>
          </div>
      </div>
      <div class="row callout warning">A rendelési azonosítóval tudsz majd belépni a recepción, kérjük hozd magaddal!</div>
      <div class="row">
          <div class="column small-6">
              Simple tranzakció azonosító
          </div>
          <div class="column small-4">
              {{response.simpleTransactionId}}
          </div>
      </div>
      <div class="row">
          <div class="column small-6">
              Simple tranzakció dátuma
          </div>
          <div class="column small-4">
              {{response.date}}
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