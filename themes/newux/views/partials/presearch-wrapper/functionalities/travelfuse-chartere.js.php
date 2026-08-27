import BaseFunctionality from './base-functionality.js?newux=<?php echo !empty($_GET['newux']) ? (string)$_GET['newux'] : 1 ; ?>';
import merch_type from './travelfuse/merch_type.json.js?newux=<?php echo !empty($_GET['newux']) ? (string)$_GET['newux'] : 1 ; ?>';
import FormLegend from '../../form/legend.js?newux=<?php echo !empty($_GET['newux']) ? (string)$_GET['newux'] : 1 ; ?>';

export default {
	name: <?php echo json_encode($a, JSON_UNESCAPED_SLASHES); ?>,
	extends: BaseFunctionality,
	components:{
		'FormLegend': FormLegend,
	},
	data: () => {
		return {
			display_content: false,
			allownextclick: false,
			key: '<?php echo basename(__FILE__, '.js.php'); ?>',
			menu: {
				title: 'Chartere',
				icon: 'mdi-map-marker',
				backgroundImage: 'https://accenttravel.ro/resources/images/Tema/barca_plutind.webp',
			},
			functionalities: [
				// 'transport',
				'destination-city',
				'departure-city',
				'check-in',
				'check-out',
				'travellers',
				'submit',
			],
		}
	},
	methods: {
		clickSelected: function(who, what){
			this.$emit('click-selected',who,what);
			if(this.allownextclick){
			this.$nextTick(() => {
			var a = who.key.replace('<?php echo basename(__FILE__, '.js.php'); ?>/', '');
			var p = this.filtered_functionalities.indexOf(a);
			if(-1 !== p && a !== 'travellers'){
				var i = document.getElementsByClassName('menu-<?php echo basename(__FILE__, '.js.php'); ?>/' + this.filtered_functionalities[1 + p] + '-wrapper'); 
				if(i && i[0]){
					who.opened = false;
					i[0].click()
				}
			}
			console.warn('clickSelected', a, p, this.functionalities[1 + p], who.key, what)
			});
			}
		},
	},
	computed: {
		filtered_functionalities() {
			return (this.functionalities || []).filter(f => {
				// if(this.is_item && 'destination-city' == f) return false;
				// if(this.is_item && 'departure-city' == f) return false;
				return true;
			})<?php if ($this->theme->_can_edit){ ?>
			.concat(['linker'])
			<?php } ?>;
		},
	},
	
	created() {
		if(this.content_type == 'default'){
			var step = parseInt(window_url.searchParams.get("step"));
			if(!isNaN(step) && step > 0){
				this.data.defaults['<?php echo basename(__FILE__, '.js.php'); ?>'] = {...(this.data.defaults['<?php echo basename(__FILE__, '.js.php'); ?>']||{}), submit:true};
				
			} else {
				console.error('blocked forced submit', this);
			}
		}
	},
	mounted() {
		if(this.content_type == 'default'){
			setTimeout(() => {
				this.allownextclick = true;
			},1500);
		}
	},
	watch: {
		'data.<?php echo basename(__FILE__, '.js.php'); ?>/submit': {
			handler: function(nv,ov){
				if(!nv) return;
				if(!this.is_item){
					if(this.content_type != 'default'){
						return;
					}
					
					this.display_content = false;
					var to_save_data = this.filtered_functionalities.filter(f => !/submit$/.test(f)).reduce((c,i) => {
						c['<?php echo basename(__FILE__, '.js.php'); ?>/' + i] = this.data['<?php echo basename(__FILE__, '.js.php'); ?>/' + i] ?? null;
						return c;
					},{});
					saveStorage('newux-search-wrapper', to_save_data, true);
					console.error('submit', to_save_data);
					setTimeout(() => {
						this.display_content = true;
					}, 10)
				}
				this.$emit('set-value', {'<?php echo basename(__FILE__, '.js.php'); ?>/submit': false});
			},
			immediate: true
		},
	},
	template : `
		<template v-if="submenu_only">
			<component :is="loadViewAsync('partials/presearch-wrapper')" :functionalities="filtered_functionalities.map(v => key + '/' + v)" :data="data" v-on:set-value="(v) => $.emit('set-value', v)" v-on:click-selected="clickSelected" :search_wrapper_step="search_wrapper_step" :key_path="(key_path ? key_path + '.' : '') + key">
				<template v-slot:before="{ active_menu }" v-if="search_wrapper_step ==1 && data['<?php echo basename(__FILE__, '.js.php'); ?>/destination-city']">
					<v-container class="section-header" v-for="result in [data['<?php echo basename(__FILE__, '.js.php'); ?>/destination-city']]">
						<div class="text-h4">Oferte vacanta <span v-text="[result.Name, result.Destination, result.Country].filter(v => !!v).join(' - ')"></span></div>
						<FormLegend :title="data['<?php echo basename(__FILE__, '.js.php'); ?>/destination-city'].Name + ' este o alegere grozava! Iata si rezultatele cautarii tale. Vezi care pachet ti se pare potrivit si de indata ce ai ales, nu uita sa faci rezervarea!'"></FormLegend>
					</v-container>
				</template>
			</component>
		</template>
		<template v-else>
		<component :is="loadViewAsync('partials/presearch-wrapper/functionality')" :active_menu="active_menu" :data="data" v-on:set-value="(v) => $.emit('set-value', v)" v-on:click-selected="clickSelected" :search_wrapper_step="search_wrapper_step" :disabled="disabled">
			<template v-slot:default>
			
				<div id="<?php echo basename(__FILE__, '.js.php'); ?>-menu"></div>
				<teleport :to="!search_wrapper_step ? '#<?php echo basename(__FILE__, '.js.php'); ?>-menu' : '#search-wrapper-step-1'">
					<component :is="loadViewAsync('partials/presearch-wrapper')" :functionalities="filtered_functionalities.map(v => key + '/' + v)" :data="data" v-on:set-value="(v) => $.emit('set-value', v)" v-on:click-selected="clickSelected" :search_wrapper_step="search_wrapper_step" :key_path="(key_path ? key_path + '.' : '') + key">
					<template v-slot:before="{ active_menu }" v-if="search_wrapper_step ==1 && data['<?php echo basename(__FILE__, '.js.php'); ?>/destination-city']">
						<v-container class="section-header" v-for="result in [data['<?php echo basename(__FILE__, '.js.php'); ?>/destination-city']]">
							<div class="text-h4">Oferte vacanta <span v-text="[result.Name, result.Destination, result.Country].filter(v => !!v).join(' - ')"></span></div>
							<FormLegend :title="data['<?php echo basename(__FILE__, '.js.php'); ?>/destination-city'].Name + ' este o alegere grozava! Iata si rezultatele cautarii tale. Vezi care pachet ti se pare potrivit si de indata ce ai ales, nu uita sa faci rezervarea!'"></FormLegend>
						</v-container>
					</template>
					</component>
					<template v-if="display_content && content_type == 'default'">
						<teleport to="#search-wrapper-content">
						<component :is="loadViewAsync('partials/presearch-wrapper/functionalities/<?php echo basename(__FILE__, '.js.php'); ?>/search')" :data="data" v-on:set-value="(v) => $.emit('set-value', v)" v-on:click-selected="clickSelected" :key_path="(key_path ? key_path + '.' : '') + key"></component>
						</teleport>
					</template>
				</teleport>
			</template>
			<?php /* 
			<template v-slot:before="{ active_menu }" v-if="data['<?php echo basename(__FILE__, '.js.php'); ?>/destination-city']">
				<FormLegend :title="data['<?php echo basename(__FILE__, '.js.php'); ?>/destination-city'].Name + ' este o alegere grozava! Iata si rezultatele cautarii tale. Vezi care pachet ti se pare potrivit si de indata ce ai ales, nu uita sa faci rezervarea!'" class="bg-white rounded-lg"></FormLegend>
			</template> */ ?>
		</component>
		</template>
	`,
	provide() {
		return {
			search:{
				merch_type:merch_type || {},
			},
		}
	}
}
