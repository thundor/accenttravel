import BaseFunctionality from '../base-functionality.js?newux=<?php echo !empty($_GET['newux']) ? (string)$_GET['newux'] : 1 ; ?>';
import Fuse from 'https://cdn.jsdelivr.net/npm/fuse.js@7.0.0/dist/fuse.mjs'
import Loading from './loading.js?newux=<?php echo !empty($_GET['newux']) ? (string)$_GET['newux'] : 1 ; ?>';
export default {
	name: <?php echo json_encode($a, JSON_UNESCAPED_SLASHES); ?>,
	extends: BaseFunctionality,
	props: {
		data: {
			type: Object,
			default: () => ({}),
		},
	},
	components:{
		'Loading': Loading,
	},
	data(){
		return {
			autoSelectFirst: false,
			abortController: new AbortController(),
			fuse_keys: ['alias', 'Name', 'Country'],
			min_search: 0,
			search_timer: undefined,
			searched_cities: undefined,
			hash: undefined,
			loader: undefined,
			loadtimer: undefined,
			loading: false,
			key: '<?php echo basename(dirname(__FILE__)) . '/' . basename(__FILE__, '.js.php'); ?>',
			menu: {
				title: 'Destinatie',
				placeholder: 'Unde pleci',
				search_label: 'Cauta destinatia',
				icon: 'mdi-flag-variant',
			},
			opened: false,
			fetch_url: '',
			search_city: '',
			selected_city_ids: this.data['<?php echo basename(dirname(__FILE__)); ?>/<?php echo basename(__FILE__, '.js.php'); ?>'] && [this.data['<?php echo basename(dirname(__FILE__)); ?>/<?php echo basename(__FILE__, '.js.php'); ?>'] || {}] || undefined,
			fuse: undefined,
			cities: this.markRaw([]),
		}
	},
	computed: {
		city() {
			var city = this.selected_city_ids && this.selected_city_ids.length && (this.cities || this.selected_city_ids || []).find(v => this.listKey(v) == this.listKey(this.selected_city_ids[0]) && v.type == this.selected_city_ids[0].type);
			return city;
		},
		filtered_cities() {
			return this.searched_cities || this.cities || [];
		},
		fetch_data() {
			return {	}
		},
	},
	beforeUnmount() {
		this.abortController.abort();
	},
	mounted() {
		if(this.content_type != 'menu'){
			return;
		}
		var def = this.getObjectDotPathValue(this.data?.defaults, this.key.replace(/\//g, '.'));
		// console.log(this.key, def, this.data?.defaults);
		if(undefined !== def){
			this.selected_city_ids = undefined;
		}
		this.loadCities(() => {
			if(undefined !== def || this.autoSelectFirst){
				var nv = this.cities;
				this.fuse = nv && this.markRaw(new Fuse(nv, {
					includeScore: true,
					keys: this.fuse_keys,
				})) || null;
				// console.warn('this.fuse', this.fuse);
				var city = undefined !== def && this.cities && this.fuse && this.fuse.search('' + def).map(i => ({...i, ...i.item}))[0] || this.cities[0];
				// console.log(this.$options.name, def, this.autoSelectFirst, city, (this.cities || []).length);
				if(city){
					this.selected_city_ids = [city];
					if(undefined === def){
						// console.log('FOUND', city);
					}
				} else {
					// console.log('DID NOT FIND');
				}
				// console.log(this.cities, this.listKey);
			}
		});
	},
	watch: {
		
		/* 'search_wrapper_step': {
			handler: function(nv,ov){
				console.warn('list-select search_wrapper_step', nv, this);
			},
			immediate: true
		}, */
		'loading': {
			handler: function(nv,ov){
				// console.warn('loading', nv);
			},
			immediate: true
		},
		'search_city': {
			handler: function(nv,ov){
				if(this.min_search && (!nv || nv.trim().length < this.min_search)) return;
				if(!nv || !this.cities || !this.cities.length || !this.fuse){
					this.searched_cities = null;
					return;
				}
				this.loading = true;
				clearTimeout(this.search_timer);
				this.search_timer = setTimeout(() => {
					this.searched_cities = this.fuse.search(nv).map(i => ({...i, ...i.item})) || [];
					this.loading = false;
				}, 500);
			},
			immediate: true
		},
		/* 'search_wrapper_step': {
			handler: function(nv,ov){
				console.warn('search_wrapper_step', nv);
			},
			immediate: true
		}, */
		'opened': {
			handler: function(nv,ov){
				// console.warn('opened', nv, this.$refs.search);
			},
			immediate: true
		},
		'selected_city_ids': {
			handler: function(nv,ov){
				// console.warn('selected_city_ids', nv);
			},
			immediate: true
		},
		'cities': {
			handler: function(nv,ov){
				this.loading = true;
				this.fuse = nv && this.markRaw(new Fuse(nv, {
					includeScore: true,
					keys: this.fuse_keys,
				})) || null;
				setTimeout(() => {
					this.loading = this.cities ? false : true;
				}, 1000)
			},
			immediate: true
		},
		'city': {
			handler: function(nv,ov){
				// console.log('should set value', [this.key, nv]);
				this.setValue(nv);
				if(nv){
					// this.opened = false;
				}
			},
			immediate: true
		},
	},
	methods: {
		listDefaultKey: function(item){
			return this.listKey(item);
		},
		listKey: function(item){
			return [item.Id].join(',');
		},
		listTitle: function(item){
			return item.Name + ( item.type ? ' (' + item.type + ')' : '') + ( item.Country ? ' '+item.Country : '');
		},
		presentation: function(city){
			return city && city.Name || '';
		},
		clickSelected: function(what){
			console.warn('clickSelected', what);
			this.$emit('click-selected',this,what);
		},
		loadCities: function(success_callback){
			if(this.content_type != 'menu'){
				return;
			}
			this.loadtimer = setTimeout(() => {
				this.cities = null;
				clearTimeout(this.loadtimer);
				if(!this.fetch_data){
					this.cities = this.markRaw([]);
					return;
				}
				var hash = CryptoJS.MD5(JSON.stringify(this.fetch_data)).toString();
				
				if(hash != this.hash){
					this.loader = undefined;
					this.hash = hash;
				}
				var data = {
					<?php if ($this->config->item('csrf_protection')){ ?>
					<?php echo json_encode($this->security->get_csrf_token_name()); ?>: <?php echo json_encode($this->security->get_csrf_hash()); ?>,
					<?php } ?>
					
					... this.fetch_data
				};
				(this.loader || (this.loader = Promise.all((Array.isArray(this.fetch_url) && this.fetch_url || [this.fetch_url]).map(fetch_url => fetch(fetch_url, {
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
				}))).then(responses => {
					// Convert responses to JSON (or process as needed)
					return Promise.all(responses.map(response => response.json()));
				}).then((responses) => responses.flat()))).then((cities) => {
					// console.warn('cities', cities);
					if(Array.isArray(cities)){
						this.cities = this.markRaw(cities);
						if(success_callback && 'function' === typeof success_callback) success_callback(this)
						else {
							var def = this.getObjectDotPathValue(this.data?.defaults, this.key.replace(/\//g, '.'));
							
							if(undefined !== def || this.autoSelectFirst){
								var nv = this.cities;
								this.fuse = nv && this.markRaw(new Fuse(nv, {
									includeScore: true,
									keys: this.fuse_keys,
								})) || null;
								var city = undefined !== def && this.cities && this.fuse.search('' + def).map(i => ({...i, ...i.item}))[0] || this.cities[0];
								// console.log(this.$options.name, def, this.autoSelectFirst, city, (this.cities || []).length);
								if(city){
									this.selected_city_ids = [city];
								}
							}
						}
					} else {
						throw "Expected array";
					}
				}).catch((e) => {
					if(e && ('object' === typeof e) && e.name === 'AbortError') return;
					console.error("Failed to fetch destination cities", e, typeof e);
					this.cities = this.markRaw([]);
					// Do nothing
				})
			}, 100)
		}
	},
	template : `
<component :is="loadViewAsync('partials/presearch-wrapper/functionality')" :active_menu="active_menu" class="pa-0" :disabled="disabled" :search_wrapper_step="search_wrapper_step" :data="data">
<template v-slot:menu>
<v-menu
	  v-model="opened"
	  :close-on-content-click="false"
	  location="bottom"
	  class="rounded-xl search-type-ul-menu"
	  :disabled="disabled"
	>
	<template v-slot:activator="{ props }">
		<v-text-field
			:loading="loading"
			class="pt-2 pb-1 ps-15 pe-2"
			:class="{['menu-' + key + '-wrapper']: 1}"
			:label="menu.title"
			:placeholder="menu.placeholder"
			persistent-placeholder
			hide-details
			variant="plain"
			v-bind="props"
			:value="presentation(city)"
			readonly
			:type="city ? 'hidden' : 'text'"
			:id="'menu-' + key"
			@click="disabled && ((data.step --) || 1)"
		>
			<slot name="default">
				<div v-if="city" class="d-flex flex-column flex-nowrap text-truncate" style="font-size: 12px;line-height: 1;">
					<span v-text="presentation(city)"></span>
				</div>
			</slot>
		</v-text-field>
	  
	</template>
	<v-card min-width="300">
		<v-text-field autofocus
			:label="menu.search_label"
			ref="search"
			class="px-4"
			hide-details
			clearable
			variant="underlined"
			v-model="search_city"
		></v-text-field>
		<Loading class="mt-5" v-if="loading"></Loading>
		<p v-else-if="min_search && (!search_city || search_city.trim().length < min_search)" class="mt-5 text-center">Introduceti minim 3 litere</p>
		<v-list v-else
			v-model:selected="selected_city_ids" mandatory
			@click:select="clickSelected"
			>
			<v-virtual-scroll
			  :height="300"
			  :item-height="48"
			  :items="filtered_cities"
			>
			  <template v-slot:default="{ item }">
					<v-list-item
						prepend-icon="mdi-map-marker"
						:value="item"
						:key="listKey(item)"
						:title="listTitle(item)"
					>
					</v-list-item>
			  </template>
			</v-virtual-scroll>
		</v-list>
		<v-card-actions>
			<v-spacer></v-spacer>
			<v-btn
				variant="text"
				@click="opened = false"
			>
				Inchide
			</v-btn>
		</v-card-actions>
	</v-card>
</v-menu>
</template>
		<template v-slot:default>
			Oras plecare
		</template>
		</component>
	`,
}
