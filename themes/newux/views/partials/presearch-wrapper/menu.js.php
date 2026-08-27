export default {
	name: <?php echo json_encode($a, JSON_UNESCAPED_SLASHES); ?>,
	emits: ['activate-menu', 'set-value', 'click-selected'],
	data: () => {
	},
	props: {
		data: {
		  type: Object,
		  default: () => ({}),
		},
		key_path: {
			type: String,
			default: () => (''),
		},
		active_menu: {
			type: String,
			default: () => (undefined),
		},
		functionalities: {
			type: Array,
			default: () => ([]),
		},
		search_wrapper_step: {
		  type: Number,
		  default: () => (0),
		},
	},
	template : `
	<template v-if="1">
		<div class="search-type-ul-wrapper">
			<ul class="search-type-ul search-type-ul-root">
				<slot name="prepend"></slot>
				<component v-for="(component, component_index) in components" :search_wrapper_step="search_wrapper_step" :is="component" v-on:activate-menu="(v) => $.emit('activate-menu', v)" v-on:set-value="(v) => $emit('set-value', v)" v-on:click-selected="(a,b) => $emit('click-selected', a, b)" content_type="menu" :active_menu="active_menu" :data="data" :key_path="key_path" :functionality_index="component_index" :functionalities="functionalities"></component>
				<slot name="append"></slot>
				<li class="search-type-ul-close">
					<v-icon icon="mdi-close"></v-icon>
				</li>
			</ul>
			<div :id="'after-' + key_path" class="d-flex flex-wrap"></div>
		</div>
	</template>
	`,
	beforeCreate() {},
	mounted() {},
	computed: {
		components() {
			return this.functionalities.map(f => this.loadViewAsync('partials/presearch-wrapper/functionalities/' + f));
		}
	},
	methods: {
	},
	watch: {
		'components': {
			handler: function(nv,ov){
				// console.warn('components', nv);
			},
			immediate: true
		},
		'active_menu': {
			handler: function(nv,ov){
				// console.warn('active_menu', nv, this.functionalities);
			},
			immediate: true
		},
	},
}
