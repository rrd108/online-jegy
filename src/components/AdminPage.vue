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
      <nav>
        <a @click="view = 'guests'" class="button">Vendégek</a>
        <a @click="view = 'closed'" class="button">Zárt napok</a>
        <a @click="view = 'products'" class="button">Barangolások</a>
        <a @click="view = 'days'" class="button">Napok</a>
      </nav>

      <Products v-show="view == 'products'" :products="products" />
      <Days v-show="view == 'days'" :products="products" />

      <div v-show="view == 'closed'">
        <h2>Zárt napok</h2>
        <input type="date" @change="addSpecialDay" v-model="newSpecialDay" />
        <transition-group tag="ul" name="list">
          <li v-for="day in sortedSpecialDays" :key="day">{{ day }}</li>
        </transition-group>
      </div>

      <div v-show="view == 'guests'">
        <h2>Vendégek</h2>
        <input
          type="search"
          v-model="searchTerm"
          @input="search"
          placeholder="Keresés azonosító / név / email"
        />
        <h3>{{ timeSlot }}</h3>
        <ul>
          <li
            v-for="visitor in visitors"
            :key="visitor.id"
            :class="[
              visitor.payed == 1 ? 'payed' : 'unpayed',
              visitor.used == 1 ? 'used' : '',
            ]"
            :title="[
              visitor.payed == 1 ? 'payed' : 'unpayed',
              visitor.used == 1 ? 'used' : '',
            ]"
          >
            <font-awesome-icon
              icon="check-circle"
              size="xs"
              @click="useTicket(visitor)"
            />

            <font-awesome-icon
              icon="pen"
              size="xs"
              @click="editTicket(visitor)"
              title="Foglalás szerkesztése"
              class="action"
            />

            <font-awesome-icon
              icon="trash"
              size="xs"
              v-show="visitor.payed != 1"
              @click="removeBooking(visitor)"
              title="Foglalás törlése"
              class="action"
            />

            <font-awesome-icon
              icon="money-bill"
              size="xs"
              v-show="visitor.payed != 1"
              @click="setPayed(visitor)"
              class="action"
              title="Fizetvére állítom"
            />
            {{ visitor.id }}
            <span v-show="!timeSlot">{{ visitor.date }}</span>
            {{ visitor.name }} <br />
            {{ visitor.email }} {{ visitor.phone }} <br />
            {{ visitor.adult }} F, {{ visitor.child }} GY,
            {{ visitor.amount | toNumFormat }} Ft
          </li>
        </ul>
      </div>
    </aside>
    <calendar-view
      v-if="token"
      :startingDayOfWeek="1"
      :show-date="showDate"
      :events="checkins"
      @click-event="clickEvent"
      class="small-12 large-6 theme-default"
    >
      <calendar-view-header
        slot="header"
        slot-scope="t"
        :header-props="t.headerProps"
        @input="setShowDate"
      />
    </calendar-view>
  </div>
</template>

<script>
  import axios from 'axios'
  import swal from 'sweetalert'
  import Products from './Products.vue'
  import Days from './Days.vue'
  import { CalendarView, CalendarViewHeader } from 'vue-simple-calendar'
  // https://github.com/richardtallent/vue-simple-calendar

  require('vue-simple-calendar/static/css/default.css')

  export default {
    name: 'AdminPage',
    components: {
      CalendarView,
      CalendarViewHeader,
      Days,
      Products,
    },
    created() {
      axios
        .get(process.env.VUE_APP_API_URL + '?products')
        .then(response => (this.products = response.data))
        .catch(error => console.log(error))

      axios
        .get(process.env.VUE_APP_API_URL + '?checkins')
        .then(response => (this.checkins = response.data))
        .catch(error => console.log(error))

      axios
        .get(process.env.VUE_APP_API_URL + '?specialDays')
        .then(response => (this.specialDays = response.data))
        .catch(error => console.log(error))
    },
    data() {
      return {
        checkins: [],
        email: '',
        newSpecialDay: null,
        visitors: [],
        password: '',
        products: [],
        searchTerm: '',
        showDate: new Date(),
        specialDays: [],
        timeSlot: '',
        token: '',
        view: 'guests',
      }
    },
    computed: {
      sortedSpecialDays() {
        let specialDays = this.specialDays
        return specialDays.sort((a, b) => (a < b ? 1 : -1))
      },
    },
    methods: {
      addSpecialDay() {
        axios
          .patch(process.env.VUE_APP_API_URL, {
            newSpecialDay: this.newSpecialDay,
          })
          .then(response => {
            console.log(response.data)
            this.specialDays.push(response.data.newSpecialDay)
            this.newSpecialDay = null
          })
          .catch(error => console.log(error))
      },
      clickEvent(event) {
        // event.id : 2020-05-17 14:00:00
        this.timeSlot = event.id
        axios
          .get(process.env.VUE_APP_API_URL + '?visitors=' + event.id)
          .then(response => (this.visitors = response.data))
          .catch(error => console.log(error))
      },
      editTicket(visitor) {
        swal({
          title: 'Foglalás módosítása ' + visitor.name,
          text: 'Add meg az új dátumot (éééé-hh-nn óó) formátumban',
          icon: 'warning',
          buttons: true,
          dangerMode: true,
          content: 'input',
        })
          .then(newDate => {
            if (newDate != visitor.date.substr(0, 13)) {
              newDate = newDate + ':00:00'
              axios
                .patch(process.env.VUE_APP_API_URL, {
                  newDate,
                  booking: visitor.id,
                })
                .then(response => {
                  if (response.data.changedRecords === 1) {
                    const visitorsChanged =
                      parseInt(visitor.adult || 0) +
                      parseInt(visitor.child || 0)

                    // remove current booking from the list
                    this.visitors = this.visitors.filter(
                      v => v.id != visitor.id
                    )

                    // change old chekin title in the calendar
                    const oldCheckin = this.checkins.find(
                      checkin => checkin.id == this.timeSlot
                    )
                    const oldCheckinVisitorsNumber = parseInt(
                      oldCheckin.title.substr(
                        3,
                        oldCheckin.title.indexOf(' ') - 3
                      )
                    )
                    if (oldCheckinVisitorsNumber - visitorsChanged) {
                      oldCheckin.title = oldCheckin.title.replace(
                        `${oldCheckinVisitorsNumber} fő`,
                        `${oldCheckinVisitorsNumber - visitorsChanged} fő`
                      )
                    } else {
                      oldCheckin.title = ' '
                    }

                    const newCheckin = this.checkins.find(
                      checkin => checkin.startDate == newDate
                    )
                    if (!newCheckin) {
                      // add new checkin to the calendar
                      this.checkins.push({
                        id: newDate,
                        startDate: newDate,
                        title: `${newDate.substr(11, 2)}-${visitorsChanged} fő`,
                      })
                    }
                    if (newCheckin) {
                      // update new checkin title
                      const newCheckinVisitorsNumber = parseInt(
                        newCheckin.title.substr(
                          3,
                          newCheckin.title.indexOf(' ') - 3
                        )
                      )
                      newCheckin.title = newCheckin.title.replace(
                        `${newCheckinVisitorsNumber} fő`,
                        `${newCheckinVisitorsNumber + visitorsChanged} fő`
                      )
                    }
                  } else {
                    console.error(response.data)
                  }
                })
            }
          })
          .catch(error => console.log(error))
      },
      login() {
        axios
          .post(process.env.VUE_APP_API_TOKEN_URL, {
            email: this.email,
            password: this.password,
          })
          .then(response => (this.token = response.data))
          .catch(error => console.log(error))
      },
      removeBooking(visitor) {
        swal({
          title: 'Foglalás törlése ' + visitor.name,
          text: 'Ezt a foglalást töröljük? ' + visitor.id,
          icon: 'warning',
          buttons: true,
          dangerMode: true,
        }).then(ticket => {
          if (ticket) {
            axios
              .delete(process.env.VUE_APP_API_URL, {
                data: { delete: visitor.id },
              })
              .then(
                response =>
                  (this.visitors = this.visitors.filter(
                    visitor => visitor.id != response.data.delete
                  ))
              )
              .catch(error => console.log(error))
          }
        })
      },
      search() {
        this.timeSlot = ''
        this.visitors = []
        if (this.searchTerm.length > 2) {
          axios
            .get(process.env.VUE_APP_API_URL + '?search=' + this.searchTerm)
            .then(response => (this.visitors = response.data))
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
        }).then(ticket => {
          if (ticket) {
            axios
              .patch(process.env.VUE_APP_API_URL, {
                setPayed: visitor.id,
              })
              .then(
                response =>
                  (this.visitors = this.visitors.map(visitor =>
                    visitor.id == response.data.setPayed
                      ? { ...visitor, ...{ payed: 1 } }
                      : visitor
                  ))
              )
              .catch(error => console.log(error))
          }
        })
      },
      setShowDate(d) {
        this.showDate = d
      },
      useTicket(visitor) {
        if (visitor.payed == 1) {
          swal({
            title: 'Jegy érvényesítés ' + visitor.name,
            text: 'Ezt a jegyet használjuk most fel? ' + visitor.id,
            icon: 'warning',
            buttons: true,
            dangerMode: true,
          }).then(ticket => {
            if (ticket) {
              axios
                .post(process.env.VUE_APP_API_URL, {
                  used: visitor.id,
                })
                .then(() => (visitor.used = Math.abs(visitor.used - 1)))
                .catch(error => console.log(error))
            }
          })
        }
      },
    },
  }
</script>

<style scoped>
  #admin {
    height: 75vh;
  }
  nav a {
    margin: 0 0.5rem;
  }
  ul {
    list-style-type: none;
  }
  .payed {
    color: green;
  }
  .fa-check-circle:hover {
    color: #fff;
    background: #258dad;
    cursor: pointer;
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
    color: #258dad;
  }

  .list-enter-active,
  .list-leave-active {
    transition: all 1s;
  }
  .list-enter,
  .list-leave-to {
    opacity: 0;
    transform: translateY(30px);
  }
</style>
