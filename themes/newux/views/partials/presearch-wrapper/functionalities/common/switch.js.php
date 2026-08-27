import BaseFunctionality from '../base-functionality.js?newux=<?php echo !empty($_GET['newux']) ? (string)$_GET['newux'] : 1 ; ?>';
import Loading from './loading.js?newux=<?php echo !empty($_GET['newux']) ? (string)$_GET['newux'] : 1 ; ?>';
export default {
	name: <?php echo json_encode($a, JSON_UNESCAPED_SLASHES); ?>,
	extends: BaseFunctionality,
	emits: ['activate-menu', 'set-value', 'click-selected'],
	props: {
		data: {
			type: Object,
			default: () => ({}),
		},
		active_menu: {
			type: String,
			default: () => (undefined),
		},
		functionalities: {
			type: Array,
			default: () => ([]),
		},
		functionality_index: {
			type: Number,
			default: () => (-1),
		},
		search_wrapper_step: {
		  type: Number,
		  default: () => (0),
		},
	},
	components:{
		'Loading': Loading,
	},
	data(){
		return {
			abortController: new AbortController(),
			disable_selected: false,
			afterExistCheckTimer: undefined,
			afterExistCheck: false,
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
			internvalue: '',
			fetch_url: '',
			search_city: '',
			selected_city_ids: this.data['<?php echo basename(dirname(__FILE__)); ?>/<?php echo basename(__FILE__, '.js.php'); ?>'] && [this.data['<?php echo basename(dirname(__FILE__)); ?>/<?php echo basename(__FILE__, '.js.php'); ?>'] || {}] || undefined,
			cities: this.markRaw([]),
		}
	},
	computed: {
		city() {
			var city = this.selected_city_ids && this.selected_city_ids.length && (this.cities || this.selected_city_ids || []).find(v => v.Id == this.selected_city_ids[0].Id && v.type == this.selected_city_ids[0].type);
			return city;
		},
		filtered_cities() {
			return !this.search_city && this.cities || [];
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
			if(undefined !== def){
				var city = (this.cities || []).find(v => '' + this.listKey(v) === '' + def);
				if(city){
					this.selected_city_ids = [city];
					// console.log('FOUND');
				} else {
					// console.log('DID NOT FIND');
				}
				// console.log(this.cities, this.listKey);
			}
		});
	},
	watch: {
		'active_menu': {
			handler: function(nv,ov){
				clearTimeout(this.afterExistCheckTimer); 
				this.afterExistCheckTimer = setTimeout(() => {
					this.afterExistCheck = !!document.getElementById('after-' + this.key_path);
				}, 100)
				// console.warn('active_menu', nv, this);
			},
			immediate: true
		},
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
				if(this.content_type == 'menu'){
				// console.warn('selected_city_ids', nv);
				this.internvalue = ((nv||[])[0] || {}).Id || '';
				// console.warn('internvalue', this.internvalue);
				}
			},
			immediate: true
		},
		'internvalue': {
			handler: function(nv,ov){
				if(this.content_type == 'menu'){
					if(!nv){
						if(this.disable_selected && this.cities.length){
							this.internvalue = this.cities[0].Id;
							return;
						}
					}
					this.selected_city_ids = (this.cities || []).filter(c => c.Id == nv);
				}
			},
			// immediate: true
		},
		'cities': {
			handler: function(nv,ov){
				this.loading = true;
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
		listKey: function(item){
			return [item.Id].join(',');
		},
		listTitle: function(item){
			return item.Name;
		},
		clickSelected: function(what){
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
				(this.loader || (this.loader = fetch(this.fetch_url, {
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
				}).then((response) => response.json()))).then((cities) => {
					if(Array.isArray(cities)){
						this.cities = this.markRaw(cities);
						if(success_callback && 'function' === typeof success_callback) success_callback(this)
					} else {
						throw "Expected array";
					}
				}).catch((e) => {
					console.error("Failed to fetch destination cities", e);
					this.cities = this.markRaw([]);
					// Do nothing
				})
			}, 100)
		}
	},
	template : `
<template  v-if="this.content_type == 'menu'">
<teleport v-if="afterExistCheck" :to="'#after-' + key_path">
<div class="d-table mx-auto" :class="['switches-' + key.replace(/.*\\//, '')]" :id="'menu-' + key" :style="{order:functionality_index}" @click="disabled && ((data.step --) || 1)">
<div class="d-flex flex-wrap">
<v-switch v-for="city in cities" :disabled="disabled"
	class="ms-2"
  v-model="internvalue"
  color="primary"
  :label="listTitle(city)"
  :value="listKey(city)"
  hide-details
  density="compact"
></v-switch>
</div>
</div>
</teleport>
</template>
	`,
}
