import Vue from 'vue'
import VueRouter from 'vue-router'

Vue.use(VueRouter)

import dashboard from '../components/dashboard.vue'
import appDefaults from '../components/appDefaults/index.vue'

import customers from '../components/customers/index.vue'
import unverifiedCustomers from '../components/customers/unverifiedCustomers.vue'

import pushNotification from '../components/pushNotification/index.vue'

const routes = [
  {name:'dashboard',  path: '/', component: dashboard },

  {name:'appDefaults',  path: '/v/appDefaults', component: appDefaults },

  {name:'customers',  path: '/v/customers', component: customers },
  {name:'unverifiedCustomers',  path: '/v/unverifiedCustomers', component: unverifiedCustomers },

  {name:'pushNotification',  path: '/v/push-notification', component: pushNotification },
]

export const router = new VueRouter({
	mode: 'history',
  	routes // short for `routes: routes`
})
