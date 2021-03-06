import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex)

export const store = new Vuex.Store({
	state:{
		currentPage:'',
		currentMenu:'',
		orders:[],
		ordersCount:{},
		orderDetails:{},
		drivers:[],
		customers:{},
		services:{},
		categories:{},
		items:{},
		mainAreas:{},
		offers:{},
		coupons:{},
		orderStatus:{},
		orderTime:{},
		address:{},
		notifications:{},
		appDefaults:{},
		orderStatusCount:{},
		user:{},
		errors:{},
	},
	getters:{
		orders(state){
			return state.orders;
		},
		orderDetails(state){
			return state.orderDetails;
		},
		drivers(state){
			return state.drivers;
		},
		customers(state){
			return state.customers;
		},
		orderStatus(state){
			return state.orderStatus;
		},
		orderTime(state){
			return state.orderTime;
		},
		address(state){
			return state.address;
		},
		services(state){
			return state.services;
		},
		categories(state){
			return state.categories;
		},
		items(state){
			return state.items;
		},
		mainAreas(state){
			return state.mainAreas;
		},
		offers(state){
			return state.offers;
		},
		coupons(state){
			return state.coupons;
		},
		notifications(state){
			return state.notifications;
		},
		appDefaults(state){
			return state.appDefaults;
		},
		user(state){
			return state.user;
		},
		errors(state){
			return state.errors;
		}
	},
	mutations:{ 
		changeCurrentPage(state, currentPage) {
			state.currentPage = currentPage
		},
		changeCurrentMenu(state, currentMenu) {
			state.currentMenu = currentMenu
		},
		setOrders(state, orders){
			state.orders = orders
		},
		setOrdersCount(state, ordersCount){
			state.ordersCount = ordersCount
		},
		setOrderDetails(state, orderDetails){
			state.orderDetails = orderDetails
		},
		setDrivers(state, drivers){
			state.drivers = drivers
		},
		setCustomers(state, customers){
			state.customers = customers
		},
		setOrderStatus(state, orderStatus){
			state.orderStatus = orderStatus
		},
		setOrderTime(state, orderTime){
			state.orderTime = orderTime
		},
		setAddress(state, address){
			state.address = address
		},
		setServices(state, services){
			state.services = services
		},
		setCategories(state, categories){
			state.categories = categories
		},
		setItems(state, items){
			state.items = items
		},
		setMainAreas(state, mainAreas){
			state.mainAreas = mainAreas
		},
		setOffers(state, offers){
			state.offers = offers
		},
		setCoupons(state, coupons){
			state.coupons = coupons
		},
		setNotifications(state, notifications){
			state.notifications = notifications
		},
		setAppDefaults(state, appDefaults){
			state.appDefaults = appDefaults
		},
		setOrderStatusCount(state, orderStatusCount){
			state.orderStatusCount = orderStatusCount
		},
		setUser(state, user){
			state.user = user
		},
		setErrors(state, errors){
			state.errors = errors
		}
	},
	actions:{
		getAppDefaults(context){
			axios.get('/appDefaults')
	        .then(response => {
	        	context.commit('setAppDefaults',response.data)
	        });
		},
		getNotifications(context){
			axios.get('/notifications')
	        .then(response => {
	        	context.commit('setNotifications',response.data)
	        });
		},
		setAllNotificationsRead(context){
			axios.get('/markAllAsRead')
	        .then(response => {
	        	context.commit('setNotifications',[])
	        	showNotify('success','All Notifications Cleared')
	        });
		},
		getOrderStatus(context){
			axios.get('/getSettings/orderStatus')
	        .then(response => {
	        	context.commit('setOrderStatus',response.data)
	        });
		},
		getOrderTime(context){
			axios.get('/orderTime')
	        .then(response => {
	        	context.commit('setOrderTime',response.data)
	        });
		},
		getOrdersCount(context){
			axios.get('/getOrdersCount')
	        .then(response => {
	        	context.commit('setOrdersCount',response.data)
	        });
		},
		getAddress(context,customer_id){
			axios.get('/address/'+customer_id)
	        .then(response => {
	        	context.commit('setAddress',response.data)
	        });
		},
		getOrders(context,orderObj){
			axios.get('/getOrders/'+orderObj.status+'?page=' + orderObj.page)
	        .then(response => {
	        	context.commit('setOrders',response.data)
	        });
		},
		getOrderDetails(context,order_id){
			axios.get('/orders/'+order_id)
	        .then(response => {
	        	context.commit('setOrderDetails',response.data)
	        });
		},
		getDrivers(context){
			axios.get('/driver/all')
	        .then(response => {
	        	context.commit('setDrivers',response.data)
	        });
		},
		getServices(context){
			axios.get('/services')
		        .then(response => {
		          context.commit('setServices',response.data)
		        });	
		},
		getCategories(context){
			axios.get('/categories')
		        .then(response => {
		          context.commit('setCategories',response.data)
		        });	
		},
		getItems(context){
			axios.get('/items')
		        .then(response => {
		          context.commit('setItems',response.data)
		        });	
		},
		getMainAreas(context){
			axios.get('/mainAreas')
		        .then(response => {
		          context.commit('setMainAreas',response.data)
		        });	
		},
		getOffers(context){
			axios.get('/offers')
		        .then(response => {
		          context.commit('setOffers',response.data)
		        });	
		},
		getCoupons(context){
			axios.get('/coupons')
		        .then(response => {
		          context.commit('setCoupons',response.data)
		        });	
		},
		getOrderStatusCount(context){
			axios.get('/orders/count/indStatus')
		        .then(response => {
		          context.commit('setOrderStatusCount',response.data)
		        });	
		},
		getUser(context){
			axios.get('/authUser')
		        .then(response => {
		          context.commit('setUser',response.data)
		        });	
		},
		getCustomers(context){
			axios.get('/customers')
		        .then(response => {
		          context.commit('setCustomers',response.data)
		        });	
		}
	}
})