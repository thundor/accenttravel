import BaseFunctionality from '../base-functionality.js?newux=<?php echo !empty($_GET['newux']) ? (string)$_GET['newux'] : 1 ; ?>';
import DatePickerSelect from '../../../form/datepicker-select.js?newux=<?php echo !empty($_GET['newux']) ? (string)$_GET['newux'] : 1 ; ?>';
import CustomDatePicker from '../../../form/datepicker.js?newux=<?php echo !empty($_GET['newux']) ? (string)$_GET['newux'] : 1 ; ?>';
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
		'DatePickerSelect': DatePickerSelect,
		'CustomDatePicker': CustomDatePicker,
	},
	data(){
		return {
			autoSelectFirst: false,
			abortController: new AbortController(),
			prevent_click_selected: false,
			search_timer: undefined,
			searched_cities: undefined,
			max_date: undefined,
			min_date: undefined,
			force_min_date: undefined,
			diffdate: undefined,
			years: 2,
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
			saved_selected_date: undefined,
			selected_date: undefined,
			selected_date_date: undefined,
			selected_city_ids: this.data['<?php echo basename(dirname(__FILE__)); ?>/<?php echo basename(__FILE__, '.js.php'); ?>'] && [this.data['<?php echo basename(dirname(__FILE__)); ?>/<?php echo basename(__FILE__, '.js.php'); ?>'] || {}] || [],
			fuse: undefined,
			cities: this.markRaw([]),
		}
	},
	computed: {
		allowedDatesList() {
			return this.fetch_url && (this.cities || []).map(v => v.Id);
		},
		minimum_date() {
			return this.force_min_date || this.min_date || undefined;
		},
		maximum_date() {
			return this.max_date || undefined;
		},
		city() {
			return this.selected_date_date && ({...(this.cities && this.cities.find((v) => v.Id == this.selected_date) || {}), ...{
				Id: this.selected_date,
				Name: this.presentation(this.selected_date_date),
			}});
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
	created() {
	},
	mounted() {
		if(this.content_type != 'menu'){
			return;
		}
		var selected_date = this.getObjectDotPathValue(this.data?.defaults, this.key.replace(/\//g, '.'));
		if(undefined !== selected_date){
			this.selected_date = selected_date;
			// console.log(this.key, this.selected_date, this.data?.defaults);
		}
		var curdate = new Date();
		this.min_date = new Date(Date.UTC(curdate.getFullYear(), curdate.getMonth(), curdate.getDate()));
		this.min_date.setDate(this.min_date.getDate() + 1);
		this.min_date = this.min_date.toISOString().replace(/T.*/,'');
		
		this.max_date = new Date(Date.UTC(curdate.getFullYear() + this.years, curdate.getMonth(), curdate.getDate())).toISOString().replace(/T.*/,'');
		if(this.content_type == 'menu'){
			this.loadCities();
		}
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
		'allowedDatesList': {
			handler: function(nv,ov){
				if(this.content_type != 'menu'){
					return;
				}
				if(nv){
					if(this.selected_date){
						if(-1 === nv.indexOf(this.selected_date)){
							// console.log('this.saved_selected_date', this.selected_date);
							this.saved_selected_date = '' + this.selected_date;
							this.selected_date = null;
							this.setValue(null);
						}
					} else {
						// console.warn('this.saved_selected_date', this.saved_selected_date);
						if(this.saved_selected_date && -1 !== nv.indexOf(this.saved_selected_date)){
							// console.error('this.saved_selected_date', this.saved_selected_date, this.cities);
							// this.saved_selected_date = null;
							// this.selected_date = this.saved_selected_date;
							
							
							this.selected_city_ids = [{...(this.cities && this.cities.find((v) => v.Id == this.saved_selected_date) || {}), ...{
								Name: this.presentation(new Date(this.saved_selected_date)),
							}}];
							
							this.setValue(this.selected_city_ids[0]);
						}
					}
				}
			},
			immediate: true
		},
		'selected_date': {
			handler: function(nv,ov){
				if(nv === ov) return;
				if(false && this.content_type != 'menu'){
					return;
				}
				// console.warn('selected_date', this.$options.name, nv, JSON.parse(JSON.stringify(this.data)));
				if(!nv){
					if(this.selected_date_date)
						this.selected_date_date = null;
					if(this.selected_city_ids && this.selected_city_ids.length)
						this.selected_city_ids = null;
				} else {
					var changed_date = false;
					if(!this.selected_date_date || (new Date(Date.UTC(this.selected_date_date.getFullYear(), this.selected_date_date.getMonth(), this.selected_date_date.getDate()))).toISOString().replace(/T.*/,'') != nv){
						changed_date = !!this.selected_date_date;
						var date = new Date(nv);
						var d = new Date(Date.UTC(date.getFullYear(), date.getMonth(), date.getDate()));
						this.selected_date_date = new Date(Date.UTC(date.getFullYear(), date.getMonth(), date.getDate()) + (d.getTimezoneOffset() * 60000));
					}
					if(!(this.selected_city_ids && this.selected_city_ids.length && this.selected_city_ids[0] && this.selected_city_ids[0].Id == nv)){
						this.selected_city_ids = [{...(this.cities && this.cities.find((v) => v.Id == this.selected_date) || {}), ...{
							Id: nv,
							Name: this.presentation(this.selected_date_date),
					}}];
						//console.warn('checking', this.selected_city_ids);
						if(changed_date){
							this.prevent_click_selected = true;
						}
						this.setValue(this.selected_city_ids[0]);
						this.clickSelected({id: this.selected_city_ids[0], path: this.selected_city_ids, value: true});
					}
				}
			},
			immediate: true
		},
		'selected_city_ids': {
			handler: function(nv,ov){
				if(nv === ov) return;
				if(false && this.content_type != 'menu'){
					return;
				}
				if(!nv || !nv.length){
					if(this.selected_date)
						this.selected_date = null;
				} else {
					try{
						var date = new Date(nv[0].Id);
						if(isNaN(date) || ((new Date(Date.UTC(date.getFullYear(), date.getMonth(), date.getDate()))).toISOString().replace(/T.*/,'') != nv[0].Id)){
							throw 'Invalid date';
						}
						if(!this.selected_date || nv[0].Id != this.selected_date){
							this.selected_date = nv[0].Id;
						}
					} catch(e){
						this.selected_date = null;
					}
				}
			},
			immediate: true,
		},
		'selected_date_date': {
			handler: function(nv,ov){
				if(nv === ov) return;
				if(false && this.content_type != 'menu'){
					return;
				}
				if(!nv){
					if(this.selected_date)
						this.selected_date = null;
				} else {
					if(!this.selected_date || (new Date(Date.UTC(nv.getFullYear(), nv.getMonth(), nv.getDate()))).toISOString().replace(/T.*/,'') != this.selected_date){
						this.selected_date = (new Date(Date.UTC(nv.getFullYear(), nv.getMonth(), nv.getDate()))).toISOString().replace(/T.*/,'');
					}
				}
			},
			immediate: true
		},
		'search_city': {
			handler: function(nv,ov){
				if(!nv || !this.cities || !this.cities.length || !this.fuse){
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
		'cities': {
			handler: function(nv,ov){
				this.loading = true;
				this.fuse = nv && this.markRaw(new Fuse(nv, {
					includeScore: true,
					keys: ['Name', 'Country'],
				})) || null;
				setTimeout(() => {
					this.loading = this.cities ? false : true;
				}, 1000)
			},
			immediate: true
		},
	},
	methods: {
		presentation: function(date){
			return date && date.toLocaleDateString('ro-RO', {
				weekday: 'short',
				year: 'numeric',
				month: 'short',
				day: 'numeric',
			}) || ''
		},
		clickSelected: function(what){
			if(this.prevent_click_selected){
				this.prevent_click_selected = false;
				return;
			}
			this.$emit('click-selected',this,what);
		},
		loadCities: function(success_callback){
			if(!this.fetch_url) return;
			if(this.content_type != 'menu'){
				return;
			}
			// console.warn('loadCities');
			clearTimeout(this.loadtimer);
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
						// console.error('date-selector-cities', this.cities);
						if(success_callback && 'function' === typeof success_callback) success_callback(this)
						else {
							if(this.autoSelectFirst){
								this.selected_city_ids = [cities[0]];
								this.setValue(this.selected_city_ids[0]);
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
<v-menu ref="menu"
	  v-model="opened"
	  :close-on-content-click="false"
	  location="bottom"
	  class="rounded-xl search-type-ul-menu"
	  :disabled="disabled"
	   @click="disabled && (data.step --)"
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
			:value="presentation(selected_date_date)"
			readonly
			:type="city ? 'hidden' : 'text'"
			:id="'menu-' + key"
			@click="disabled && (data.step --)"
		>
			<slot name="default">
				<div v-if="selected_date_date" class="d-flex flex-column flex-nowrap text-truncate" style="font-size: 12px;line-height: 1;">
					<span v-text="presentation(selected_date_date)"></span>
					<template v-if="diffdate" v-for="nopti in [Math.floor(Math.abs(selected_date_date - diffdate) / (1000 * 60 * 60 * 24))]">
					<span v-if="1 == nopti" v-text="'1 noapte'"></span>
					<span v-else v-text="nopti + ' nopti'"></span>
					</template>
				</div>
			</slot>
		</v-text-field>
	  
	</template>
	<v-card min-width="390">
		<div class="d-flex justify-center align-center flex-column pt-4">
		<?php /* <DatePickerSelect ref="datepickerselect" v-model="selected_date" hide-details :min-date="minimum_date" :max-date="maximum_date"></DatePickerSelect> */ ?>
		<?php /* <v-text-field autofocus
			:label="menu.search_label"
			ref="search"
			class="px-4"
			hide-details
			clearable
			variant="underlined"
			v-model="search_city"
		></v-text-field> */ ?>
		<Loading class="mt-5" v-if="loading" style="position: absolute;top: 0;bottom: 0;right: 0;left: 0;margin: 0 !important;display: flex;flex-direction: column;justify-content: center;background: rgba(255, 255, 255, 0.7);z-index: 1;"></Loading>
		<CustomDatePicker ref="vdatepicker" v-model="selected_date_date" :allowed-dates-list="allowedDatesList || undefined" :min="minimum_date" :max="maximum_date" hide-header title="" landscape first-day-of-week="1" show-adjacent-months></CustomDatePicker>
		</div>
		<?php /* <v-list v-else
			v-model:selected="selected_city_ids"
			@click:select="clickSelected"
			>
		</v-list> */ ?>
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
