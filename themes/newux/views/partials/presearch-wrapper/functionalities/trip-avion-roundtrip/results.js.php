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
				'duration_asc' : 'Durata ascendent',
				'duration_desc' : 'Durata descendent',
				<?php switch (basename(dirname($a))){ 
				case 'trip-citybreak':
				?>
				'departure_date_asc' : 'Data plecare ascendent',
				'destination_date_asc' : 'Data retur ascendent',
				<?php 
					break;
				default:
				?>
				<?php 
					break;
				} ?>
				// 'special' : 'Oferte speciale',
				// 'Recommended 1' : 'Recomandate',
			},
			currentTime: Date.now(),
			timeInterval: undefined,
			dialog: false,
			selected_offer: undefined,
			show_details: undefined,
			allresults: [],
			combinations: [],
			sort_menu: false,
			text_result_type: ['zbor', 'zboruri'],
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
	<div class="bg-background flight-results">
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
					<template v-else>Lista Expirata <v-btn size="small" variant="outlined" @click="$.emit('research')">Incarca rezultate actualizate</v-btn></template>
				</div>
				<div v-if="results.updated">Data listare: {{ formatDateFull(1000 * results.updated) }}</div>
				<?php /*<div v-if="results.updated && undefined !== results.life">Expirare lista: {{ formatDateFull(1000 * (results.expiry)) }}</div> */ ?>
			</div>
			<div class="results-header px-2">
				<div class="results-total">
					<template v-if="combinations.length == 1">Am gasit <b v-text="combinations.length"></b> singur <span v-text="text_result_type[0]"></span> care se potriveste cautarii tale.</template>
					<template v-else-if="combinations.length < 20">Am gasit <b v-text="combinations.length"></b> <span v-text="text_result_type[1]"></span> care se potrivesc cautarii tale.</template>
					<template v-else>Am gasit <b v-text="combinations.length"></b> de <span v-text="text_result_type[1]"></span> care se potrivesc cautarii tale.</template>
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
			<v-dialog v-model="dialog" class="detalii-zbor-modal">
				<template v-slot:default="{ isActive }">
				<v-card class="align-self-center w-100" style="max-width: min(95vw, 630px);">
				<v-card-text>
			  <v-list lines="two" subheader theme="light" class="max-height pa-4" rounded="theme">
				<v-list-item-title class="pb-4 px-0 text-h5" v-text="'Detalii zbor'"></v-list-item-title>
			  <template v-if="show_details" v-for="result in [show_details]">
				<small class="d-flex flex-column mt-3">
					<span v-if="result.Flights.length > 1"><b>Durata totala a calatoriilor:</b> {{ minutesToFormattedDuration(result.Duration) }}</span>
					<template v-if="result.StopTime">
						<span><b>Total durata zbor:</b> {{ minutesToFormattedDuration(result.Duration - result.StopTime) }}</span>
						<span><b>Total durata escale:</b> {{ minutesToFormattedDuration(result.StopTime) }}</span>
					</template>
				</small>
				<hr class="my-4"/>
				<template v-for="(route, routeIndex) in result.Flights||[]">
				  <div class="flight-route">
					  <hr v-if="routeIndex" class="my-4"/>
					<small class="d-flex flex-column" v-if="result.BrandedFare">
						<span v-if="result.BrandedFare?.BrandDetails?.[routeIndex]?.Cabin"><b>Clasa:</b> {{ result.BrandedFare.BrandDetails[routeIndex].Cabin }}</span>
						<template v-else >
						<template v-for="classes in [Object.values(result.Flights.reduce((c,f) => (f.Segment.forEach(s => (s.Flight || {}).CabinType && (c[s.Flight.CabinType.toLowerCase()] = s.Flight.CabinType)), c), []))]">
							<template v-if="classes.length">
								<span><b>Clase zboruri:</b> {{ classes.join(', ') }}</span>
							</template>
						</template>
						</template>
						<span v-if="result.BrandedFare?.BrandDetails?.[routeIndex]?.Code"><b>Fare Family:</b> {{ result.BrandedFare.BrandDetails[routeIndex].Code }}</span>
						<span v-if="result.BrandedFare?.BrandDetails?.[routeIndex]?.Description">{{ result.BrandedFare.BrandDetails[routeIndex].Description }}</span>
					</small>
				  <div v-for="segment in route.Segment" class="route-segment">
					<div v-if="segment.Flight.StopTime" class="v-theme--dark bg-highlight rounded-theme text-center">
					  <small><strong>{{ durationToFormatted(segment.Flight.StopTime) }}</strong> escala in {{ segment.Origin.Airport._ }} {{ segment.Origin.Airport.City }} ({{ segment.Origin.Airport.Code }})</small>
					</div>
					<div class="d-flex justify-start">
						<div class="pe-2"><img v-if="company_images[segment.Carrier.Marketing.Code]" :src="company_images[segment.Carrier.Marketing.Code]" :title="segment.Carrier.Marketing._" style="max-width: 60px;max-height: 100%;object-fit: contain;height: 60px;background: #fff;padding: 2px;" /></div>
						<div class="d-flex justify-space-between flex-fill">
						  <div class="d-flex flex-column">
							<strong class="">{{ segment.Origin.Time.replace(/^([0-9]+:[0-9]+).*/,'$1') }}</strong>
							<small class="">{{ formatDateDM(segment.Origin.Date) }}</small>
							<small class="">{{ segment.Origin.Airport._ || segment.Origin.Airport.Code || segment.Origin.Airport.CityCode }}, {{ segment.Origin.Airport.City }}</small>
						  </div>
						  <div class="d-flex flex-column">
							<span class="my-auto duration-line" v-if="segment.Flight.Duration"><strong>{{ durationToFormatted(segment.Flight.Duration) }}</strong></span>
						  </div>
						  <div class="d-flex flex-column">
							<strong class="">{{ segment.Destination.Time.replace(/^([0-9]+:[0-9]+).*/,'$1') }}</strong>
							<small class="">{{ formatDateDM(segment.Destination.Date) }}</small>
							<small class="">{{ segment.Destination.Airport._ || segment.Destination.Airport.Code || segment.Destination.Airport.CityCode }}, {{ segment.Destination.Airport.City }}</small>
						  </div>
						</div>
					</div>
				  </div>
				  </div>
				</template>
			  </template>
			  </v-list>
			  </v-card-text>
			  <v-card-actions>
				<v-spacer></v-spacer>
				<v-btn class="d-flex text-none font-weight-normal cancel-button" size="large" variant="outlined" @click="dialog = false"><v-icon icon="mdi-arrow-left"></v-icon> Inchide</v-btn>
			  </v-card-actions>
			  </v-card>
			</template>
			</v-dialog>
			<div v-for="result in all_results">
				<v-card class="mt-3 flex-wrap" :class="{[(select_first && selected_offer && selected_offer.ItineraryCode == result.ItineraryCode && selected_offer.Combination == result.Combination && 'border-lg border-primary' || '')]:1}">
					<v-card-text class="pa-0 pa-sm-4 flex-fill">
					  <template v-for="(route, routeIndex) in result.Flights||[]">
						<hr v-if="routeIndex" class="my-4" style="border-color: transparent;
							margin: 0 -15px;
							border-bottom-width: 0;
							height: 1px !important;
							box-shadow: 0px 0px 2px rgb(var(--v-theme-surface)) inset;
							border-left-width: 0;
							border-right-width: 0;"/>
						<div class="d-flex justify-space-between">
						  <div class="d-flex flex-row align-center justify-start" style="gap:15px;">
							<img v-if="company_images[route.Segment[0].Carrier.Marketing.Code]" :src="company_images[route.Segment[0].Carrier.Marketing.Code]" style="max-width: 50px;max-height: 100%;object-fit: contain;height: 30px;background: #fff;padding: 2px;" />
							<span class="color-dark-light" v-text="route.Segment[0].Carrier.Marketing._"></span>
						  </div>
							<v-btn v-if="!routeIndex" class="d-flex text-none buton-detalii ms-auto me-3" size="small" variant="outlined" @click="(show_details = result, dialog = true)">Detalii</v-btn>
						  <v-icon class="color-dark-light" :icon="!routeIndex ? 'mdi-airplane-takeoff' : 'mdi-airplane-takeoff mdi-flip-h'"></v-icon>
						</div>
						<div class="d-flex justify-space-between">
						  <div class="d-flex flex-column pe-2">
							<span class="text-h5" style="font-weight: 300;">{{ route.Segment[0].Origin.Time.replace(/^([0-9]+:[0-9]+).*/,'$1') }}</span>
							<small class="color-dark-light">{{ route.Segment[0].Origin.Airport._ || route.Segment[0].Origin.Airport.CityCode }} - {{ formatDateDM(route.Segment[0].Origin.Date) }}</small>
						  </div>
						  <div class="d-flex flex-column flex-grow-1 text-center pe-2" @click="0 && (route.arata_escale = !route.arata_escale)">
							<span class="color-dark-light">{{ durationToFormatted(route.Duration) }}</span>
							<v-timeline class="escale-timeline" line-thickness="1" direction="horizontal" style="--v-border-color:133,147,162;--v-border-opacity:1;--v-theme-on-surface-variant: 133,147,162;max-width: 140px;margin: 0 auto;">
							  <v-timeline-item size="10px" class="nodot" v-if="route.Segment.length == 1">
							  </v-timeline-item>
							  <v-timeline-item v-for="bulina in route.Segment.length-1" size="6px">
							  </v-timeline-item>
							</v-timeline>
							<span :class="route.Segment.length == 1 ? 'color-dark-light' : 'text-primary'">{{ route.Segment.length == 1 ? 'direct' : ((route.Segment.length - 1) + ' ' + (route.Segment.length == 2 ? 'escala' : 'escale')) }}</span>
						  </div>
						  <div class="d-flex flex-column ps-2">
							<span class="text-h5" style="font-weight: 300;">{{ route.Segment[route.Segment.length-1].Destination.Time.replace(/^([0-9]+:[0-9]+).*/,'$1') }}</span>
							<small class="color-dark-light">{{ route.Segment[route.Segment.length-1].Destination.Airport._ || route.Segment[route.Segment.length-1].Destination.Airport.CityCode }} - {{ formatDateDM(route.Segment[route.Segment.length-1].Destination.Date) }}</small>
						  </div>
						</div>
						<div v-if="route.arata_escale" class="bg-primary pa-3">
						<template v-for="segment in route.Segment">
							<div v-if="segment.Flight.StopTime" class="v-theme--dark bg-highlight rounded-theme my-4 py-2 text-center">
							  <small><strong>{{ durationToFormatted(segment.Flight.StopTime) }}</strong> escala in {{ segment.Origin.Airport._ }} {{ segment.Origin.Airport.City }} ({{ segment.Origin.Airport.Code }})</small>
							</div>
							<div class="d-flex justify-space-between">
							  <div class="d-flex flex-row align-center justify-start" style="gap:15px;">
								<img v-if="company_images[segment.Carrier.Marketing.Code]" :src="company_images[segment.Carrier.Marketing.Code]" style="max-width: 50px;max-height: 100%;object-fit: contain;height: 30px;background: #fff;padding: 2px;" />
								<strong v-text="segment.Carrier.Marketing._"></strong>
							  </div>
							</div>
							<div class="d-flex justify-space-between">
							  <div class="d-flex flex-column">
								<strong class="">{{ segment.Origin.Time.replace(/^([0-9]+:[0-9]+).*/,'$1') }}</strong>
								<small class="">{{ formatDateDM(segment.Origin.Date) }}</small>
								<small class="">{{ segment.Origin.Airport._ }}, {{ segment.Origin.Airport.City }}</small>
							  </div>
							  <div class="d-flex flex-column">
								<span class="my-auto duration-line" v-if="segment.Flight.Duration"><strong>{{ durationToFormatted(segment.Flight.Duration) }}</strong></span>
							  </div>
							  <div class="d-flex flex-column">
								<strong class="">{{ segment.Destination.Time.replace(/^([0-9]+:[0-9]+).*/,'$1') }}</strong>
								<small class="">{{ formatDateDM(segment.Destination.Date) }}</small>
								<small class="">{{ segment.Destination.Airport._ }}, {{ segment.Destination.Airport.City }}</small>
							  </div>
							</div>
						  </template>
						</div>
					  </template>
					</v-card-text>
					<v-card-actions class="bg-background2 pa-4 align-center justify-space-between flex-fill flex-sm-0-0">
					  <div class="d-flex flex-column text-end">
						<span class="text-h5 text-primary">{{ format_price(parseFloat(result.PriceDetail.Amount) + extra_price, result.PriceDetail.Currency) }}</span>
						<template v-if="select_first && selected_offer">
							<span v-if="0" class="text-h6 text-primary">{{ format_price(result.PriceDetail.Amount - selected_offer.PriceDetail.Amount, result.PriceDetail.Currency, 1, 1) }}</span>
							<span>Cazare inclusa</span>
						</template>
						<small class="color-dark-light" style="line-height:1.2"><v-icon title="Upgradeable" v-if="result.UpsellSupport" icon="mdi-arrow-up-bold-hexagon-outline"></v-icon> Tarif <b>final</b>, <b>toate taxele incluse</b></small>
						
						<small v-if="0" class="d-flex flex-column mt-3">
							<span v-if="result.Flights.length > 1"><b>Durata totala a calatoriilor:</b> {{ minutesToFormattedDuration(result.Duration) }}</span>
							<template v-if="result.StopTime">
								<span><b>Total durata zbor:</b> {{ minutesToFormattedDuration(result.Duration - result.StopTime) }}</span>
								<span><b>Total durata escale:</b> {{ minutesToFormattedDuration(result.StopTime) }}</span>
							</template>
						</small>
					  </div>
					  <v-btn
						variant="outlined"
						class="text-none px-4 buton-selecteaza"
						size="large"
						:disabled = "select_first && selected_offer && selected_offer.ItineraryCode == result.ItineraryCode && selected_offer.Combination == result.Combination"
						@click.stop="setOffer(result)"
						v-text="select_first ? 'Alege' : 'Selecteaza'"
					  >
					  </v-btn>
					</v-card-actions>
				</v-card>
				
			</div>
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
		company_images() {
			var ci = (((this.results || {}).filters || {}).companies || []).reduce((c, i) => (c[i.code] = i.img, c), {});
			// console.warn('company_images', ci);
			return ci;
		},
		all_results() {
			var results = [...(this.combinations || [])];
			
			results = results.sort(this['sort_' + this.sort]);
			
			// console.warn('combinations results', results);
			
			var ar = results.slice(this.limit * (this.page - 1), this.limit * this.page).map(v => this.mapCombination(v));
			this.allresults = ar;
			console.warn('all_results', this.allresults);
			return this.allresults;
		},
		pages() {
			return Math.ceil(parseInt(this.combinations.length || 0) / this.limit);
		},
	},
	methods: {
		setOffer(offer){
			this.selected_offer = null;
			setTimeout(() => {
				this.selected_offer = offer;
			}, 0)
		},
		mapCombination(combination) {
			var flights = ((this.results || {})._embedded||{}).flights || [];
			var flight = flights[combination.FlightIndex];
			return {...flight, Combination: combination.CombinationCode, Duration: combination.Duration, StopTime: combination.StopTime, DepartureDate: combination.DepartureDate, DestinationDate: combination.DestinationDate, Flights: combination.Routes.map((route, routeIndex) => ({...flight.Routes[routeIndex].Route[route]})) }
		},
		getCombinations(flights){
			return flights.reduce((Combinations, FlightsSet, FlightIndex) => {
				FlightsSet.Combinations.every((c, i) => {
				  if(!c) return true;
				  var ca = c.split('|');
				  var ra = {FlightIndex: FlightIndex, Routes: [], CombinationIndex: i, CombinationCode: c, Duration: 0, StopTime: 0, Price: FlightsSet.PriceDetail.Amount, Currency: FlightsSet.PriceDetail.Currency, System: FlightsSet.System, NonRefundable: FlightsSet.NonRefundable, BrandedFare: FlightsSet.BrandedFare};
				  if(!ca.every((v,i) => {
					  var g = v.match(/^(\d)/)[1];
					  var ri = v.match(/^\d(\d+)/)[1];
					  var o = FlightsSet.Routes[parseInt(g)].Route.findIndex((j) => j.Ref == ri);
					  
					  if(-1 == o){
						return false;
					  }
					  
					  var r = FlightsSet.Routes[parseInt(g)].Route[o];
					  ra.Duration += this.durationToMin(r.Duration);
					  ra.DepartureDate = ra.DepartureDate || r.Segment[0].Origin.Date + ' ' + r.Segment[0].Origin.Time;
					  ra.DestinationDate = r.Segment[0].Destination.Date + ' ' + r.Segment[0].Destination.Time;
					  ra.StopTime += r.Segment.reduce((c,s) => c + (s.Flight.StopTime && this.durationToMin(s.Flight.StopTime) || 0), 0);
				  
					  ra.Routes.push(o);
					  return true;
				  })){
					  console.error('O NOT FOUND!!');
					  return true;
				  }
				  if(!ra.Routes.length) {
					   console.error('O2 NOT FOUND!!');
					  return true;
				  }
				  Combinations.push(ra);
				  return true;
				})
				return Combinations;
			},[]);
		},
		sort_price_asc(a, b){
			return a.Price - b.Price;
		},
		sort_price_desc(a, b){
			return -this.sort_price_asc(a,b);
		},
		sort_duration_asc(a, b){
			return a.Duration - b.Duration;
		},
		sort_departure_date_asc(a, b){
			return a.DepartureDate > b.DepartureDate ? 1 : (a.DepartureDate < b.DepartureDate ? -1 : 0);
		},
		sort_destination_date_asc(a, b){
			return a.DestinationDate > b.DestinationDate ? 1 : (a.DestinationDate < b.DestinationDate ? -1 : 0);
		},
		sort_duration_desc(a, b){
			return -this.sort_duration_asc(a,b);
		},
		sort_special(a, b){
			return (-(a.SpecialDeal - b.SpecialDeal)) || (a.Price - b.Price) || (a.Stars - b.Stars);
		},
		base_sort_price_asc(a, b){
			return a.Price - b.Price;
		},
		base_sort_price_desc(a, b){
			return -this.base_sort_price_asc(a,b);
		},
		base_sort_special(a, b){
			return (-(a.SpecialDeal - b.SpecialDeal)) || (a.Price - b.Price);
		},
	},
	watch: {
		'show_details': {
			handler: function(nv,ov){
				console.warn('show_details', nv);
			},
		},
		'sorted.page': {
			handler: function(nv,ov){
				this.page = nv || 1;
			},
		},
		'sort': {
			handler: function(nv,ov){
				this.page = 1;
				
				this.$emit('sorted_results', this.all_results);
				
				/* if(this.page > 1){
				} else {
					this.$emit('sorted', {sort: nv, page: 1});
				} */
			},
			immediate: true
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
				this.$emit('offer',nv, this.results);
			},
			immediate: true
		},
		'results': {
			handler: function(nv,ov){
				this.page = 1;
				var step = parseInt(window_url.searchParams.get("step"));
				// console.warn('results', nv);
				this.combinations = [];
				if(nv && (nv._embedded||{}).flights && (nv._embedded||{}).flights){
					this.combinations = this.getCombinations(((nv._embedded||{}).flights || []));
				}
				if(this.results.research_hash){
					var selected_offer;
					var rh = this.results.research_hash.flight;
					console.warn('this.results.research_hash', rh);
					var flights = ((nv._embedded||{}).flights || []);
					var selected_offer = this.combinations.find((combination, ci) => {
						return (true || rh?.Price === combination?.Price) &&
							(rh?.Currency === combination?.Currency) &&
							(rh?.DepartureDate === combination?.DepartureDate) &&
							(rh?.DestinationDate === combination?.DestinationDate) &&
							(rh?.System === combination?.System) &&
							(rh?.NonRefundable === combination?.NonRefundable) &&
							(rh?.BrandedFare === JSON.stringify(combination?.BrandedFare)) &&
							(!(rh?.Flights || []).find((flightHash, routeIndex) => JSON.stringify((flights?.[combination.FlightIndex]?.Routes?.[routeIndex]?.Route[combination?.Routes?.[routeIndex]]?.Segment || []).map(v => JSON.parse(JSON.stringify(v))).map(a => (a.Flight.NumberOfSeats=null, a))) != flightHash))
					});
					if(selected_offer){
						this.selected_offer = this.mapCombination(selected_offer);
					} else {
						console.error('Offer not found');
						if(this.data.step > 1)
							this.data.step = 1;
					}
				}
				if(this.combinations.length){
					if(!this.selected_offer){
						// console.warn('this.combinations', this.combinations);
						if(this.select_first || (!isNaN(step) && step > 1 && step < 4)){
							console.error('forced result click');
							this.selected_offer = this.all_results[0];
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
