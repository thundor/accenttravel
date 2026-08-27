export default {
	name: <?php echo json_encode($a, JSON_UNESCAPED_SLASHES); ?>,
	emits: ['set-value', 'click-selected', 'activate-menu'],
	inheritAttrs: false,
	data(){
		return {
			active_menu: '',
		}
	},
	props: {
		data: {
		  type: Object,
		  default: () => ({}),
		},
		activate_menu: {
		  type: String,
		  default: () => (''),
		},
		key_path: {
			type: String,
			default: () => (''),
		},
		search_wrapper_step: {
		  type: Number,
		  default: () => (0),
		},
		functionalities: {
			type: Array,
			default: () => ([]),
		},
	},
	template : `
	<slot name="before" :active_menu="active_menu"></slot>
	<div class="presearch-wrapper">
		<component :is="loadViewAsync('partials/presearch-wrapper/functionalities')" v-if="functionalities && functionalities.length" :active_menu="active_menu" v-on:activate-menu="activateMenu" :search_wrapper_step="search_wrapper_step" v-on:set-value="(v) => $emit('set-value', v)" v-on:click-selected="(a,b) => $emit('click-selected', a, b)" :functionalities="functionalities" :data="data" :key_path="key_path"></component>
		<component v-if="active_menu" :search_wrapper_step="search_wrapper_step" :is="loadViewAsync('partials/presearch-wrapper/functionalities/' + (active_menu.split('.').pop()))" v-on:set-value="(v) => $emit('set-value', v)" v-on:click-selected="(a,b) => $emit('click-selected', a, b)" content_type="default" :active_menu="active_menu" :data="data" :key_path="key_path"></component>
	</div>
	`,
	beforeCreate() {},
	mounted() {},
	computed: {
	},
	methods: {
		activateMenu: function(key){
			this.$emit('activate-menu', key);
			// console.warn('Activated Menu', this.active_menu);
		}
	},
	watch: {
		'active_menu': {
			handler: function(nv,ov){
				if(nv){
					this.$emit('activate-menu', nv)
				}
			},
			immediate: true
		},
		'activate_menu': {
			handler: function(nv,ov){
				this.active_menu = nv;
			},
			immediate: true
		},
	},
	provide() {
		return {
		}
	}
}
