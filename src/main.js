import Vue from 'vue'
import store from './store'
import App from './App.vue'
import { library } from '@fortawesome/fontawesome-svg-core'
import {
  faAt,
  faBars,
  faClock,
  faChild,
  faCheckCircle,
  faChevronCircleDown,
  faFire,
  faInfoCircle,
  faMale,
  faMoneyBill,
  faPen,
  faPhone,
  faUser,
  faTrash,
} from '@fortawesome/free-solid-svg-icons'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import router from './router'

Vue.config.productionTip = false

library.add(
  faAt,
  faBars,
  faClock,
  faChild,
  faCheckCircle,
  faChevronCircleDown,
  faFire,
  faInfoCircle,
  faMale,
  faMoneyBill,
  faPen,
  faPhone,
  faUser,
  faTrash
)

Vue.component('font-awesome-icon', FontAwesomeIcon)

Vue.filter('toNumFormat', function(
  value,
  decimals = 0,
  dec_point,
  thousands_sep
) {
  let n = value,
    c = isNaN((decimals = Math.abs(decimals))) ? 2 : decimals
  let d = dec_point == undefined ? ',' : dec_point
  let t = thousands_sep == undefined ? ' ' : thousands_sep,
    s = n < 0 ? '-' : ''
  let i = parseInt((n = Math.abs(+n || 0).toFixed(c))) + '',
    j = (j = i.length) > 3 ? j % 3 : 0
  return (
    s +
    (j ? i.substr(0, j) + t : '') +
    i.substr(j).replace(/(\d{3})(?=\d)/g, '$1' + t) +
    (c
      ? d +
        Math.abs(n - i)
          .toFixed(c)
          .slice(2)
      : '')
  )
})

new Vue({
  router,
  store,
  render: h => h(App),
}).$mount('#app')
