let availability_labels = {
	yes: ["Disponibil", 'success'],
	ask: ["La cerere", 'warning'],
	no: ["Indisponibil", 'error'],
};
export default {
	name: <?php echo json_encode($a, JSON_UNESCAPED_SLASHES); ?>,
	inject: ['search'],
	emits: ['offer', 'sorted_results', 'sorted'],
	data: () => {
		return {
			sorts: {
				'price_asc' : 'Pret ascendent',
				'price_desc' : 'Pret descendent',
				'special' : 'Oferta speciala',
			},
			sort_menu: false,
			result_type: null,
			text_result_type: ['rezultat', 'rezultate'],
			text_searching: 'In curs de cautare...',
			text_no_results: 'Niciun rezultat gasit',
			page: 1,
			limit: 10,
			sort: 'price_asc',
			availability_label: Object.freeze(availability_labels),
		}
	},
	props: {
      results: {
          type: Array,
          default: () => ([]),
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
  },
	template : `
	<div class="bg-background">
		<?php
		/* <div>
			<div v-for="(k, v) in applied_filters">
				<span v-text="k"></span>
				<span v-text="JSON.stringify(v)"></span>
			</div>
		</div> */ ?>
		<div v-if="searching" v-text="text_searching"></div>
		<div v-else-if="!results.length" v-text="text_no_results"></div>
		<div v-else>
			<template v-for="display_results in [display_results]">
			<div class="results-header px-2">
				<div class="results-total">
					<template v-if="results.length == 1">Am gasit <b v-text="results.length"></b> singur <span v-text="text_result_type[0]"></span> care se potriveste cautarii tale.</template>
					<template v-else-if="results.length < 20">Am gasit <b v-text="results.length"></b> <span v-text="text_result_type[1]"></span> care se potrivesc cautarii tale.</template>
					<template v-else>Am gasit <b v-text="results.length"></b> de <span v-text="text_result_type[1]"></span> care se potrivesc cautarii tale.</template>
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
						<v-radio
						  value="price_asc"
						  :label="sorts.price_asc"
						  color="primary"
						  @click="sort_menu = false"
						  hide-details
						></v-radio>
						<v-radio
						  value="price_desc"
						  :label="sorts.price_desc"
						  color="primary"
						  @click="sort_menu = false"
						  hide-details
						></v-radio>
						<v-radio
						  value="special"
						  :label="sorts.special"
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
			<template v-for="result in display_results">
				<v-card class="mt-4 px-2">
					<div class="d-flex flex-no-wrap justify-space-between w-100">
					<div class="mb-4 pt-1">
					  <v-avatar v-if="(result.MainImage || {'ExternalUrl': '/themes/newux/assets/images/placeholder.webp'}).ExternalUrl"
						class="ma-4 mb-0"
						rounded="lg"
						density="compact"
						style="height:200px;width:300px"
					  >
						<v-img :src="(result.MainImage || {'ExternalUrl': '/themes/newux/assets/images/placeholder.webp'}).ExternalUrl" cover></v-img>
					  </v-avatar>
					  <template v-for="offer in [(result.Offers||[])[0]||{}]">
						<div v-if="offer.InitialPrice && offer.Price && offer.InitialPrice > offer.Price" class="ms-4 text-h6 mt-4 text-primary">
						<v-chip variant="elevated" color="warning">
							<span>
								Reducere <strong v-text="- Math.ceil(100 * (offer.InitialPrice - offer.Price)/offer.InitialPrice) + '%'"></strong>
							</span>
						</v-chip>
						</div>
					  </template>
					  <?php /*
					  <template
						v-for="i in ((result.Content|| {}).ImageGallery || {}).Items || []"
					  >
					  <v-avatar
						class="ma-4 mb-0"
						v-if="i.ExternalUrl != (result.MainImage || {}).ExternalUrl"
						rounded="lg"
						density="compact"
						style="height:200px;width:300px"
					  >
						<v-img :src="i.ExternalUrl"></v-img>
					  </v-avatar>
					  </template> */ ?>
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
									<span v-if="result.Address" v-text="[(result.Address.City||({})).Name, (result.Address.Destination||({})).Name, ((result.Address.City||({})).Country||{}).Name].filter(v => !!v).join(' - ')"></span>
								</v-card-subtitle>
							</div>
							<div v-for="offer in [(result.Offers||[])[0]||{}]" class="d-flex flex-column">
								<span>de la</span>
								<span class="text-primary text-h4 font-weight-bold text-pre" v-text="format_price(offer.Price,(offer.Currency || {}).Code)"></span>
								<div v-if="offer.InitialPrice && offer.Price && offer.InitialPrice > offer.Price" class="text-h6 text-primary text-decoration-line-through text-pre" v-text="format_price(offer.InitialPrice, offer.Currency.Code)"></div>
								<span>/calatorie</span>
							</div>
						</div>
						
						<v-card-text class="pa-0">
							<hr class="my-3" />
							<div class="d-flex justify-space-between ga-2 flex-wrap flex-sm-nowrap flex-fill flex-sm-0-0-0" v-for="offer in [(result.Offers||[])[0]||{}]">
								<div class="w-100">
									<div v-if="result.Period">
										<div>Durata: <span v-text="result.Period"></span> <span v-text="result.Period == 1 ? 'noapte' : 'nopti'"></span></div>
										<div v-for="plecare in [(offer.Items || []).find(i => (i.Merch||{}).type == 'Transport')].filter(i=>i)">Plecari: <span v-text="plecare.Merch.DepartureTime"></span></div>
										
									</div>
									<div v-if="result.ShortContent" v-text="result.ShortContent"></div>
									<div v-if="((result.Content||{}).ShortDescription)" v-html="((result.Content||{}).ShortDescription)"></div>
									<div class="included-services">
									<small class="text-primary">Servicii incluse in pachet</small>
									<div class="d-flex ga-3 justify-space-between" v-if="offer.facilities">
									<div class="d-flex flex-column">
										<div v-if="offer.facilities.Meal">
											<v-icon icon="mdi-silverware-variant" class="me-2"></v-icon> 
											<span v-text="offer.facilities.Meal.join(', ')"></span>
										</div>
										<template v-if="offer.facilities.Transport">
											<div v-if="offer.facilities.Transport == 'plane'">
												<v-icon icon="mdi-airplane" class="me-2"></v-icon> 
												<span>Bilete de avion + taxe</span>
											</div>
											<div v-if="offer.facilities.Transport == 'bus'">
												<v-icon icon="mdi-bus"></v-icon> 
												<span>Bilete de autocar + taxe</span>
											</div>
										</template>
									</div>
									<div class="d-flex flex-column">
										<div v-if="offer.facilities.Type">
											<v-icon icon="mdi-bed" class="me-2"></v-icon> 
											<span v-text="offer.facilities.Type.join(', ')"></span>
										</div>
										<div v-if="offer.facilities.Transfer">
											<v-icon icon="mdi-taxi" class="me-2"></v-icon> 
											<span v-text="offer.facilities.Transfer.join(', ')"></span>
										</div>
									</div>
									</div>
									</div>
									<?php if ($this->theme->_can_edit){ ?>
									<a v-if="result_type && 'travelfuse/circuit' != result_type[0]" :href="'/' + result_type[0] + '/' + result.Id" v-text="'/' + result_type[0] + '/' + result.Id"></a>
									<?php } ?>
								</div>
							<?php /* <div v-html="result.Content.Content"></div> */ ?>
								<div class="d-flex flex-column flex-fill flex-sm-0-0-0">
									<div class="d-flex ga-2 justify-space-between">
										<v-chip  v-if="offer.InitialPrice && offer.Price && offer.InitialPrice > offer.Price" variant="elevated" color="warning" density="compact" size="20" style="font-size: 16px">
											<v-icon icon="mdi-percent-outline"></v-icon>
										</v-chip>
										<template v-for="availability in [((offer.Items||[]).find((i) => (i.Merch || {}).type == 'Room') || {}).Availability || 'no']">
										<v-chip  variant="elevated" :color="availability_label[availability][1]" density="compact" class="ms-auto" v-text="availability_label[availability][0]"></v-chip>
										</template>
										<?php /*
										<v-chip variant="elevated" color="warning" density="compact">
											Indisponibil
										</v-chip>
										*/ ?>
									</div>
								<v-btn
									class="ms-2 mt-2"
									@click="$emit('offer', result);"
									size="large"
									text="Detalii oferta"
									variant="outlined"
								  ></v-btn>
								</div>
							</div>
						</v-card-text>
					  </div>

					</div>
					<?php /*
					<div v-for="o in result.Offers">
						
						
						<v-expansion-panels>
						  <v-expansion-panel>
							<v-expansion-panel-title>
							<div class="d-flex w-100 justify-space-between">
							<div v-for="(tfacilities,ftype) in o.facilities">
								<strong v-text="ftype" v-if="tfacilities.length"></strong>
								<span v-for="(facility) in tfacilities" class="ms-2" v-html="(search.merch_type[ftype][facility] || ['', facility])[1]"></span>
							</div>
							<div class="text-h6 text-primary" v-text="format_price(o.Price, o.Currency.Code)"></div>
						</div>
							
							</v-expansion-panel-title>
							<v-expansion-panel-text>
								<div class="text-h6" v-text="o.Info"></div>
								<ol class="ps-5">
								<li v-for="i in o.Items">
									<p v-if="i.Merch && i.Merch.Title" class="ma-0"><span>Merch:</span> <span v-text="i.Merch.Title"></span></p>
									<div class="ps-5">
										<p v-if="i.Availability" class="ma-0"><span>Availability:</span> <span v-text="i.Availability"></span></p>
										<p v-if="i.Quantity" class="ma-0"><span>Quantity:</span> <span v-text="i.Quantity"></span></p>
										<p v-if="i.UnitPrice" class="ma-0"><span>UnitPrice:</span> <span v-text="i.UnitPrice"></span></p>
										<p v-if="i.CheckinBefore" class="ma-0"><span>CheckinBefore:</span> <span v-text="i.CheckinBefore"></span></p>
										<p v-if="i.CheckinAfter" class="ma-0"><span>CheckinAfter:</span> <span v-text="i.CheckinAfter"></span></p>
										<p v-if="i.Currency && i.Currency.Code" class="ma-0"><span>Currency:</span> <span v-text="i.Currency.Code"></span></p>
										<p v-if="i.Merch && i.Merch.type" class="ma-0"><span>Type:</span> <span v-text="i.Merch.type"></span></p>
										<p v-if="i.Merch && i.Merch.Code" class="ma-0"><span>Code:</span> <span v-text="i.Merch.Code"></span></p>
									</div>
								</li>
								</ol>
						
							  <v-btn
								class="ms-2"
								@click="$emit('offer', {...result, Offer: {...o}});"
								size="large"
								text="Detalii oferta"
								variant="outlined"
							  ></v-btn>
							</v-expansion-panel-text>
						  </v-expansion-panel>
						</v-expansion-panels>
					</div>
					*/ ?>
				</v-card>
				
			</template>
			<div class="text-center" v-if="pages > 1">
				<v-pagination
				  v-model="page"
				  :length="pages"
				  rounded="0"
				></v-pagination>
			</div>
			</template>
		</div>
	</div>
	`,
	beforeCreate() {
	},
	mounted() {
	},
	computed: {
		display_results() {
			this.$emit('sorted_results', this.sorted_results);
			return this.sorted_results.slice(this.limit * (this.page - 1), this.limit * this.page);
		},
		sorted_results() {
			var results = [...this.results];
			results.map(r => {
				r.Offers.sort(this['base_sort_' + this.sort]);
			});
			
			// console.warn('this.results', JSON.parse(JSON.stringify(this.results)));
			results = results.sort(this['sort_' + this.sort]);
			return results;
		},
		pages() {
			return Math.ceil((this.results || []).length / this.limit);
		},
	},
	methods: {
		sort_price_asc(a, b){
			return a.Offers[0].Price - b.Offers[0].Price;
		},
		sort_price_desc(a, b){
			return -this.sort_price_asc(a,b);
		},
		sort_special(a, b){
			return (-(a.Offers[0]._SpecialPercent - b.Offers[0]._SpecialPercent)) || (a.Offers[0].Price - b.Offers[0].Price);
		},
		base_sort_price_asc(a, b){
			return a.Price - b.Price;
		},
		base_sort_price_desc(a, b){
			return -this.base_sort_price_asc(a,b);
		},
		base_sort_special(a, b){
			return (-(a._SpecialPercent - b._SpecialPercent)) || (a.Price - b.Price);
		},
	},
	watch: {
		'sort': {
			handler: function(nv,ov){
				this.$emit('sorted', this.nv);			
			},
			immediate: true
		},
		'page': {
			handler: function(nv,ov){
				window.scrollTo({
				  top: 0,
				  left: 0,
				  behavior: 'smooth',
				});						
			},
			immediate: true
		},
		'results': {
			handler: function(nv,ov){
				this.page = 1;
				var step = parseInt(window_url.searchParams.get("step"));
				if(nv && nv.length){
					if(!isNaN(step) && step > 1 && step < 4){
						console.error('forced result click');
						this.$emit('offer',nv[0]);
					} else {
						console.error('blocked forced result click');
					}
				}
			},
			immediate: true
		},
	},
}
