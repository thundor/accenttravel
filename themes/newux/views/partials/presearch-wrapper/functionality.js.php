import FormLegend from '../form/legend.js?newux=<?php echo !empty($_GET['newux']) ? (string)$_GET['newux'] : 1 ; ?>';
export default {
	name: <?php echo json_encode($a, JSON_UNESCAPED_SLASHES); ?>,
	emits: ['activate-menu', 'set-value', 'click-selected'],
	inject: ['key_path', 'key', 'content_type', 'menu'],
	props: {
		data: {
		  type: Object,
		  default: () => ({}),
		},
		disabled: {
			type: Boolean,
			default: () => (false),
		},
		active_menu: {
			type: String,
			default: () => (undefined),
		},
		search_wrapper_step: {
		  type: Number,
		  default: () => (0),
		},
	},
	components:{
		'FormLegend': FormLegend,
	},
	data: () => {
	},
	template : `
	<template v-if="content_type == 'menu'">
		<component :is="loadViewAsync('partials/presearch-wrapper/menu/item')" :search_wrapper_step="search_wrapper_step" :ref="key" :active_menu="active_menu" :data="data" :disabled="disabled" v-on:activate-menu="(a)  => $.emit('activate-menu', a)" v-on:set-value="(v) => $emit('set-value', v)" v-on:click-selected="(a,b) => $emit('click-selected', a, b)">
			<slot name="menu">
				<div class="menu-item">
				<v-icon :icon="menu.icon" class="me-3"></v-icon>
				<span v-text="menu.title"></span>
				</div>
			</slot>
		</component>
	</template>
	<template v-else-if="content_type == 'default'">
		<slot name="default">
			<span v-text="'functionality ' + menu.title + ' ' + active_menu"></span>
			<strong v-text="key_path"></strong>
			<em v-text="key"></em>
		</slot>
	</template>
	<?php /* <template v-else-if="content_type == 'before'">
		<slot name="before">
			<FormLegend title="Hai sa cautam impreuna o vacanta pe placul tau! Iti ia doar cateva click-uri si in curand poti incepe sa-ti faci bagajele" class="bg-white rounded-lg"></FormLegend>
		</slot>
	</template> */ ?>
	`,
	beforeCreate() {},
	mounted() {},
	computed: {},
	methods: {
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
