import BaseFunctionality from '../base-functionality.js?newux=<?php echo !empty($_GET['newux']) ? (string)$_GET['newux'] : 1 ; ?>';
import Loading from './loading.js?newux=<?php echo !empty($_GET['newux']) ? (string)$_GET['newux'] : 1 ; ?>';
import { useClipboard } from '@vueuse/core'

export default {
	name: <?php echo json_encode($a, JSON_UNESCAPED_SLASHES); ?>,
	extends: BaseFunctionality,
	emits: ['activate-menu', 'set-value', 'click-selected'],
	props: {
		data: {
			type: Object,
			default: () => ({}),
		},
		active_menu: {
			type: String,
			default: () => (undefined),
		},
		functionalities: {
			type: Array,
			default: () => ([]),
		},
		functionality_index: {
			type: Number,
			default: () => (-1),
		},
		search_wrapper_step: {
		  type: Number,
		  default: () => (0),
		},
	},
	components:{
		'Loading': Loading,
	},
	data(){
		return {
			link: '',
			afterExistCheckTimer: undefined,
			afterExistCheck: false,
		}
	},
	computed: {
		linkerparams() {
			return [];
		},
		linkerlink() {
			return this.link + '?' + this.linkerparams.join('&');
		},
		linkerdata() {
			var n = this.$options.name.replace(/.*\/([^\/]+)\/[^\/]+$/,'$1');
			var d = Object.keys(this.data).reduce((c, k) => {
				if(0 === ('' + k).indexOf(n + '/')){
					c[('' + k).replace(/.*?\//, '')] = this.data[k];
				}
				return c;
			}, {});
			return d;
		},
	},
	beforeUnmount() {
	},
	mounted() {},
	watch: {
		'active_menu': {
			handler: function(nv,ov){
				clearTimeout(this.afterExistCheckTimer); 
				this.afterExistCheckTimer = setTimeout(() => {
					this.afterExistCheck = !!document.getElementById('after-' + this.key_path);
				}, 100)
				// console.warn('active_menu', nv, this);
			},
			immediate: true
		},
	},
	methods: {
		copyLinkerLink: function(){
			const { text, copy, copied, isSupported } = useClipboard(this.linkerlink)
			copy(this.linkerlink)
		},
		accessLinkerLink: function(){
			window.open('/' + this.linkerlink, '_blank').focus();
		}
	},
	template : `
<template v-if="this.content_type == ''">
	<pre v-if="0" v-text="JSON.stringify(linkerdata, null, 2)"></pre>
	<v-text-field variant="solo-filled" label="Search link" v-model="linkerlink" max-width="800" class="mx-auto" readonly @click:prepend="copyLinkerLink" prepend-icon="mdi-content-copy" @click:append="accessLinkerLink" append-icon="mdi-open-in-new"></v-text-field>
</template>
	`,
}
