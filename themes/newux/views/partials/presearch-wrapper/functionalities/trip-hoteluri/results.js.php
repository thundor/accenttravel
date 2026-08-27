let availability_labels = {
	yes: ["Disponibil", 'success'],
	ask: ["La cerere", 'warning'],
	no: ["Indisponibil", 'error'],
};
export default {
	name: <?php echo json_encode($a, JSON_UNESCAPED_SLASHES); ?>,
	inject: ['search'],
	emits: ['offer', 'sorted_results', 'sorted', 'research'],
	data: () => {
		return {
			sorts: {
				'price_asc' : 'Pret ascendent',
				'price_desc' : 'Pret descendent',
				'stars_asc' : 'Stele ascendent',
				'stars_desc' : 'Stele descendent',
				'special' : 'Oferte speciale',
				// 'Recommended 1' : 'Recomandate',
			},
			currentTime: Date.now(),
			timeInterval: undefined,
			selected_offer: undefined,
			sort_menu: false,
			text_result_type: ['hotel', 'hoteluri'],
			text_searching: 'In curs de cautare...',
			text_no_results: 'Niciun rezultat gasit',
			page: 1,
			limit: 10,
			sort: 'price_asc',
			availability_label: Object.freeze(availability_labels),
		}
	},
	props: {
      sorted: {
          type: Object,
          default: () => (undefined),
      },
      data: {
          type: Object,
          default: () => ({}),
      },
      results: {
          type: Object,
          default: () => ({}),
      },
      search_data: {
          type: Object,
          default: () => ({}),
      },
      applied_filters: {
          type: Object,
          default: () => ({}),
      },
      searching: {
          type: Boolean,
          default: () => (false),
      },
		select_first: {
			type: Boolean,
			default: () => (false),
		},
		extra_price: {
			type: Number,
			default: () => (0),
		},
  },
	template : `
	<div class="bg-background hoteluri-results">
		<?php
		/* <div>
			<div v-for="(k, v) in applied_filters">
				<span v-text="k"></span>
				<span v-text="JSON.stringify(v)"></span>
			</div>
		</div> */ ?>
		<div v-if="searching && undefined === results.Id" v-text="text_searching"></div>
		<div v-else-if="!all_results.length && undefined === results.Id" v-text="text_no_results"></div>
		<div v-else :class="{'loading-section': searching}">
			
			<div class="list-expiry d-flex justify-space-between px-2 px-2">
				<div v-if="results.updated && undefined !== results.life">
					<template v-if="results.expiry && currentTime < 1000 * (results.expiry)">Lista expira in: {{ secondsToFormattedDuration((results.expiry) - parseInt(currentTime/1000)) }}</template>
					<template v-else>Lista Expirata </template>
				</div>
				<v-btn size="small" variant="outlined" @click="$.emit('research')">Incarca rezultate actualizate</v-btn>
				<div v-if="results.updated">Data listare: {{ formatDateFull(1000 * results.updated) }}</div>
				<?php /*<div v-if="results.updated && undefined !== results.life">Expirare lista: {{ formatDateFull(1000 * (results.expiry)) }}</div> */ ?>
			</div>
			<div class="results-header px-2">
				<div class="results-total">
					<template v-if="results.total_items == 1">Am gasit <b v-text="results.total_items"></b> singur <span v-text="text_result_type[0]"></span> care se potriveste cautarii tale.</template>
					<template v-else-if="results.total_items < 20">Am gasit <b v-text="results.total_items"></b> <span v-text="text_result_type[1]"></span> care se potrivesc cautarii tale.</template>
					<template v-else>Am gasit <b v-text="results.total_items"></b> de <span v-text="text_result_type[1]"></span> care se potrivesc cautarii tale.</template>
				</div>
				<v-menu
					class="sort-results-dropdown"
				  v-model="sort_menu"
				  :close-on-content-click="false"
				  location="start"
				>
				  <template v-slot:activator="{ props }">
					<div class="results-sort">
					<span v-text="sorts[sort]"></span>
					<v-btn
					  color="indigo"
					  v-bind="props"
					  density="compact"
					  :icon="sort_menu ? 'mdi-close' : 'mdi-chevron-down'"
					>
					</v-btn>
					</div>
				  </template>

				  <v-card min-width="300">
					<v-radio-group
						  v-model="sort">
						<v-radio v-for="(ktext, ksort) in sorts"
						  :value="ksort"
						  :label="ktext"
						  color="primary"
						  @click="sort_menu = false"
						  hide-details
						></v-radio>
					</v-radio-group>

					<v-card-actions>
					  <v-spacer></v-spacer>

					  <v-btn
						variant="text"
						@click="sort_menu = false"
					  >
						Inchide
					  </v-btn>
					</v-card-actions>
				  </v-card>
				</v-menu>
			</div>
			<template v-for="result in all_results">
				<v-card class="mt-4 pa-2" :class="{[(select_first && selected_offer && selected_offer.Id == result.Id && ' border-lg border-primary' || '')]:1}">
					<div class="d-flex flex-no-wrap justify-space-between w-100">
					<div class="mb-4 pb-1">
					  <v-avatar
						class="ma-4 mb-0"
						rounded="lg"
						density="compact"
						style="height:200px;width:300px"
					  >
						<v-img :src="(result.force_image_because_error || result.Image || '/themes/newux/assets/images/placeholder.webp')" v-on:error="result.force_image_because_error = '/themes/newux/assets/images/placeholder.webp'"
						cover
						:class="{toGrayscale: this.searching}"
						></v-img>
					  </v-avatar>
						<div v-if="parseInt(result.SpecialDeal)" class="ms-4 text-h6 mt-4 text-primary">
						<v-chip variant="elevated" color="warning">
							<span>
								Pret special!
							</span>
						</v-chip>
						</div>
						<div v-if="parseInt(result.Recommended)" class="ms-4 text-h6 mt-4 text-primary">
						<v-chip variant="elevated" color="warning">
							<span>
								Recomandat!
							</span>
						</v-chip>
						</div>
					</div>
					  <div class="pa-5 flex-fill mb-4">
						<div class="d-flex justify-space-between w-100">
							<div class="flex-fill">
								<v-card-title class="pa-0 text-wrap">
									<div class="text-h6" v-if="undefined !== result.Stars">
										<v-icon v-if="result.Stars" icon="mdi-star" v-for="n in parseInt(result.Stars||0)" color="#fcc200"></v-icon>
										<v-icon v-else icon="mdi-star-off-outline" color="#fcc200"></v-icon>
									</div>
									<div class="text-h5 font-weight-bold" v-text="result.Name"></div>
								</v-card-title>

								<v-card-subtitle class="pa-0 text-wrap" v-if="result.Address">
									<v-icon size="28" icon="mdi-map-marker-outline" class="me-3"></v-icon>
									<span v-if="result.Address" v-text="result.Address"></span>
								</v-card-subtitle>
							</div>
							<div class="d-flex flex-column">
								<span>de la</span>
								<span class="text-primary text-h4 font-weight-bold text-pre" v-text="format_price(extra_price + parseFloat(result.MinPrice),(result.Currency || {}))"></span>
								<template v-if="select_first && selected_offer">
									<span v-if="0" class="text-primary text-h6 font-weight-bold text-pre" v-text="format_price(result.MinPrice - selected_offer.MinPrice,(result.Currency || {}), 1, 1)"></span>
									<span>/calatorie, zbor inclus</span>
								</template>
								<template v-else>
									<span>/calatorie</span>
								</template>
								<?php /*
								<div v-if="offer.InitialPrice && offer.Price && offer.InitialPrice > offer.Price" class="text-h6 text-primary text-decoration-line-through text-pre" v-text="format_price(offer.InitialPrice, offer.Currency.Code)"></div>
								*/ ?>
							</div>
						</div>
						
						<v-card-text class="pa-0">
							<hr class="my-3" />
							<div class="d-flex justify-space-between ga-2 flex-wrap flex-sm-nowrap">
								<div class="w-100">
									<div v-if="result.ShortDesc" v-text="result.ShortDesc"></div>
									<div class="included-services">
									<small class="text-primary">Facilitati</small>
									<div class="d-flex ga-3 justify-space-between" v-if="result.Facilities">
									<div class="hotel-facilities" v-text="result.Facilities"></div>
									</div>
									<div class="hotel-facilities-desc" v-if="result.FacilitiesDesc" v-text="result.FacilitiesDesc"></div>
									</div>
									<div class="d-flex ga-3 justify-space-between hotel-pois-wrapper" v-if="result.Pois">
									<div class="hotel-pois" v-text="result.Pois"></div>
									</div>
									<?php if ($this->theme->_can_edit){ ?>
									<a :href="'/trip/hotel/' + result.Id" v-text="'/trip/hotel/' + result.Id"></a>
									<?php } ?>
								</div>
							<?php /* <div v-html="result.Content.Content"></div> */ ?>
								<div class="d-flex flex-column justify-end flex-fill flex-sm-0-0-0">
								<v-btn
									class="ms-2 mt-2"
									@click="setOffer(result)"
									size="large"
									v-text="select_first ? 'Alege' : 'Detalii oferta'"
									:disabled = "select_first && selected_offer && selected_offer.Id == result.Id"
									variant="outlined"
								  ></v-btn>
								</div>
							</div>
						</v-card-text>
					  </div>

					</div>
				</v-card>
				
			</template>
			<div class="text-center" v-if="pages > 1">
				<v-pagination
				  v-model="page"
				  :length="pages"
				  rounded="0"
				></v-pagination>
			</div>
		</div>
	</div>
	`,
	beforeCreate() {
	},
	beforeUnmount() {
		clearInterval(this.timeInterval);
	},
	mounted() {
		this.timeInterval = setInterval(() => {this.currentTime = Date.now()}, 1000);
	},
	computed: {
		all_results() {
			var results = ((this.results._embedded||{}).hotels || []);
			
			results = results.sort(this['sort_' + this.sort]);
			
			return results.slice(this.limit * (this.page - 1), this.limit * this.page);
		},
		pages() {
			return Math.ceil(parseInt((this.results || {}).total_items || 0) / this.limit);
		},
	},
	methods: {
		sort_price_asc(a, b){
			return a.MinPrice - b.MinPrice;
		},
		sort_price_desc(a, b){
			return -this.sort_price_asc(a,b);
		},
		sort_stars_asc(a, b){
			return a.Stars - b.Stars;
		},
		sort_stars_desc(a, b){
			return -this.sort_stars_asc(a,b);
		},
		sort_special(a, b){
			return (-(a.SpecialDeal - b.SpecialDeal)) || (a.MinPrice - b.MinPrice) || (a.Stars - b.Stars);
		},
		base_sort_price_asc(a, b){
			return a.MinPrice - b.MinPrice;
		},
		base_sort_price_desc(a, b){
			return -this.base_sort_price_asc(a,b);
		},
		base_sort_special(a, b){
			return (-(a.SpecialDeal - b.SpecialDeal)) || (a.MinPrice - b.MinPrice);
		},
		setOffer(offer){
			this.selected_offer = null;
			setTimeout(() => {
				this.selected_offer = offer;
			}, 0)
		},
	},
	watch: {
		'sorted.page': {
			handler: function(nv,ov){
				this.page = nv || 1;
			},
		},
		'sort': {
			handler: function(nv,ov){
				if(this.page > 1){
					this.page = 1;
				} else {
					this.$emit('sorted', {sort: nv, page: 1});
				}
			},
		},
		'page': {
			handler: function(nv,ov){
				// this.$emit('sorted', {sort: this.sort, page: nv});
				
				window.scrollTo({
				  top: 0,
				  left: 0,
				  behavior: 'smooth',
				});
			},
		},
		'selected_offer': {
			handler: function(nv,ov){
				if(!nv) return;
				console.warn('this.selected_offer', nv);
				this.$emit('offer', nv, this.results);
			},
			immediate: true
		},
		'results._embedded': {
			handler: function(nv,ov){
				this.page = 1;
				var step = parseInt(window_url.searchParams.get("step"));
				if(this.results.research_hash){
					console.warn('this.results.research_hash', this.results.research_hash);
					var selected_offer = this.all_results.find(h => h.Id == this.results.research_hash.hotel_id);
					if(selected_offer){
						this.selected_offer = selected_offer;
					} else {
						console.error('Offer not found');
						if(this.data.step > 1)
							this.data.step = 1;
					}
					return;
				}
				// console.warn('hotel results', nv);
				if(this.results && this.results.summary && this.results.summary.progress == 100 && this.results.status && this.all_results.length){
					if(!this.selected_offer){
						if(this.select_first || (!isNaN(step) && step > 1 && step < 4)){
							this.selected_offer = this.all_results[0];
							console.error('forced result click', this.selected_offer, this.all_results);
						} else {
							console.error('blocked forced result click');
						}
					}
				}
			},
			immediate: true
		},
	},
}
