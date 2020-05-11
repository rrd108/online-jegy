<template>
  <div>
      <ul>
          <li v-for="checkin in checkins" :key="checkin.date">{{checkin.date}} - {{checkin.visitors}} fő</li>
      </ul>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  name: 'AdminPage',
  created() {
    axios.get(process.env.VUE_APP_API_URL + '?checkins')
        .then(response => this.checkins = response.data)
        .catch(error => console.log(error))
  },
  data() {
      return {
          checkins: {},
      }
  }
}
</script>

<style>

</style>