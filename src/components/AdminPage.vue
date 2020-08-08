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
        <li @click="useTicket(visitor)" v-for="visitor in visitors" :key="visitor.id" :class="[visitor.payed == 1 ? 'payed' : 'unpayed', visitor.used == 1 ? 'used' : '']" :title="[visitor.payed == 1 ? 'payed' : 'unpayed', visitor.used == 1 ? 'used' : '']">
          <font-awesome-icon icon="check-circle" size="xs" />
          <font-awesome-icon icon="trash" size="xs" v-show="visitor.payed != 1" @click="removeBooking(visitor)" title="Foglalás törlése" class="action" />
          <font-awesome-icon icon="money-bill" size="xs" v-show="visitor.payed != 1" @click="setPayed(visitor)" class="action" title="Fizetvére állítom" />
          {{visitor.id}}
          <span v-show="!timeSlot">{{visitor.date}}</span>
          {{visitor.name}}
          {{visitor.email}} <br>
          {{visitor.adult}} F,
          {{visitor.child}} GY,
          {{visitor.amount | toNumFormat}} Ft
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
import swal from 'sweetalert'
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
    removeBooking(visitor) {
      swal({
          title: 'Foglalás törlése ' + visitor.name,
          text: 'Ezt a foglalást töröljük? ' + visitor.id,
          icon: 'warning',
          buttons: true,
          dangerMode: true,
        })
        .then((ticket) => {
          if (ticket) {
            axios.delete(process.env.VUE_APP_API_URL, {
              data: {delete: visitor.id}
            })
            .then(response => this.visitors = this.visitors.filter(visitor => visitor.id != response.data.delete))
            .catch(error => console.log(error))
          }
        })
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
    setPayed(visitor) {
      swal({
          title: 'Foglalás fizetett ' + visitor.name,
          text: 'Ezt a foglalást állítsuk kifizetettre? ' + visitor.id,
          icon: 'warning',
          buttons: true,
          dangerMode: true,
        })
        .then((ticket) => {
          if (ticket) {
            axios.patch(process.env.VUE_APP_API_URL, {
              setPayed: visitor.id
            })
            .then(response => this.visitors = this.visitors.map(visitor => visitor.id == response.data.setPayed ? { ...visitor, ...{payed: 1}} : visitor))
            .catch(error => console.log(error))
          }
        })
    },
    setShowDate(d) {
      this.showDate = d;
    },
    useTicket(visitor) {
      if (visitor.payed == 1) {
        swal({
          title: 'Jegy érvényesítés ' + visitor.name,
          text: 'Ezt a jegyet használjuk most fel? ' + visitor.id,
          icon: 'warning',
          buttons: true,
          dangerMode: true,
        })
        .then((ticket) => {
          if (ticket) {
            axios.post(process.env.VUE_APP_API_URL, {
              used: visitor.id
            })
            .then(() => visitor.used = Math.abs(visitor.used - 1))
            .catch(error => console.log(error))
          }
        })
      }
    }
	},
}
</script>

<style scoped>
#admin {
  height: 75vh;
}
ul {
  list-style-type: none;
}
.payed {
  color: green;
  cursor: pointer;
}
.payed:hover {
  color: #fff;
  background: #258DAD;
}
.unpayed {
  color: gray;
}
.used {
  text-decoration: line-through;
}
.action {
  cursor: pointer;
}
.action:hover {
  color: #258DAD;
}
</style>