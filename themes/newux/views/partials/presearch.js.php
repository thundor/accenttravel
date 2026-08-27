export default {
	name: <?php echo json_encode($a, JSON_UNESCAPED_SLASHES); ?>,
	emits: ['results', 'searching'],
	data: () => {
		return {
			results: Object.freeze([]),
			presearch:{
				searching: false,
				rooms: 0,
				travellers:{
					'ADT': 0, 
					'YTH': 0, 
					'CHD': [], 
				},
				origin:{
					selected: {
						date: null,
					},
					date: null,
				},
				destination:{
					search: {
						step: 0,
						country: '',
						city: '',
					},
					selected: {
						country_ids: [],
						city_ids: [],
						date: null,
					},
					filtered: {
						countries: [],
						cities: [],
					},
					cities: null,
					country: null,
					city: null,
					date: null,
				},
			},
			imenu: {
				0: false,
				1: false,
				2: false,
				3: false,
			},
			menu: {
				active: null,
				items: [
					{
						key: 'travelfuse-holiday',
						icon: 'mdi-weather-sunset',
						text: 'Vacante',
						children: [
							{
								key: 'travelfuse-romania',
								icon: 'mdi-flag-variant',
								text: 'Romania',
							},
							{
								key: 'travelfuse-strainatate',
								icon: 'mdi-earth-arrow-up',
								text: 'Strainatate',
							},
							{
								key: 'travelfuse-circuite',
								icon: 'mdi-map-marker-path',
								text: 'Circuite',
							},
							{
								key: 'travelfuse-croaziere',
								icon: 'mdi-ship-wheel',
								text: 'Croaziere',
							},
						]
					},
					{
						key: 'hotel',
						icon: 'mdi-bed-outline',
						text: 'Hoteluri',
					},
					{
						key: 'plane',
						icon: 'mdi-airplane-search',
						text: 'Avion',
					},
					{
						key: 'citybreak',
						icon: 'mdi-plane-car',
						text: 'City Break',
					},
				]
			}
		}
	},
	template : `
	<div class="search-type-ul-wrapper">
		<ul class="search-type-ul search-type-ul-root mt-5 mb-5">
			<li
				v-for="(item, i) in [...menu.items]"
				:class="{'has-children': !!item.children, 'active': menu.active && -1 != (item.children||[]).map(v=>v.key).concat([item.key]).indexOf(menu.active)}"
				:style="{'--bg-color': 'rgba(' + (255 - 5*i) + ',' + (255 - 5*i) + ',' + (255 - 5*i) + ',1)', 'z-index': menu.items.length - i}"
				@click.stop="(!item.children || !item.children.length) && (menu.active = item.key)"
			>
				<v-icon :icon="item.icon" class="me-3"></v-icon>
				<span v-text="item.text"></span>
				
				<ul v-if="item.children" class="search-type-ul">
					<li
						:style="{'--bg-color': 'rgba(' + (255 - 5*i) + ',' + (255 - 5*i) + ',' + (255 - 5*i) + ',1)', 'z-index': menu.items.length + 1, 'flex': '0 0 calc(' + (100/menu.items.length) + '% + var(--search-type-button-radius-base) + var(--search-type-button-padding-base) + 20px)'}"
					>
						<v-icon :icon="item.icon" class="me-3"></v-icon>
						<span v-text="item.text"></span>
					</li>
					<li
						v-for="(subitem, j) in [...item.children]"
						:class="{'active': menu.active == subitem.key }"
						:style="{'--bg-color': 'rgba(' + (255 - 5*(j+1)) + ',' + (255 - 5*(j+1)) + ',' + (255 - 5*(j+1)) + ',1)', 'z-index': item.children.length - j, 'padding-left': '50px', 'padding-right': '10px'}"
						@click.stop="menu.active = subitem.key"
					>
						<v-icon :icon="subitem.icon" class="me-3"></v-icon>
						<span v-text="subitem.text"></span>
						
					</li>
				</ul>
				
			</li>
			<li class="search-type-ul-close">
				<v-icon icon="mdi-close"></v-icon>
			</li>
		</ul>
	</div>
	<div class="search-type-ul-wrapper" v-if="-1 !== ['travelfuse-romania', 'travelfuse-strainatate', 'travelfuse-circuite'].indexOf(menu.active)">
		<ul class="search-type-ul search-type-ul-root mt-5 mb-5 align-center">
			<li v-for="j in [0]" class="pa-0"
				  :class="{'bg-info': imenu[j]}"
				  :style="{'z-index': 5, 'background-color': '#fff', flex: 2}">
				<v-menu
				  v-model="imenu[j]"
				  :close-on-content-click="false"
				  location="bottom"
				  class="rounded-xl search-type-ul-menu"
				>
				  <template v-slot:activator="{ props }">
					<v-text-field
						class="pt-2 pb-1 ps-15 pe-2"
						:label="'Destinatia ta'"
						placeholder="Unde calatoresti"
						persistent-placeholder
						hide-details
						variant="plain"
						v-bind="props"
						:value="presearch.destination.country && presearch.destination.city ? ((presearch.destination.country ? presearch.destination.country.Name : '') + ' ' + (presearch.destination.city ? presearch.destination.city.Name : '')) : ''"
						readonly
						:type="presearch.destination.country && presearch.destination.city ? 'hidden' : 'text'"
					>
						<slot name="default">
							<p v-if="presearch.destination.country && presearch.destination.city" class="d-flex flex-column flex-nowrap text-truncate" style="font-size: 12px;line-height: 1;">
								<strong v-if="menu.active != 'travelfuse-romania'" v-text="presearch.destination.country.Name"></strong>
								<span v-text="presearch.destination.city.Name"></span>
							</p>
						</slot>
					</v-text-field>
				  
				  </template>
				  <v-card min-width="300">
<v-window v-model="presearch.destination.search.step" direction="vertical">
<v-window-item :value="1">
					<v-text-field
						label="Cauta o destinatie"
						class="px-4"
						hide-details
						ref="destination_country_search"
						clearable
						variant="underlined"
						v-model="presearch.destination.search.country"
					></v-text-field>
				<v-list
					v-model:selected="presearch.destination.selected.country_ids"
					>
					<v-virtual-scroll
					  :height="300"
					  :item-height="48"
					  :items="presearch.destination.filtered.countries"
					>
					  <template v-slot:default="{ item }">
						  <v-list-item
							prepend-icon="mdi-map-marker"
							:value="item"
							:key="item.Code"
							:title="item.Name"
						  >
						  </v-list-item>
					  </template>
					</v-virtual-scroll>
					<v-card-actions>
					  <v-spacer></v-spacer>

					  <v-btn
						variant="text"
						@click="imenu[0] = false"
					  >
						Cancel
					  </v-btn>
					</v-card-actions>
				</v-list>
</v-window-item>
<v-window-item :value="2">
					<v-text-field
						label="Cauta o destinatie"
						ref="destination_city_search"
						class="px-4"
						hide-details
						variant="underlined"
						v-model="presearch.destination.search.city"
					></v-text-field>
				<v-list
					v-model:selected="presearch.destination.selected.city_ids"
					>
					<v-virtual-scroll
					  :height="300"
					  :item-height="48"
					  :items="presearch.destination.filtered.cities"
					>
					  <template v-slot:default="{ item }">
						  <v-list-item
							prepend-icon="mdi-map-marker"
							:value="item"
							:key="item.Code"
							:title="item.Name + ' (' + item.type + ')'"
						  >
						  </v-list-item>
					  </template>
					</v-virtual-scroll>
				</v-list>
				<v-card-actions>
					  <v-spacer></v-spacer>
					  <v-btn v-if="menu.active != 'travelfuse-romania'"
						variant="text"
						@click="presearch.destination.search.step = 1"
					  >
						Cancel
					  </v-btn>
					  <v-btn v-else
						variant="text"
						@click="imenu[0] = false"
					  >
						Cancel
					  </v-btn>
				</v-card-actions>
</v-window-item>
</v-window>

					
				  </v-card>
				</v-menu>
			</li>
			<li v-for="j in [1,2]"  class="pa-0"
				  :class="{'bg-info': imenu[j]}"
				  :style="{'z-index': 4 - j , 'background-color': '#fff'}" @click="imenu[j] = !imenu[j]">
				
				<v-menu
				  v-model="imenu[j]"
				  :close-on-content-click="false"
				  location="bottom"
				  class="rounded-xl search-type-ul-menu"
				>
				  <template v-slot:activator="{ props }" v-if="j==1">
					<v-text-field
						class="pt-2 pb-1 ps-15 pe-2"
						:label="'Data plecare'"
						placeholder="Selecteaza data"
						persistent-placeholder
						hide-details
						variant="plain"
						v-bind="props"
						readonly
						:type="presearch.origin.date ? 'hidden' : 'text'"
					>
						<slot name="default">
							<p v-if="presearch.origin.date" class="d-flex flex-column flex-nowrap text-truncate" style="font-size: 12px;line-height: 1;">
								<strong v-text="new Date(presearch.origin.date).toDateString()"></strong>
							</p>
						</slot>
					</v-text-field>
				  
				  </template>
				  <template v-slot:activator="{ props }" v-if="j==2">
					<v-text-field
						class="pt-2 pb-1 ps-15 pe-2"
						:label="'Data retur'"
						placeholder="Selecteaza data"
						persistent-placeholder
						hide-details
						variant="plain"
						v-bind="props"
						readonly
						:type="presearch.destination.date ? 'hidden' : 'text'"
					>
						<slot name="default">
							<p v-if="presearch.destination.date" class="d-flex flex-column flex-nowrap text-truncate" style="font-size: 12px;line-height: 1;">
								<strong v-text="new Date(presearch.destination.date).toDateString()"></strong>
							</p>
						</slot>
					</v-text-field>
				  
				  </template>
				  <v-card min-width="300">
					
					<v-date-picker v-if="j==1" show-adjacent-months
						hide-header
						title=""
						:min="new Date()"
						v-model="presearch.origin.selected.date"
					></v-date-picker>
					<v-date-picker v-if="j==2" show-adjacent-months
						hide-header
						title=""
						:min="(tomorrow = new Date()) && tomorrow.setDate(tomorrow.getDate() + 1) && tomorrow"
						v-model="presearch.destination.selected.date"
					></v-date-picker>

					<v-card-actions>
					  <v-spacer></v-spacer>

					  <v-btn
						variant="text"
						@click="imenu[j] = false"
					  >
						Cancel
					  </v-btn>
					</v-card-actions>
				  </v-card>
				</v-menu>
				
			</li>
			
			<li v-for="j in [3]" class="pa-0"
			<?php /* :class="{'bg-info': imenu[j]}" */ ?>
				  :style="{'z-index': 1, 'background-color': '#fff'}">
				<v-menu
				  v-model="imenu[j]"
				  :close-on-content-click="false"
				  location="bottom"
				  class="rounded-xl search-type-ul-menu"
				>
				  <template v-slot:activator="{ props }">
					<v-text-field
						class="pt-2 pb-1 ps-15 pe-2"
						:label="'Calatori'"
						placeholder="Adauga calator"
						persistent-placeholder
						hide-details
						variant="plain"
						v-bind="props"
						readonly
						:type="presearch.travellers.ADT ? 'hidden' : 'text'"
					>

						<template v-slot:default>
							<p v-if="presearch.travellers.ADT" class="d-flex flex-column flex-nowrap text-truncate" style="font-size: 12px;line-height: 1;">
								<div class="d-flex ga-3">
								<strong v-if="presearch.travellers.ADT == 1" v-text="presearch.travellers.ADT + ' Adult'"></strong>
								<template v-else-if="presearch.travellers.ADT">
								<strong v-text="presearch.travellers.ADT + ' Adulti'"></strong>
								<span v-if="presearch.rooms == 1" v-text="presearch.rooms + ' Camera'"></span>
								<span v-else-if="presearch.rooms" v-text="presearch.rooms + ' Camere'"></span>
								</template>
								</div>
								<div class="d-flex ga-3">
								<span v-if="presearch.travellers.CHD.length == 1" v-text="presearch.travellers.CHD.length + ' Copil'"></span>
								<span v-else-if="presearch.travellers.CHD.length" v-text="presearch.travellers.CHD.length + ' Copii'"></span>
								<span v-if="presearch.travellers.YTH == 1" v-text="presearch.travellers.YTH + ' Tanar'"></span>
								<span v-else-if="presearch.travellers.YTH" v-text="presearch.travellers.YTH + ' Tineri'"></span>
								</div>
							</p>
						</template>					
					</v-text-field>
				  
				  </template>
				  <v-card min-width="300">
					<?php /*
					<v-list-item
						subtitle="Maxim una per adult"
						title="Camere"
					>
						<template v-slot:append>
						  <v-btn
							icon="mdi-plus"
							color="success"
							variant="text"
							@click="presearch.rooms < presearch.travellers.ADT && (presearch.rooms++)"
						  ></v-btn>
						</template>
						<template v-slot:prepend>
						  <v-btn
							icon="mdi-minus"
							variant="text"
							color="error"
							@click="presearch.rooms > 1 && (presearch.rooms--)"
						  ></v-btn>
							<div class="text-h3 ps-2 pe-5" v-text="presearch.rooms"></div>
						</template>
					</v-list-item>
					*/ ?>
					
					<v-list>
					  <v-list-item
						subtitle="Peste 18 ani"
						title="Adulti"
					  >
						<template v-slot:append>
						  <v-btn
							icon="mdi-plus"
							color="success"
							variant="text"
							@click="presearch.travellers.ADT < 6 && (presearch.travellers.ADT++)"
						  ></v-btn>
						</template>
						<template v-slot:prepend>
						  <v-btn
							icon="mdi-minus"
							color="error"
							variant="text"
							@click="presearch.travellers.ADT > 1 && (presearch.travellers.ADT--)"
						  ></v-btn>
							<div class="text-h3 ps-2 pe-5" v-text="presearch.travellers.ADT"></div>
						</template>
					  </v-list-item>
					  
					  
					  <v-list-item
						subtitle="Intre 12 - 17 ani"
						title="Tineri"
					  >
						<template v-slot:append>
						  <v-btn
							icon="mdi-plus"
							color="success"
							variant="text"
							@click="presearch.travellers.YTH < 9 && (presearch.travellers.YTH++)"
						  ></v-btn>
						</template>
						<template v-slot:prepend>
						  <v-btn
							icon="mdi-minus"
							variant="text"
							color="error"
							@click="presearch.travellers.YTH >= 1 && (presearch.travellers.YTH--)"
						  ></v-btn>
							<div class="text-h3 ps-2 pe-5" v-text="presearch.travellers.YTH"></div>
						</template>
					  </v-list-item>
					  
					  <v-list-item
						subtitle="Sub 12 ani, maxim 4 copii"
						title="Copii"
					  >
						<template v-slot:append>
						  <v-btn
							icon="mdi-plus"
							color="success"
							variant="text"
							@click="presearch.travellers.CHD.length < 4 && (presearch.travellers.CHD.push(0))"
						  ></v-btn>
						</template>
						<template v-slot:prepend>
						  <v-btn
							icon="mdi-minus"
							variant="text"
							color="error"
							@click="presearch.travellers.CHD.length >= 1 && (presearch.travellers.CHD.pop())"
						  ></v-btn>
							<div class="text-h3 ps-2 pe-5" v-text="presearch.travellers.CHD.length"></div>
						</template>
					  </v-list-item>
					  
					  <v-list-item
						v-for="(age, child_index) in presearch.travellers.CHD"
						subtitle="Varsta implinita la plecare"
						:title="'Varsta copil ' + (child_index + 1)"
					  >
						<template v-slot:append>
						  <v-btn
							icon="mdi-plus"
							color="success"
							variant="text"
							@click="age < 11 && (presearch.travellers.CHD[child_index]++)"
						  ></v-btn>
						</template>
						<template v-slot:prepend>
						  <v-btn
							icon="mdi-minus"
							variant="text"
							color="error"
							@click="age >= 1 && (presearch.travellers.CHD[child_index]--)"
						  ></v-btn>
							<div class="text-h3 ps-2 pe-5" v-text="age"></div>
						</template>
					  </v-list-item>
					</v-list>


					<v-card-actions>
					  <v-spacer></v-spacer>

					  <v-btn
						variant="text"
						@click="imenu[j] = false"
					  >
						Cancel
					  </v-btn>
					</v-card-actions>
				  </v-card>
				</v-menu>
			</li>
			
			<li class="search-type-ul-close pt-5 pb-4 bg-info" :class="{'shown': presearch_valid}" @click="initiatePreSearch()">
				<v-icon v-if="presearch.searching" class="mdi-spin position-absolute" icon="mdi-loading" size="28" style="margin-left: -4px;margin-top: -6px;"></v-icon>
				<v-icon icon="mdi-magnify" size="22"></v-icon>
			</li>
		</ul>
	</div>
	`,
	beforeCreate() {
	},
	mounted() {
		let savedPreseach = getStorage('saved-presearch', '', {});
		// this.menu.active = savedPreseach.active_menu || 'travelfuse-romania';
		this.menu.active = savedPreseach.active_menu || '';
		this.presearch.rooms = savedPreseach.rooms || 1;
		this.presearch.travellers.ADT = savedPreseach.adt || 0;
		// this.presearch.origin.date = savedPreseach.startdate || '2024-08-15';
		this.presearch.origin.date = savedPreseach.startdate || null;
		// this.presearch.destination.date = savedPreseach.enddate || '2024-08-17';
		this.presearch.destination.date = savedPreseach.enddate || null;
		// this.presearch.destination.country = savedPreseach.destcountry || {"Name":"Romania","Id":126,"Code":"RO"};
		this.presearch.destination.country = savedPreseach.destcountry || null;
		/* this.presearch.destination.city = savedPreseach.destcity || {
			"Name": "Mamaia",
			"Id": 3333,
			"Providers": {
				"100005": {
					"Id": 100005,
					"Caption": "Paralela 45"
				},
				"100008": {
					"Id": 100008,
					"Caption": "CockTail Holidays V2"
				},
				"100002": {
					"Id": 100002,
					"Caption": "Karpaten"
				},
				"100003": {
					"Id": 100003,
					"Caption": "Holiday Office V2"
				}
			},
			"type": "city"
		}; */
		this.presearch.destination.city = savedPreseach.destcity || null;
		this.initiatePreSearch()
	},
	computed: {
		presearch_valid () {
			return this.presearch.destination.city &&
				this.presearch.origin.date &&
				this.presearch.destination.date &&
				this.presearch.rooms &&
				this.presearch.travellers.ADT && 
				true || false
		}
	},
	methods: {
		reloadDestinationCities: function(){
			var nv = this.presearch.destination.search.city || '';
			
			this.presearch.destination.filtered.cities = [];
			if(!nv){
				this.presearch.destination.filtered.cities = this.presearch.destination.cities || [];
			} else {
				var regexp = new RegExp(nv.replace(/[.*+?^${}()|[\]\\]/g, '\\$&').replace(/\s+/g, '.*'),'i');
				this.presearch.destination.filtered.cities = (this.presearch.destination.cities || []).filter(v => {
					return regexp.test(v.Name)
				});
			}
		},
		initiatePreSearch: function(){
			if(this.presearch.searching) return;
			
			let savedPreseach = {};
			savedPreseach.active_menu = this.menu.active;
			savedPreseach.adt = this.presearch.travellers.ADT;
			savedPreseach.rooms = this.presearch.rooms;
			savedPreseach.startdate = this.presearch.origin.date;
			savedPreseach.enddate = this.presearch.destination.date;
			savedPreseach.destcountry = this.presearch.destination.country;
			savedPreseach.destcountry = this.presearch.destination.country;
			savedPreseach.destcity = this.presearch.destination.city;
			
			saveStorage('saved-presearch', savedPreseach);
			
			this.presearch.searching = true;
			// setTimeout(() => {
				// this.presearch.searching = false;
			// },2000)
			try {
				var data = {
					CityCode: this.presearch.destination.city.Id,
					DestinationType: this.presearch.destination.city.type,
					CheckIn: this.presearch.origin.date,
					CheckOut: this.presearch.destination.date,
					Adults: [
						this.presearch.travellers.ADT + this.presearch.travellers.YTH,
					],
					ChildrenAge: [
						this.presearch.travellers.CHD,
					],
					<?php if ($this->config->item('csrf_protection')){ ?>
					<?php echo json_encode($this->security->get_csrf_token_name()); ?>: <?php echo json_encode($this->security->get_csrf_hash()); ?>,
					<?php } ?>
				};
				
				fetch(`${newux_url}/search/hotels?${append_url}`,{
					method: 'POST',
					headers: {
					  'Accept': 'application/json'
					},
					body: new URLSearchParams(objToSerialize(data))
				}).then((response) => {
					response.json().then((hotels) => {
						this.presearch.searching = false;
						this.results = Object.freeze(hotels);
					})
				})
			} catch(e){
				this.results = Object.freeze([]);
				this.presearch.searching = false;
			}
		}
	},
	watch: {
		'results': {
			handler: function(nv,ov){
				this.$emit('results', this.results);
			},
			immediate: true
		},
		'presearch.searching': {
			handler: function(nv,ov){
				this.$emit('searching', this.searching);
			},
			immediate: true
		},
		'presearch.destination.search.country': {
			handler: function(nv,ov){
				this.presearch.destination.filtered.countries = [];
				if(!nv){
					this.presearch.destination.filtered.countries = this.travelfuse.countries.filter(v => v.Code != 'RO') || [];
				} else {
					var regexp = new RegExp(nv.replace(/[.*+?^${}()|[\]\\]/g, '\\$&').replace(/\s+/g, '.*'),'i');
					this.presearch.destination.filtered.countries = (this.travelfuse.countries || []).filter(v => {
						return v.Code != 'RO' && regexp.test(v.Name)
					});
				}
			},
			immediate: true
		},
		'presearch.destination.search.city': {
			handler: function(nv,ov){
				this.reloadDestinationCities();
			},
			immediate: true
		},
		'imenu.0': {
			handler: function(nv,ov){
				if(!nv){
					this.presearch.destination.search.step = 0;
					this.presearch.destination.selected.country_ids = [];
					this.presearch.destination.selected.city_ids = [];
					this.presearch.destination.cities = [];
				} else {
					if(this.menu.active == 'travelfuse-romania'){
						this.presearch.destination.selected.country_ids = [{"Name":"Romania","Id":126,"Code":"RO"}];
					} else {
						this.presearch.destination.search.step = 1;
					}
				}
			},
			immediate: true
		},
		'imenu.3': {
			handler: function(nv,ov){
				if(nv && !this.presearch.travellers.ADT){
					this.presearch.travellers.ADT ++ ;
					this.presearch.rooms ++ ;
				}
			},
			immediate: true
		},
		'presearch.travellers.ADT': {
			handler: function(nv,ov){
				if(nv < this.presearch.rooms){
					this.presearch.rooms = nv;
				}
			},
			immediate: true
		},
		'presearch.destination.search.step': {
			handler: function(nv,ov){
				var ref;
				if(nv == 1){
					this.presearch.destination.filtered.cities = [];
				}
				setTimeout(() => {
					if(nv == 1){
						var ref = this.$refs.destination_country_search;
					} else if(nv == 2){
						var ref = this.$refs.destination_city_search;
					}
					if(ref){
						if(ref){
							const input = ref[0].$el.querySelector('input:not([type=hidden]),textarea:not([type=hidden])')
						  if (input) {
							  setTimeout(() => {
							  input.focus()
							  }, 100);
						  }
						}
					}
				}, 100);
				if(!nv){
					this.presearch.destination.selected.country_ids = [];
					this.presearch.destination.selected.city_ids = [];
					this.presearch.destination.cities = [];
				}
			},
			immediate: true
		},
		'presearch.destination.cities': {
			handler: function(nv,ov){
				this.presearch.destination.search.city = null;
				this.reloadDestinationCities();
			},
			immediate: true
		},
		'presearch.origin.selected.date': {
			handler: function(nv,ov){
				if(nv){
					this.imenu[1] = false;
					this.imenu[2] = true;
					var d = new Date(nv.getTime());
					this.presearch.origin.date = new Date(nv.getTime() - d.getTimezoneOffset() * 60000).toISOString().replace(/T.*/,'');
					
					
					if(this.presearch.destination.date && this.presearch.origin.date >= this.presearch.destination.date){
						this.presearch.destination.date = null;
					}
				}
			},
			immediate: true
		},
		'presearch.destination.selected.date': {
			handler: function(nv,ov){
				if(nv){
					this.imenu[2] = false;
					var d = new Date(nv.getTime());
					this.presearch.destination.date = new Date(nv.getTime() - d.getTimezoneOffset() * 60000).toISOString().replace(/T.*/,'');
					
					if(this.presearch.origin.date && this.presearch.destination.date <= this.presearch.origin.date){
						this.presearch.origin.date = null;
						this.imenu[1] = true;
					} else {
						this.imenu[3] = true;
					}
				}
			},
			immediate: true
		},
		'presearch.destination.selected.country_ids': {
			handler: function(nv,ov){
				this.presearch.destination.filtered.cities = [];
				if(nv && nv.length){
					this.presearch.destination.search.step = 2;
				}
				
				if(nv && nv.length){
					this.presearch.destination.cities = null;
					try {
						fetch(`${newux_url}/nomenclator/cities.json?${append_url}&country_id=${nv[0].Id}`).then((response) => {
							response.json().then((cities) => {
								if(Array.isArray(cities)){
									this.presearch.destination.cities = Object.freeze(cities);
								} else {
									this.presearch.destination.cities = Object.freeze([]);
								}
							})
						})
					} catch(e){
						console.error(e);
						this.presearch.destination.cities = Object.freeze([]);
					}
				}
			},
			immediate: true
		},
		'presearch.destination.selected.city_ids': {
			handler: function(nv,ov){
				if(nv && nv.length){
					this.imenu[0] = false;
					this.presearch.destination.country = this.presearch.destination.selected.country_ids[0];
					this.presearch.destination.city = this.presearch.destination.selected.city_ids[0];
					
					this.imenu[1] = true;
				}
			},
			immediate: true
		},
		'menu.active': {
			handler: function(nv,ov){
				this.imenu[0] = false;
				this.imenu[1] = false;
				this.imenu[2] = false;
				this.imenu[3] = false;
				if(-1 !== ['travelfuse-romania','travelfuse-strainatate'].indexOf(nv)){
					setTimeout(() => {
						if(-1 !== ['travelfuse-romania','travelfuse-strainatate'].indexOf(ov)){
							this.presearch.destination.country = null;
						}
						if(!this.results.length)
						this.imenu[0] = true;
					},100)
				}
			},
			immediate: true
		}
	}
}
