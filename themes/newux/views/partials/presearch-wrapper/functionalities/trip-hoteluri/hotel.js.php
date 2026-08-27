import BaseFunctionality from './search.js?newux=<?php echo !empty($_GET['newux']) ? (string)$_GET['newux'] : 1 ; ?>';
import { reactive } from 'vue';
export default {
	name: <?php echo json_encode($a, JSON_UNESCAPED_SLASHES); ?>,
	extends: BaseFunctionality,
	props: {
		hotel: {
		  type: Object,
		  default: () => (reactive({})),
		},
	},
	data(){
		return {
			prevent_initiate: true,
		}
	},
	computed: {},
	mounted() {},
	watch: {
		'data.<?php echo basename(dirname($a), '.js.php'); ?>/submit': {
			handler: function(nv,ov){
				if(nv){
					this.prevent_initiate = false;
					this.initiate();
				}
				// console.warn('Should initiate', nv);
			}
		},
		'searching': {
			handler: function(nv,ov){
				if(!nv && ov){
					var list = this.inspection?._embedded?.[this.list_variable];
					if(list && list.length){
						this.setOffer(list[0], this.inspection);
						this.data.step = 2;
					}
				}
			}
		}
	},
	methods: {
		setValue: function(){
			Object.assign(this.data, ...arguments);
			// console.warn('SETTING VALUE', JSON.stringify(arguments), this.data);
		},
	},
	template: `<component :is="loadViewAsync(offer_component)" :offer="offer || hotel" :is_item="true" :inspection="inspection" :searching="searching" :prepend_breadcrumbs="breadcrumbs" v-on:hash="researchHash" v-on:research="research" v-on:offer="(r) => r && setOffer(r)" :results="(sorted_results && sorted_results.length ? sorted_results : formatted_results) || inspection || []" :applied_filters="applied_filters" v-on:set-value="setValue" :search_data="full_search_data" :set_checkout_component="checkout_component" :data="data" :search_wrapper_step="data.step" ></component>
	`
}
