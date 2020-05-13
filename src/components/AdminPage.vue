<template>
  <div>
    <calendar-view
      :startingDayOfWeek="1"
      :show-date="showDate"
      :events="checkins"
      @click-date="clickDay"
			class="theme-default">
			<calendar-view-header
				slot="header"
				slot-scope="t"
				:header-props="t.headerProps"
				@input="setShowDate" />
		</calendar-view>
    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Magnam, quis esse facilis, exercitationem sit id eum inventore eius, fuga laboriosam ab aspernatur est error maiores quod?</p>
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
        showDate: new Date(),
    }
  },
  methods: {
    clickDay(day) {
      console.log(day)
    },
    setShowDate(d) {
      this.showDate = d;
    },
	},
}
</script>

<style>

</style>