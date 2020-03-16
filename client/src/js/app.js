import Vue from 'vue'
import Search from './components/Search.vue'

Vue.component('Search', Search);

new Vue({
	render: h => h(Search)
}).$mount('#vue-parts-search');
