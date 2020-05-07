<template>
  <div class="column">
    <section v-show="!summary">
        <h3>Látogatás dátuma</h3>
        <p class="callout alert" v-show="dateError">Válassz időpontot!</p>
        <div class="row">
            <font-awesome-icon icon="clock" size="lg" class="column small-2"/>
            <date-picker @close="checkAvailableSlots" v-model="date" :default-value="afterTomorrow.setHours(11, 0, 0, 0)" type="datetime" format="YYYY-MM-DD HH:mm" placeholder="Dátum" :editable="false" :show-minute="false" :show-second="false" :time-picker-options="{start: '11:00', step:'3:00' , end: '14:00', format: 'HH:mm' }" :disabled-date="isDisabledDate" class="column small-10" />
        </div>
        <h3>Vendégek száma</h3>
        <h4>Max {{slots}} fő erre az időpontra</h4>
        <p class="callout alert" v-show="parseInt(adult) + parseInt(child) > parseInt(slots)">Erre az időpontra csak {{slots}} helyünk van!</p>
        <p class="callout alert" v-show="manError">Add meg a létszámot!</p>
        <div class="row">
            <font-awesome-icon icon="male" size="lg" class="column small-2"/>
            <input type="number" @blur="manError = false" min="0" v-model="adult" class="column small-2">
            <span class="column small-8">felnőtt 4.000 Ft/fő</span>
        </div>
        <div class="row">
            <font-awesome-icon icon="child" size="lg" class="column small-2"/>
            <input type="number" @blur="manError = false" min="0" v-model="child" class="column small-2">
            <span class="column small-8">gyerek/nyugdíjas 3.000 Ft/fő</span>
        </div>
        <h3>Elérhetőségeid</h3>
        <p class="callout alert" v-show="nameError">Add meg a neved!</p>
        <div class="row">
            <font-awesome-icon icon="user" size="lg" class="column small-2"/>
            <input type="text" @blur="nameError = false" placeholder="Neved" v-model="name" class="column small-9">
        </div>
        <p class="callout alert" v-show="emailError">Add meg az email címedet!</p>
        <div class="row">
            <font-awesome-icon icon="at" size="lg" class="column small-2"/>
            <input type="email" @blur="emailError = false" placeholder="Email címed" v-model="email" class="column small-9">
        </div>
        <p class="callout alert" v-show="phoneError">Add meg a telefonszámod!</p>
        <div class="row">
            <font-awesome-icon icon="phone" size="lg" class="column small-2"/>
            <input type="text" @blur="phoneError = false" placeholder="Telefonszámod" v-model="phone" class="column small-9">
        </div>
        <h3>Fizetendő</h3>
        <div class="row align-center">
            <font-awesome-icon icon="money-bill" size="lg" class="column small-2"/>
            <strong>{{amount | toNumFormat}} Ft</strong>
        </div>
        <p class="callout alert" v-show="tosError">A rendelési feltételek elfogadása nélkül nem tudsz tovább lépni!</p>
        <div class="row align-center">
            <input type="checkbox" v-model="tos" id="tos" @change="tosError = false">
            <label for="tos">Elfogadom a szolgáltatási feltételeket</label>
        </div>
        <div class="row align-center">
            <input type="checkbox" v-model="newsletter" id="newsletter">
            <label for="newsletter">Feliratkozom a krisna-völgy hírlevélre</label>
        </div>
        <div class="row align-center">
            <button class="button" @click="order">Megrendelés</button>
        </div>
    </section>

    <section v-show="summary">
        <h3>Összegzés</h3>
        <div class="row">
            <font-awesome-icon icon="clock" size="lg" class="column small-2"/>
            {{date ? date.toLocaleString() : ''}}
        </div>
        <div class="row">
            <font-awesome-icon icon="male" size="lg" class="column small-2"/>
            {{adult}} felnőtt
        </div>
        <div class="row">
            <font-awesome-icon icon="child" size="lg" class="column small-2"/>
            {{child}} gyerek/nyugdíjas
        </div>
        <div class="row">
            <font-awesome-icon icon="money-bill" size="lg" class="column small-2"/>
            {{amount | toNumFormat}} Ft
        </div>
        <div class="row">
            <font-awesome-icon icon="user" size="lg" class="column small-2"/>
            {{name}}
        </div>
        <div class="row">
            <font-awesome-icon icon="at" size="lg" class="column small-2"/>
            {{email}}
        </div>
        <div class="row">
            <font-awesome-icon icon="phone" size="lg" class="column small-2"/>
            {{phone}}
        </div>
        <div class="row align-center">
            <span v-html="simpleForm"></span>
        </div>
    </section>
  </div>
</template>

<script>
import DatePicker from 'vue2-datepicker'
import 'vue2-datepicker/index.css'
import 'vue2-datepicker/locale/hu'
import axios from 'axios'

const today = new Date()
today.setHours(0, 0, 0, 0)

export default {
  name: 'DateSelect',
  components: { DatePicker },
  data() {
    return {
        adult: null,
        afterTomorrow: new Date(new Date(today).setDate(new Date(today).getDate() + 2)),
        child: null,
        date: null,
        dateError: false,
        email: null,
        emailError: false,
        manError: false,
        name: null,
        nameError: false,
        newsletter: false,
        summary: false,
        phone: null,
        phoneError: false,
        simpleForm :'',
        slots: 50,
        tomorrow: new Date(new Date(today).setDate(new Date(today).getDate() + 1)),
        tos: false,
        tosError: false,
    }
  },
  computed : {
    amount() {
        return this.adult * 4000 + this.child * 3000
    },
  },
  methods: {
    checkAvailableSlots() {
        this.dateError = false
        // TODO check available slots for this time from API
        this.slots = 10
    },
    isDisabledDate(date) {
        return this.isPast(date) || this.isToday(date) || this.isTomorrow(date) || this.isSpecialDate(date)
    },
    isPast(date) {
        return date < today
    },
    isToday(date) {
        return date.getTime() == today.getTime()
    },
    isTomorrow(date) {
        return date.getTime() == this.tomorrow.getTime()
    },
    isSpecialDate(date) {
        // TODO get specialDays from the API
        const specialDates = ['2020-05-20', '2020-05-21', '2020-05-24']
        // we should add 1 day to get it work as expected
        const d = new Date(date.setDate(date.getDate() + 1))
        return specialDates.indexOf(d.toISOString().split('T')[0]) != -1
    },
    order() {
        // TODO email validation
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

        this.summary = true

        axios.post(process.env.VUE_APP_API_URL, {
            date: this.date,
            date: this.date.getFullYear() + '-0' + (parseInt(this.date.getMonth())+1) + '-' + this.date.getDate() + ' ' + this.date.getHours() + ':' + this.date.getMinutes() + ':00',
            adult: this.adult,
            child: this.child,
            name: this.name,
            email: this.email,
            phone: this.phone,
            newsletter: this.newsletter ? 1 : 0
        })
        .then(response => {
            this.simpleForm = response.data
        })
        .catch(error => {
            console.log(error)
        });
    }
  }
}
</script>

<style scoped>
h3 {
  margin-top: 1.2rem;;
}
span {
  font-size: 1rem;
}
.button, span >>> button {
  background-color: #574634;
}
span >>> button {
    display: inline-block;
    vertical-align: middle;
    margin: 0 0 1rem 0;
    padding: .85em 1em;
    border: 1px solid transparent;
    border-radius: 3px;
    transition: background-color .25s ease-out,color .25s ease-out;
    font-family: inherit;
    font-size: .9rem;
    -webkit-appearance: none;
    line-height: 1;
    text-align: center;
    cursor: pointer;
    color: #fefefe;
}
</style>