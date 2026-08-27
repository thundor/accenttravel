import FormLegend from '../../../form/legend.js?newux=<?php echo !empty($_GET['newux']) ? (string)$_GET['newux'] : 1 ; ?>';
export default {
	name: <?php echo json_encode($a, JSON_UNESCAPED_SLASHES); ?>,
	inject: ['search'],
	emits: ['offer','set-value','research','research-hash'],
	components:{
		'FormLegend': FormLegend,
	},
	data: () => {
		return {
			//item_data: {},
			carousel_slide: undefined,
			chosen_package: undefined,
			details_package: undefined,
			room_details_dialog: false,
			room_details_dialog_tab: 0,
			view_offer_count: 3,
			view_room_offer_limit: 3,
			view_room_offer_count: {},
			expand2: [],
			expand: [],
			hotel: {},
			result_details_url: `${newux_url}/partials/presearch-wrapper/functionalities/<?php echo basename(dirname($a)); ?>/result-details.json?${append_url}`,
			offer_details_url: `${newux_url}/partials/presearch-wrapper/functionalities/<?php echo basename(dirname($a)); ?>/offer-details.json?${append_url}`,
			room_combination_details_url: `${newux_url}/partials/presearch-wrapper/functionalities/<?php echo basename(dirname($a)); ?>/room-combination-details.json?${append_url}`,
			fullscreen_image: '',
			fullscreen_image2: '',
			chosen_index: undefined,
			text_result_type: [],
			checkout_component: undefined,
		}
	},
	props: {
      is_item: {
          type: Boolean,
          default: () => (false),
      },
      results: {
          type: [Array, Object],
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
          default: () => ({}),
      },
      offer: {
          type: Object,
          default: () => (undefined),
      },
      flight_inspection: {
          type: Object,
          default: () => (undefined),
      },
      flight_offer: {
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
				console.error('offer', nv);
				this.hotel = nv;
				// this.setResearchHash();
				this.view_offer_count = 3;
				this.expand = [];
				this.chosen_package = undefined;
				this.fetchHotelDetails(this.hotel);
			},
			immediate: true
		},
		'results': {
			handler: function(nv,ov){
				// console.warn('hotel results',nv); 
			},
			immediate: true
		},
	},
	beforeCreate() {
	},
	created() {
		if(this.is_item){
			this.$emit('set-value', {'<?php echo basename(dirname($a)); ?>/hotel-id': this.offer.Id});
			// console.warn('this.offer.Id', this.offer.Id);
		}
	},
	mounted() {
		var step = parseInt(window_url.searchParams.get("step"));
		if(!isNaN(step) && step > 2){
			console.error('forced offer click');
			this.chosen_index = undefined;
			this.$emit('set-value',{'step': 3});
		} else {
			console.error('blocked forced offer click');
		}
		/*
		
		*/
	},
	computed: {
		hash() {
			return {
				search_data: {
					cityId: this.search_data.cityId,
					dIn: this.search_data.dIn,
					dOut: this.search_data.dOut,
					r: this.search_data.r,
				},
				hotel_id: this.hotel?.Id,
				package: {
					openPackageDetails: this.room_details_dialog,
					Board: this.chosen_package?.Board,
					Complete: this.chosen_package?.Complete,
					Info: this.chosen_package?.Info,
					Name: this.chosen_package?.Name,
					Price: this.chosen_package?.Price?.Amount,
					Currency: this.chosen_package?.Price?.Currency,
					Rooms: (this.chosen_package?.PackageRooms?.PackageRoom || []).map((packageRoom, packageRoom_index) => {
						return (packageRoom?.RoomRefs?.RoomRef || []).filter((roomRef) => this.chosen_package?.SelectedRooms[packageRoom_index] === packageRoom.PackageRoomCode + ':' + roomRef.RoomCode).map(roomRef => {
							return {
								Name: roomRef?.Name,
								Price: roomRef?.Price?.Amount,
								Currency: roomRef?.Price?.Currency,
								Info: roomRef?.Info,
								Board: roomRef?.Board,
								Status: roomRef?.Status,
								BoardBasis: roomRef?.BoardBasis,
								NonRefundable: roomRef?.NonRefundable,
								Description: roomRef?.Description,
							}
						})[0]
					}),
				},
			};
		},
		firstoffer() {
			return {};
		},
		breadcrumbs() {
			return [
				... this.prepend_breadcrumbs,
				{title: this.hotel.Name, step: 2},
			];
		},
	},
	methods: {
		setResearchHash(){
			this.inspection.research_hash = JSON.parse(JSON.stringify(this.hash));
		},
		itemSetValue(){
			this.$emit('set-value', ...arguments);
			// console.warn(this.data);
			// Object.assign(this.item_data, ...arguments);
			// console.warn('this.item_data', this.item_data);
		},
		setFlightOffer(offer){
			console.error('setFlightOffer', offer)
		},
		format_price_obj_amount_currency(obj){
			return this.format_price(obj.Amount, obj.Currency);
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
			return [(hotel.Image || '')].concat(hotel.Gallery|| []).filter(i => i).filter((v, i, a) => a.indexOf(v) === i);
		},
		fetchRoomCombinationDetails(RoomCombination){
			this.inspection.checking = this.inspection.checking || {};
			this.inspection.checking.package = true;
			
			this.details_package = undefined;
			var data = {
				<?php if ($this->config->item('csrf_protection')){ ?>
				<?php echo json_encode($this->security->get_csrf_token_name()); ?>: <?php echo json_encode($this->security->get_csrf_hash()); ?>,
				<?php } ?>
				HotelId: this.hotel.Id,
				Code: this.inspection.code || '',
				PackageCode: (this.chosen_package || {}).PackageCode || '',
				RoomsCombination: (RoomCombination || Object.values(this.chosen_package.SelectedRooms || [])).join('-'),
			};
			// console.warn('fetchRoomCombinationDetails', data);
			fetch(this.room_combination_details_url, {
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
				this.details_package = h;
			}).catch((e) => {
				this.$emit('research', JSON.parse(JSON.stringify(this.hash)), 'hotel');
				console.error("Failed to fetch hotel details", e);
				// Do nothing
			}).finally(() => {
				if(this.inspection?.checking)
					this.inspection.checking.package = false;
			})
		},
		fetchHotelDetails(hotel){
			var fetch_url = this.result_details_url;
			if(!fetch_url) return;
			
			this.inspection.checking = this.inspection.checking || {};
			this.inspection.checking.hotel = true;
			
			hotel.loadingDetails = true;
			var data = {
				<?php if ($this->config->item('csrf_protection')){ ?>
				<?php echo json_encode($this->security->get_csrf_token_name()); ?>: <?php echo json_encode($this->security->get_csrf_hash()); ?>,
				<?php } ?>
				HotelId: hotel.Id,
			};
			fetch(fetch_url, {
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
				hotel.Details = h;
				console.warn('hotel_details', hotel);
				this.fetchDetails(hotel);
			}).catch((e) => {
				console.error("Failed to fetch hotel details", e);
				// Do nothing
			}).finally(() => {
				if(this.inspection?.checking)
					this.inspection.checking.hotel = false;
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
		fetchDetails(hotel, force){
			var fetch_url = this.offer_details_url;
			if(!fetch_url) return;
			
			if(!this.inspection.code) return;
			
			this.inspection.checking = this.inspection.checking || {};
			this.inspection.checking.offer = true;
			hotel.loadingDetails = true;
			var data = {
				...this.search_data, 
				<?php if ($this->config->item('csrf_protection')){ ?>
				<?php echo json_encode($this->security->get_csrf_token_name()); ?>: <?php echo json_encode($this->security->get_csrf_hash()); ?>,
				<?php } ?>
				full: null,
				HotelId: hotel.Id,
				Code: this.inspection.code || '',
			};
			// console.warn(data);
			fetch(fetch_url, {
				method: 'POST',
				headers: {
					'Init-Research': force ? 1 : 0,
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
				if(!h || undefined !== h.length && !h.length){
					throw 'Could not get info';
				}
				this.hotel.Packages = ((h._embedded || {}).packages || []).filter(p => p.Complete);
				this.hotel.Packages.forEach(p => p.SelectedRooms = (p.PackageRooms.PackageRoom.map(packageRoom => packageRoom.PackageRoomCode + ':' + packageRoom.RoomRefs.RoomRef.find(rr => rr.Selected).RoomCode)));
				this.chosen_package = undefined;
				// console.warn('this.inspection', this.inspection);
				var new_chosen;
				if(this.inspection?.research_hash?.package){
					var h_package = JSON.parse(JSON.stringify(this.inspection.research_hash.package));
					// console.warn('h_package', h_package);
					new_chosen = (this.hotel.Packages || []).find((pkg, pkg_index) => {
						if((pkg?.Name === h_package?.Name) &&
						(pkg?.Info === h_package?.Info) &&
						(pkg?.Board === h_package?.Board) &&
						(pkg?.Complete === h_package?.Complete) &&
						(pkg?.Price?.Currency === h_package?.Currency) &&
						(true || pkg?.Price?.Amount === h_package?.Price)){
							var selectedRooms = [];
							var view_room_offer_index = [];
							if((h_package?.Rooms || []).find((h_room, room_index) => {
								var packageRoom = pkg?.PackageRooms?.PackageRoom?.[room_index];
								// console.warn('packageRoom', packageRoom);
								if(packageRoom){
									var roomRefIndex = (packageRoom?.RoomRefs?.RoomRef || []).findIndex(rr => {
										var probs = [];
										var g = (rr?.Board === h_room?.Board) &&
										(rr?.BoardBasis === h_room?.BoardBasis) &&
										(rr?.Price?.Currency === h_room?.Currency) &&
										(true || rr?.Price.Amount === h_room?.Price) &&
										(rr?.Description === h_room?.Description) &&
										(rr?.Info === h_room?.Info) &&
										(rr?.Name === h_room?.Name) &&
										(rr?.NonRefundable === h_room?.NonRefundable) &&
										(rr?.Status === h_room?.Status) && true;
										// console.warn('probs', g)
										return g ? true : false;
									});
									if(-1 !== roomRefIndex){
										var roomRef = packageRoom.RoomRefs.RoomRef[roomRefIndex];
										console.log('room_index', room_index);
										console.error('roomRef', roomRef);
										view_room_offer_index.push(roomRefIndex);
										selectedRooms.push(packageRoom.PackageRoomCode + ':' + roomRef.RoomCode);
										return false;
									}
								}
								return true;
							})){
								return false;
							}
							pkg.SelectedRooms = selectedRooms;
							this.view_offer_count = 3 * (1 + parseInt(pkg_index/3));
							// console.warn('this.view_room_offer_limit', this.view_room_offer_limit, view_room_offer_index);
							this.view_room_offer_count = view_room_offer_index.reduce((c, i, ri) => (c[ri] = this.view_room_offer_limit * parseInt(i/this.view_room_offer_limit), c), {});
							return true;
						}
						return false
					});
					console.error('COMPARE', this.inspection.research_hash.package, new_chosen);
					if(!new_chosen){
						// console.warn('SETTING STEP', 2);
						this.data.step = this.data.step > 2 ? 2 : this.data.step;
					} else {
						this.chosen_package = new_chosen;
						if(h_package.openPackageDetails || this.data.step > 2){
							this.fetchRoomCombinationDetails();
						}
					}
				}
				this.chosen_package = this.chosen_package || this.hotel.Packages[0]
				
				this.setResearchHash();
				// this.fetchRoomCombinationDetails();
				// console.warn(h);
				return;
			}).catch((e) => {
				this.$emit('research', JSON.parse(JSON.stringify(this.hash)), 'hotel');
				console.error("Failed to fetch offer details", e);
				// Do nothing
			}).finally(() => {
				if(this.inspection?.checking)
					this.inspection.checking.offer = false;
				hotel.loadingDetails = false;
			})
		},
	},
	template : `
	<v-container class="bg-background pa-0" v-if="hotel" id="wrapper-oferta-hoteluri">
		<div class="offer-above px-4">
		<v-breadcrumbs :items="breadcrumbs">
			<template v-slot:divider>
				<v-icon icon="mdi-menu-right"></v-icon>
			</template>
			<template v-slot:item="{ item }">
				<v-breadcrumbs-item href="javascript:void(0)" :active="item.active" active-color="green" :disabled="item.step == 2" @click.stop="$emit('set-value', {'step': item.step})" v-text="item.title"></v-breadcrumbs-item>
			</template>
		</v-breadcrumbs>
		
		<div class="d-flex justify-space-between w-100 hotel-details-wrapper flex-wrap">
			<div class="flex-fill hotel-details order-1" style="min-width:50%">
				<v-card-title class="pa-0">
					<div class="text-h6" v-if="undefined !== hotel.Stars">
						<v-icon v-if="hotel.Stars" icon="mdi-star" v-for="n in parseInt(hotel.Stars||0)" color="#fcc200"></v-icon>
						<v-icon v-else icon="mdi-star-off-outline" color="#fcc200"></v-icon>
					</div>
					<div class="text-h5 font-weight-bold text-wrap" v-text="hotel.Name"></div>
				</v-card-title>

				<v-card-subtitle class="pa-0 text-wrap">
					<v-icon size="28" icon="mdi-map-marker-outline" class="me-3"></v-icon>
					<span v-text="[(hotel.Address||''), (hotel.CityName||''), (hotel.CountryName||'').Name].filter(v => !!v).join(', ')"></span>
				</v-card-subtitle>
			</div>
			<template v-if="$props.is_item">
				<div style="width:600px;max-width:100%;">
					<component :is="loadViewAsync('partials/presearch-wrapper/functionalities/trip-hoteluri')" content_type="menu" submenu_only is_item :data="data" v-on:set-value="itemSetValue"/>
				</div>
			</template>
			<template v-else>
			<div class="offer-summary-wrapper order-3 order-lg-2">
				<div class="text-h5">Sumarul cautarii tale:</div>
				<div class="offer-summary-content d-flex flex-wrap ga-5">
					<div class="offer-summary-item">
						<div class="text-h6">Check-in:</div>
						<span v-text="(((search_data || {}).full || {}).CheckIn || {}).Name"></span>
					</div>
					<div class="offer-summary-item">
						<div class="text-h6">Check-out:</div>
						<div class="d-flex flex-column">
						<span v-text="(((search_data || {}).full || {}).CheckOut || {}).Name"></span>
						<span v-if="search_data.full.Nights"><b><span v-text="search_data.full.Nights"></span> <span v-if="search_data.full.Nights == 1">Noapte</span><span v-else>Nopti</span></b></span>
						</div>
					</div>
					<div class="offer-summary-item" v-if="(search_data || {}).CheckOut">
						<div class="text-h6">Data retur:</div>
						<span v-text="(search_data || {}).CheckOut"></span>
					</div>
					<div class="offer-summary-item">
						<div class="text-h6">Nr. calatori:</div>
						<div v-if="search_data.full.ADT"><b><span v-text="search_data.full.ADT"></span> <span v-if="search_data.full.ADT == 1">Adult</span><span v-else>Adulti</span></b></div>
						<div v-if="search_data.full.CHD"><b><span v-text="search_data.full.CHD"></span> <span v-if="search_data.full.CHD == 1">Copil</span><span v-else>Copii</span></b></div>
					</div>
				</div>	
			</div>
			<div class="d-flex flex-column offer-price order-2 order-lg-3">
				<span>de la</span>
				<span class="text-primary text-h4 font-weight-bold text-pre" v-text="format_price(offer.MinPrice, offer.Currency)"></span>
				<div v-if="offer.InitialPrice && offer.Price && offer.InitialPrice > offer.Price" class="text-body-2 text-primary text-decoration-line-through" v-text="format_price(offer.InitialPrice, offer.Currency.Code)"></div>
				<span>/calatorie</span>
				<?php /* <v-btn
					class=""
					@click="(chosen_index = 0, $.emit('set-value', {step: 3}))"
					size="large"
					text="rezerva sejur"
					variant="outlined"
				></v-btn> */ ?>
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
					  <Gallery :images="allHotelImages(hotel.Details || {})"></Gallery>
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
					<?php /* <v-card-text id="detalii_sejur_wrapper">
					<v-card class="d-flex justify-space-between flex-wrap" id="detalii_sejur">
						<v-card-text class="d-flex justify-space-between flex-wrap">
						<div>
							<h3>Detaliile sejurului de:</h3>
							<div v-if="search_data.full.Nights"><b><span v-text="search_data.full.Nights"></span> <span v-if="search_data.full.Nights == 1">Noapte</span><span v-else>Nopti</span></b></div>
							<div v-if="search_data.full.ADT"><b><span v-text="search_data.full.ADT"></span> <span v-if="search_data.full.ADT == 1">Adult</span><span v-else>Adulti</span></b></div>
							<div v-if="search_data.full.CHD"><b><span v-text="search_data.full.CHD"></span> <span v-if="search_data.full.CHD == 1">Copil</span><span v-else>Copii</span></b></div>
							<template v-if="hotel.Period">
								<div><b>Durata:</b> <span v-text="hotel.Period"></span> <span v-text="hotel.Period == 1 ? 'noapte' : 'nopti'"></span></div>
								<div v-for="plecare in [(firstoffer.Items || []).find(i => (i.Merch||{}).type == 'Transport')].filter(i=>i)"><b>Plecari:</b> <span v-text="plecare.Merch.DepartureTime"></span></div>
								
							</template>
						</div>
						<div v-for="(t, ti) in (firstoffer.Items || []).filter(i => (i.Merch || {}).type == 'Transport')">
							<template v-if="(t.Merch || {}).TransportType == 'plane'">
								<h3>
								<v-icon :icon="!ti ? 'mdi-airplane-takeoff' : 'mdi-airplane-landing'" class="me-2"></v-icon>
								<span v-text="!ti ? 'Zbor plecare' : 'Zbor retur'"></span>: <span v-text="format_transport((t.Merch || {}).Title)"></span></h3>
								<div v-if="(t.Merch || {}).DepartureTime"><b>Plecare:</b> <span v-text="(t.Merch || {}).DepartureTime"></span></div>
								<div v-if="(t.Merch || {}).ArrivalTime"><b>Sosire:</b> <span v-text="(t.Merch || {}).ArrivalTime"></span></div>
							</template>
							<template v-else-if="(t.Merch || {}).TransportType == 'bus'">
								<h3>Autocar: <span v-text="format_transport((t.Merch || {}).Title)"></span></h3>
								<div v-if="(t.Merch || {}).DepartureTime"><b>Plecare:</b> <span v-text="(t.Merch || {}).DepartureTime"></span></div>
								<div v-if="(t.Merch || {}).ArrivalTime"><b>Sosire:</b> <span v-text="(t.Merch || {}).ArrivalTime"></span></div>
							</template>
							<template v-else>- Unknown -</template>
						</div>
						
						<div v-for="t in (firstoffer.Items || []).filter(i => (i.Merch || {}).type == 'Room')">
							<h3><v-icon icon="mdi-bed-outline" class="me-2"></v-icon><span>Cazare</span></h3>
							<div v-if="t.CheckinAfter"><b>Check-in:</b> <span v-text="t.CheckinAfter"></span></div>
							<div v-if="t.CheckinBefore"><b>Check-out:</b> <span v-text="t.CheckinBefore"></span></div>
						</div>
						</v-card-text>
					</v-card>
					</v-card-text>
					 */ ?>

					</div>
					<v-card-text class="" id="available-offers-wrapper">
					<template v-if="hotel.Packages && hotel.Packages.length || hotel.loadingDetails || searching && !(hotel.Packages && hotel.Packages.length)">
					<FormLegend title="Disponibilitate si preturi"></FormLegend>
					<div v-if="hotel.loadingDetails || searching">
					<component :is="loadViewAsync('partials/presearch-wrapper/functionalities/common/loading')"></component>
					</div>
					<v-row v-else id="available-offers" class="available-offers pb-4" v-if="hotel.Packages && hotel.Packages.length">
						<template v-for="(package, package_index) in hotel.Packages.slice(0, view_offer_count)">
						<v-col cols="12" md="6" lg="4" :class="{
											  ['order-' + (chosen_package && chosen_package.PackageCode == package.PackageCode && '0' || '1')]: false
										  }">
						  <v-card :class="{
							  active: chosen_package && chosen_package.PackageCode == package.PackageCode
						  }">
							<v-card-title>
								<div class="d-flex w-100 justify-space-between">
									<div class="other-offer-title text-wrap d-flex flex-wrap">
									<span v-text="'Oferta #' + (package_index+1)"></span>
									</div>
									<div class="other-offer-price">
									
									<span>de la</span>
									
									<div class="text-h6 text-primary" v-text="format_price_obj_amount_currency(package.PackageRooms.PackageRoom.reduce((c, p) => (c.Amount+=((p.Price || {}).Amount && (c.Currency = p.Price.Currency, p.Price.Amount) || ([p.RoomRefs.RoomRef[0]].reduce((a, r) => a + ((c.Currency = r.Price.Currency), (r.Price||{}).Amount || 0),0)) || (package.Price || {}).Amount && (c.Currency = package.Price.Currency, package.Price.Amount) || 0), c),{Amount: 0, Currency: 'RON'}))"></div>
									</div>
								</div>
							</v-card-title>
							<v-card-subtitle>
								<div class="text-wrap" v-text="package.PackageRooms.PackageRoom.map(packageRoom => packageRoom.RoomRefs.RoomRef.map(rr => rr.NonRefundable && 'NonRefundable' || 'Refundable')).flat().filter((v, i, a) => a.indexOf(v) === i).join(', ')"></div>
								<div class="text-wrap" v-text="package.PackageRooms.PackageRoom.map(packageRoom => packageRoom.RoomRefs.RoomRef.map(rr => rr.Name)).flat().filter((v, i, a) => a.indexOf(v) === i).join(', ')"></div>
							</v-card-subtitle>
							<v-card-text>
								<div class="d-flex align-center justify-space-between">
								<v-btn
									:active="chosen_package && chosen_package.PackageCode == package.PackageCode"
									class="button-alege"
									@click="(package.SelectedRooms = package.SelectedRooms || package.PackageRooms.PackageRoom.map(packageRoom => packageRoom.PackageRoomCode + ':' + packageRoom.RoomRefs.RoomRef.find(rr => rr.Selected).RoomCode), chosen_package = package)"
									size="large"
									text="Alege"
									variant="outlined"
								  ></v-btn>
								</div>
							</v-card-text>
						  </v-card>
						</v-col>
						  </template>
					</v-row>
					  <template v-if="hotel.Packages && hotel.Packages[view_offer_count]">
						<div class="text-center">
						<v-btn @click="view_offer_count+=6"
							class="ms-2 see-more-offers-button"
							size="large"
							text="Vezi mai multe oferte"
							variant="outlined"
						></v-btn>
						</div>
					  </template>
				  </template>
				  <div v-if="chosen_package && !searching" v-for="package in [chosen_package]" class="pachet-ales">
					<FormLegend>
						<div class="d-flex justify-space-between">
							<div class="v-list-item-title">Rezervare camere</div>
							<div class="d-flex flex-column">
								<div class="text-h4 font-weight-bold text-primary text-no-wrap text-end" v-text="format_price_obj_amount_currency(package.PackageRooms.PackageRoom.reduce((c, packageRoom, packageRoom_index) => (c.Amount+=((packageRoom.Price || {}).Amount && (c.Currency = packageRoom.Price.Currency, packageRoom.Price.Amount) || (packageRoom.RoomRefs.RoomRef.filter(roomRef => packageRoom.PackageRoomCode + ':' + roomRef.RoomCode == package.SelectedRooms[packageRoom_index]).reduce((a, r) => a + ((c.Currency = r.Price.Currency), (r.Price||{}).Amount || 0),0)) || (package.Price || {}).Amount && (c.Currency = package.Price.Currency, package.Price.Amount) || 0), c),{Amount: 0, Currency: 'RON'}))"></div>
								<div class="v-list-item-subtitle flex-no-wrap d-flex ga-3">
									<div v-if="search_data.full.Nights"><span v-text="search_data.full.Nights"></span> <span v-if="search_data.full.Nights == 1">Noapte</span><span v-else>Nopti</span></div>
									<div v-if="search_data.full.ADT"><span v-text="search_data.full.ADT"></span> <span v-if="search_data.full.ADT == 1">Adult</span><span v-else>Adulti</span></div>
									<div v-if="search_data.full.CHD"><span v-text="search_data.full.CHD"></span> <span v-if="search_data.full.CHD == 1">Copil</span><span v-else>Copii</span></div>
								</div>
							</div>
						</div>
					</FormLegend>
					
					<v-dialog v-model="room_details_dialog" class="offer-details-modal">
					  <template v-slot:default="{ isActive }">
						<div v-if="!details_package">
							<component :is="loadViewAsync('partials/presearch-wrapper/functionalities/common/loading')"></component>
						</div>
						<v-card v-else>
							<v-card-text class="max-height overflow-y-auto">
							<template v-if="details_package" v-for="package in [details_package]">
					<?php /*
					<div class="text-h6" v-if="package.Info">Info: <span v-text="package.Info"></span></div>
					<div class="text-h6" v-if="package.Board">Board: <span v-text="package.Board"></span></div>
					<div class="text-h6" v-if="package.Name">Name: <span v-text="package.Name"></span></div>
					<div class="text-h6" v-if="package.RateDetails">RateDetails: <span v-text="package.RateDetails"></span></div>
					<div class="text-h6" v-if="package.Status">Status: <span v-text="package.Status"></span></div>
					<div class="text-h6" v-if="package.Price">Price: <span v-text="format_price_obj_amount_currency(package.Price)"></span></div>
					*/ ?>
					<div v-if="((package.PackageRooms || {}).PackageRoom || []).length">
						<v-window v-model="room_details_dialog_tab" multiple>
							<template v-for="(packageRoom, packageRoom_index) in ((package.PackageRooms || {}).PackageRoom || [])">
							  <v-window-item v-if="((packageRoom.RoomRefs || {}).RoomRef || []).length">
								<template v-for="(roomRef, roomRef_index) in (((packageRoom.RoomRefs || {}).RoomRef || []).filter(rr => rr.Selected) || [])" style="width: 100%">
									<FormLegend>
										<div class="d-flex justify-space-between ga-3 flex-column flex-sm-row">
										<div class="">
											<div class="v-list-item-title" v-text="'Camera #' + (1 + packageRoom_index)"></div>
											<div class="v-list-item-subtitle" v-text="[(packageRoom.Occupancy.Adults ? (packageRoom.Occupancy.Adults == 1 ? '1 Adult' : packageRoom.Occupancy.Adults + ' Adulti') : ''),(packageRoom.Occupancy.Children ? (packageRoom.Occupancy.Children == 1 ? '1 Copil' : packageRoom.Occupancy.Children + ' Copii') + (packageRoom.Occupancy.ChildAge && packageRoom.Occupancy.ChildAge.length ? (' (' + packageRoom.Occupancy.ChildAge.join(', ') + ' ani)') : '') : '')].filter(a => !!a).join(', ')"></div>
										</div>
										<div class="d-flex ga-3">
											<div class="other-offer-title text-wrap d-flex flex-wrap flex-column">
												<div class="v-list-item-title text-wrap" v-text="roomRef.Name"></div>
												<div class="v-list-item-subtitle">
													<div v-if="roomRef.Board">Board: <span v-text="roomRef.Board"></span></div>
													<div v-if="roomRef.Status">Status: <span v-text="roomRef.Status"></span></div>
													<div v-if="roomRef.NonRefundable">NonRefundable</div>
													<div v-else>Refundable</div>
												</div>
											</div>
											<?php /*
											<div class="other-offer-price align-self-center ms-0">
												<div class="text-h6 text-primary text-no-wrap" v-text="format_price_obj_amount_currency(((roomRef.Price || {}).Amount && roomRef.Price || package.Price))"></div>
											</div>
											*/ ?>
										</div>
										</div>
									</FormLegend>
								<div>
									<Gallery :images="((roomRef.Images || {}).Image || []).map(v => v.URL)"></Gallery>
									  <?php /*<div>Price: <span v-text="format_price_obj_amount_currency(((roomRef.Price || {}).Amount && roomRef.Price || package.Price))"></span></div>
									  <div v-if="roomRef.Name">Nume: <span v-text="roomRef.Name"></span></div>
									  <div v-if="roomRef.Status">Status: <span v-text="roomRef.Status"></span></div>
									  <div v-if="roomRef.Board">Board: <span v-text="roomRef.Board"></span></div>*/ ?>
									  <div class="offer-room-minimal-info">
										  <div class="offer-room-info" v-if="roomRef.Info">Info: <span v-text="roomRef.Info"></span></div>
										  <div class="offer-room-board-basis" v-if="roomRef.BoardBasis">BoardBasis: <span v-text="roomRef.BoardBasis"></span></div>
										  <div class="offer-room-board-status" v-if="roomRef.Status">Status: <span v-text="roomRef.Status"></span></div>
										  <div class="offer-room-board-non-refundable" v-if="roomRef.NonRefundable">NonRefundable</div>
										  <div class="offer-room-board-refundable" v-else>Refundable</div>
										  <div class="offer-room-extra-services" v-if="roomRef.ExtraServices && roomRef.ExtraServices.length">ExtraServices: <span v-text="roomRef.ExtraServices"></span></div>
										</div>
									  <div class="offer-room-description text-pre-line" v-if="roomRef.Description">Description: <span v-html="roomRef.Description.replace(/([\\n])+/g, '$1')"></span></div>
									  <div class="offer-room-facilities" v-if="roomRef.FacilitiesDescription">Facilitati: <span v-text="roomRef.FacilitiesDescription"></span></div>
								</div>
								</template>
							  </v-window-item>
							</template>
						</v-window>
					</div>
					<v-expansion-panels>
						<v-expansion-panel v-if="package.CancellationPolicy">
						<v-expansion-panel-title>
							<FormLegend title="Politica Anulare"></FormLegend>
						</v-expansion-panel-title>
						<v-expansion-panel-text>
							<div v-if="package.CancellationPolicy.AutocancelDate">AutocancelDate: <span v-text="formatDateFull(package.CancellationPolicy.AutocancelDate)"></span></div>
							<div v-if="package.CancellationPolicy.Policy && package.CancellationPolicy.Policy.length">
								<template v-for="policy in package.CancellationPolicy.Policy">
									<div v-if="'autocancel' != policy.Type">
										<div v-if="package.CancellationPolicy.AutocancelDate"><span v-text="policy.Type"></span>: <span v-text="formatDateFull(policy.Limit)"></span><span v-if="policy.Charge" v-text="' (' + format_price_obj_amount_currency(policy.Charge) + ')'"></span></div>
									</div>
								</template>							
							</div>
						</v-expansion-panel-text>
						</v-expansion-panel>
						<v-expansion-panel v-if="package.RateDetails">
						<v-expansion-panel-title>
							<FormLegend :title="package.RateDetails.Name || 'RateDetails'"></FormLegend>
						</v-expansion-panel-title>
						<v-expansion-panel-text>
							<div class="text-pre-line" v-html="(package.RateDetails.Description || '').replace(/([\\n])+/g, '$1')"></div>
						</v-expansion-panel-text>
						</v-expansion-panel>
						<v-expansion-panel v-if="package.Remarks">
						<v-expansion-panel-title>
							<FormLegend title="Remarks"></FormLegend>
						</v-expansion-panel-title>
						<v-expansion-panel-text>
							<div v-if="package.Remarks.RemarksCategory">
								<ul>
									<li v-for="remark in package.Remarks.RemarksCategory">
										<strong v-text="remark.Name"></strong>
										<ol class="ps-2">
											<li class="text-pre-line" v-for="text in remark.Remark" v-html="(text || '').replace(/([\\n])+/g, '$1')"></li>
										</ol>
									</li>
								</ul>
							</div>
							<div v-else>RemarksCategory Missing</div>
						</v-expansion-panel-text>
						</v-expansion-panel>
						<v-expansion-panel v-if="package.Taxes && package.Taxes.length">
						<v-expansion-panel-title>
							<FormLegend>
								<div class="d-flex w-100 justify-space-between">
									<span>Taxe aditionale incluse si neincluse</span>
									<span class="text-primary" v-text="format_price_obj_amount_currency(package.Taxes.reduce((c, tax, tax_index) => ((c.Amount += tax.Amount && (c.Currency = tax.Currency, tax.Amount) || 0), c),{Amount: 0, Currency: hotel.Currency || 'RON'}))"></span>
								</div>
							</FormLegend>
						</v-expansion-panel-title>
						<v-expansion-panel-text>
							<div>
								<ul>
									<li v-for="tax in package.Taxes" :class="{[('tax-type-' + tax.Type)]: 1, [(tax.IsIncluded ? 'tax-is-included' : 'tax-is-not-included')]: 1}" class="d-flex w-100 flex-wrap justify-space-between ga-2">
										<div class="d-flex ga-2"><strong v-text="tax.Name"></strong><span v-text="tax.Description"></span></div>
										<div class="text-end d-flex ga-2"><span v-if="tax.Amount" class="text-primary" v-text="format_price(tax.Amount, tax.Currency)"></span><span class="tax-inc tax-inc-included" v-if="tax.IsIncluded">(Inclus)</span><span class="tax-inc tax-inc-not-included" v-if="!tax.IsIncluded">(Neinclus)</span></div>
									</li>
								</ul>
							</div>
						</v-expansion-panel-text>
						</v-expansion-panel>
					</v-expansion-panels>
				  </template>
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
					
					<?php /*
					<div class="text-h6" v-if="package.Info">Info: <span v-text="package.Info"></span></div>
					<div class="text-h6" v-if="package.Board">Board: <span v-text="package.Board"></span></div>
					<div class="text-h6" v-if="package.Name">Name: <span v-text="package.Name"></span></div>
					<div class="text-h6" v-if="package.RateDetails">RateDetails: <span v-text="package.RateDetails"></span></div>
					<div class="text-h6" v-if="package.Status">Status: <span v-text="package.Status"></span></div>
					<div class="text-h6" v-if="package.Price">Price: <span v-text="format_price_obj_amount_currency(package.Price)"></span></div>
					*/ ?>
					<div v-if="((package.PackageRooms || {}).PackageRoom || []).length">
						<v-expansion-panels v-model="expand" multiple id="optiuni-camere">
							<template v-for="(packageRoom, packageRoom_index) in ((package.PackageRooms || {}).PackageRoom || [])">
							  <v-expansion-panel id="optiuni-camera">
								<v-expansion-panel-title>
									<FormLegend v-for="roomRef in [packageRoom.RoomRefs.RoomRef.find(roomRef => packageRoom.PackageRoomCode + ':' + roomRef.RoomCode == package.SelectedRooms[packageRoom_index])]">
										<div class="d-flex justify-space-between ga-3 flex-column flex-sm-row">
										<div class="">
											<div class="v-list-item-title" v-text="'Camera #' + (1 + packageRoom_index)"></div>
											<div class="v-list-item-subtitle" v-text="[(packageRoom.Occupancy.Adults ? (packageRoom.Occupancy.Adults == 1 ? '1 Adult' : packageRoom.Occupancy.Adults + ' Adulti') : ''),(packageRoom.Occupancy.Children ? (packageRoom.Occupancy.Children == 1 ? '1 Copil' : packageRoom.Occupancy.Children + ' Copii') + (packageRoom.Occupancy.ChildAge && packageRoom.Occupancy.ChildAge.length ? (' (' + packageRoom.Occupancy.ChildAge.join(', ') + ' ani)') : '') : '')].filter(a => !!a).join(', ')"></div>
										</div>
										<div class="d-flex ga-3 flex-fill justify-space-between">
											<div class="other-offer-title text-wrap d-flex flex-wrap flex-column">
												<div class="v-list-item-title text-wrap" v-text="roomRef.Name"></div>
												<div class="v-list-item-subtitle">
													<div v-if="roomRef.Board">Board: <span v-text="roomRef.Board"></span></div>
													<div v-if="roomRef.Status">Status: <span v-text="roomRef.Status"></span></div>
													<div v-if="roomRef.NonRefundable">NonRefundable</div>
													<div v-else>Refundable</div>
													<div class="text-body-2" v-text="roomRef.Info"></div>
												</div>
											</div>
											<div class="other-offer-price align-self-center ms-0">
												<div class="text-h6 text-primary text-no-wrap" v-text="format_price_obj_amount_currency(((roomRef.Price || {}).Amount && roomRef.Price || package.Price))"></div>
											</div>
										</div>
										<div class="align-self-center d-flex flex-wrap ga-3 justify-end">
											<v-btn
												class="button-detalii align-self-center"
												@click.stop="((room_details_dialog_tab=packageRoom_index), (room_details_dialog = true), fetchRoomCombinationDetails([...package.SelectedRooms].map((selectedRoom, roomIndex) => roomIndex == packageRoom_index && (packageRoom.PackageRoomCode + ':' + roomRef.RoomCode) || selectedRoom)))"
												size="large"
												text="Detalii"
												variant="outlined"
											  ></v-btn>
											<div class="text-body-2 text-primary text-no-wrap align-self-center">Alege alt tip de camera</div>
										</div>
										  <?php /* 
										*/ ?>
										</div>
									</FormLegend>
								</v-expansion-panel-title>
								<v-expansion-panel-text>
									<v-row id="available-room-offers" class="available-offers pb-4" v-if="((packageRoom.RoomRefs || {}).RoomRef || []).length">
										<template v-for="(roomRef, roomRef_index) in ((packageRoom.RoomRefs || {}).RoomRef || []).slice(0, (view_room_offer_count[packageRoom_index]||0) + view_room_offer_limit )">
										<v-col cols="12" md="6" lg="4" :class="{
											  ['order-' + (package.SelectedRooms[packageRoom_index] == (packageRoom.PackageRoomCode + ':' + roomRef.RoomCode) && '0' || '1')]: false
										  }">
										  <v-card :class="{
											  active: package.SelectedRooms[packageRoom_index] == (packageRoom.PackageRoomCode + ':' + roomRef.RoomCode)
										  }">
											<v-card-title>
												<div class="d-flex w-100 justify-space-between">
													<div class="other-offer-title text-wrap d-flex flex-wrap">
													<span v-text="roomRef.Name"></span>
													</div>
													<div class="other-offer-price">
													
													<span></span>
													
													<div class="text-h6 text-primary" v-text="format_price_obj_amount_currency(((roomRef.Price || {}).Amount && roomRef.Price || package.Price))"></div>
													</div>
												</div>
											</v-card-title>
											<v-card-subtitle>
												<div class="text-wrap">
													<div v-text="roomRef.Info"></div>
													<div v-if="roomRef.Board">Board: <span v-text="roomRef.Board"></span></div>
													<div v-if="roomRef.Status">Status: <span v-text="roomRef.Status"></span></div>
													<div v-if="roomRef.NonRefundable">NonRefundable</div>
													<div v-else>Refundable</div>
												</div>
											</v-card-subtitle>
											<v-card-text>
												<div class="d-flex flex-column align-center justify-space-between ga-3">
												<?php /* <v-checkbox :disabled="package.SelectedRooms[packageRoom_index] == packageRoom.PackageRoomCode + ':' + roomRef.RoomCode" type="radio" v-model="package.SelectedRooms[packageRoom_index]" :value="packageRoom.PackageRoomCode + ':' + roomRef.RoomCode" ></v-checkbox> */ ?>
												<v-btn
													class="button-detalii"
													@click="((room_details_dialog_tab=packageRoom_index), (room_details_dialog = true), fetchRoomCombinationDetails([...package.SelectedRooms].map((selectedRoom, roomIndex) => roomIndex == packageRoom_index && (packageRoom.PackageRoomCode + ':' + roomRef.RoomCode) || selectedRoom)))"
													size="large"
													text="Detalii"
													variant="outlined"
												  ></v-btn>
												<v-btn
													:active="package.SelectedRooms[packageRoom_index] == (packageRoom.PackageRoomCode + ':' + roomRef.RoomCode)"
													class="button-alege"
													@click="package.SelectedRooms[packageRoom_index] = (packageRoom.PackageRoomCode + ':' + roomRef.RoomCode)"
													size="large"
													text="Alege"
													variant="outlined"
												  ></v-btn>
												</div>
											</v-card-text>
										  </v-card>
										</v-col>
										  </template>
									</v-row>
									<template v-if="((packageRoom.RoomRefs || {}).RoomRef || [])[(view_room_offer_count[packageRoom_index]||0) + view_room_offer_limit]">
										<div class="text-center">
										<v-btn @click="view_room_offer_count[packageRoom_index] = (view_room_offer_count[packageRoom_index] || 0)+view_room_offer_limit"
											class="ms-2 see-more-offers-button"
											size="large"
											text="Vezi mai multe oferte"
											variant="outlined"
										></v-btn>
										</div>
									  </template>
									<?php /*
									<table v-if="((packageRoom.RoomRefs || {}).RoomRef || []).length" style="width: 100%">
										<thead>
										<tr>
											<th>Nume</th>
											<th>Info</th>
											<th>Status</th>
											<th>Board</th>
											<th>Pret</th>
											<th>Alege</th>
										</tr>
										</thead>
										<tbody>
										<tr v-for="(roomRef, roomRef_index) in ((packageRoom.RoomRefs || {}).RoomRef || [])">
											<td v-text="roomRef.Name"></td>
											<td v-text="roomRef.Info"></td>
											<td class="text-center" v-text="roomRef.Status"></td>
											<td class="text-center" v-text="roomRef.Board"></td>
											<td class="text-center" v-text="format_price_obj_amount_currency(((roomRef.Price || {}).Amount && roomRef.Price || package.Price))"></td>
											<td class="text-center"><v-checkbox :disabled="package.SelectedRooms[packageRoom_index] == packageRoom.PackageRoomCode + ':' + roomRef.RoomCode" type="radio" v-model="package.SelectedRooms[packageRoom_index]" :value="packageRoom.PackageRoomCode + ':' + roomRef.RoomCode" ></v-checkbox></td>
										</tr>
										</tbody>
									</table>
									*/ ?>
								</v-expansion-panel-text>
							  </v-expansion-panel>
							</template>
						</v-expansion-panels>
					</div>
				  </div>
				  
					<div class="text-center mb-3" v-if="hotel.Details && !searching && (hotel.Packages && hotel.Packages.length)">
						<v-btn
							:loading="hotel.loadingDetails"
							class="ms-2"
							@click="(setResearchHash(), fetchDetails(hotel, true))"
							size="large"
							text="Actualizeaza ofertele"
							variant="outlined"
						  ></v-btn>
					</div>
					<v-expansion-panels v-if="hotel.Details && hotel.Details.FullDesc">
					  <v-expansion-panel>
						<v-expansion-panel-title>
							<FormLegend :title="'Descriere ' + (text_result_type[0] || '')" :subtitle="hotel.Name + ', ' + [(hotel.Address||''), (hotel.CityName||''), (hotel.CountryName||'').Name].filter(v => !!v).join(', ')"></FormLegend>
						</v-expansion-panel-title>
						<v-expansion-panel-text>
							<div v-html="hotel.Details.FullDesc" class="mt-5 text-pre-line"></div>
						</v-expansion-panel-text>
					  </v-expansion-panel>
					</v-expansion-panels>
					<v-expansion-panels v-if="hotel.Details && hotel.Details.FacilitiesDetail && hotel.Details.FacilitiesDetail.length">
					  <v-expansion-panel>
						<v-expansion-panel-title>
							<FormLegend :title="'Facilitati'"></FormLegend>
						</v-expansion-panel-title>
						<v-expansion-panel-text>
							<div v-html="hotel.Details.FacilitiesDetail.map(v => (v||'').trim()).join(', ')" class="mt-5"></div>
						</v-expansion-panel-text>
					  </v-expansion-panel>
					</v-expansion-panels>
					<?php /* 
					HASH:
					<pre v-text="JSON.stringify(hash, null, 2)"></pre> */ ?>
					<template v-if="0" v-for="filtered_results in [(results || []).filter(v => v.Id != hotel.Id)]">
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
										<div v-for="o in [(item.Offers || [])[0]]" class="d-flex flex-column text-right position-absolute" style="bottom:5px;right:5px;">
										<div v-if="o && o.InitialPrice && o.Price && o.InitialPrice > o.Price" class="text-h6 text-primary">
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
													<span v-text="[(item.Address||''), (item.CityName||''), (item.CountryName||'').Name].filter(v => !!v).join(', ')"></span>
													<small>Pret pentru <b v-text="search_data.full.ADT + search_data.full.YTH + search_data.full.CHD"></b> persoane <br />/ <b v-text="search_data.full.Nights"></b> nopti/calatorie</small>
												</div>
											</div>
											<span v-for="o in [(item.Offers || [])]" class="d-flex flex-column text-right">
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
		<component v-if="flight_offer" :is="loadViewAsync('partials/presearch-wrapper/functionalities/trip-citybreak/offer')" :show_breadcrumbs="false" :offer="flight_offer" :inspection="flight_inspection" :searching="searching" :prepend_breadcrumbs="breadcrumbs" v-on:hash="(h, t) => $emit('research-hash', h, t)" v-on:research="(h, t) => $emit('research', h, t)" :hotel="{...hotel, Package:chosen_package}" :hotel_inspection="inspection"
		 v-on:offer="(r) => r && setFlightOffer(r)"
		:results="[]" :applied_filters="undefined" v-on:set-value="(r) => ($emit('set-value', r))" :search_data="search_data" :set_checkout_component="set_checkout_component || checkout_component" :data="data" :search_wrapper_step="data.step" ></component>
		<template v-else>
		<teleport to="#pos-stick-b-t" v-if="(2 == search_wrapper_step)">
			<div class="d-flex w-100 justify-space-between py-1"  v-if="hotel && !hotel.loadingDetails">
				<v-spacer></v-spacer>
				<?php /* <template v-if="chosen_package && chosen_package.SelectedRooms">
					<template v-for="(SelectedRoom, packageRoom_index) in chosen_package.SelectedRooms">
						<v-btn v-if="chosen_package" class="buton-sumar" variant="outlined" size="small" @click="((room_details_dialog_tab=packageRoom_index), (room_details_dialog = true), fetchRoomCombinationDetails(chosen_package.SelectedRooms))">Vezi detalii camera #{{ packageRoom_index + 1 }}</v-btn>
					</template>
				</template> */ ?>
				<template v-if="chosen_package && chosen_package.SelectedRooms">
					<template v-for="(SelectedRoom, packageRoom_index) in chosen_package.SelectedRooms">
						<div class="d-none d-md-flex flex-column ga-2">
							<template v-for="room in [(hotel && chosen_package && chosen_package.PackageRooms.PackageRoom || []).reduce((c, packageRoom, packageRoom_index2) => (packageRoom_index2 != packageRoom_index && c) || (c.Amount+=((packageRoom.RoomRefs.RoomRef.filter(roomRef => (!chosen_package.SelectedRooms ? roomRef.Selected : (packageRoom.PackageRoomCode + ':' + roomRef.RoomCode == chosen_package.SelectedRooms[packageRoom_index]))).reduce((a, r) => a + ((c.Currency = r.Price.Currency, c.Room = r), (r.Price||{}).Amount || 0),0)) || (chosen_package.Price || {}).Amount && (c.Currency = chosen_package.Price.Currency, chosen_package.Price.Amount) || 0), c),{Amount: 0, Currency: 'RON', Room: undefined})]">
							<div class="d-flex w-100 justify-space-between ga-2"><span>Camera #{{ packageRoom_index + 1 }}</span><strong>{{ format_price_obj_amount_currency(room) }}</strong></div>
							<template v-if="room.Room" v-for="roomRef in [room.Room]">
							<div class="d-flex w-100 justify-space-between ga-2" v-if="roomRef.Name">
								<span v-text="roomRef.Name"></span>
							</div>
							<?php /* <div class="d-flex w-100 justify-space-between ga-2" v-if="roomRef.Board">
								Board: <span v-text="roomRef.Board"></span>
							</div> */ ?>
							</template>
							</template>
						</div>
						<v-spacer></v-spacer>
					</template>
				</template>
				<v-btn
					:loading="!hotel || hotel.loadingDetails"
					class="button-alege"
					@click=" $.emit('set-value', {step: 3})"
					size="large"
					variant="outlined"
				>
				<span>Finalizare </span> 
				<span v-if="chosen_package" v-text="'(' + format_price_obj_amount_currency((hotel && chosen_package && chosen_package.PackageRooms.PackageRoom || []).reduce((c, packageRoom, packageRoom_index) => (c.Amount+=((packageRoom.RoomRefs.RoomRef.filter(roomRef => (!chosen_package.SelectedRooms ? roomRef.Selected : (packageRoom.PackageRoomCode + ':' + roomRef.RoomCode == chosen_package.SelectedRooms[packageRoom_index]))).reduce((a, r) => a + ((c.Currency = r.Price.Currency), (r.Price||{}).Amount || 0),0)) || (chosen_package.Price || {}).Amount && (c.Currency = chosen_package.Price.Currency, chosen_package.Price.Amount) || 0), c),{Amount: 0, Currency: 'RON'})) + ')'"></span>
				</v-btn>
			</div>
			<div v-else>
				<component :is="loadViewAsync('partials/presearch-wrapper/functionalities/common/loading')"></component>
			</div>
		</teleport>
		<teleport to="#search-wrapper-item-checkout" v-if="chosen_package">
			<component :is="loadViewAsync(set_checkout_component || checkout_component)" :search_data="search_data" :result="{Hotel: {...hotel, Package:chosen_package}, Hotels: inspection}" :prepend_breadcrumbs="breadcrumbs" :data="data" :search_wrapper_step="search_wrapper_step" v-on:set-value="(v) => $emit('set-value', v)" v-on:hash="(h, t) => $emit('research-hash', h, t)" v-on:research="(h, t) => $emit('research', h, t)"></component>
		</teleport>
		</template>
	</v-container>
	`,
}
