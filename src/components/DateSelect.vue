<template>
  <div class="column">
    <section>
      <h3>Túra időpont</h3>
      <p class="callout alert" v-show="dateError">Válassz időpontot!</p>
      <div class="row">
        <font-awesome-icon icon="clock" size="lg" class="column small-2" />
        <select
          v-model="tour"
          class="column small-10"
          @change="setData"
          :disabled="summary"
        >
          <option v-for="day in filteredDays" :key="day[0]">
            {{ day[0] }} {{ day[1] }}
          </option>
        </select>
      </div>
    </section>

    <section v-show="type">
      <h3>Vendégek száma</h3>
      <div>
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
          <span class="column small-8">
            felnőtt {{ product.adult | toNumFormat }} Ft/fő
          </span>
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
          <span class="column small-8">
            gyerek/nyugdíjas {{ product.child | toNumFormat }} Ft/fő
          </span>
        </div>
      </div>

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
        <button class="button" @click="order" :disabled="overBooking">
          Megrendelés
        </button>
      </div>
    </section>

    <section v-show="summary" id="summary">
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
          {{ date ? date.toLocaleString().slice(0, -3) : '' }}
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
        >
          <img src="@/assets/simple.jpg" />
        </a>
      </div>
      <div class="row align-center">
        <img src="@/assets/ajax-loader.gif" v-show="!simpleForm" />
        <small> Fizetéshez kérjük kattints az alábbi gombra</small>
        <span v-html="simpleForm"></span>
      </div>
    </section>
  </div>
</template>

<script>
  import axios from 'axios'

  const today = new Date()
  today.setHours(0, 0, 0, 0)

  export default {
    name: 'DateSelect',
    props: ['day'],
    data() {
      return {
        adult: null,
        nextTourDay: null,
        child: null,
        date: null,
        dateError: false,
        days: [],
        email: null,
        emailError: false,
        manError: false,
        name: null,
        nameError: false,
        newsletter: true,
        summary: false,
        phone: null,
        phoneError: false,
        product: {},
        products: [],
        simpleForm: '',
        slots: 0,
        specialDays: [],
        tos: false,
        tosError: false,
        tour: '',
        type: '',
      }
    },
    computed: {
      amount() {
        return this.adult * this.product.adult + this.child * this.product.child
      },
      filteredDays() {
        return Object.entries(this.days).sort((a, b) => (a[0] < b[0] ? -1 : 1))
      },
      overBooking() {
        const slots = this.slots
        const adult = this.adult ? this.adult : 0
        const child = this.child ? this.child : 0
        return parseInt(adult) + parseInt(child) > parseInt(slots)
      },
    },
    created() {
      axios
        .get(import.meta.env.VITE_APP_API_URL + '?products')
        .then(response => {
          this.products = response.data
          axios
            .get(`${import.meta.env.VITE_APP_API_URL}?days`)
            .then(response => {
              this.days = response.data
              const days = []
              for (const prop in response.data) {
                const d = {}
                d[prop] = response.data[prop]
                days.push(d)
              }
              const nextTourDay = Object.keys(days[days.length - 1])[0]
              this.nextTourDay = new Date(nextTourDay).setHours(11, 0, 0, 0)

              if (this.day) {
                // we got a selected date in the url
                this.tour = `${this.day} ${this.days[this.day]}`
                this.setData()
              }
            })
            .catch(error => console.log(error))
        })
        .catch(error => console.error(error))
    },
    methods: {
      setData() {
        console.log('setData')
        const date = this.tour.split(' ')[0]
        this.date = new Date(`${date}T11:00:00`)
        this.type = this.days[date]
        this.product = this.products.find(
          product => product.product === this.type
        )

        this.$emit('productChanged', this.product)

        this.dateError = false
        axios
          .get(
            `${import.meta.env.VITE_APP_API_URL}?slots=${this.getFormattedDate(
              this.date
            )}`
          )
          .then(response => {
            this.slots = this.product.slots - response.data
          })
          .catch(error => console.log(error))
      },

      getFormattedDate(date) {
        const timezoneOffset = date.getTimezoneOffset() * 60000
        return (
          new Date(date - timezoneOffset)
            .toISOString()
            .slice(0, 17)
            .replace('T', ' ') + '00'
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

        window.fbq('track', 'AddToCart')

        this.summary = true
        setTimeout(() => window.scrollBy(0, window.innerHeight - 20), 100)

        axios
          .post(import.meta.env.VITE_APP_API_URL, {
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
          .then(response => {
            this.simpleForm = response.data
          })
          .catch(error => {
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
    background-color: #258dad;
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
  #summary {
    margin-bottom: 2rem;
  }
</style>
<style>
  .mx-date-row .cell:not(.disabled) {
    color: green;
  }
  .mx-calendar-content .cell.active {
    color: #fff;
  }
</style>
