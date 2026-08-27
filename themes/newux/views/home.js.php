let presearch_destination_search_timer;
export default {
	name: <?php echo json_encode($a, JSON_UNESCAPED_SLASHES); ?>,
	data: () => {
		return {
		}
	},
	template : `
	<component :is="loadViewAsync('layout/default')"><slot />
	</component>
	`,
	beforeCreate() {
	},
	computed: {
	},
	watch: {
		'menu.active': {
			handler: function(nv,ov){
				// console.warn(nv,ov);
			},
			immediate: true
		},
	}
}
