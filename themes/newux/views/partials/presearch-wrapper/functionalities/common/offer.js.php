import FormLegend from '../../../form/legend.js?newux=<?php echo !empty($_GET['newux']) ? (string)$_GET['newux'] : 1 ; ?>';
export default {
	name: <?php echo json_encode($a, JSON_UNESCAPED_SLASHES); ?>,
	inject: ['search'],
	emits: ['offer','set-value'],
	components:{
		'FormLegend': FormLegend,
	},
	data: () => {
		return {
			abortController: new AbortController(),
			show_itinerariu: false,
			view_offer_count: 6,
			expand: [0],
			hotel: {},
			result_details_url: '',
			offer_details_url: '',
			fullscreen_image: '',
			chosen_index: undefined,
			text_result_type: [],
			checkout_component: {
				  type: String,
				  default: () => (undefined),
			},
		}
	},
	props: {
      is_item: {
          type: Boolean,
          default: () => (false),
      },
      results: {
          type: Array,
          default: () => ([]),
      },
      applied_filters: {
          type: Object,
          default: () => ({}),
      },
      data: {
          type: Object,
          default: () => ({}),
      },
      search_data: {
          type: Object,
          default: () => (undefined),
      },
      prepend_breadcrumbs: {
          type: Array,
          default: () => ([]),
      },
      inspection: {
          type: Object,
          default: () => (undefined),
      },
      offer: {
          type: Object,
          default: () => (undefined),
      },
      searching: {
          type: Boolean,
          default: () => (false),
      },
      set_checkout_component: {
          type: String,
          default: () => (undefined),
      },
      sorted: {
          type: String,
          default: () => (''),
      },
	  search_wrapper_step: {
		  type: Number,
		  default: () => (0),
		},
	},
	watch: {
		'offer': {
			handler: function(nv,ov){
				this.hotel = nv;
				// console.error('offer', this.hotel);
				this.fetchHotelDetails(this.hotel);
				this.expand = [0];
			},
			immediate: true
		},
	},
	beforeCreate() {
		if(this.is_item){
			this.$emit('set-value', {[this.$options.name.replace(/.*\/([^\/]+)\/[^\/]+$/,'$1') + '/hotel-id']: this.offer.Id});
			// console.warn('this.offer.Id', this.offer.Id, this.$options.name);
		}
	},
	beforeUnmount() {
		this.abortController.abort();
	},
	mounted() {
		var step = parseInt(window_url.searchParams.get("step"));
		if(!isNaN(step) && step > 2){
			console.error('forced offer click');
			this.chosen_index = 0;
			this.$emit('set-value',{'step': 3});
		} else {
			console.error('blocked forced offer click');
		}
		/*
		
		*/
	},
	computed: {
		firstoffer() {
			return this.hotel?.Offers?.[0] || {};
		},
		breadcrumbs() {
			return [
				... this.prepend_breadcrumbs,
				{title: this.hotel.Name, step: 2},
			];
		},
	},
	methods: {
		itemSetValue(){
			this.$emit('set-value', ...arguments);
			// console.warn(this.data);
			// Object.assign(this.item_data, ...arguments);
			// console.warn('this.item_data', this.item_data);
		},
		format_transport(text){
			text = (text||'').trim();
			text = text.replace(/^transport\b[\:\s\-]*/i, '');
			text = text.replace(/^avion\b[\:\s\-]*/i, '');
			text = text.replace(/^dus\b[\:\s\-]*/i, '');
			text = text.replace(/^retur\b[\:\s\-]*/i, '');
			text = text.replace(/\s*\(.*?\)\s*/g, ' ');
			text = text.replace(/([a-z])\s*[1-9][0-9]{3,}.*/ig, '$1');
			text = text.replace(/[\:\s\-]*\b(0[1-9]|[1-2][0-9]|3[0-1])([\.\-\/])(0[1-9]|1[0-2])\2([1-9][0-9]{3,})\b[\:\s\-]*(([0-1][0-9]|2[0-3])([\:\s\-]*[0-5][0-9])?)?[\:\s\-]*/g, ' ');
			// text = text.replace(/\s*[a-z]+\s+-\s+.*?()/g, ' ');
			text = text.replace(/\s+/, ' ').trim();
			
			return text;
		},
		allHotelImages(hotel){
			return [(hotel.MainImage || {}).ExternalUrl].concat((((hotel.Content|| {}).ImageGallery || {}).Items || []).map(i => (i || {}).ExternalUrl)).filter(i => i).filter((v, i, a) => a.indexOf(v) === i);
		},
		fetchHotelDetails(hotel){
			var fetch_url = this.result_details_url;
			if(!fetch_url) return;
			
			hotel.loadingDetails = true;
			var data = {
				<?php if ($this->config->item('csrf_protection')){ ?>
				<?php echo json_encode($this->security->get_csrf_token_name()); ?>: <?php echo json_encode($this->security->get_csrf_hash()); ?>,
				<?php } ?>
				ProductCode: hotel.Id,
			};
			fetch(fetch_url, {
				signal: this.abortController.signal,
				method: 'POST',
				headers: {
				  'Accept': 'application/json'
				},
				body: new URLSearchParams(objToSerialize(data))
			}).then((response) => {
				if (!response.ok) {
					if(response.status == 403){
						// CSRF
						window.location = window.location.href.replace(/#.*/, '');
						throw new Error("Network response failed. Redirecting to self", {cause: response });
					}
					throw new Error("Network response was not ok", {cause: response });
				}
				return response;
			}).then((response) => response.json()).then((h) => {
				if(!h || !h.Id){
					throw "Could not get info";
				}
				Object.assign(hotel, h);
				if(this.is_item && !hotel.Offers && hotel._initiate){
					this.fetchDetails(hotel);
				}
			}).catch((e) => {
				console.error("Failed to fetch hotel details", e);
				// Do nothing
			}).finally(() => {
				hotel.loadingDetails = false;
			})
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
		fetchDetails(hotel, offer){
			// console.error('fetchDetails', this.search_data);
			var fetch_url = this.offer_details_url;
			if(!fetch_url) return;
			if(offer){
				offer.loadingDetails = true;
			} else {
				hotel.loadingDetails = true;
			}
			var ssearch_data;
			if(this.search_data[0]){
				ssearch_data = {...this.search_data[0], full: this.search_data.full, Transport: ['bus', 'plane']};
			} else {
				ssearch_data = {...this.search_data};
			}
			var searches = [];
			if(Array.isArray(ssearch_data.Transport)){
				searches = ssearch_data.Transport.map(tr => ({...ssearch_data, Transport: tr}));
			} else {
				searches = [ssearch_data];
			}
			if(offer){
				// console.error("OFFER", offer);
			}
			if(offer && offer.Transport){
				searches = [{...searches[0], Transport: offer.Transport }];
			}
			
			var fetches = [];
			searches.forEach(search_data => {
				// console.warn('search_data', search_data);
				var data = {
					...search_data, 
					<?php if ($this->config->item('csrf_protection')){ ?>
					<?php echo json_encode($this->security->get_csrf_token_name()); ?>: <?php echo json_encode($this->security->get_csrf_hash()); ?>,
					<?php } ?>
					full: undefined,
					Children: search_data.Children.length,
					ChildrenAge: search_data.Children,
					ProductCode: hotel.Id,
					// OfferId: offer.SearchId,
					// OfferId: offer.Code,
				};
				if(offer){
					data.OfferId = offer.Code;
				}
				// console.warn(data);
				fetches.push(fetch(fetch_url, {
						signal: this.abortController.signal,
						method: 'POST',
						headers: {
						  'Accept': 'application/json'
						},
						body: new URLSearchParams(objToSerialize(data))
					}).then((response) => {
						if (!response.ok) {
							if(response.status == 403){
								// CSRF
								window.location = window.location.href.replace(/#.*/, '');
								throw new Error("Network response failed. Redirecting to self", {cause: response });
							}
							throw new Error("Network response was not ok", {cause: response });
						}
						return response;
					}).then((response) => response.json()).then((h) => {
						if(h && h.length){
							h.forEach(v => {
								if(v.Offers)
								v.Offers.forEach((o) => {
									o.Transport = search_data.Transport;
								})
							})
						}
						// console.warn('OFFERS', h);
						return h;
					}).catch((e) => {
					console.error("Failed to fetch offer details", e);
					// Do nothing
				})
				);
			});
			
			return Promise.all(fetches).then((responses) => {
				var offers = [];
				responses.forEach(h => {
					if(!h){
						throw "Could not get info";
					}
					h = h[0] || {};
					if(h){
						if(offer){
							offer.Details = (h.Offers || [])[0];
						} else if(h.Offers){
							offers = offers.concat(h.Offers);
						}
					}
				});
				if(!offer){
					var sorted = this.sorted;
					if(sorted){
						offers.sort(this['base_sort_' + this.sort]);
					}
					var applied_filters = this.applied_filters;
					if(applied_filters){
						offers = offers.filter(o => {
							if(applied_filters.availabily){
								var i = ((o.Items || []).find((i) => (i.Merch || {}).type == 'Room') || {});
								var availability = i.Availability || 'no';
								if(applied_filters.availabily != availability) return false;
							}
							if(applied_filters.prices){
								if(applied_filters.prices[0] && o.Price < applied_filters.prices[0]){
									return false;
								}
								if(applied_filters.prices[1] && o.Price > applied_filters.prices[1]){
									return false;
								}
							}
							if(applied_filters.facilities && -1 !== Object.keys(applied_filters.facilities).filter(k => applied_filters.facilities[k] && applied_filters.facilities[k].length).findIndex(k => !(o.facilities[k] && o.facilities[k].filter(j => -1 !== applied_filters.facilities[k].indexOf(j)).length == applied_filters.facilities[k].length))){
								return false;
							}
							return true;
						})
					}
					hotel.Offers = offers;
					// console.error('hotel.Offers', hotel);
				}
			}).catch((e) => {
				console.error("Failed to fetch offer", e);
				// Do nothing
			}).finally(() => {
				if(offer){
					offer.loadingDetails = false;
				} else {
					hotel.loadingDetails = false;
				}
			})
		},
	},
	template : `
	<v-container class="bg-background pa-0" v-if="hotel">
		<div class="offer-above px-4">
		<v-breadcrumbs :items="breadcrumbs">
			<template v-slot:divider>
				<v-icon icon="mdi-menu-right"></v-icon>
			</template>
			<template v-slot:item="{ item }">
				<v-breadcrumbs-item href="javascript:void(0)" :active="item.active" active-color="green" :disabled="item.step == 2" @click.stop="$emit('set-value', {'step': item.step})" v-text="item.title"></v-breadcrumbs-item>
			</template>
		</v-breadcrumbs>
		
		<div class="d-flex justify-space-between w-100 ps-2 hotel-details-wrapper flex-wrap">
			<div class="flex-fill hotel-details">
				<v-card-title class="pa-0">
					<div class="text-h6" v-if="undefined !== hotel.Stars">
						<v-icon v-if="hotel.Stars" icon="mdi-star" v-for="n in parseInt(hotel.Stars||0)" color="#fcc200"></v-icon>
						<v-icon v-else icon="mdi-star-off-outline" color="#fcc200"></v-icon>
					</div>
					<div class="text-h5 font-weight-bold text-wrap" v-text="hotel.Name"></div>
				</v-card-title>

				<v-card-subtitle class="pa-0 text-wrap">
					<v-icon size="28" icon="mdi-map-marker-outline" class="me-3"></v-icon>
					<span v-text="[(hotel?.Address?.City||({}))?.Name, (hotel?.Address?.Destination||({}))?.Name, ((hotel?.Address?.City||({}))?.Country||{})?.Name].filter(v => !!v).join(' - ')"></span>
				</v-card-subtitle>
			</div>
			<template v-if="$props.is_item">
				<div style="width:600px;max-width:100%;">
					<component :is="loadViewAsync($options.name.replace(/^(.*)\\/[^\\/]+$/, '$1'))" content_type="menu" submenu_only is_item :data="data" v-on:set-value="itemSetValue"/>
				</div>
			</template>
			<template v-else>
			<div class="offer-summary-wrapper">
				<div class="text-h5">Sumarul cautarii tale:</div>
				<pre v-if="0" v-html="JSON.stringify(search_data.full, null, 2)"></pre>
				<div class="offer-summary-content d-flex flex-wrap ga-5">
					<div class="offer-summary-item">
						<div class="text-h6">Plecare din:</div>
						<span v-text="(((search_data || {}).full || {}).Departure || {}).Name"></span>
					</div>
					<div class="offer-summary-item">
						<div class="text-h6">Data plecare:</div>
						<span v-if="hotel.Period" v-text="search_data?.full?.CheckIn?.Name"></span>
						<span v-else v-text="search_data?.full?.CheckIn?.Id"></span>
					</div>
					<div class="offer-summary-item" v-if="(search_data.full || {}).CheckOut">
						<div class="text-h6">Data retur:</div>
						<span v-text="search_data?.full?.CheckOut?.Id"></span>
					</div>
					<div class="offer-summary-item">
						<div class="text-h6">Nr. calatori:</div>
						<div v-if="search_data.full.ADT"><b><span v-text="search_data.full.ADT"></span> Adulti</b></div>
						<div v-if="search_data.full.YTH"><b><span v-text="search_data.full.YTH"></span> Tineri</b></div>
						<div v-if="search_data.full.CHD"><b><span v-text="search_data.full.CHD"></span> Copii</b></div>
					</div>
				</div>	
			</div>
			<div v-for="offer in [(hotel.Offers||[])[0]||{}]" class="d-flex flex-column offer-price">
				<span>de la</span>
				<span class="text-primary text-h4 font-weight-bold text-pre" v-text="format_price(offer.Price,(offer.Currency || {}).Code)"></span>
				<div v-if="offer.InitialPrice && offer.Price && offer.InitialPrice > offer.Price" class="text-body-2 text-primary text-decoration-line-through" v-text="format_price(offer.InitialPrice, offer.Currency.Code)"></div>
				<span>/calatorie</span>
				<v-btn
					class=""
					@click="(chosen_index = 0, $.emit('set-value', {step: 3}))"
					size="large"
					text="rezerva sejur"
					variant="outlined"
				></v-btn>
			</div>
			</template>
		</div>
		</div>
		<div>
			<div>
				<v-card class="mt-3">
					<div class="d-flex flex-column flex-no-wrap justify-space-between w-100">
					
					  <div class="pa-5 flex-fill" v-if="0">
						<v-expansion-panels>
						  <v-expansion-panel
							title="JSON Search data"
						  >
							<v-expansion-panel-text>
							<pre v-text="JSON.stringify(search_data,null,2)"></pre>
							</v-expansion-panel-text>
						  </v-expansion-panel>
						</v-expansion-panels>
						<?php /* <v-card-text class="pa-0">
							<hr class="my-3" />
							<div v-text="hotel.ShortContent"></div>
						</v-card-text> */ ?>

					  </div>
					<div class="mb-4">
					  <?php /* <v-avatar
						class="ma-4 mb-0"
						rounded="lg"
						density="compact"
						style="height:200px;width:300px"
					  >
						<v-img :src="(hotel.MainImage || {}).ExternalUrl"></v-img>
					  </v-avatar> */ ?>
					  <Gallery :images="allHotelImages(hotel)"></Gallery>
					  <?php /*
					  <template
						v-for="i in ((hotel.Content|| {}).ImageGallery || {}).Items || []"
					  >
					  <v-avatar
						class="ma-4 mb-0"
						v-if="i.ExternalUrl != (hotel.MainImage || {}).ExternalUrl"
						rounded="lg"
						density="compact"
						style="height:200px;width:300px"
					  >
						<v-img :src="i.ExternalUrl"></v-img>
					  </v-avatar>
					  </template> */ ?>
					</div>
					<v-card-text id="detalii_sejur_wrapper">
					<v-card class="d-flex justify-space-between flex-wrap" id="detalii_sejur">
						<v-card-text class="d-flex justify-space-between flex-wrap">
						<div>
							<h3>Detaliile sejurului de:</h3>
							<div v-if="search_data.full.Nights"><b><span v-text="search_data.full.Nights"></span> Nopti</b></div>
							<div v-if="search_data.full.ADT"><b><span v-text="search_data.full.ADT"></span> Adulti</b></div>
							<div v-if="search_data.full.YTH"><b><span v-text="search_data.full.YTH"></span> Tineri</b></div>
							<div v-if="search_data.full.CHD"><b><span v-text="search_data.full.CHD"></span> Copii</b></div>
							<template v-if="hotel.Period">
								<div><b>Durata:</b> <span v-text="hotel.Period"></span> <span v-text="hotel.Period == 1 ? 'noapte' : 'nopti'"></span></div>
								<div v-for="plecare in [(firstoffer.Items || []).find(i => (i.Merch||{}).type == 'Transport')].filter(i=>i)"><b>Plecari:</b> <span v-text="formatDateFull(plecare.Merch.DepartureTime)"></span></div>
								
							</template>
						</div>
						<div v-for="(t, ti) in (firstoffer.Items || []).filter(i => (i.Merch || {}).type == 'Transport')">
							<template v-if="(t.Merch || {}).TransportType == 'plane'">
								<h3>
								<v-icon :icon="!ti ? 'mdi-airplane-takeoff' : 'mdi-airplane-landing'" class="me-2"></v-icon>
								<span v-text="!ti ? 'Zbor plecare' : 'Zbor retur'"></span>: <span v-text="format_transport((t.Merch || {}).Title)"></span></h3>
								<div v-if="(t.Merch || {}).DepartureTime"><b>Plecare:</b> <span v-text="formatDateFull((t.Merch || {}).DepartureTime)"></span></div>
								<div v-if="(t.Merch || {}).ArrivalTime"><b>Sosire:</b> <span v-text="formatDateFull((t.Merch || {}).ArrivalTime)"></span></div>
							</template>
							<template v-else-if="(t.Merch || {}).TransportType == 'bus'">
								<h3>Autocar: <span v-text="format_transport((t.Merch || {}).Title)"></span></h3>
								<div v-if="(t.Merch || {}).DepartureTime"><b>Plecare:</b> <span v-text="formatDateFull((t.Merch || {}).DepartureTime)"></span></div>
								<div v-if="(t.Merch || {}).ArrivalTime"><b>Sosire:</b> <span v-text="formatDateFull((t.Merch || {}).ArrivalTime)"></span></div>
							</template>
							<template v-else>- Unknown -</template>
						</div>
						
						<div v-for="t in (firstoffer.Items || []).filter(i => (i.Merch || {}).type == 'Room')">
							<h3><v-icon icon="mdi-bed-outline" class="me-2"></v-icon><span>Cazare</span></h3>
							<div v-if="t.CheckinAfter"><b>Check-in:</b> <span v-text="formatDateFull(t.CheckinAfter)"></span></div>
							<div v-if="t.CheckinBefore"><b>Check-out:</b> <span v-text="formatDateFull(t.CheckinBefore)"></span></div>
						</div>
						</v-card-text>
					</v-card>
					</v-card-text>

					</div>
					<v-card-text class="" id="available-offers-wrapper">
						
						<?php /* <pre v-text="JSON.stringify(search_data, null, 2)"></pre> */ ?>
						
					<v-expansion-panels v-model="expand" multiple id="available-offers" v-if="0">
						
						<template v-for="(o, ok) in hotel.Offer && [hotel.Offer] || hotel.Offers">
						  <v-expansion-panel>
							<v-expansion-panel-title>
								<div class="d-flex w-100 justify-space-between">
									<div v-for="(tfacilities,ftype) in o.facilities">
										<strong v-text="ftype" v-if="tfacilities.length"></strong>
										<span v-for="(facility) in tfacilities" class="ms-2" v-html="((search.merch_type[ftype] || {})[facility] || ['', facility])[1]"></span>
									</div>
									<div class="text-h6 text-primary" v-text="format_price(o.Price, o.Currency.Code)"></div>
									<div v-if="o.InitialPrice && o.Price && o.InitialPrice > o.Price" class="text-h6 text-primary text-decoration-line-through text-pre" v-text="format_price(o.InitialPrice, o.Currency.Code)"></div>
								</div>
							</v-expansion-panel-title>
							<v-expansion-panel-text>
								<div class="text-h6" v-text="o.Info"></div>
								<ol class="ps-5">
								<li v-for="i in o.Items">
									<p v-if="i.Merch && i.Merch.Title" class="ma-0"><span>Merch:</span> <span v-text="i.Merch.Title"></span></p>
									<div class="ps-5">
										<p v-if="i.Availability" class="ma-0"><span>Availability:</span> <span v-text="i.Availability"></span></p>
										<?php /* <p v-if="i.Quantity" class="ma-0"><span>Quantity:</span> <span v-text="i.Quantity"></span></p>
										<p v-if="i.UnitPrice" class="ma-0"><span>UnitPrice:</span> <span v-text="i.UnitPrice"></span></p>
										<p v-if="i.CheckinBefore" class="ma-0"><span>CheckinBefore:</span> <span v-text="i.CheckinBefore"></span></p>
										<p v-if="i.CheckinAfter" class="ma-0"><span>CheckinAfter:</span> <span v-text="i.CheckinAfter"></span></p>
										<p v-if="i.Currency && i.Currency.Code" class="ma-0"><span>Currency:</span> <span v-text="i.Currency.Code"></span></p>
										<p v-if="i.Merch && i.Merch.type" class="ma-0"><span>Type:</span> <span v-text="i.Merch.type"></span></p>
										<p v-if="i.Merch && i.Merch.Code" class="ma-0"><span>Code:</span> <span v-text="i.Merch.Code"></span></p> */ ?>
									</div>
								</li>
								</ol>
								<div v-if="o.loadingDetails">
								<component :is="loadViewAsync('partials/presearch-wrapper/functionalities/common/loading')"></component>
								</div>
								<div v-if="o.Details" v-for="detail in [o.Details]">
									<v-expansion-panels v-if="0">
									  <v-expansion-panel
										title="JSON detalii oferta"
									  >
										<v-expansion-panel-text>
										<pre v-text="JSON.stringify(detail,null,2)"></pre>
										</v-expansion-panel-text>
									  </v-expansion-panel>
									</v-expansion-panels>
									<p>Conditii plata</p>
									<ul v-if="detail.Installments && detail.Installments.length">
										<li v-for="installment in detail.Installments">
											<span v-text="installment.PayAfter"></span> - <b v-text="installment.PayUntil"></b>: <span class="text-primary" v-text="format_price(installment.Amount, (installment.Currency||{}).Code)"></span>
										</li>
									</ul>
									<p>Conditii anulare</p>
									<ul v-if="detail.CancelFees && detail.CancelFees.length">
										<li v-for="cancelFee in detail.CancelFees">
											<span v-text="cancelFee.DateStart"></span> - <b v-text="cancelFee.DateEnd"></b>: <span class="text-primary" v-text="format_price(cancelFee.Price, (cancelFee.Currency||{}).Code)"></span>
										</li>
									</ul>
								</div>
						<?php /* <pre v-text="JSON.stringify(o, null, 2)"></pre> */ ?>
							  <v-btn
								class="ms-2"
								@click="(chosen_index = ok, $.emit('set-value', {step: 3}))"
								size="large"
								text="Rezerva"
								variant="outlined"
							  ></v-btn>
							  <v-btn v-if="undefined === o.loadingDetails"
								class="ms-2"
								@click="fetchDetails(hotel, o, o.Code)"
								size="large"
								text="Vezi conditii anulare si plata"
								variant="outlined"
							  ></v-btn>
							</v-expansion-panel-text>
						  </v-expansion-panel>
						  </template>
					</v-expansion-panels>
					<FormLegend title="Disponibilitate si preturi"></FormLegend>
					<div class="text-center mb-3">
					<v-btn
						v-if="hotel.Offers || hotel.loadingDetails"
						:loading="hotel.loadingDetails"
						class="ms-2"
						@click="fetchDetails(hotel)"
						size="large"
						text="Actualizeaza ofertele"
						variant="outlined"
					  ></v-btn>
					</div>
					<template v-for="offers in [(hotel.Offer && [hotel.Offer] || hotel.Offers || [])]">
					<v-row id="available-offers" class="pb-4">
						<template v-for="(o, ok) in offers.slice(0, view_offer_count)">
						<v-col cols="12" md="6" lg="4">
						  <v-card>
							<v-card-title>
								<div class="d-flex w-100 justify-space-between">
									<div class="other-offer-title text-wrap d-flex flex-wrap">
									<template v-for="(tfacilities,ftype) in o.facilities">
										<div v-if="-1 == ['Merch', 'Transport', 'Tax'].indexOf(ftype)">
										<strong v-text="ftype" v-if="false && tfacilities.length"></strong>
										<span v-for="(facility) in tfacilities" class="ms-2" v-html="((search.merch_type[ftype] || {})[facility] || ['', facility])[1]"></span>
										</div>
									</template>
									</div>
									<div class="other-offer-price">
									<div class="text-h6 text-primary" v-text="format_price(o.Price, o.Currency.Code)"></div>
									<div v-if="o.InitialPrice && o.Price && o.InitialPrice > o.Price" class="text-h6 text-primary text-decoration-line-through" v-text="format_price(o.InitialPrice, o.Currency.Code)"></div>
									</div>
								</div>
							</v-card-title>
							<v-card-subtitle>
								<div v-text="o.Info"></div>
							</v-card-subtitle>
							<v-card-text>
								<ol class="ps-0" style="list-style:none">
								<li v-for="i in (o.Items.filter(v =>  (-1 == ['0','1'].indexOf((v.Merch || {}).Code)) && -1 == ['Transport'].indexOf((v.Merch || {}).type)))">
									<p v-if="i.Merch && i.Merch.Title" class="ma-0"><span v-text="i.Merch.type"></span>: <span v-text="i.Merch.Title"></span>
									<span v-if="0 && i.UnitPrice" class="ma-0"> (<span class="text-primary" v-text="format_price(i.UnitPrice, (i.Currency||{}).Code)"></span>)</span>
									
									</p>
									<div class="ps-5" v-if="0">
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
								<v-dialog>
									<template v-slot:activator="{ props }">
									  <span
											class="text-primary"
											@click="(e) => ((!o.Details && fetchDetails(hotel, o, o.Code)), props.onClick(e))"
											size="large"
											v-text="'Vezi conditii anulare si plata'"
											variant="outlined"
											:loading="hotel.loadingDetails"
										></span>
									</template>
									<template v-slot:default="{ isActive }">
									<v-card class="align-self-center">
										<v-card-title>Conditii anulare si plata</v-card-title>
										<v-card-text>
											<div v-if="o.loadingDetails">
												<component :is="loadViewAsync('partials/presearch-wrapper/functionalities/common/loading')"></component>
											</div>
											<div v-else-if="o.Details" v-for="detail in [o.Details]">
												<v-expansion-panels v-if="0">
												  <v-expansion-panel
													title="JSON detalii oferta"
												  >
													<v-expansion-panel-text>
													<pre v-text="JSON.stringify(detail,null,2)"></pre>
													</v-expansion-panel-text>
												  </v-expansion-panel>
												</v-expansion-panels>
												<strong>Conditii plata</strong>
												<div v-if="detail.Installments && detail.Installments.length">
													<p v-for="installment in detail.Installments" class="d-flex flex-wrap justify-space-between ga-3">
														<span v-text="dateIntervalFormatted(installment.PayAfter, installment.PayUntil)"></span> <span class="text-primary" v-text="format_price(installment.Amount, (installment.Currency||{}).Code)"></span>
													</p>
												</div>
												<div v-else>-</div>
												<strong>Conditii anulare</strong>
												<div v-if="detail.CancelFees && detail.CancelFees.length">
													<p v-for="cancelFee in detail.CancelFees" class="d-flex flex-wrap justify-space-between ga-3">
														<span v-text="dateIntervalFormatted(cancelFee.DateStart, cancelFee.DateEnd)"></span> <span class="text-primary" v-text="format_price(cancelFee.Price, (cancelFee.Currency||{}).Code)"></span>
													</p>
												</div>
												<div v-else>-</div>
											</div>
											<div v-else>-</div>
										</v-card-text>
										<v-card-actions>
											<v-spacer></v-spacer>

											<v-btn
											  text="Inchide"
											  size="large"
												variant="outlined"
											  @click="isActive.value = false"
											></v-btn>
										  </v-card-actions>
										
									</v-card>
									</template>
								</v-dialog>
						<?php /* <pre v-text="JSON.stringify(o, null, 2)"></pre> */ ?>
							<div class="d-flex align-center justify-space-between">
							  <v-btn
								class=""
								@click="(chosen_index = ok, $.emit('set-value', {step: 3}))"
								size="large"
								text="Rezerva"
								variant="outlined"
								:loading="hotel.loadingDetails || o.loadingDetails"
							  ></v-btn>
							</div>
							</v-card-text>
						  </v-card>
						</v-col>
						  </template>
					</v-row>
					  <template v-if="offers[view_offer_count]">
						<div class="text-center">
						<v-btn @click="view_offer_count+=6"
							class="ms-2 see-more-offers-button"
							size="large"
							text="Vezi mai multe optiuni si camere"
							variant="outlined"
						></v-btn>
						</div>
					  </template>
				  </template>
					<v-expansion-panels v-if="(hotel.Content || {}).Content">
					  <v-expansion-panel>
						<v-expansion-panel-title>
							<FormLegend :title="'Descriere ' + (text_result_type[0] || '')" :subtitle="hotel.Name + ', ' + [(hotel?.Address?.City||({}))?.Name, (hotel?.Address?.Destination||({}))?.Name, ((hotel?.Address?.City||({}))?.Country||{})?.Name].filter(v => !!v).join(' - ')"></FormLegend>
						</v-expansion-panel-title>
						<v-expansion-panel-text>
							<div v-html="(hotel.Content || {}).Content || ''" class="mt-5"></div>
						</v-expansion-panel-text>
					  </v-expansion-panel>
					</v-expansion-panels>
					
					<v-expansion-panels v-if="show_itinerariu">
					  <v-expansion-panel>
						<v-expansion-panel-title>
							<FormLegend :title="'Itinerariu'" :subtitle="hotel.Name + ', ' + [(hotel?.Address?.City||({}))?.Name, (hotel?.Address?.Destination||({}))?.Name, ((hotel?.Address?.City||({}))?.Country||{})?.Name].filter(v => !!v).join(' - ')"></FormLegend>
						</v-expansion-panel-title>
						<v-expansion-panel-text>
							<div v-if="(hotel.Stages || []).length" v-for="(stage, stageIndex) in (hotel.Stages || [])" class="mt-5">
								<b v-text="'Ziua ' + (1 + stageIndex)"></b>
								<div v-text="(((stage||{}).Content || {}).ShortDescription || '').replace(/\\\\/, '').replace(/(<br\s*\\/*>)+/gs, '\\n').trim()" class="text-pre-line"></div>
							</div>
							<div v-else>Intinerariu indisponibil</div>
						</v-expansion-panel-text>
					  </v-expansion-panel>
					</v-expansion-panels>
					
					<template v-for="filtered_results in [(results || []).filter(v => v.Id != hotel.Id)]">
					<template v-if="filtered_results.length">
					<v-expansion-panels>
					  <v-expansion-panel>
						<v-expansion-panel-title>
							<FormLegend title="Oferte asemanatoare"></FormLegend>
						</v-expansion-panel-title>
						<v-expansion-panel-text>
					<v-carousel
						height="400"
						show-arrows="hover"
						progress="primary"
						hide-delimiters
					  >
						<v-carousel-item
						  v-for="(slide, i) in Math.ceil(filtered_results.length/4)"
						  :key="i"
						>
						  <v-sheet
							height="100%"
							tile
						  >
							<v-row dense>
								<v-col
								  v-for="(item, j) in filtered_results.slice(i*4, (i + 1)*4)"
								  :key="j"
								  cols="12"
								  md="3"
								>
								<v-card
									class="mx-auto"
									max-width="344"
								  >
									<v-avatar v-if="(item.MainImage || {'ExternalUrl': '/themes/newux/assets/images/placeholder.webp'}).ExternalUrl"
										rounded="lg"
										density="compact"
										style="height:200px;width:300px;max-width:100%;"
									  >
										<v-img :src="(item.MainImage || {'ExternalUrl': '/themes/newux/assets/images/placeholder.webp'}).ExternalUrl" cover></v-img>
										<div class="text-body position-absolute bg-white rounded-xl px-1" style="left:5px;top:5px;" v-if="undefined !== item.Stars">
											<v-icon v-if="item.Stars" icon="mdi-star" v-for="n in parseInt(item.Stars||0)" color="#fcc200"></v-icon>
											<v-icon v-else icon="mdi-star-off-outline" color="#fcc200"></v-icon>
										</div>
										<div v-for="o in [item.Offers[0]]" class="d-flex flex-column text-right position-absolute" style="bottom:5px;right:5px;">
										<div v-if="o.InitialPrice && o.Price && o.InitialPrice > o.Price" class="text-h6 text-primary">
										<v-chip variant="elevated" color="warning">
											<span>
												PROMOTIE <strong v-text="'- ' + Math.ceil(100 * (o.InitialPrice - o.Price)/o.InitialPrice) + '%'"></strong>
											</span>
										</v-chip>
										</div>
										</div>
									  </v-avatar>

									<v-card-title v-text="item.Name"></v-card-title>

									<v-card-subtitle>
										<div class="d-flex flex-fill justify-space-between ga-3">
											<div class="d-flex">
												<v-icon size="28" icon="mdi-map-marker-outline" class="me-3"></v-icon>
												<div class="d-flex flex-column text-wrap">
													<span v-text="[(item?.Address?.City||({}))?.Name, (item?.Address?.Destination||({}))?.Name, ((item?.Address?.City||({}))?.Country||{})?.Name].filter(v => !!v).join(' - ')"></span>
													<small>Pret pentru <b v-text="search_data.full.ADT + search_data.full.YTH + search_data.full.CHD"></b> persoane <br />/ <b v-text="search_data.full.Nights"></b> nopti/calatorie</small>
												</div>
											</div>
											<span v-for="o in [item.Offers[0]]" class="d-flex flex-column text-right">
												<span>de la</span>
												<span class="text-primary text-body-2 font-weight-bold" v-text="format_price(o.Price,(o.Currency || {}).Code)"></span>
												<div v-if="o.InitialPrice && o.Price && o.InitialPrice > o.Price" class="text-body-2 text-primary text-decoration-line-through" v-text="format_price(o.InitialPrice, o.Currency.Code)"></div>
											</span>
										</div>
									</v-card-subtitle>

									<v-card-actions>
										<div class="d-flex align-center">
											<v-icon size="28" icon="mdi-calendar-blank-outline" class="me-3"></v-icon>
											<div class="d-flex flex-column">
												<span v-text="(search_data || {}).CheckIn"></span>
												<span v-text="(search_data || {}).CheckOut"></span>
											</div>
										</div>
									  <v-spacer></v-spacer>
									  <v-btn
										class="ms-2"
										@click="$emit('offer', item);"
										size="large"
										text="Rezerva"
										variant="outlined"
									  ></v-btn>
									</v-card-actions>
								  </v-card>
								</v-col>
							  </v-row>
						  </v-sheet>
						</v-carousel-item>
					</v-carousel>
						</v-expansion-panel-text>
					  </v-expansion-panel>
					</v-expansion-panels>
					
					  
					  </template>
					  </template>
					
					</v-card-text>

				</v-card>
				
			</div>
		</div>
		<teleport to="#search-wrapper-item-checkout" v-if="undefined !== chosen_index">
			<component :is="loadViewAsync(set_checkout_component || checkout_component)" :search_data="search_data" :result="{...hotel, Offers:[hotel.Offers[chosen_index]]}" :prepend_breadcrumbs="breadcrumbs" :data="data" :search_wrapper_step="search_wrapper_step" v-on:set-value="(v) => $emit('set-value', v)"></component>
		</teleport>
	</v-container>
	`,
}
