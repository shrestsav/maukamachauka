import Vue from 'vue'
import VueRouter from 'vue-router'

Vue.use(VueRouter)

import dashboard from '../components/dashboard.vue'
import appDefaults from '../components/appDefaults/index.vue'

import customers from '../components/customers/index.vue'
import unverifiedCustomers from '../components/customers/unverifiedCustomers.vue'

import categories from '../components/categories/index.vue'
import createCategory from '../components/categories/create.vue'

import brands from '../components/brands/index.vue'
import createBrand from '../components/brands/create.vue'

import offers from '../components/offers/index.vue'
import createOffer from '../components/offers/create.vue'

import pushNotification from '../components/pushNotification/index.vue'

const routes = [
  {name:'dashboard',  path: '/', component: dashboard },

  {name:'appDefaults',  path: '/v/appDefaults', component: appDefaults },

  {name:'customers',  path: '/v/customers', component: customers },
  {name:'unverifiedCustomers',  path: '/v/unverifiedCustomers', component: unverifiedCustomers },

  {name:'categories',  path: '/v/categories', component: categories },
  {name:'createCategory',  path: '/v/categories/create', component: createCategory },
  
  {name:'brands',  path: '/v/brands', component: brands },
  {name:'createBrand',  path: '/v/brands/create', component: createBrand },
  
  {name:'offers',  path: '/v/offers', component: offers },
  {name:'createOffer',  path: '/v/offers/create', component: createOffer },

  {name:'pushNotification',  path: '/v/push-notification', component: pushNotification },
]

export const router = new VueRouter({
	mode: 'history',
  	routes // short for `routes: routes`
})
