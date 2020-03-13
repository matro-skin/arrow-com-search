<template>
	<div class="arrowComSearch__container">
		<form method="post" ref="form" @submit.prevent="submit" class="arrowComSearch__form">
			<label for="arrow-com-search-input">Search</label>
			<input type="search" v-model="query"
			       :readonly="searching"
			       id="arrow-com-search-input"
			       placeholder="Type to search..." />
			<button type="submit" ref="submit" :disabled="searching || query.length === 0" v-html="searching ? 'Searching...' : 'Search'" />
		</form>
		<div class="arrowComSearch__result" v-if="items.length > 0">
			<div class="arrowComSearch__total">
				Найдено позиций: {{ items.length }}
			</div>
			<div class="arrowComSearch__items row">
				<div v-for="item in sortedByLabel" :key="item.id" class="col-md-3">
					<div class="card mb-3">
						<div class="card-header" v-html="item.id" />
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
				items: []

			}
		},

		computed: {

			sortedByLabel() {
				return this.items.sort((a,b) => (a.label.toLowerCase() > b.label.toLowerCase()) ? 1 : ((b.label.toLowerCase() > a.label.toLowerCase()) ? -1 : 0));
			},

			offset() {
				return this.items.length
			},

			url() {
				return `${arrowComSearchUrl}?search=${this.query}&offset=${this.offset}`
			}

		},

		methods: {

			async submit() {

				for (let i = 0; i < 5; i++) {
					this.items = [ ...this.items, ...await this.load() ]
				}

			},

			async load() {


				return await new Promise((resolve, reject) => {

					this.searching = true;

					axios.get(this.url)
						.then(response => {
							// resolve(Object.assign([],[ ...this.items, ...response.data.data ]) )
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
