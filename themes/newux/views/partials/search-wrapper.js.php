import DatePickerSelect from './form/datepicker-select.js?newux=<?php echo !empty($_GET['newux']) ? (string)$_GET['newux'] : 1 ; ?>';
import FormLegend from './form/legend.js?newux=<?php echo !empty($_GET['newux']) ? (string)$_GET['newux'] : 1 ; ?>';
import { VPhoneInput } from 'v-phone-input';
import Loading from './presearch-wrapper/functionalities/common/loading.js?newux=<?php echo !empty($_GET['newux']) ? (string)$_GET['newux'] : 1 ; ?>';

let setScroll;
export default {
	name: <?php echo json_encode($a, JSON_UNESCAPED_SLASHES); ?>,
	data(){
		return {
			abortController: new AbortController(),
			telephone: {phone: ''},
			testFetching: false,
			testdate: '',
			functionalities: [
				'travelfuse-vacante',
				'trip-hoteluri',
				'trip-avion',
				'trip-citybreak',
<?php if(!empty($_GGET['activate-roundtrip'])){ ?>
		'trip-avion-roundtrip',
<?php } ?>
			],
			data: {},
			scrollTo: {},
			active_menu: '',
		}
	},
	props: {
		activate_menu: {
		  type: String,
		  default: () => (''),
		},
		defaults: {
		  type: Object,
		  default: () => ({}),
		},
	},
	components:{
		'VPhoneInput': VPhoneInput,
		'DatePickerSelect': DatePickerSelect,
		'FormLegend': FormLegend,
		'Loading': Loading,
	},
	template : `
	<v-container class="pt-3">
		<v-window v-model="data.step" id="search-wrapper-windows" :data-step="data.step" :touch="false">
			<v-window-item ref="window-0" id="search-wrapper-menu">
				<v-container class="search-wrapper-menu-container" id="search-wrapper-menu-container">
					<slot name="before"></slot>
					<component :is="loadViewAsync('partials/presearch-wrapper')" :functionalities="functionalities" :activate_menu="active_menu || '<?php echo !empty($_GGET['active_menu']) ? htmlspecialchars($_GGET['active_menu']) : '' ?>'" v-on:activate-menu="activateMenu" :data="data" v-on:set-value="setValue" :search_wrapper_step="data.step">
						<template v-slot:before="{ active_menu, search_wrapper_step }">
							<v-container class="section-header" id="search-wrapper-menu-before">
							<component v-if="active_menu" :search_wrapper_step="search_wrapper_step" :is="loadViewAsync('partials/presearch-wrapper/functionalities/' + (active_menu.split('.').pop()))" v-on:set-value="(v) => $emit('set-value', v)" v-on:click-selected="(a,b) => $emit('click-selected', a, b)" content_type="before" :active_menu="active_menu" :data="data"></component>
							</v-container>
						</template>
					</component>
				</v-container>
				<slot />
				<?php /* <VPhoneInput country-label="Tara" label="Telefon" countryIconMode="svg" v-model="telephone.phone" class="billing-phone"
			hide-details-obs default-country="RO" display-format="national"></VPhoneInput>
			<span v-text="telephone.phone"></span> */ ?>
				<?php /* <v-btn @click="testFetch">TestFetch</v-btn>
				<v-btn @click="abortTestFetch" v-if="testFetching">AbortTestFetch</v-btn> */ ?>
			</v-window-item>
			<v-window-item ref="window-1" id="search-wrapper-content">
			</v-window-item>
			<v-window-item ref="window-2" id="search-wrapper-item-content">
			</v-window-item>
			<v-window-item ref="window-3" id="search-wrapper-item-checkout" eager>
				<button v-if="0" @click="data.step--">--</button>
			</v-window-item>
			<v-window-item ref="window-4" id="search-wrapper-item-success">
				<v-container>
				<v-card>
					<v-card-text class="text-center">
					<div class="text-h3">Comanda finalizata</div>
					<br />
					<hr />
					<br />
					<div class="text-h4">Va multumim pentru utilizarea serviciilor noastre</div>
					<div class="text-h5">Veti fi contactat de un operator pentru detalii suplimentare.</div>
					
					Detalii comanda:
					<div id="checkout-success-data"></div>
					</v-card-text>
					
					
				</v-card>
				</v-container>
			</v-window-item>
		</v-window>
	</v-container>
	`,
	created() {
		this.setMainRouter(this);
		this.data = getStorage('newux-search-wrapper', '', {});
		this.data.defaults = this.$props.defaults;
		if(this.activate_menu){
		if(!this.noStepFuncs())
			this.setRouterStep(() => {
				this.noStepFuncs(true);
				this.active_menu = this.activate_menu;
				this.step = 0;
				this.$nextTick(() => {
					this.noStepFuncs(false);
				});
			}, {active_menu:this.active_menu, step: 0});
		}
	},
	mounted() {
		console.warn('search-wrapper', this.data);
		var step = parseInt(window_url.searchParams.get("step"));
		if(!isNaN(step) && step == 4){
			console.error('forced success');
			this.data.step = 4;
		} else {
			
		}
	},
	computed: {},
	methods: {
		activateMenu: function(val){
			console.warn('activateMenu', val);
			this.active_menu = val;
		},
		setValue: function(obj){
			// console.error('setValue', obj);
			if(obj.set_default){
				Object.assign(this.data.defaults, JSON.parse(JSON.stringify({...obj, set_default: undefined})));
			} else {
				Object.assign(this.data, obj);
			}
			// this.data = {...this.data, ...obj};
		},
		abortTestFetch: function(){
			this.abortController.abort('Aborted manually');
			this.abortController = new AbortController();
		},
		testFetch: function(){
			this.testFetching = true;
			var data = {
				<?php if ($this->config->item('csrf_protection')){ ?>
				<?php echo json_encode($this->security->get_csrf_token_name()); ?>: <?php echo json_encode($this->security->get_csrf_hash()); ?>,
				<?php } ?>
			};
			var fetch_url = `${newux_url}/test.json?${append_url}`;
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
						return;
					}
					throw new Error("Network response was not ok", {cause: response });
				}
				return response;
			}).catch((error) => {
				console.warn('Fetch error', error);
			}).finally((data) => {
				this.testFetching = false;
			}).then(data => {
				console.log('received data', data);
			});
		},
	},
	watch: {
		'activate_menu': {
			handler: function(nv,ov){
				this.active_menu = nv;
			},
			immediate: true
		},
		'active_menu': {
			handler: function(nv,ov){
				if(nv != ov ){
					this.data.step = 0;
				}
				this.setValue({'active_menu': nv});
				var step = parseInt((!ov && window_url.searchParams.get("step") || 0)|| 0);
				if(!this.noStepFuncs())
				this.setRouterStep(() => {
					this.noStepFuncs(true);
					this.active_menu = nv;
					this.$nextTick(() => {
						this.noStepFuncs(false);
					});
				}, {...(nv ? {active_menu:nv, step: step && !isNaN(step) && step || 0} : {})});
			}
		},
		'testdate': {
			handler: function(nv,ov){
				console.warn('testdate', nv);
			},
			// immediate: true
		},
		'data': {
			handler: function(nv,ov){
				// console.warn('data', nv);
				// saveStorage('newux-search-wrapper', nv);
			},
			immediate: true
		},
		'data.step': {
			handler: function(nv,ov){
				if(!(!nv && !ov)){
					if(!this.noStepFuncs())
					this.setRouterStep(() => {
						this.noStepFuncs(true);
						this.data.step = nv;
						this.$nextTick(() => {
							this.noStepFuncs(false);
						});
					}, {...(nv ? {active_menu: this.active_menu, step:nv} : {})});
				}
				
				console.warn('data.step', nv, ov);
				if(nv > ov && nv && !isNaN(ov)){
					this.scrollTo['' + ov] = {y: window.scrollY, x: window.scrollX};
				}
				if(this.scrollTo['' + nv] && nv < ov){
					console.log('scrolling to ', this.scrollTo['' + nv]);
					var retries = 10;
					setScroll = setInterval(() => {
						retries --;
						if(retries >= 0 && document.documentElement.scrollHeight < this.scrollTo['' + nv].y){
							window.scrollTo({
							  top: this.scrollTo['' + nv].y,
							  left: this.scrollTo['' + nv].x,
							  behavior: 'instant',
							});
							return;
						}
						clearTimeout(setScroll);
						clearInterval(setScroll);
						window.scrollTo({
						  top: this.scrollTo['' + nv].y,
						  left: this.scrollTo['' + nv].x,
						  behavior: 'instant',
						});
						setScroll = setTimeout(() => {
						window.scrollTo({
						  top: this.scrollTo['' + nv].y,
						  left: this.scrollTo['' + nv].x,
						  behavior: 'instant',
						});
						}, 200);
					}, 100)
				} else { // if((nv||0) != (ov||0))
					console.warn('Scrolling to smth');
					clearTimeout(setScroll);
					clearInterval(setScroll);
					console.log('scrolling to top', nv, ov);
					setScroll = setTimeout(() => {
						window.scrollTo({
						  top: 0,
						  left: 0,
						  behavior: 'instant',
						});						
					}, 300)
				}
			}
		},
	},
	provide() {
		return {
		}
	}
}
