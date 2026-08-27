import BaseFunctionality from '../trip-avion/search.js?newux=<?php echo !empty($_GET['newux']) ? (string)$_GET['newux'] : 1 ; ?>';
export default {
	name: <?php echo json_encode($a, JSON_UNESCAPED_SLASHES); ?>,
	extends: BaseFunctionality,
	data: () => {
		return {
			activate_next_step: 0,
			prevent_initiate: true,
			wind: 0,
			hotel: undefined,
			hotels: undefined,
			flight: undefined,
			flights: undefined,
			// filters_url: `${newux_url}/partials/presearch-wrapper/functionalities/<?php echo basename(dirname($a)); ?>/filters-search.json?${append_url}`,
			// summary_url: `${newux_url}/partials/presearch-wrapper/functionalities/<?php echo basename(dirname($a)); ?>/summary-search.json?${append_url}`,
			initiate_url: '',
			inspect_url: '',
			fetch_url: '',
			filter_component: '',
			results_component: '',
			offer_component: 'partials/presearch-wrapper/functionalities/trip-hoteluri/offer',
			checkout_component: 'partials/presearch-wrapper/functionalities/trip-citybreak/checkout',
		}
	},
	watch: {
		'wind': {
			handler: function(nv,ov){
				window.scrollTo({
				  top: 0,
				  left: 0,
				  behavior: 'smooth',
				});
			},
			immediate: true
		},
	},
	template : `
	<v-container class="pa-0">
	<div class="results-filter-above px-4">
		<v-breadcrumbs :items="breadcrumbs">
			<template v-slot:divider>
				<v-icon icon="mdi-menu-right"></v-icon>
			</template>
			<template v-slot:item="{ item }">
				<v-breadcrumbs-item href="javascript:void(0)" :active="item.active" active-color="green" :disabled="item.step == 1" @click.stop="$emit('set-value', {'step': item.step})" v-text="item.title"></v-breadcrumbs-item>
			</template>
		</v-breadcrumbs>
		
		<div id="search-wrapper-step-1"></div>
	</div>
	<v-btn-toggle v-if="0"
        v-model="wind"
        class="flight-hotel-toggle flex-wrap mb-4 ga-3"
        theme="light"
      >
        <v-btn :value="0">
          <small>Hoteluri</small>
        </v-btn>
        <v-btn :value="1">
          <small>Zboruri</small>
        </v-btn>
	</v-btn-toggle>
	<v-window v-model="wind" id="flights-hotels-window">
		<v-window-item>
			<template v-if="flight" v-for="result in [flight]">
				<v-card class="mb-8 border-lg border-primary detalii-zbor-ales detalii-ales">
					<v-card-text class="pa-4 d-flex justify-space-between ga-4 flex-wrap">
					  <template v-for="(route, routeIndex) in result.Flights||[]">
					<div class="ruta-zbor flex-fill flex-column">
						<div class="d-flex flex-fill my-2 ga-2">
							<div v-if="route.Segment[0].Origin && route.Segment[route.Segment.length-1].Destination" class="text-wrap flight-oras-plecare-destinatie align-center d-flex flex-fill ga-2">
							<v-icon :icon="!routeIndex ? 'mdi-airplane-takeoff' : 'mdi-airplane-takeoff mdi-flip-h'" class="mr-4"></v-icon>
						  
							<strong class="flight-oras oras-plecare" v-html="route.Segment[0].Origin.Airport.City"></strong>
							―
							<strong class="text-capitalize flight-stop" v-html="durationToFormatted(route.Duration)"></strong>
							→
							<strong class="flight-oras oras-destinatie" v-html="route.Segment[route.Segment.length-1].Destination.Airport.City"></strong>
						  </div>
							<strong class="text-capitalize ms-auto" v-html="route.Segment.length == 1 ? 'Direct' : (route.Segment.length == 2 ? '1 Escala' : (route.Segment.length -1) + ' Escale')"></strong>
						</div>
					<div class="flex-fill">
						<div class="d-flex justify-space-between">
						  <div class="d-flex flex-row align-center justify-start" style="gap:15px;">
							<img v-if="company_images[route.Segment[0].Carrier.Marketing.Code]" :src="company_images[route.Segment[0].Carrier.Marketing.Code]" style="max-width: 50px;max-height: 100%;object-fit: contain;height: 30px;background: #fff;padding: 2px;" />
							<span class="color-dark-light" v-text="route.Segment[0].Carrier.Marketing._"></span>
						  </div>
						</div>
						<div class="d-flex justify-space-between align-center">
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
						  <div class="d-flex flex-column ps-2 text-end">
							<span class="text-h5" style="font-weight: 300;">{{ route.Segment[route.Segment.length-1].Destination.Time.replace(/^([0-9]+:[0-9]+).*/,'$1') }}</span>
							<small class="color-dark-light">{{ route.Segment[route.Segment.length-1].Destination.Airport._ || route.Segment[route.Segment.length-1].Destination.Airport.CityCode }} - {{ formatDateDM(route.Segment[route.Segment.length-1].Destination.Date) }}</small>
						  </div>
						</div>
					</div>
					</div>
					<v-dialog class="detalii-zbor-modal" v-if="!routeIndex">
						  <template v-slot:activator="{ props }">
						  <v-btn v-bind="props" class="d-flex text-none buton-detalii ms-auto me-3" size="small" variant="outlined">Detalii</v-btn>
						</template>
						<template v-slot:default="{ isActive }">
							<v-card class="align-self-center w-100" style="max-width: min(95vw, 630px);">
							<v-card-text>
						  <v-list lines="two" subheader theme="light" class="max-height pa-4" rounded="theme">
							<v-list-item-title class="pb-4 px-0 text-h5" v-text="'Detalii zbor'"></v-list-item-title>
						  <template v-if="1">
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
							<v-btn class="d-flex text-none font-weight-normal cancel-button" size="large" variant="outlined" @click="isActive.value = false"><v-icon icon="mdi-arrow-left"></v-icon> Inchide</v-btn>
						  </v-card-actions>
						  </v-card>
						</template>
						</v-dialog>
					  </template>
					</v-card-text>
				</v-card>
			</template>
			<component ref="hotel_search" :is="loadViewAsync('partials/presearch-wrapper/functionalities/<?php echo basename(dirname($a)); ?>/search-hotels')" select_first :data="data" :key_path="key_path" :show_breadcrumbs="false" v-on:offer="(h,l) => (hotel = h, hotels = l)" :extra_price="parseFloat(((flight || {}).PriceDetail || {}).Amount || 0)"></component>
		</v-window-item>
		<v-window-item eager>
			<template v-if="hotel" v-for="result in [hotel]">
				<v-card class="mb-8 pa-2 border-lg border-primary detalii-hotel-ales detalii-ales">
					<div class="d-flex flex-no-wrap justify-space-between w-100">
					<div class="">
					  <v-avatar
						class=""
						rounded="lg"
						density="compact"
						style="height:150px;width:200px"
					  >
						<v-img :src="(result.force_image_because_error || result.Image || '/themes/newux/assets/images/placeholder.webp')" v-on:error="result.force_image_because_error = '/themes/newux/assets/images/placeholder.webp'"
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
					  <div class="pa-5 flex-fill">
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
						</div>
						<v-card-text class="pa-0">
							<div class="d-flex ga-3 justify-space-between mt-2" v-if="result.Facilities">
								<div class="hotel-facilities" v-text="result.Facilities"></div>
							</div>
							<div class="hotel-facilities-desc" v-if="result.FacilitiesDesc" v-text="result.FacilitiesDesc"></div>
						</v-card-text>
					  </div>

					</div>
				</v-card>
			</template>
			<component ref="flight_search" :is="loadViewAsync('partials/presearch-wrapper/functionalities/<?php echo basename(dirname($a)); ?>/search-flights')" select_first :data="data" :key_path="key_path" :show_breadcrumbs="false" v-on:offer="(h,l) => (flight = h, flights = l)" :extra_price="parseFloat((hotel || {}).MinPrice || 0)"></component>
		</v-window-item>
	</v-window>
	</v-container>
	<teleport to="#pos-stick-b-t" v-if="(1 == data.step)">
		<div v-if="flight && hotel" class="d-flex w-100 justify-space-between py-1 align-center ga-2">
			<v-btn class="buton-sumar" variant="outlined" size="large" @click="wind = wind ? 0 : 1"><span v-if="!wind">Alege alt zbor</span>
					<span v-if="wind">Alege alt hotel</span>
			</v-btn>
			<div class="d-none d-md-flex flex-column ga-1">
				<div class="d-flex w-100 justify-space-between ga-2"><span>Cazare</span><strong>{{ format_price((getObjectDotPathValue(hotel,'MinPrice',0)), getObjectDotPathValue(hotel,'Currency'), 1) }}</strong></div>
			</div>
			<div class="d-none d-md-flex flex-column ga-1">
				<div class="d-flex w-100 justify-space-between ga-2"><span>Bilete avion</span><strong>{{ format_price((getObjectDotPathValue(flight,'PriceDetail.Amount',0)), getObjectDotPathValue(flight,'Currency'), 1) }}</strong></div>
			</div>
			<v-btn
				class="button-alege justify-self-end"
				@click="(activate_next_step++, setOffer(hotel))"
				size="large"
				variant="outlined"
			>
				<span>
					<span>Configurare City Break</span> <span v-text="'(' + format_price(parseFloat(flight.PriceDetail.Amount) + parseFloat(hotel.MinPrice), flight.Currency) + ')'"></span>
				</span>
			</v-btn>
		</div>
		<div v-else-if="searching">
			<component :is="loadViewAsync('partials/presearch-wrapper/functionalities/common/loading')"></component>
		</div>
	</teleport>
	<teleport to="#search-wrapper-item-content" v-if="(2 <= data.step) && hotel && activate_next_step">
		<component :is="loadViewAsync(offer_component)" :offer="hotel" :inspection="hotels" :flight_offer="flight" :flight_inspection="flights" :searching="searching" :prepend_breadcrumbs="breadcrumbs" v-on:hash="researchHash" v-on:research="research"
		 v-on:offer="(r) => r && setOffer(r)"
		:results="[]" :applied_filters="undefined" v-on:set-value="(r) => ($emit('set-value', r))" :search_data="full_search_data" :set_checkout_component="checkout_component" :data="data" :search_wrapper_step="data.step" ></component>
	</teleport>
	`,
	mounted() {
		this.$emit('set-value', {'step': 1});
	},
	methods: {
		researchHash(obj){
			this.research_hash = obj;
		},
		research(hash, type){
			console.warn('Should research', type, hash, this.$refs[type + '_search']);
			this.$refs[type + '_search'].research(hash);
		},
		setOffer(offer){
			this.$emit('set-value', {step:1});
			console.warn('SETTING STEP', 2);
			setTimeout(() => (this.hotel = offer, this.$emit('set-value', {step:2})), 0)
		},
	},
	computed: {
		breadcrumbs() {
			return [
				{title: 'Acasa', step: 0},
				{title: 'City Break', step: 0},
				{title: (this.data['<?php echo basename(dirname($a)); ?>/departure-city'] || {}).Name, step: 1},
				{title: (this.data['<?php echo basename(dirname($a)); ?>/destination-city'] || {}).Name, step: 1},
			];
		},
		fetch_data() {
			var dep = (this.data['<?php echo basename(dirname($a)); ?>/departure-city'] || {});
			var dest = (this.data['<?php echo basename(dirname($a)); ?>/destination-city'] || {});
			var obj = {
				depLocationId: dep.CityId && dep.Id || 0,
				depCityId: dep.CityId || dep.Id,
				destLocationId: dest.CityId && dest.Id || 0,
				destCityId: dest.CityId || dest.Id,
				class: (this.data['<?php echo basename(dirname($a)); ?>/cabin'] || {}).Id || 0,
				type: (this.data['<?php echo basename(dirname($a)); ?>/type'] || {}).Id || 0,
				dIn: (this.data['<?php echo basename(dirname($a)); ?>/check-in'] || {}).Id || '',
				dOut: (this.data['<?php echo basename(dirname($a)); ?>/check-out'] || {}).Id || '',
				r: this.data['<?php echo basename(dirname($a)); ?>/travellers'] || [],
			}
			return obj;
		},
		country() {
			return (this.data['<?php echo basename(dirname($a)); ?>/destination-city'] || {}).type == 'country' && (this.data['<?php echo basename(dirname($a)); ?>/destination-city'] || {});
		},
		company_images() {
			var ci = (((this.flights || {}).filters || {}).companies || []).reduce((c, i) => (c[i.code] = i.img, c), {});
			// console.warn('company_images', ci);
			return ci;
		},
		full_search_data() {
			var passengers = (this.data['<?php echo basename(dirname($a)); ?>/travellers'] || {});
			var travellers = (passengers || []).reduce((c, i) => {
				c['ROM'] = c['ROM'] ?? 0;
				c['ROM']++;
				Object.keys(i).forEach(a => {
					if(!i[a]) return;
					if(Array.isArray(i[a])){
						if(!i[a].length) return;
						if(!c[a]) {
							c[a] = i[a];
						} else {
							if(!Array.isArray(c[a])){
								console.error(passengers);
								throw "Invalid travellers";
							}
							c[a] = c[a].concat(i[a]);
						}
					} else {
						if(isNaN(i[a])) return;
						if(!c[a]) {
							c[a] = parseInt(i[a]);
						} else {
							if(Array.isArray(c[a])){
								console.error(passengers);
								throw "Invalid travellers";
							}
							c[a] = parseInt(c[a]) + parseInt(i[a]);
						}
					}
					// console.warn(a, i[a], i, c);
				});
				return c;
			}, {});
			var obj = {
				... this.fetch_data,
				full: {
					ADT: (!isNaN(parseInt(travellers.ADT)) && (parseInt(travellers.ADT) > 0) && parseInt(travellers.ADT) || 0),
					SEN: (!isNaN(parseInt(travellers.SEN)) && (parseInt(travellers.SEN) > 0) && parseInt(travellers.SEN) || 0),
					YTH: (!isNaN(parseInt(travellers.YTH)) && (parseInt(travellers.YTH) > 0) && parseInt(travellers.YTH) || 0),
					CHD: (travellers.CHD && Array.isArray(travellers.CHD) && travellers.CHD || []).length,
					Children: (travellers.CHD && Array.isArray(travellers.CHD) && travellers.CHD || []),
					Departure: this.data['<?php echo basename(dirname($a)); ?>/departure-city'] && {
						Id: this.data['<?php echo basename(dirname($a)); ?>/departure-city'].Id,
						Name: this.data['<?php echo basename(dirname($a)); ?>/departure-city'].Name,
						City: this.data['<?php echo basename(dirname($a)); ?>/departure-city'].City,
						Country: this.data['<?php echo basename(dirname($a)); ?>/departure-city'].Country,
						County: this.data['<?php echo basename(dirname($a)); ?>/departure-city'].County
					} || undefined,
					Destination: this.data['<?php echo basename(dirname($a)); ?>/destination-city'] && {
						Id: this.data['<?php echo basename(dirname($a)); ?>/destination-city'].Id,
						Name: this.data['<?php echo basename(dirname($a)); ?>/destination-city'].Name,
						City: this.data['<?php echo basename(dirname($a)); ?>/destination-city'].City,
						Country: this.data['<?php echo basename(dirname($a)); ?>/destination-city'].Country,
						County: this.data['<?php echo basename(dirname($a)); ?>/destination-city'].County
					} || undefined,
					CheckIn: this.data['<?php echo basename(dirname($a)); ?>/check-in'] && {
						...this.data['<?php echo basename(dirname($a)); ?>/check-in']
					} || undefined,
					CheckOut: this.data['<?php echo basename(dirname($a)); ?>/check-out'] && {
						...this.data['<?php echo basename(dirname($a)); ?>/check-out']
					} || undefined,
					Nights: Math.round((((new Date((this.data['<?php echo basename(dirname($a)); ?>/check-out'] || {}).Id))) - (new Date((this.data['<?php echo basename(dirname($a)); ?>/check-in'] || {}).Id))) / 86400000),
				}
			}
			return obj;
		},
	},
}
