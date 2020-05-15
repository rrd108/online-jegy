<template>
  <div class="row" id="admin">
    <calendar-view
      :startingDayOfWeek="1"
      :show-date="showDate"
      :events="checkins"
      @click-event="clickEvent"
			class="small-6 theme-default">
			<calendar-view-header
				slot="header"
				slot-scope="t"
				:header-props="t.headerProps"
				@input="setShowDate" />
		</calendar-view>
    <aside class="small-6">
      <h2>Vendégek</h2>
      <h3>{{timeSlot}}</h3>
      <ul>
        <li v-for="visitor in events" :key="visitor.id">
          {{visitor.id}}
          {{visitor.name}}
          {{visitor.email}} <br>
          {{visitor.adult}} F,
          {{visitor.child}} GY,
          {{visitor.amount}} Ft
          {{visitor.payed}}
        </li>
      </ul>
    </aside>
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
      timeSlot: '',
      events: [],
      showDate: new Date(),
    }
  },
  methods: {
    clickEvent(event) {
      // event.id : 2020-05-17 14:00:00
      this.timeSlot = event.id
      axios.get(process.env.VUE_APP_API_URL + '?visitors=' + event.id)
        .then(response => this.events = response.data)
        .catch(error => console.log(error))
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