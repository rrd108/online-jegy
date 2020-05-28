<template>
  <div class="row" id="admin">
    <form @submit.prevent="login" v-if="!token">
      <fieldset>
        <div class="input email required">
          <label for="email">Email</label>
          <input type="email" v-model="email" required="required" id="email" />
        </div>
        <div class="input password required">
          <label for="password">Jelszó</label>
          <input
            type="password"
            v-model="password"
            required="required"
            id="password"
          />
        </div>
      </fieldset>
      <div class="row align-center">
        <button class="button" type="submit">Belép</button>
      </div>
    </form>

    <aside v-if="token" class="small-12 large-6">
      <h2>Vendégek</h2>
      <input type="search" v-model="searchTerm" @input="search" placeholder="Keresés azonosító / név / email">
      <h3>{{timeSlot}}</h3>
      <ul>
        <li v-for="visitor in visitors" :key="visitor.id">
          {{visitor.id}}
          {{visitor.date}}
          {{visitor.name}}
          {{visitor.email}} <br>
          {{visitor.adult}} F,
          {{visitor.child}} GY,
          {{visitor.amount}} Ft
          {{visitor.payed}}
        </li>
      </ul>
    </aside>
    <calendar-view
      v-if="token"
      :startingDayOfWeek="1"
      :show-date="showDate"
      :events="checkins"
      @click-event="clickEvent"
      class="small-12 large-6 theme-default">
      <calendar-view-header
        slot="header"
        slot-scope="t"
        :header-props="t.headerProps"
        @input="setShowDate" />
    </calendar-view>
  </div>
</template>

<script>
import axios from 'axios'
import { CalendarView, CalendarViewHeader } from "vue-simple-calendar"
// https://github.com/richardtallent/vue-simple-calendar

require("vue-simple-calendar/static/css/default.css")

export default {
  name: 'AdminPage',
  components : {
    CalendarView,
    CalendarViewHeader,
  },
  created() {
    axios.get(process.env.VUE_APP_API_URL + '?checkins')
      .then(response => this.checkins = response.data)
      .catch(error => console.log(error))
  },
  data() {
    return {
      checkins: [],
      email : '',
      visitors: [],
      password : '',
      searchTerm: '',
      showDate: new Date(),
      timeSlot: '',
      token : '',
    }
  },
  methods: {
    clickEvent(event) {
      // event.id : 2020-05-17 14:00:00
      this.timeSlot = event.id
      axios.get(process.env.VUE_APP_API_URL + '?visitors=' + event.id)
        .then(response => this.visitors = response.data)
        .catch(error => console.log(error))
    },
    login() {
      axios.post(process.env.VUE_APP_API_TOKEN_URL, {
          email: this.email,
          password: this.password,
        })
        .then(response => this.token = response.data)
        .catch(error => console.log(error))
    },
    search() {
      this.timeSlot = ''
      this.visitors = []
      if (this.searchTerm.length > 2) {
      axios.get(process.env.VUE_APP_API_URL + '?search=' + this.searchTerm)
        .then(response => this.visitors = response.data)
        .catch(error => console.log(error))
      }
    },
    setShowDate(d) {
      this.showDate = d;
    },
	},
}
</script>

<style scoped>
#admin {
  height: 75vh;
}
</style>