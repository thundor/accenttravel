export default {
	name: <?php echo json_encode($a, JSON_UNESCAPED_SLASHES); ?>,
	emits: ['activate-menu', 'set-value', 'click-selected'],
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
	data: () => {
		return {
		}
	},
	template : `
	<template v-if="true">
		<component v-for="(component, functionality_index) in components" :search_wrapper_step="search_wrapper_step" :is="component" :data="data" :key_path="key_path" :active_menu="active_menu" v-on:set-value="(v) => $emit('set-value', v)" v-on:click-selected="(a,b) => $emit('click-selected', a, b)"></component>
		<component :is="loadViewAsync('partials/presearch-wrapper/menu')" :search_wrapper_step="search_wrapper_step" :key_path="key_path" :active_menu="active_menu" :data="data" v-on:activate-menu="(a)  => $.emit('activate-menu', a)" v-on:set-value="(v) => $emit('set-value', v)" :functionalities="functionalities" v-on:click-selected="(a,b) => $emit('click-selected', a, b)">
			<template v-slot:prepend><slot name="prepend"></slot></template>
			<template v-slot:append><slot name="append"></slot></template>
		</component>
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
	},
	provide() {
		return {
			functionalities: this.functionalities,
		}
	}
}
