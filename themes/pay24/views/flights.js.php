<?php $this->load->library('encryption');  ?>
let search_axios, search_axios_timer, search_axios_cancel;
const CancelToken = axios.CancelToken; 
export default {
	components : {
		'FlightsDisclaimerRyanair' : loadViewAsync('flights_disclaimer_ryanair'),
		'FlightsTerms' : loadViewAsync('flights_terms'),
		'FlightsSearch' : loadViewAsync('flights_search'),
		'FlightsSearchResultsList' : loadViewAsync('flights_search_results_list'),
		'FlightsSearchListPreferences' : loadViewAsync('flights_search_list_preferences'),
		'FlightPassengerDetails' : loadViewAsync('flight_passenger_details'),
		'FlightDetails' : loadViewAsync('flight_details'),
		'FlightBilling' : loadViewAsync('flight_billing'),
		'FlightFAQ' : loadViewAsync('flights_faq'),
		'Coupons' : loadViewAsync('coupons'),
		'CheckLoaded' : {
			emits: ['mounted', 'unmounted'],
			props: {
				slot: {
					type: String,
				},
			},
			template : `<slot />`,
			mounted: function() {
				this.$emit('mounted', this.slot)
			},
			unmounted: function() {
				this.$emit('unmounted', this.slot)
			},
		},
	},
	data: () => ({
		simulate: false,
		validating: false,
		checkout_errors: [],
		coupon_discount: 0,
		search_data: undefined,
		flight_data: undefined,
		ancillery: undefined,
		result: undefined,
		billing_info: undefined,
      	step: 1,
      	show_search_data: true,
		search_loading: true,
		search_data_mounted: false,
		selected_flight: undefined,
		filters: undefined,
		summary: undefined,
		flights: undefined,
		show_faq: false,
		applied_filters: {},
		company_images: {},
		loaded: {},
		flights_search_list_preferences: undefined,
		classes: {
			'1': 'Economy',
			'2': 'First class',
			'3': 'Business',
			'4': 'Premium',
		},
    }),
	template : `
<v-card class="w-100 fill-height d-flex flex-column">
	<v-card-title class="d-flex text-h6 font-weight-regular justify-space-between align-center ps-2" id="backbutton">
		<template v-if="step == 1">
			<?php /* <router-link style="text-decoration: none; color: inherit;" :to="{ name: 'home'}" class="d-flex align-center">
				<v-icon :icon="'mdi-chevron-left'"></v-icon> <span>{{ currentTitle }}</span>
			</router-link> */ ?>
			<a class="d-flex align-center mr-auto backlink" style="text-decoration: none; color: inherit;" href="/" @click.prevent="communicateWithPay24('close')">
				<v-icon :icon="'mdi-chevron-left'"></v-icon> <span>{{ currentTitle }}</span>
			</a>
		</template>
		<template v-else-if="step == 9">
			<a class="d-flex align-center mr-auto backlink" style="text-decoration: none; color: inherit;" href="/" @click.prevent="step = 1">
				<v-icon :icon="'mdi-chevron-left'"></v-icon> <span>{{ currentTitle }}</span>
			</a>
		</template>

		<template v-else>
			<a class="d-flex align-center mr-auto backlink" style="text-decoration: none; color: inherit;" href="/" @click.prevent="step == 8 ? step = 1 : step--">
				<v-icon :icon="'mdi-chevron-left'"></v-icon> <span>{{ currentTitle }}</span>
			</a>
		</template>
		<a style="margin-right:50px;opacity:0" href="https://accenttravel.ro/pay24/test/passenger_details/3">-</a>
		<v-icon v-if="search_data && step == 2 && !search_loading" @click="show_search_data = !show_search_data" icon="mdi-filter"></v-icon>
		<FlightFAQ />
		<v-avatar v-if="false" color="primary" size="24" v-text="step"></v-avatar>
	</v-card-title>
	<v-window ref="flights_window" v-model="step" class="w-100 fill-height" :touch="{left: allowTouchLeft, right: allowTouchRight}">
		<v-window-item :value="1" class="fill-height">
			<v-card-text class="fill-height overflow-y-auto">
				<FlightsSearch ref="flight_search" />
			</v-card-text>
		</v-window-item>

		<v-window-item :value="2" class="fill-height">
			<div class="fill-height overflow-y-auto">
			<div v-if="search_data && !search_loading" class="v-banner--sticky bg-surface" style="z-index:1;">
			<v-expand-transition mode="default">
			<div v-show="show_search_data && !search_loading" class="current-search pb-0 mx-4">
				<div class=" d-table w-100">
				<?php /*<v-list-subheader class="pl-4 mt-2">Cautare curenta</v-list-subheader> */ ?>
				<div class="d-flex w-100 align-center justify-space-between py-4 bg-background rounded-theme mb-4">
				<v-list v-if="search_data" class="w-100 pa-0 bg-background"
					:items="[search_data]"
					@click:select="setSearch()"
					density="compact"
					lines="2"
					:item-props="(item) => {
						return {
						value: 1,
						class: 'mb-0 py-0 bg-background',
						}
					}"
				>
					<?php /*
					<template v-slot:prepend="{ item }">
					<v-icon icon="mdi-pencil"></v-icon>
					</template>
					*/ ?>
					<template v-slot:title="{ item }">
					<div v-if="item.origin && item.destination" class="text-wrap">
						<strong v-html="(item.origin.LocationId ? item.origin.LocationCode + ', ' : '') + item.origin.CityName + ' - ' + (item.destination.LocationId ? item.destination.LocationCode + ', ' : '') + item.destination.CityName"></strong>
						<small v-if="item.direct" class="pl-2" v-html="'(Direct)'"></small>
					</div>
					</template>
					<template v-slot:subtitle="{ item }">
					<div class="text-wrap">
						<strong class="text-capitalize" v-html="dateInterval(item)"></strong>
						<v-icon class="ms-2" v-if="item.flex" icon="mdi-plus-minus-box"></v-icon>
						<span class="ms-2" v-html="passengers(item)"></span>
						<span class="ms-2 text-primary" v-if="item.cabine && classes[item.cabine]" v-html="classes[item.cabine]"></span>
					</div>
					</template>
				</v-list>
					<CheckLoaded v-if="search_data.flex" slot="calendar_wrapper" v-on:mounted="(s) => loaded[s] = true" v-on:unmounted="(s) => loaded[s] = false"><div id="calendar-tarife-wrapper" class="pe-1"></div></CheckLoaded>
				</div>
				<FlightsSearchListPreferences ref="flights_search_list_preferences" v-model="flights_search_list_preferences"  :flights="flights" :filters="filters" :search_data="search_data" :company_images="company_images" v-on:save="applyFilters" :calendar_location_mounded="loaded.calendar_wrapper" />
				</div>
			</div>
			</v-expand-transition>
			</div>
			<v-card-text class="pt-0">
				<FlightsSearchResultsList ref="flights_search_results_list" :company_images="company_images" v-model="search_data" v-on:filters="saveFilters" v-on:summary="saveSummary" v-on:search_loading="saveSearchLoading" v-on:flights="saveFlights" :applied_filters="applied_filters" v-on:selected_flight="(f) => (this.selected_flight = f, !!f && (step=3))" />
			</v-card-text>
			</div>
		</v-window-item>

		<v-window-item :value="3" class="fill-height">
			<div class="fill-height overflow-y-auto">
			<div class="pa-4 text-center">
				<FlightDetails ref="flight_details" v-model="selected_flight" :company_images="company_images" :filters="filters" v-on:selected_ancillery="(a) => this.ancillery = a" v-on:flight_data="(a) => this.flight_data = a"></FlightDetails>
			</div>
			</div>
		</v-window-item>
		<v-window-item :value="4" class="fill-height">
			<div class="fill-height overflow-y-auto">
			<div class="pa-4 text-center">
				<FlightPassengerDetails v-if="flight_data" ref="passenger_details" :flight_data="flight_data" :passport_required="passport_required" v-on:result="(r) => (console.warn('res',r),this.result = r)" v-on:save="step=5"></FlightPassengerDetails>
			</div>
			</div>
		</v-window-item>
		<v-window-item :value="5" class="fill-height">
			<div class="fill-height overflow-y-auto">
			<div class="pa-4 text-center pt-0">
				<FlightBilling ref="flight_billing" v-on:save="(o) => {billing_info=o;step=6}"></FlightBilling>
			</div>
			</div>
		</v-window-item>
		<v-window-item :value="6" class="fill-height">
			
			<div class="fill-height overflow-y-auto" v-if="flight_data && result">
				<v-list theme="light" class="ma-4 mt-0 max-height pa-4 mt-4" rounded="theme">
					<div class="d-flex align-center">
						<img width="120" src="<?php echo $this->theme->theme_url; ?>assets/images/logo2.png" />
						<div class="text-left ps-4">
							<strong>Accent Travel &amp; Events</strong>
							<div class="mt-2">Bilete avion</div>
						</div>
					</div>
					<div class="">
					</div>
					<div class="bg-highlight rounded-theme pa-4 mt-5">
						<div class="d-flex w-100 justify-space-between mb-2"><h3>Sumar</h3><a @click.stop="step++">Vezi detalii <i class="fa fa-chevron-right"></i></a></div>
						<div class="d-flex w-100 justify-space-between"><small>Bilete avion</small><strong>{{ format_price((getObjectDotPathValue(flight_data,'FareDetails.FullFare',0)), getObjectDotPathValue(flight_data,'FareDetails.Currency')) }}</strong></div>
						<div v-if="(result.paidOptions || []).length" class="d-flex w-100 justify-space-between"><small>Servicii Extra</small><strong>{{ format_price((getObjectDotPathValue(result,'optionsPrice',0)), getObjectDotPathValue(flight_data,'Currency')) }}</strong></div>
						<div v-if="result.paidSeats.length" class="d-flex w-100 justify-space-between"><small>Locuri preferentiale</small><strong>{{ format_price((getObjectDotPathValue(result,'seatsPrice',0)), getObjectDotPathValue(flight_data,'Currency')) }}</strong></div>
						<div class="d-flex w-100 justify-space-between"><small>Taxa de serviciu</small><strong>{{ format_price((getObjectDotPathValue(flight_data,'FareDetails.ServiceFee',0)), getObjectDotPathValue(flight_data,'FareDetails.Currency')) }}</strong></div>
						<div v-if="coupon_discount" class="d-flex w-100 justify-space-between"><small>Cupoane</small><strong>-{{ format_price(coupon_discount, getObjectDotPathValue(flight_data,'FareDetails.Currency')) }}</strong></div>
						<div class="d-none" v-html="flight_data.upsellCode"></div>
					</div>
				</v-list>
				
				<div class="pa-4 pt-0"><Coupons ref="coupons" v-model="coupon_discount" :type="'flight'" :total="result.totalPrice" :currency="flight_data.Currency" /></div>

				<v-list theme="dark" class="ma-4 mt-0 max-height pa-4 bg-background" rounded="theme">
					<div class="d-flex w-100 justify-space-between"><span>Vei plati in total</span><strong class="text-primary">{{ format_price((getObjectDotPathValue(result,'totalPrice',0) - coupon_discount), getObjectDotPathValue(flight_data,'Currency')) }}</strong></div>
				</v-list>
				
				
				<v-messages class="v-theme--light pa-4 ma-4 bg-theme bg-background rounded-theme" color="error" :active="!!checkout_errors && !!checkout_errors.length" v-show="!!checkout_errors && !!checkout_errors.length" :messages="checkout_errors"></v-messages>
				
				<FlightsTerms ref="terms" />
				
				<FlightsDisclaimerRyanair v-if="-1 != [...new Set(getObjectDotPathValue(flight_data, 'Routes.*.Segment.*.Carrier.Marketing.Code').flat(1))].indexOf('FR')" :value="1"/>
				
				<v-list theme="dark" class="ma-4 mt-0 max-height pa-4 bg-background" rounded="theme">
					<p><small>Dupa finalizarea comenzii vei primi pe adresa de e-mail detaliile rezervarii zborurilor</small></p>
					<br />
					<p><small>De asemenea vei putea accesa biletele de zbor achizitionate in sectiunea "Portofel" a aplicatiei 24Pay</small></p>
				</v-list>
			</div>
		</v-window-item>
		<v-window-item :value="7" class="fill-height">
			<div class="fill-height overflow-y-auto">
				<div class="bg-background rounded-theme ma-4" style="overflow:hidden;">
					<div class="bg-background2 pa-4 d-flex justify-start align-center">
						Detalii plata
					</div>
					<div class="pa-4">
						<div class="d-flex w-100 justify-space-between mb-2"><span>Pasageri</span><strong>{{ format_price((getObjectDotPathValue(flight_data,'FareDetails.FullFare',0)), getObjectDotPathValue(flight_data,'Currency')) }}</strong></div>
						<div v-for="pf in (getObjectDotPathValue(flight_data,'FareDetails.PaxFare') || [])" class="d-flex w-100 justify-space-between"><small>{{ translate_ptc[pf.PTC][1] }} x {{ pf.Count }}</small><small>{{ format_price((getObjectDotPathValue(pf,'FullFare',0) * pf.Count), getObjectDotPathValue(flight_data,'Currency')) }}</small></div>
						<template v-if="result.paidOptions.length">
							<hr class="my-4" />
							<div  class="d-flex w-100 justify-space-between mb-2"><span>Servicii Extra</span><strong>{{ format_price((getObjectDotPathValue(result,'optionsPrice',0)), getObjectDotPathValue(flight_data,'Currency')) }}</strong></div>
							<div class="d-flex w-100 justify-space-between mb-2" v-for="option in result.paidOptions">
								<div>
									<div>{{ translate_ptc[option.Target][1] }} #{{ (parseInt(option.PassengerIndex) + 1) }} {{ getObjectDotPathValue(option,'Service.Name') }} ({{ getObjectDotPathValue(option,'Service.CategoryName') }}) ({{ getObjectDotPathValue(option,'Route.From') + '-' + getObjectDotPathValue(option,'Route.To') }})</div>
									<div>{{ [...(option.Description || [])].join('; ') }}</div>
								</div>
								<small>{{ format_price(getObjectDotPathValue(option,'Option.Price.Amount',0), getObjectDotPathValue(option,'Option.Price.Currency')) }}</small></div>
						</template>
						<template v-if="result.paidSeats.length">
							<hr class="my-4" />
							<div class="d-flex w-100 justify-space-between mb-2"><span>Locuri preferentiale</span><strong>{{ format_price((getObjectDotPathValue(result,'seatsPrice',0)), getObjectDotPathValue(flight_data,'Currency')) }}</strong></div>
							<div class="d-flex w-100 justify-space-between mb-2" v-for="paidSeat in result.paidSeats">
								<div>{{ translate_ptc[paidSeat.Target][1] }} #{{ (parseInt(paidSeat.PassengerIndex) + 1) }} Loc: {{ getObjectDotPathValue(paidSeat,'seatNumber') }}{{ getObjectDotPathValue(paidSeat,'seatColumn') }} Ruta: {{ getObjectDotPathValue(paidSeat,'Route.From') + '-' + getObjectDotPathValue(paidSeat,'Route.To') }}
								</div>
								<small>{{ format_price(getObjectDotPathValue(paidSeat,'amount',0), getObjectDotPathValue(paidSeat,'currency')) }}</small></div>
						</template>
						<hr class="my-4" />
						<div class="d-flex w-100 justify-space-between mb-2"><span>Taxa de serviciu</span><strong>{{ format_price((getObjectDotPathValue(flight_data,'FareDetails.ServiceFee',0)), getObjectDotPathValue(flight_data,'FareDetails.Currency')) }}</strong></div>
						<div v-for="pf in (getObjectDotPathValue(flight_data,'FareDetails.PaxFare') || [])" class="d-flex w-100 justify-space-between"><small>{{ translate_ptc[pf.PTC][1] }} x {{ pf.Count }}</small><small>{{ format_price((getObjectDotPathValue(pf,'ServiceFee',0) * pf.Count), getObjectDotPathValue(flight_data,'Currency')) }}</small></div>
						<template v-if="coupon_discount">
							<hr class="my-4" />
							<div class="d-flex w-100 justify-space-between mb-2"><span>Cupoane</span><strong>-{{ format_price(coupon_discount, getObjectDotPathValue(flight_data,'Currency')) }}</strong></div>
						</template>
					</div>
				</div>
			</div>
		</v-window-item>
		<v-window-item :value="8" class="fill-height">
			<div class="fill-height overflow-y-auto">
				<div class="pa-4 text-center">
					<FlightsLoader></FlightsLoader>
				</div>
			</div>
		</v-window-item>
		<v-window-item :value="9" class="fill-height">
			<div class="fill-height overflow-y-auto">
				<v-btn class="text-none font-weight-normal cancel-button" size="x-large" color="secondary" rounded="theme" variant="flat" @click="plataZero">Plata Zero</v-btn>
			</div>
		</v-window-item>
	</v-window>
	<template  v-if="step == 1 || step==3 || step==4 || step==5 || step==6 || step==7">
		<v-divider v-if="false"></v-divider>
		<v-card-actions class="mt-auto justify-start d-flex px-4 pb-4 align-end" style="gap:15px;">
			<?php /* <v-btn v-if="step == 1" class="text-none font-weight-normal cancel-button" size="x-large" color="secondary" rounded="theme" variant="flat" style="margin-top:-30px;" @click="step = 9">Test:plata zero</v-btn> */ ?>
			
			<v-btn class="text-none font-weight-normal cancel-button" size="x-large" color="secondary" rounded="theme" variant="flat" v-if="step > 1 && step != 6" style="margin-top:-30px;" @click="step == 8 ? step = 1 : (can_step(-1) && step--)"><v-icon icon="mdi-arrow-left"></v-icon></v-btn>
			<?php /* <v-btn class="text-none font-weight-normal" size="x-large" color="secondary" rounded="theme" style="flex:1;margin-top:-30px;" variant="flat" v-if="step==6" @click="resetCustom">
				Reset
			</v-btn> */ ?>
			<v-btn class="text-none font-weight-normal" size="x-large" color="primary" rounded="theme" style="flex:1;margin-top:-30px;" variant="flat" v-if="step != 2 && step < 7 && (step != 3 || flight_data)" @click="step == 6 ? (sim(0) && can_step(2) && (step+=2)) : (can_step(1) && step++)">
				<template v-if="step == 3">
					<div class="d-flex flex-wrap align-center">
						<div>Continua</div>
						<small>
						({{ format_price((getObjectDotPathValue(flight_data,'Price',0)), getObjectDotPathValue(flight_data,'Currency')) }})
						</small>
					</div>
				</template>
				<template v-else-if="step == 4">
					<div class="d-flex flex-wrap align-center">
						<div>Continua</div>
						<small>
						({{ format_price((getObjectDotPathValue(result,'totalPrice',0)), getObjectDotPathValue(flight_data,'Currency')) }})
						</small>
					</div>
				</template>
				<template v-else>
					{{ step == 1 ? 'Cauta zboruri' : (step == 6 ? "Plateste acum" : 'Continua') }}
				</template>
			</v-btn>
			<?php /* <v-btn class="text-none font-weight-normal" size="x-large" color="error" rounded="theme" style="flex:1;margin-top:-30px;" variant="flat" v-if="step == 6" @click="step == 6 ? (sim(1) && can_step(2) && (step+=2)) : (can_step(1) && step++)">
				SIM.PL.ESUAT
			</v-btn> */ ?>
		</v-card-actions>
	</template>
</v-card>
	`,

	computed: {
		passport_required () {
			var destination_country = getObjectDotPathValue(this.search_data || {}, 'destination.CountryName', '');
			var origin_country = getObjectDotPathValue(this.search_data || {}, 'origin.CountryName', '');
			var UE_countries = [
				'Austria', 'Belgium', 'Bulgaria', 'Croatia', 'Cyprus', 'Czech Republic', 'Denmark', 'Estonia', 'Finland', 'France', 'Germany', 'Greece', 'Hungary', 'Ireland', 'Italy', 'Latvia', 'Lithuania', 'Luxembourg', 'Malta', 'Netherlands', 'Poland', 'Portugal', 'Romania', 'Slovakia', 'Slovenia', 'Spain', 'Sweden',
				
				"Belgia","Cipru","Cehia","Germania","Danemarca","Spania","Finlanda","Franta","Grecia","Ungaria","Republica Irlanda","Italia","Lituania","Luxemburg","Letonia","Tarile de Jos","Polonia","Portugalia","Suedia","Slovacia"
			];
			var US_passport_free = [
				'United States',
				'Guam',
				'American Samoa',
				'Northern Mariana Islands',
				'Puerto Rico',
				'United States Virgin Islands',
				
				'Samoa americana',
				'Insulele Mariane de Nord',
				'Statele Unite ale Americii',
			];
			if(-1 !== US_passport_free.indexOf(destination_country) && -1 !== US_passport_free.indexOf(origin_country)){
			} else if(-1 !== UE_countries.indexOf(destination_country) && -1 !== UE_countries.indexOf(origin_country)){
			} else if(destination_country != origin_country){
				return true;
			}
			return false;
		},
		currentTitle () {
			switch (this.step) {
				case 1: return 'Bilete de avion';
				case 2: return this.search_loading ? 'Preluare lista zboruri' : (this.flights ? this.flights.length + ' Zboruri' : 'Lista zboruri');
				case 3: return 'Detalii zbor';
				case 4: return 'Detalii pasageri';
				case 5: return 'Date de facturare';
				case 6: return 'Sumar de plata';
				case 7: return 'Detalii sumar';
				case 8: return 'Creare comanda';
				default: return 'TODO';
			}
		},
	},
	methods: {
		plataZero(item){
			return;
			communicateWithPay24('pay', JSON.stringify({
				"order_id": "52c1c81b621339fdab7cd5f3e976ea7bcc4185255afa7be4cd684d8dc4606983360fc81c6d83355a4c9077b47ca2de73fe4e15410979fc4823b75dc4613aafa8MjbgBaWhm11B98Ivzb302xLo2NlHDHbFlFOX7ui8V9w=",
				"order_link": "https://accenttravel.ro/pay24/order_details/a472e5e67d4021123cf1bc481947214d38b361c394d0674493289291c3488a9bde44cc779ce8029b10c9f97f209da2b24db9ec1f6e9082a346d2659f514a36cau3BhDliYUdZBh9xUjud%24E%24nuUhlCBVENFgrFXcfUPRo%3D",
				"total": "0.00",
				"currency": "EUR",
				"date": "2023-09-19 14:25:00"
			}));
			this.resetCustom();
		},
		dateInterval(item){
			if(item.date){
				return dateIntervalFormatted(item.date, '0' != item.type && item.days);
			}
		},
		passengers(item){
			var txts = [];
			if(item.passengers.adt){
				txts.push(item.passengers.adt + ' ' + translate_ptc['ADT'][item.passengers.adt == 1 ? 0 : 1]);
			}
			if(item.passengers.sen){
				txts.push(item.passengers.sen + ' ' + translate_ptc['SEN'][item.passengers.sen == 1 ? 0 : 1]);
			}
			if(item.passengers.yth){
				txts.push(item.passengers.yth + ' ' + translate_ptc['YTH'][item.passengers.yth == 1 ? 0 : 1]);
			}
			if(item.passengers.chd){
				txts.push(item.passengers.chd + ' ' + translate_ptc['CHD'][item.passengers.chd == 1 ? 0 : 1]);
			}
			if(item.passengers.inf + item.passengers.ins){
				txts.push((item.passengers.inf + item.passengers.ins) + ' ' + general_translate_ptc['INF'][item.passengers.inf + item.passengers.ins == 1 ? 0 : 1]);
			}
			if(txts.length) return txts.join(', ');
		},
		applyFilters (filters) {
			this.applied_filters = filters;
		},
		saveFlights (flights) {
			this.flights = flights;
		},
		saveSearchLoading (search_loading) {
			this.search_loading = search_loading;
		},
		saveSummary (summary) {
			this.summary = summary;
		},
		saveFilters (filters) {
			this.filters = filters;
		},
		setSearch () {
			// console.warn('setSearch', this.search_data);
			this.$refs.flight_search.setSearch(this.search_data);
			this.step = 1;
		},
		allowTouchRight () {
			if(this.step > 1){
				this.step --;
			}
		},
		allowTouchLeft () {
			if(this.step == 1 && !!this.search_data){
				this.step ++;
				return true;
			}
			return false;
		},
		sim(cnt){
			this.simulate = !!cnt;
			return true;
		},
		can_step(cnt){
			var wanted_step = this.step + cnt;
			if(1 == cnt){
				if(this.step == 1){
					if(this.$refs.flight_search && this.$refs.flight_search.validate() && this.$refs.flight_search.addSearch()){
						this.search_data = JSON.parse(JSON.stringify(this.$refs.flight_search.searches[0]));
						return true;
					}
					return false;
				}
				if(this.step == 4){
					this.$refs.passenger_details.validateAndSave();
					return false;
				}
				if(this.step == 3){
					return !!this.flight_data;
				}

				if(this.step == 5){
					this.$refs.flight_billing.validateAndSave();
					return false;
				}
				
				return true;
			} else if(2 == cnt) {
				if(this.step == 6){
					var v = this.$refs.terms.isValid();
					if(v){
						this.checkoutAndPay();
					}
					return false;
				}
				return true;
			} else {
				if(this.step == 1){
					return false;
				}
				return true;
			}
		},
		checkoutAndPay(){
			if(this.validating){
				return;
			}
			this.checkout_errors = [];
			this.validating = true;
			let successful = false;
			var billing_info = {...this.billing_info};
			billing_info.regcom = billing_info.regcom || '-';
			billing_info.iban = billing_info.iban || '-';
			billing_info.bank = billing_info.bank || '-';
			var phone_prefix_country = countries.find(c => (c.prefix || '').replace(/[^0-9]/,'') == billing_info.phone_prefix);
			billing_info.phone_prefix_country = phone_prefix_country ? phone_prefix_country.value : 'RO';
			var s_params = 
			{
				billing: billing_info,
				search_data: this.search_data,
				flight: {
					code: this.flight_data.FlightsCode,
					itinerary_code: this.flight_data.ItineraryCode,
					expected_price: this.result.totalPrice,
					price: this.result.totalPrice - this.coupon_discount,
					currency: this.flight_data.Currency,
					type: this.search_data.type,
					passenger: this.result.serviceData.passenger,
					optionalServices: this.result.serviceData.optionalServices,
					paidSeats: this.result.serviceData.paidSeats,
					upsellCode: this.flight_data.upsellCode || null,
				}
			}
			;
			var url = '<?php echo site_url('pay24/create/flight?force_ajax=1&pay_24=1'); ?>'; 

			this.step = 8;
			/*
			console.warn('PAYING', {
							order_id:'',
							order_link:'',
							flight_data: this.flight_data,
							result: this.result,
						});
						return false;
						*/
			
			if(undefined !== window['gtag']){
				gtag('event', 'twentyfour_pay', {value: s_params.flight.price, currency: s_params.flight.currency});
			}
			return axios.post(url,objToSerialize({
				<?php if ($this->config->item('csrf_protection')){ ?>
				<?php echo json_encode($this->security->get_csrf_token_name()); ?>: <?php echo json_encode($this->security->get_csrf_hash()); ?>,
				<?php } ?>
				...s_params
				}), {
				headers: {
					'Content-Type': 'application/x-www-form-urlencoded charset=UTF-8',
					'X-Requested-With': '<?php echo $_SERVER['HTTP_X_REQUESTED_WITH']; ?>',
				},
				validateStatus: function (status) {return status == 200},
				cancelToken: new CancelToken(function executor(c) {
					search_axios_cancel = c;
				})
			}).then((result) => {
				console.warn('validated', result);
				if(result.data){
					if(result.data.status=="success"){
						var ls = this.flight_data.Routes[this.flight_data.Routes.length - 1].Segment[this.flight_data.Routes[this.flight_data.Routes.length - 1].Segment.length-1];
						var p = communicateWithPay24('pay', JSON.stringify({
							order_id:result.data.data.order_id,
							reference:result.data.data.reference,
							reservation_id:result.data.data.ReservationId,
							accent_id:result.data.data.accent_id,
							// failed: this.simulate ? true : false,
							order_link:result.data.data.order_link,
							// flight_data: this.flight_data,
							// result: this.result,
							total: this.result.totalPrice - this.coupon_discount,
							currency: this.flight_data.Currency,
							date: ls.Origin.Date + ' ' + ls.Origin.Time,
						}));
						successful = true;
						
						this.resetCustom();
						
						// window.location.href = result.data.data.order_link;
					} else {
						var ce = (getObjectDotPathValue(result.data, 'messages.*.*',[]) || []).flat(1);
						if((!ce || !ce.length)){
							var m = getObjectDotPathValue(result.data, 'message');
							if(m){
								ce = [m];
							}
						}
						this.checkout_errors = JSON.parse(JSON.stringify(ce));
						console.warn(this.checkout_errors);
					}
				}

			}).finally(() => {
				this.validating = false;
				if(!successful){
					this.step = 6;
				} else {
					// clearStorage('pay24.flight.results');
					// clearStorage('pay24.flight.flight_data');
					// this.reset();
					// this.step = 1;
				}
			})
		},
		resetCustom(){
			clearStorage('pay24.flight.results');
			clearStorage('pay24.flight.flight_data');
			this.reset();
			this.step = 1;
			this.$refs.coupons && this.$refs.coupons.reset();
		},
		reset(){
			this.$refs.flights_search_list_preferences && this.$refs.flights_search_list_preferences.reset();
			this.$refs.flights_search_results_list && this.$refs.flights_search_results_list.reset();
			this.$refs.flight_details && this.$refs.flight_details.reset();
			this.$refs.passenger_details && this.$refs.passenger_details.reset();
			this.$refs.coupons && this.$refs.coupons.reset();
		},
	},
	beforeCreate: () => {
		
		// console.warn('router', router);
	},
	mounted: function() {
		var r = communicateWithPay24('getMyAccount');
		// document.getElementById('accountinfo').innerHTML = "3:" + document.getElementById('accountinfo').innerHTML + r + "\n";
		// document.getElementById('accountinfo').style.display='block';
		// if(r instanceof Promise){
			// r.then((v) => {
				// document.getElementById('accountinfo').innerHTML = "2:" + document.getElementById('accountinfo').innerHTML + v + "\n";
				// document.getElementById('accountinfo').style.display='block';
			// });
		// }
		if(false) setTimeout(() => {
			// console.warn('checkstep');
			this.step == 1 && this.$refs.flight_search && this.$refs.flight_search.isValid() && this.$refs.flight_search.addSearch() && (this.search_data = JSON.parse(JSON.stringify(this.$refs.flight_search.searches[0]))) && this.step++;
		},1000)
	},
	watch: {
		'filters':{
			handler(newValue, oldValue){
				this.company_images = {};
				if(newValue && newValue.companies && newValue.companies.length){
					this.company_images = newValue.companies.reduce((o, v) => ((o[v.code] = v.img), o), {})
				}
			}
		},
		'selected_flight':{
			handler(newValue, oldValue){
				this.reset();
			}
		},
		'step':{
			handler(newValue, oldValue){
				this.search_data_mounted = false;
				var url = '<?php echo site_url('pay24/setStep?force_ajax=1&pay24=1'); ?>';
				var step_data = {};
				if(undefined !== window['gtag']){
					switch(newValue){
						case 1: gtag('event', 'twentyfour_form');  break;
						case 2: gtag('event', 'twentyfour_list');  break;
						case 3: gtag('event', 'twentyfour_details');  break;
						case 4: gtag('event', 'twentyfour_passengers');  break;
						case 5: gtag('event', 'twentyfour_billing');  break;
						case 6: gtag('event', 'twentyfour_checkout');  break;
						case 7: gtag('event', 'twentyfour_summary');  break;
					}
				}
				switch(newValue){
					case 2: step_data = ({account: pay24Account || {}});  break;
					case 4: 
						step_data = ({
							flight: this.flight_data
						});
						break;
					case 5: 
						step_data = ({
							checkout: {
								code: this.flight_data.FlightsCode,
								itinerary_code: this.flight_data.ItineraryCode,
								expected_price: this.result.totalPrice,
								price: this.result.totalPrice - this.coupon_discount,
								currency: this.flight_data.Currency,
								type: this.search_data.type,
								passenger: this.result.serviceData.passenger,
								optionalServices: this.result.serviceData.optionalServices,
								paidSeats: this.result.serviceData.paidSeats,
								upsellCode: this.flight_data.upsellCode || null,
							}
						});
						break;
					case 6: step_data = ({billing: this.billing_info || {}});  break;
				}
				step_data['account'] = pay24Account;
				saveLogData({step: newValue, device: browserDevice, step_data: step_data ? JSON.stringify(step_data) : null});
				/* this.$refs.flights_window && this.$refs.flights_window.$el && this.$refs.flights_window.$el.scrollTo({
					top: 0,
					behavior: 'smooth'
				}); */
				// scrollElemIntoView(this.$refs.flights_window);
				// if(1 == oldValue && 2 == newValue){
					// 	this.$refs.flight_search.addSearch();
				// }
			}
		},
	}
}
