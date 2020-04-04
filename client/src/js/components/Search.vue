<template>
	<div class="arrowComSearch__container">
		<form v-if="showInput" method="post" ref="form" @submit.prevent="submit(true)" class="arrowComSearch__form">
			<label for="arrow-com-search-input">Search</label>
			<input type="search" v-model="query"
			       :readonly="searching"
			       id="arrow-com-search-input"
			       placeholder="Type to search..." @change="offset = 0" />
			<button type="submit" ref="submit" :disabled="searching || query.length === 0"
			        v-html="searching ? 'Searching...' : 'Search'" />
		</form>
		<div class="arrowComSearch__result" v-if="items.length > 0">
			<div class="arrowComSearch__total">
				Найдено позиций: {{ items.length }}
			</div>
			<div class="arrowComSearch__items row">
				<Item v-for="item in sortedByExtId" :key="item.loop_key" :item="item" />
			</div>
			<div class="arrowComSearch__more" v-show="false">
				<button type="button" :disabled="searching" v-html="searching ? 'Поиск...' : 'Загрузить еще'"
				        @click="$refs.submit.click()" />
			</div>
		</div>
		<div v-else-if="loaded === true" class="arrowComSearch__empty">
			Ничего не найдено, повторите запрос
		</div>
		<div v-else-if="showInput">
			Начните поиск
		</div>
		<div v-if="instantSearch && searching">
			Поиск...
		</div>
	</div>
</template>

<script>
	import axios from 'axios'
	import Item from './Item'

	export default {

		data () {
			return {

				validConfig: false,
				loaded: false,
				searching: false,
				query: '',
				items: [],
				offset: 0,

			}
		},

		computed: {

			sortedByExtId () {
				return this.items.sort((a, b) => (a.external_id.toLowerCase() > b.external_id.toLowerCase())
					? 1
					: ((b.external_id.toLowerCase() > a.external_id.toLowerCase()) ? -1 : 0))
			},

			showInput () {
				return partsSearchConfig.input === true
			},

			instantSearch () {
				return partsSearchConfig.instant === true
			},

		},

		methods: {

			getUrl (driver) {
				return `${partsSearchConfig.url}?driver=${driver}&search=${this.query}&offset=${this.offset}`
			},

			async submit (reset) {

				if (reset) {
					this.items = []
				}

				for (let driver of partsSearchConfig.drivers) {
					let response = await this.load(driver),
						items = response.data
					this.items = [...this.items, ...items]
					this.offset += items.length
				}

			},

			async load (driver) {

				return await new Promise((resolve, reject) => {

					this.searching = true

					axios.get(this.getUrl(driver)).then(response => {
						resolve(response.data)
					}).catch(error => {
						console.error(error)
						reject(error)
					}).finally(() => {
						this.loaded = true
						this.searching = false
					})

				})

			},

			getSearchValue () {
				let regex = new RegExp('[?&]q(=([^&#]*)|&|#|$)'),
					results = regex.exec(window.location.search)
				if (! results || !results[2]) {
					return ''
				}
				return decodeURIComponent(results[2].replace(/\+/g, ' '))
			},

			runInstantSearch() {
				let query = this.getSearchValue();
				if(query.length === 0) {
					return
				}
				this.query = query;
				this.searching = true;
				this.submit(true)
			}

		},

		mounted () {

			if (typeof partsSearchConfig === 'undefined') {
				console.error('Undefined const partsSearchConfig')
				return
			}

			if (typeof partsSearchConfig.url === 'undefined') {
				console.error('Undefined const partsSearchConfig.url')
				return
			}

			if (typeof partsSearchConfig.drivers === 'undefined') {
				console.error('Undefined const partsSearchConfig.drivers')
				return
			}

			if (partsSearchConfig.drivers.length === 0) {
				console.error('Empty partsSearchConfig.drivers')
				return
			}

			this.validConfig = true

			if (this.instantSearch) {
				this.runInstantSearch()
			}

		},

		components: {
			Item: Item,
		},

	}
</script>
