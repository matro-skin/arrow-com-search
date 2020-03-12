<template>
	<form method="post" @submit.prevent="submit">
		<label for="arrow-com-search-input">Search</label>
		<input type="search" v-model="query"
		       :readonly="searching"
		       id="arrow-com-search-input"
		       placeholder="Type to search..." />
		<button type="submit" :disabled="searching || query.length === 0">Search</button>
	</form>
</template>

<script>
	import axios from 'axios'
	axios.defaults.baseURL = 'api/';
	// axios.defaults.headers.accept = 'application/json';
	// axios.defaults.headers.client_id = process.env.OAUTH_USERNAME;
	export default {

		data() {
			return {
				// token: null,
				// ls_token: 'arrowcom_oauth_token',
				searching: false,
				// url: 'https://my.arrow.com/api/priceandavail/search',
				query: ''
				// query: {
				// 	billTo: 2268103,
				// 	shipTo: 2270544,
				// 	Currency: 'USD',
				// 	Limit: 10,
				// 	Search: '',
				// }
			}
		},

		computed: {

			// urlQuery() {
			// 	return this.url + '?' + Object.keys(this.query).map(k => k + '=' + this.query[k]).join('&')
			// }

		},

		methods: {

			submit() {

				this.searching = true;
				// axios.get(this.urlQuery,{
				axios.get('/?search',{
					headers: {
						authorization: this.token
					}
				})
				.then(response => {

				})
				.catch(error => {
					console.error(error)
				})
				.finally(() => {
					this.searching = false
				})

			},

		},

		created() {

		}

	}
</script>
