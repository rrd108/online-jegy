<template>
  <div>
    <section v-show="!summary">
      <h3>Túra típus</h3>
      <div class="row">
        <font-awesome-icon icon="fire" size="lg" class="column small-2" />
        <select
          class="column small-10"
          v-model="type"
          @change="checkAvailableSlots"
        >
          <option value="tematic">Spirituális zarándoklat</option>
          <option value="tematic-extra">
            Extra Spirituális zarándoklat csomag
          </option>
          <!--option value="herbs">Gyógynövény</!--option-->
        </select>
      </div>

      <h3>Túra időpont</h3>
      <p class="callout alert" v-show="dateError">Válassz időpontot!</p>
      <div class="row">
        <font-awesome-icon icon="clock" size="lg" class="column small-2" />
        <date-picker
          v-show="type == 'tematic'"
          @close="checkAvailableSlots"
          v-model="date"
          :default-value="nextTourDay.setHours(11, 0, 0, 0)"
          type="datetime"
          format="YYYY-MM-DD HH:mm"
          placeholder="Dátum"
          :editable="false"
          :show-minute="false"
          :show-second="false"
          :time-picker-options="{
            start: '11:00',
            step: '1:00',
            end: '11:00',
            format: 'HH:mm',
          }"
          :disabled-date="isDisabledDate"
          class="column small-10"
        />

        <!--p v-show="type=='herbs'" class="column small-10">2020. október 11. 09:00</!--p-->
      </div>

      <h3>Vendégek száma</h3>
      <div v-if="type == 'tematic'">
        <h4>Max {{ slots }} fő erre az időpontra</h4>
        <p class="callout alert" v-show="overBooking">
          Erre az időpontra csak {{ slots }} helyünk van!
        </p>
        <p class="callout alert" v-show="manError">Add meg a létszámot!</p>
        <div class="row">
          <font-awesome-icon icon="male" size="lg" class="column small-2" />
          <input
            type="number"
            @blur="manError = false"
            min="0"
            v-model="adult"
            class="column small-2"
          />
          <span class="column small-8"
            >felnőtt {{ prices.adult | toNumFormat }} Ft/fő</span
          >
        </div>
        <div class="row">
          <font-awesome-icon icon="child" size="lg" class="column small-2" />
          <input
            type="number"
            @blur="manError = false"
            min="0"
            v-model="child"
            class="column small-2"
          />
          <span class="column small-8"
            >gyerek/nyugdíjas {{ prices.child | toNumFormat }} Ft/fő</span
          >
        </div>
      </div>
      <!--div v-if="type=='herbs'">
            <h4>Max {{herbSlots}} fő erre az időpontra</h4>
            <p class="callout alert" v-show="overBooking">Erre az időpontra csak {{herbSlots}} helyünk van!</p>
            <p class="callout alert" v-show="manError">Add meg a létszámot!</p>
            <div class="row">
                <font-awesome-icon icon="male" size="lg" class="column small-2"/>
                <input type="number" @blur="manError = false" min="0" v-model="adult" class="column small-2">
                <span class="column small-8">felnőtt {{prices.herbs | toNumFormat}} Ft/fő</span>
            </div>
        </div-->

      <h3>Elérhetőségeid</h3>
      <p class="callout alert" v-show="nameError">Add meg a neved!</p>
      <div class="row">
        <font-awesome-icon icon="user" size="lg" class="column small-2" />
        <input
          type="text"
          @blur="nameError = false"
          placeholder="Neved"
          v-model="name"
          class="column small-9"
        />
      </div>
      <p class="callout alert" v-show="emailError">Add meg az email címedet!</p>
      <div class="row">
        <font-awesome-icon icon="at" size="lg" class="column small-2" />
        <input
          type="email"
          @blur="emailError = false"
          placeholder="Email címed"
          v-model="email"
          class="column small-9"
        />
      </div>
      <p class="callout alert" v-show="phoneError">Add meg a telefonszámod!</p>
      <div class="row">
        <font-awesome-icon icon="phone" size="lg" class="column small-2" />
        <input
          type="text"
          @blur="phoneError = false"
          placeholder="Telefonszámod"
          v-model="phone"
          class="column small-9"
        />
      </div>
      <h3>Fizetendő</h3>
      <div class="row align-center">
        <font-awesome-icon icon="money-bill" size="lg" class="column small-2" />
        <strong>{{ amount | toNumFormat }} Ft</strong>
      </div>
      <p class="callout alert" v-show="tosError">
        A rendelési feltételek elfogadása nélkül nem tudsz tovább lépni!
      </p>
      <div class="row align-center">
        <input
          type="checkbox"
          v-model="tos"
          id="tos"
          @change="tosError = false"
        />
        <label for="tos">Elfogadom az ászf-t</label>
      </div>
      <div class="row align-center">
        <input type="checkbox" v-model="newsletter" id="newsletter" />
        <label for="newsletter">Feliratkozom a krisna-völgy hírlevélre</label>
      </div>
      <div class="row align-center">
        <button class="button" @click="order">Megrendelés</button>
      </div>
    </section>

    <section v-show="summary">
      <h3>Összegzés</h3>
      <div class="row">
        <font-awesome-icon icon="user" size="lg" class="column small-2" />
        <p class="column small-10">{{ name }}</p>
      </div>
      <div class="row">
        <font-awesome-icon icon="at" size="lg" class="column small-2" />
        <p class="column small-10">{{ email }}</p>
      </div>
      <div class="row">
        <font-awesome-icon icon="phone" size="lg" class="column small-2" />
        <p class="column small-10">{{ phone }}</p>
      </div>
      <div class="row">
        <font-awesome-icon icon="clock" size="lg" class="column small-2" />
        <p class="column small-10">
          {{ date ? date.toLocaleString().slice(0, -3) : "" }}
        </p>
      </div>
      <div class="row" v-show="adult">
        <font-awesome-icon icon="male" size="lg" class="column small-2" />
        <p class="column small-10">{{ adult }} felnőtt</p>
      </div>
      <div class="row" v-show="child">
        <font-awesome-icon icon="child" size="lg" class="column small-2" />
        <p class="column small-10">{{ child }} gyerek/nyugdíjas</p>
      </div>
      <div class="row">
        <font-awesome-icon icon="money-bill" size="lg" class="column small-2" />
        <p class="column small-10">
          <strong>{{ amount | toNumFormat }} Ft</strong>
        </p>
      </div>
      <div class="row align-center">
        <a
          href="http://simplepartner.hu/PaymentService/Fizetesi_tajekoztato.pdf"
          class="simplelogo column small-12 large-6"
          ><img src="../assets/simple.jpg"
        /></a>
      </div>
      <div class="row align-center">
        <img src="../assets/ajax-loader.gif" v-show="!simpleForm" />
        <span v-html="simpleForm"></span>
      </div>
    </section>
    <AppFooter />
  </div>
</template>

<script>
import AppFooter from '@/components/AppFooter'
import DatePicker from "vue2-datepicker"
import "vue2-datepicker/index.css"
import "vue2-datepicker/locale/hu"
import axios from "axios"

const today = new Date()
today.setHours(0, 0, 0, 0)

export default {
  name: "Home",
  components: { AppFooter, DatePicker },
  data() {
    return {
      adult: null,
      nextTourDay: new Date(
        new Date(today).setDate(
          new Date(today).getDate() + ((6 + 7 - today.getDay()) % 7)
        )
      ),
      child: null,
      date: null,
      dateError: false,
      email: null,
      emailError: false,
      //herbSlots: 0,
      manError: false,
      name: null,
      nameError: false,
      newsletter: true,
      summary: false,
      phone: null,
      phoneError: false,
      prices: {},
      simpleForm: "",
      slots: 0,
      specialDays: [],
      //tomorrow: new Date(new Date(today).setDate(new Date(today).getDate() + 1)),
      tos: false,
      tosError: false,
      type: "tematic", // window.location.href.search('herbs') > 0 ? 'herbs' : 'tematic'
    }
  },
  computed: {
    amount() {
      //if (this.type == 'tematic') {
      return this.adult * this.prices.adult + this.child * this.prices.child
      //}
      //return this.adult * this.prices.herbs
    },
    overBooking() {
      //const slots = (this.type == 'tematic') ? this.slots : this.herbSlots
      const slots = this.slots
      const adult = this.adult ? this.adult : 0
      const child = this.child ? this.child : 0
      return parseInt(adult) + parseInt(child) > parseInt(slots)
    },
  },
  created() {
    this.type = "tematic" //it is needed if the user come the page from elvonulas with the browser back button
    axios
      .get(process.env.VUE_APP_API_URL + "?prices")
      .then((response) => (this.prices = response.data))
      .catch((error) => console.log(error))

    axios
      .get(process.env.VUE_APP_API_URL + "?maxSlots")
      .then((response) => {
        this.slots = response.data.tematic
        //this.herbSlots = response.data.herbs
      })
      .catch((error) => console.log(error))

    axios
      .get(process.env.VUE_APP_API_URL + "?specialDays")
      .then((response) => (this.specialDays = response.data))
      .catch((error) => console.log(error))
  },
  methods: {
    checkAvailableSlots() {
      // if (this.type == 'herbs') {
      //     this.date = new Date(Date.parse('2020-10-11 09:00'))
      // }
      if (this.type == "tematic-extra") {
        window.location.href = "https://elvonulas.krisnavolgy.hu/tematikus/"
      }
      this.dateError = false
      axios
        .get(
          process.env.VUE_APP_API_URL +
            "?type=" +
            this.type +
            "&slots=" +
            this.getFormattedDate(this.date)
        )
        .then((response) => {
          // if (this.type == 'herbs') {
          //     this.herbSlots = response.data
          // }
          if (this.type == "tematic") {
            this.slots = response.data
          }
        })
        .catch((error) => console.log(error))
    },
    isDisabledDate(date) {
      return (
        this.isPast(date) ||
        this.isToday(date) ||
        /* this.isTomorrow(date) ||*/ this.isNotTourDay(date) ||
        this.isSpecialDate(date)
      )
    },
    isNotTourDay(date) {
      return date.getDay() != 6 // Saturdays
    },
    isPast(date) {
      return date < today
    },
    isToday(date) {
      return date.getTime() == today.getTime()
    },
    /*isTomorrow(date) {
        return date.getTime() == this.tomorrow.getTime()
    },*/
    isSpecialDate(date) {
      // we should add 1 day to get it work as expected
      const d = new Date(date.setDate(date.getDate() + 1))
      return this.specialDays.indexOf(d.toISOString().split("T")[0]) != -1
    },
    getFormattedDate(date) {
      const timezoneOffset = date.getTimezoneOffset() * 60000
      return (
        new Date(date - timezoneOffset)
          .toISOString()
          .slice(0, 17)
          .replace("T", " ") + "00"
      )
    },
    order() {
      let errors = 0
      if (!this.date) {
        this.dateError = true
        errors++
      }
      if (this.adult < 0 || this.chlid < 0 || this.adult + this.child <= 0) {
        this.manError = true
        errors++
      }
      if (!this.name) {
        this.nameError = true
        errors++
      }
      if (!this.email) {
        this.emailError = true
        errors++
      }
      if (
        !/^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/.test(
          this.email
        )
      ) {
        this.emailError = true
        errors++
      }
      if (!this.phone) {
        this.phoneError = true
        errors++
      }
      if (!this.tos) {
        this.tosError = true
        errors++
      }
      if (errors) {
        return false
      }

      window.fbq("track", "AddToCart")

      this.summary = true

      axios
        .post(process.env.VUE_APP_API_URL, {
          date: this.getFormattedDate(this.date),
          adult: this.adult,
          child: this.child,
          name: this.name,
          email: this.email,
          phone: this.phone,
          newsletter: this.newsletter ? 1 : 0,
          referrer: document.referrer,
          type: this.type,
        })
        .then((response) => {
          this.simpleForm = response.data
        })
        .catch((error) => {
          console.log(error)
        })
    },
  },
}
</script>

<style scoped>
h3 {
  margin-top: 1.2rem;
}
span {
  font-size: 1rem;
}
.button,
span >>> button {
  background-color: #574634;
}
span >>> button {
  display: inline-block;
  vertical-align: middle;
  margin: 0 0 1rem 0;
  padding: 0.85em 1em;
  border: 1px solid transparent;
  border-radius: 3px;
  transition: background-color 0.25s ease-out, color 0.25s ease-out;
  font-family: inherit;
  font-size: 0.9rem;
  -webkit-appearance: none;
  line-height: 1;
  text-align: center;
  cursor: pointer;
  color: #fefefe;
}
.simplelogo {
  margin: 1rem;
}
</style>
<style>
.mx-date-row .cell:not(.disabled) {
  color: green;
}
</style>