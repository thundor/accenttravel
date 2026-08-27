export default {
	name: <?php echo json_encode($a, JSON_UNESCAPED_SLASHES); ?>,
	emits: ['set-value', 'click-selected'],
	props: {
		data: {
		  type: Object,
		  default: () => ({}),
		},
		key_path: {
			type: String,
			default: () => (''),
		},
		submenu_only: {
			type: Boolean,
			default: () => (false),
		},
		is_item: {
			type: Boolean,
			default: () => (false),
		},
		active_menu: {
			type: String,
			default: () => (undefined),
		},
		content_type: {
			type: String,
			default: () => (''),
		},
		search_wrapper_step: {
		  type: Number,
		  default: () => (0),
		},
	},
	data: () => {
		return {
			key: '<?php echo basename(__FILE__, '.js.php'); ?>',
			menu: {
				title: 'No title',
				icon: 'mdi-weather-sunset',
				functionalities: [
				]
			}
		}
	},
	template : `
		<component :is="loadViewAsync('partials/presearch-wrapper/functionality')" :search_wrapper_step="search_wrapper_step" :active_menu="active_menu" v-on:set-value="setValue" v-on:click-selected="(a,b) => $emit('click-selected', a, b)" :disabled="disabled">
		</component>
	`,
	beforeCreate() {},
	mounted() {
		
	},
	computed: {
		disabled() {
			return false;
		},
	},
	methods: {
		setValue(v){
			if(!this.content_type) return;
			this.$emit('set-value', {[this.key]: v});
		}
	},
	watch: {
	},
	provide() {
		return {
			key: this.key,
			menu: this.menu,
			content_type: this.content_type,
			key_path: this.key_path,
		}
	}
}
