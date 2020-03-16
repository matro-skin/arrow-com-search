<template>
	<div class="arrowComSearch__container">
		<form method="post" ref="form" @submit.prevent="submit" class="arrowComSearch__form">
			<label for="arrow-com-search-input">Search</label>
			<input type="search" v-model="query"
			       :readonly="searching"
			       id="arrow-com-search-input"
			       placeholder="Type to search..." @change="offset = 0" />
			<button type="submit" ref="submit" :disabled="searching || query.length === 0" v-html="searching ? 'Searching...' : 'Search'" />
			offset: {{ offset }}
		</form>
		<div class="arrowComSearch__result" v-if="items.length > 0">
			<div class="arrowComSearch__total">
				Найдено позиций: {{ items.length }}
			</div>
			<div class="arrowComSearch__items row">
				<div v-for="item in sortedByPartNumber" :key="item.itemId" class="col-md-3">
					<div class="card mb-3">
						<div class="card-header" v-html="item.itemId" />
						<div class="card-body">
							<pre v-html="item" />
						</div>
					</div>
				</div>
			</div>
			<div class="arrowComSearch__more">
				<button type="button" :disabled="searching" v-html="searching ? 'Поиск...' : 'Загрузить еще'" @click="$refs.submit.click()" />
			</div>
		</div>
		<div v-else-if="loaded === true" class="arrowComSearch__empty">
			Ничего не найдено, повторите запрос
		</div>
		<div v-else>
			Начните поиск
		</div>
	</div>
</template>

<script>
	import axios from 'axios'
	export default {

		data() {
			return {

				loaded: false,
				searching: false,
				query: 'TL431ID',
				items: [],
				offset: 0

			}
		},

		computed: {

			sortedByPartNumber() {
				return this.items.sort((a,b) => (a.partNumber.toLowerCase() > b.partNumber.toLowerCase()) ? 1 : ((b.partNumber.toLowerCase() > a.partNumber.toLowerCase()) ? -1 : 0));
			},

			url() {
				return `${arrowComSearchUrl}?search=${this.query}&offset=${this.offset}`
			}

		},

		methods: {

			async submit() {

				for (let i = 0; i < 5; i++) {
					let response = await this.load(),
						items = response.pricingResponse;
					this.items = [ ...this.items, ...items ];
					this.offset += items.length
					// console.log('response', response);
					// this.items = [ ...this.items, ...await this.load() ]
				}

			},

			async load() {

				return await new Promise((resolve, reject) => {

					this.searching = true;

					axios.get(this.url)
						.then(response => {
							resolve( response.data.data )
						})
						.catch(error => {
							console.error(error);
							reject(error)
						})
						.finally(() => {
							this.loaded = true;
							this.searching = false;
						})

				})

			},

		},

		mounted() {
			if(typeof arrowComSearchUrl === 'undefined') {
				console.error('Undefined const arrowComSearchUrl');
			}
		}

	}
</script>
