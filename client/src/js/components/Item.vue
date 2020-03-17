<template>
	<div class="item_block col-4 col-md-3 col-sm-6 col-xs-6">
		<div class="catalog_item_wrapp item">
			<div class="basket_props_block" v-show="false"></div>
			<div class="catalog_item main_item_wrapper item_wrap "
			     :id="item.external_id">
				<div>
					<div class="image_wrapper_block">
						<div class="stickers"></div>
						<a href="#" class="thumb shine" :id="item.external_id + 'pict'">
							<img class="ls-is-cached"
							     :src="photo" :alt="item.name" :title="item.name" />
						</a>

					</div>
					<div class="item_info TYPE_1" >
						<div class="item-title">
							<a href="#" class="dark_link">
								<span v-html="item.name" />
							</a>
						</div>
						<div class="item-description small"
						     v-if="item.description"
						     v-html="item.description" />
						<div class="sa_block">
							<div class="item-stock">
								<span class="icon stock"></span>
								<span class="value" v-html="item.quantity" />
							</div>
							<div class="article_block"></div>
						</div>
						<div class="cost prices clearfix">
							<div class="with_matrix">
								<div class="price">
									<div class="price_value_block">
										<span class="price_value"
										      v-html="`${item.unit_price} ${item.currency}`" />
									</div>
									<span class="price_measure">/шт</span>
								</div>
							</div>
							<div class="price_matrix_block" v-if="item.price_range.length > 0">
								<div class="price_matrix_wrapper">
									<div class="price_wrapper_block"
									     v-for="range in item.price_range"
									     :key="`${range.from}-${range.to}`">
										<div class="price_interval" v-html="`${range.from}-${range.to}`" />
										<div class="price"
										     :data-currency="item.currency"
										     :data-value="range.unit_price">
											<span class="values_wrapper">
												<span class="price_value"
												      v-html="`${range.unit_price} ${item.currency}`" />
											</span>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="footer_button inner_content ">
						<div class="sku_props"></div>
						<div class="counter_wrapp">
							<label :for="item.external_id + '_quantity'"
							       class="counter_block" data-offers="N" data-item="0">
								<button type="button" :id="item.external_id + '_quant_down'" class="minus"
								        :disabled="item.cart_amount === 1"
								        @click="item.cart_amount--">-</button>
								<input type="number" class="text" :id="item.external_id + '_quantity'"
								       name="quantity" :value="item.cart_amount" />
								<button type="button" :id="item.external_id + '_quant_up'" class="plus"
								        :disabled="item.cart_amount >= item.cart_amount_max"
								        @click="item.cart_amount++">+</button>
							</label>
							<div :id="item.external_id + '_basket_actions'" class="button_block ">
								<span :data-value="item.unit_price" :data-currency="item.currency"
								      class="small to-cart btn btn-default transition_bg animate-load"
								      data-item="0" data-float_ratio data-ratio="1" data-bakset_div
								      data-props data-part_props="Y" data-add_props="Y" data-empty_props="Y"
								      data-offers data-iblockid="25" data-quantity="1">
									<i></i><span>В корзину</span>
								</span>
								<a rel="nofollow" href="/basket/"
								   class="small in-cart btn btn-default transition_bg"
								   data-item="0"
								   v-show="false">
									<i></i><span>В корзине</span>
								</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
	/*
	Item resource:

	sku: "J56"
	name: "TL431IDBVR"
	description: "V-Ref Adjustable 2.495V to 36V 100mA Automotive 5-Pin SOT-23 T/R"
	partNumber: "TL431IDBVR"
	external_id: "102475"
	photo_ext_src: "http://download.siliconexpert.com/pdfs/2018/4/5/9/21/8/643/txn_/manual/dbv0005a.jpg"
	quantity: 3000
	min_order_quantity: 3000
	unit_price: 0.06804
	currency: "EUR"
	price_range: []
	loop_key: "J56_TL431IDBVR_102475"
	 */
	export default {

		props: {

			item: {
				required: true,
				type: Object
			}

		},

		computed: {

			photo() {
				return this.item.photo_ext_src || '/images/has_not_photo.jpg'
			}

		}

	}
</script>

<style scoped>

</style>
