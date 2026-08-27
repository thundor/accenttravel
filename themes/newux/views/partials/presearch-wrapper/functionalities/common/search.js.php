import merch_type from '../travelfuse/merch_type.json.js?newux=<?php echo !empty($_GET['newux']) ? (string)$_GET['newux'] : 1 ; ?>';
import { reactive } from 'vue';
export default {
	name: <?php echo json_encode($a, JSON_UNESCAPED_SLASHES); ?>,
	emits: ['set-value', 'click-selected', 'offer'],
	props: {
		data: {
		  type: Object,
		  default: () => ({}),
		},
		key_path: {
			type: String,
			default: () => (''),
		},
		show_breadcrumbs: {
			type: Boolean,
			default: () => (true),
		},
		select_first: {
			type: Boolean,
			default: () => (false),
		},
		extra_price: {
			type: Number,
			default: () => (0),
		},
		defaults: {
		  type: Object,
		  default: () => ({}),
		},
	},
	data: () => {
		return {
			drawer: null,
			prevent_initiate: false,
			use_inspection: false,
			abortController: new AbortController(),
			research_hash: null,
			init_research: false,
			list_variable: undefined,
			resultsTimeout: undefined,
			inspectTimeout: undefined,
			summary_retries: 0,
			inspect_retries: 0,
			inspections: [],
			inspection_completes: {},
			offer: undefined,
			sorted: undefined,
			applied_filters: {},
			sorted_results: undefined,
			formatted_results: undefined,
			filtered_results: undefined,
			cities: null,
			searching: null,
			fetch_url: '',
			initiate_url: '',
			inspect_url: '',
			summary_url: '',
			filter_component: 'partials/presearch-wrapper/functionalities/common/filters',
			results_component: 'partials/presearch-wrapper/functionalities/common/results',
			offer_component: 'partials/presearch-wrapper/functionalities/common/offer',
			loading_component: 'partials/presearch-wrapper/functionalities/common/loading',
			checkout_component: 'partials/presearch-wrapper/functionalities/common/checkout',
		}
	},
	computed: {
		breadcrumbs() {
			return [
				{title: 'Acasa', step: 0},
			];
		},
		fetch_data() {
			return {	}
		},
		inspection() {
			if(this.use_inspection){
				for(var index in this.inspection_completes){
					return this.inspection_completes[index];
				}
				if(this.searching){
					return {status: 2, research_hash: this.research_hash};
				}
			}
		},
		full_search_data() {
			return this.fetch_data;
		},
		country() {}
	},
	template : `
	<v-container class="pa-0">
	<div class="results-filter-above px-4" v-if="show_breadcrumbs">
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
	<v-row class="results-filter-wrapper mx-0">
		<template v-if="null === cities">
			<v-col>
				<component :is="loadViewAsync(loading_component)"></component>
			</v-col>
		</template>
		<template v-else>
		
		<component is="style" v-if="data.step == 1">
		.v-card,
		.v-window,
		.v-layout{
			overflow: initial !important;
		}
		</component>
		<v-layout style="min-height:500px;" class="custom-layout">
		<v-navigation-drawer v-model="drawer" sticky mobile-breakpoint="md" :absolute="false" floating class="bg-transparent custom-navigation-drawer" elevation="0">
			<component :is="loadViewAsync(filter_component)" :results="formatted_results && formatted_results.length && formatted_results || inspection || []" :searching="searching" v-on:filtered="(r) => filtered_results = r" :applied_filters="applied_filters" v-on:applied="(r) => applied_filters = r" :data="data" v-on:hash="researchHash" v-on:research="research"></component>
		</v-navigation-drawer>
		<v-main class="">
			<component :is="loadViewAsync(results_component)" :search_data="full_search_data" :results="filtered_results || inspection || []" :applied_filters="applied_filters" :sorted="sorted" :searching="searching" :select_first="select_first" :extra_price="extra_price" v-on:offer="(r,l) => r && setOffer(r, l)" :data="data" v-on:hash="researchHash" v-on:research="research" v-on:sorted_results="(r) => sorted_results = r" v-on:sorted="(r) => sorted = r"></component>
		</v-main>
			<div class="text-center">
			<v-btn @click="((drawer = !drawer), clickedDrawer())" color="primary" class="position-sticky d-md-none" style="z-index:1; bottom: 80px;" :style="{opacity: drawer ? 0 : 1}">
				<v-icon>mdi-format-align-left</v-icon> Filtrare rezultate
			</v-btn>
			</div>
		</v-layout>
		</template>
	</v-row>
	</v-container>
	<teleport to="#search-wrapper-item-content" v-if="!select_first && offer">
		<component :is="loadViewAsync(offer_component)" :offer="offer" :inspection="inspection" :searching="searching" :prepend_breadcrumbs="breadcrumbs" v-on:hash="researchHash" v-on:research="research"
		 v-on:offer="(r) => r && setOffer(r)"
		:results="(sorted_results && sorted_results.length ? sorted_results : formatted_results) || inspection || []" :applied_filters="applied_filters" v-on:set-value="(r) => ($emit('set-value', r))" :search_data="full_search_data" :set_checkout_component="checkout_component" :data="data" :search_wrapper_step="data.step" ></component>
	</teleport>
	`,
	beforeCreate() {},
	unmounted() {
		clearTimeout(this.inspectTimeout);
	},
	created() {
		if(this.$props.defaults){
			this.data.defaults = this.data.defaults || {};
			Object.assign(this.data.defaults, this.$props.defaults);
		}
	},
	beforeUnmount() {
		this.abortController.abort();
	},
	mounted() {
		// this.applied_filters = {};
		// this.research_hash = null;
		this.initiate()
	},
	/* computed: {
		breadcrumbs() {
			return [
				{title: 'Acasa'},
			];
		},
	}, */
	methods: {
		clickedDrawer(){
			scrollElemIntoView(document.querySelector('.custom-navigation-drawer'), {block: 'start'});
		},
		initiate(){
			if(this.prevent_initiate) return;
			if(this.searching) return;
			this.searching = true;
			this.inspection_completes = {};
			var init_research = this.init_research;
			this.init_research = false;
			var objs = this.fetch_data;
			console.warn(objs, this.data);
			this.filtered_results = undefined;
			if(this.initiate_url){
				if(!Array.isArray(objs)){
					objs = [objs];
				}
				
				this.cities = null;
				var fetches = [];
				objs.forEach(obj => {
					var data = {
						<?php if ($this->config->item('csrf_protection')){ ?>
						<?php echo json_encode($this->security->get_csrf_token_name()); ?>: <?php echo json_encode($this->security->get_csrf_hash()); ?>,
						<?php } ?>
						... obj
					};
					var f = fetch(this.initiate_url, {
						signal: this.abortController.signal,
						method: 'POST',
						headers: {
							'Init-Research': init_research ? 1 : 0,
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
					});
					fetches.push(f);
				});
				return Promise.all(fetches).then(responses => {
					// Convert responses to JSON (or process as needed)
					return Promise.all(responses.map(response => response.json()));
				  }).then((responses) => {
					  this.inspections = responses.flat();
					  this.inspect(true);
				}).catch((e) => {
					this.searching = false;
					console.error("Failed to fetch offer list", e);
					this.cities = [];
					// Do nothing
				})
				
				return;
			}
			if(objs && this.fetch_url){
				if(!Array.isArray(objs)){
					objs = [objs];
				}
				
				this.cities = null;
				var fetches = [];
				objs.forEach(obj => {
					var data = {
						<?php if ($this->config->item('csrf_protection')){ ?>
						<?php echo json_encode($this->security->get_csrf_token_name()); ?>: <?php echo json_encode($this->security->get_csrf_hash()); ?>,
						<?php } ?>
						... obj
					};
					var f = fetch(this.fetch_url, {
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
					});
					fetches.push(f);
				});
				return Promise.all(fetches).then(responses => {
					// Convert responses to JSON (or process as needed)
					return Promise.all(responses.map(response => response.json()));
				  }).then((cities) => {
					  var cities2 = cities.map((cit, citindex) => {
						  // console.warn('cit', cit, citindex);
						  if(cit && cit.length){
							  cit.forEach(v => {
								  if(v.Offers && v.Offers.forEach){
									v.Offers.forEach(o => {
										o.Transport = objs[citindex].Transport;
									})
								  }
							  })
						  }
						  return cit;
					  })
					this.cities = cities2.flat();
					
					// console.warn('cit', cities);
				}).catch((e) => {
					this.searching = false;
					console.error("Failed to fetch offer list", e);
					this.cities = [];
					// Do nothing
				}).finally(() => {
					this.searching = false;
				})
			}
		},
		researchHash(obj){
			this.research_hash = obj;
		},
		research(hash, type){
			if(hash){
				if(JSON.stringify(hash) == JSON.stringify(this.research_hash)){
					this.data.step = 0;
					console.error('Must not re-search');
					return false;
				}
				this.research_hash = hash;
			}
			this.init_research = true;
			console.warn('research', hash);
			var initiate = this.initiate();
		},
		inspect(start){
			if(start){
			  this.inspect_retries = 0;
			  this.inspection_completes = {};
			} else {
				if(this.inspect_retries > 30){
					this.searching = false;
					this.cities = [];
					throw 'Maximum inspect retries reached';
				}
			}
			this.inspect_retries++;
			var Ids = this.inspections.map(v => v.Id).filter(Id => undefined === this.inspection_completes[Id] || (!this.inspection_completes[Id].code));
			
			if(Ids.length){
				var data = {
					<?php if ($this->config->item('csrf_protection')){ ?>
					<?php echo json_encode($this->security->get_csrf_token_name()); ?>: <?php echo json_encode($this->security->get_csrf_hash()); ?>,
					<?php } ?>
					Id: Ids,
				};
				fetch(this.inspect_url, {
					method: 'POST',
					headers: {
					  'Accept': 'application/json'
					},
					body: new URLSearchParams(objToSerialize(data))
				}).then(response => response.json()).then(response => {
					var response_Ids = Object.keys(response);
					if(Ids.find(Id => -1 === response_Ids.indexOf(Id))){
						this.searching = false;
						this.cities = [];
						throw 'Index Not found';
					}
					Object.assign(this.inspection_completes, response);
					
					Ids = this.inspections.map(v => v.Id).filter(Id => undefined === this.inspection_completes[Id] || (!this.inspection_completes[Id].code));
					
					clearTimeout(this.inspectTimeout);
					this.inspectTimeout = setTimeout(() => this.inspect(), Ids.length ? 1500 : 0);
				}).catch(e => {
					this.cities = [];
					this.searching = false;
				})
			} else if(this.inspection_completes){
				console.log('this.inspection_completes', this.inspection_completes);
				this.summary(true);
			}
		},
		summary(start){
			if(!this.summary_url){
				this.filters(true);
				return;
			}
			this.searching = true;
			if(start){
			  this.summary_retries = 0;
			} else {
				if(this.summary_retries > 5){
					this.searching = false;
					this.cities = [];
					throw 'Maximum summary retries reached';
				}
			}
			this.summary_retries++;
			var fetches = [];
			var code_to_indexes = {};
			var all_ready = true;
			for(var index in this.inspection_completes){
				var inspection = this.inspection_completes[index];
				if(undefined !== inspection._embedded){
					if(undefined !== inspection.summary){
						if(100 == inspection.summary.progress){
							continue;
						}
					} else {
						continue;
					}
				}
				all_ready = false;
				var data = {
					<?php if ($this->config->item('csrf_protection')){ ?>
					<?php echo json_encode($this->security->get_csrf_token_name()); ?>: <?php echo json_encode($this->security->get_csrf_hash()); ?>,
					<?php } ?>
					code: inspection.code,
				};
				code_to_indexes[inspection.code] = code_to_indexes[inspection.code]||[];
				code_to_indexes[inspection.code].push(index);
				
				fetches.push(fetch(this.summary_url, {
					method: 'POST',
					headers: {
					  'Accept': 'application/json'
					},
					body: new URLSearchParams(objToSerialize(data))
				}).then(response => response.json()).then(response => (response.Id = index, response)));
			}
			if(fetches.length){
				Promise.all(fetches).then((responses) => {
					responses.forEach((response) => {
						if(!response.summary){
							this.searching = false;
							this.cities = [];
							throw "Summary not found";
						}
						Object.assign(this.inspection_completes[response.Id], response);
					})
					console.warn('summary', responses, this.inspection_completes)
				}).then(() => {
					var any_progress_increase = false;
					for(var index in this.inspection_completes){
						var inspection = this.inspection_completes[index];
						if(inspection.summary){
							if(!inspection.summary['prev_progress'] || (inspection.summary['prev_progress'] < parseFloat(inspection.summary['progress']))){
								any_progress_increase = true;
							}
							inspection.summary.prev_progress = parseFloat(inspection.summary.progress);
							if(inspection._embedded[this.list_variable] && inspection._embedded[this.list_variable].length){
								this.cities = [];
							}
						}
					}
					setTimeout(() => {
						if(any_progress_increase){
							this.summary_retries --;
						}
						this.summary()
					}, 1500);
				}).catch((e) => {
					this.searching = false;
					this.cities = [];
				});
			}
			
			if(all_ready){
				this.filters(true);
			}
		},
		filters(start){
			var fetches = [];
			var all_ready = true;
			for(var index in this.inspection_completes){
				var inspection = this.inspection_completes[index];
				var data = {
					<?php if ($this->config->item('csrf_protection')){ ?>
					<?php echo json_encode($this->security->get_csrf_token_name()); ?>: <?php echo json_encode($this->security->get_csrf_hash()); ?>,
					<?php } ?>
					code: inspection.code,
				};
				fetches.push(fetch(this.filters_url, {
					method: 'POST',
					headers: {
					  'Accept': 'application/json'
					},
					body: new URLSearchParams(objToSerialize(data))
				}).then(response => response.json()).then(response => (response.Id = index, response)));
			}
			Promise.all(fetches).then((responses) => {
				responses.forEach((response) => {
					console.warn('filters', response);
					Object.assign(this.inspection_completes[response.Id], response);
					var inspection = this.inspection_completes[index];
					inspection.research_hash = this.research_hash;
					if(inspection._embedded[this.list_variable] && inspection._embedded[this.list_variable].length){
						this.cities = [];
						this.searching = false;
					}
				});
			}).then(() => {
				if(!this.cities){
					this.cities = [];
					this.results(true);
				} else {
					this.searching = false;
				}
				// all done
			});
		},
		results(start){
			clearTimeout(this.resultsTimeout);
			if(!start){
				this.resultsTimeout = setTimeout(() => this.results(true), 1500);
				return;
			}
			this.searching = true;
			var fetches = [];
			var all_ready = true;
			for(var index in this.inspection_completes){
				var inspection = this.inspection_completes[index];
				var data = {
					<?php if ($this->config->item('csrf_protection')){ ?>
					<?php echo json_encode($this->security->get_csrf_token_name()); ?>: <?php echo json_encode($this->security->get_csrf_hash()); ?>,
					<?php } ?>
					code: inspection.code,
				};
				var sorted = this.sorted || {};
				var sort = sorted.sort || 'MinPrice 1';
				var sorted_arr = sort.split(' ');
				Object.assign(data, {
					page: sorted.page || 1,
					sortType: sorted_arr[0],
					sortOrder: sorted_arr[1],
				});
				
				if(this.applied_filters){
					Object.assign(data, {filter: this.applied_filters});
				}
				
				fetches.push(fetch(this.fetch_url, {
					method: 'POST',
					headers: {
					  'Accept': 'application/json'
					},
					body: new URLSearchParams(objToSerialize(data))
				}).then(response => response.json()).then(response => (response.Id = index, response)));
			}
			Promise.all(fetches).then((responses) => {
				responses.forEach((response) => {
					console.warn('result', response);
					Object.assign(this.inspection_completes[response.Id], response);
				});
				console.warn('results', responses)
			}).finally(() => {
				this.searching = false;
				console.warn("ALL DONE", this.inspection_completes);
				// all done
			});
		},
		setOffer(offer, inspection){
			this.offer = offer;
			if(inspection){
				this.$emit('offer', offer, inspection);
			}
			if(this.data.step <= 2){
				this.$emit('set-value', {step:1});
				console.warn('SETTING STEP', 2);
				setTimeout(() => (this.$emit('set-value', {step:2})), 0)
			}
		},
		mapResults(results){
			return (results || []).map(r => {
				if(r.Stars !== undefined){
					r.Stars = r.Stars || 0;
				}
				if(!r.MainImage && ((((r.Content || {}).ImageGallery || {}).Items || [])[0] || {}).ExternalUrl){
					r.MainImage = r.Content.ImageGallery.Items[0];
				}
				if(!r.Name && r.Title){
					r.Name = r.Title;
				}
				if(!r.Address && r.Location){
					r.Address = r.Location;
				}
				if((r.Address || {}).City && !((r.Address || {}).City || {}).Country && this.country){
					r.Address.City.Country = this.country;
				}
				r.Offers = r.Offers.sort((a, b) => a.Price - b.Price);
				r.Offers = r.Offers.map(o => {
					if(0){
					o.facilities = Object.keys(merch_type).reduce((c,type) => {
						c[type] = [...new Set((c[type] || []).concat(Object.keys(merch_type[type]).filter(r => -1 !== [...o.Items 
						/*,{
							"Merch": {
								"Title": (r.Content || {}).Content || r.ShortContent,
								"type": "Other"
							}
						} */
						].findIndex((i) => i.Merch && i.Merch.Title && i.Merch.type == type && merch_type[type][r][0].test(i.Merch.Title + (/^(fm|fara masa)$/mi.test(i.Merch.Title) ? '' : "\n" + (i.UnitPrice ? 'platit' : 'inclus')) + (type == 'Merch' ? "\n" + o.Info : '') )))
						
						))];
						if('Meal' == type && !c[type].length){
							c[type].push('fm');
						}
						if('Room' == type && !c[type].length){
							c[type].push('neparsat');
						}
						return c;
					}, {});
					o.facilities['Other'] = r.Facilities;
					}
					o.all_facilities = [...new Set(Object.values(o.facilities || {}).flat())];
					return o;
				})
				return r;
			});
		}
	},
	watch: {
		'cities': {
			handler: function(nv,ov){
				this.formatted_results = nv && this.mapResults(nv) || undefined;
				// console.warn('cities', nv, this.formatted_results);
			}
		},
		'filtered_results': {
			handler: function(nv,ov){
				// console.warn('showing filtering results', nv);
			}
		},
		'applied_filters': {
			handler: function(nv,ov){
				if(this.inspection){
					var sorted = this.sorted || {};
					sorted.page = 1;
					this.sorted = sorted;
					if(!this.filtered_results){
						this.results();
					}
				}
			},
			deep: true
		},
		'sorted': {
			handler: function(nv,ov){
				if(this.inspection){
					console.warn('should search');
					if(!this.filtered_results && !this.sorted_results){
						this.results(true);
					}
				}
			},
		},
	},
	provide() {
		return {
			search:{
				merch_type: merch_type,
			},
		}
	}
}
