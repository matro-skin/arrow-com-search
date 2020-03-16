<template>
	<div class="arrowComSearch__container">
		<form method="post" ref="form" @submit.prevent="submit" class="arrowComSearch__form">
			<label for="arrow-com-search-input">Search</label>
			<input type="search" v-model="query"
			       :readonly="searching"
			       id="arrow-com-search-input"
			       placeholder="Type to search..." @change="offset = 0" />
			<button type="submit" ref="submit" :disabled="searching || query.length === 0" v-html="searching ? 'Searching...' : 'Search'" />
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

				validConfig: false,
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
			}

		},

		methods: {

			getUrl(driver) {
				return `${partsSearchConfig.url}?driver=${driver}&search=${this.query}&offset=${this.offset}`
			},

			async submit() {

				for(let driver of partsSearchConfig.drivers) {
					let response = await this.load(driver),
						items = response.items;
					// todo: uncomment when collection will be ready
					// this.items = [ ...this.items, ...items ];
					// this.offset += items.length
				}

			},

			async load(driver) {

				return await new Promise((resolve, reject) => {

					this.searching = true;

					axios.get( this.getUrl(driver) )
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

			if(typeof partsSearchConfig === 'undefined') {
				console.error('Undefined const partsSearchConfig');
				return;
			}

			if(typeof partsSearchConfig.url === 'undefined') {
				console.error('Undefined const partsSearchConfig.url');
				return;
			}

			if(typeof partsSearchConfig.drivers === 'undefined') {
				console.error('Undefined const partsSearchConfig.drivers');
				return;
			}

			if(partsSearchConfig.drivers.length === 0) {
				console.error('Empty partsSearchConfig.drivers');
				return;
			}

			this.validConfig = true;

		}

	}
</script>
