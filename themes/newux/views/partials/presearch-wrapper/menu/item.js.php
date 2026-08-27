export default {
	name: <?php echo json_encode($a, JSON_UNESCAPED_SLASHES); ?>,
	emits: ['activate-menu','set-value', 'click-selected'],
	inject: ['key_path', 'menu', 'key'],
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
	data: () => {
	},
	template : `
		<li @click.stop="activateMenu((key_path ? key_path + '.' : '') + key)" :class="{'has-children': children, 'disabled': disabled, 'active': is_active}" :style="{'--bg-color': 'rgba(' + (255 - 5*functionality_index) + ',' + (255 - 5*functionality_index) + ',' + (255 - 5*functionality_index) + ',1)', 'z-index': functionalities.length - functionality_index}" <?php /* :active_menu="active_menu" :the-key="key" :the-functionalities="functionalities.join(',')" */ ?>>
			<slot name="default"></slot>
			<slot name="submenu">
				<component v-if="menu.functionalities" :is="loadViewAsync('partials/presearch-wrapper/functionalities')" :search_wrapper_step="search_wrapper_step" :functionalities="menu.functionalities" :key_path="(key_path ? key_path + '.' : '') + key" v-on:activate-menu="(a)  => $emit('activate-menu', a)" v-on:set-value="(a)  => $emit('set-value', a)" :active_menu="active_menu" :data="data" v-on:click-selected="(a,b) => $emit('click-selected', a, b)">
				<template v-slot:prepend>
					<li @click.stop="" :class="{'active': active_menu && -1 !== active_menu.split('.').indexOf(key)}" :style="{'--bg-color': 'rgba(' + (255 - 5*0) + ',' + (255 - 5*0) + ',' + (255 - 5*0) + ',1)', 'z-index': functionalities.length + 1, 'flex': '0 1 calc(' + (100/functionalities.length) + '% + var(--search-type-button-radius-base) + var(--search-type-button-padding-base) + 20px)'}">
						<div class="menu-item">
						<v-icon :icon="menu.icon" class="me-3"></v-icon>
						<span v-text="menu.title"></span>
						</div>
					</li>
				</template>
				
				</component>
			</slot>
		</li>
	`,
	beforeCreate() {},
	mounted() {},
	computed: {
		is_active() {
			return this.active_menu && (-1 !== this.active_menu.split('.').indexOf(this.key) || -1 !== (this.menu.functionalities || []).indexOf(this.active_menu));
		},
		children() {
			return this.menu.functionalities && this.menu.functionalities.length;
		}
	},
	methods: {
		activateMenu: function(key){
			if(this.disabled) return;
			if(this.children) return;
			// console.warn('activating menu', key, this.key, this.active_menu);
			this.$emit('activate-menu', key);
		}
	},
	watch: {
		'is_active': {
			handler(newValue, oldValue){
				var $do_element = $('html.route-index #search-wrapper-menu-container');
				var css_bg_img = $do_element.css('background-image');
				if(this.menu.backgroundImage){
					var css_bg_img_arr = css_bg_img && css_bg_img.match(/".*?"/g)|| [];
					var css_bg_img_arr = [];
					// console.error('menu.backgroundImage', JSON.parse(JSON.stringify(css_bg_img_arr)));
					var add_bg = '"' + this.menu.backgroundImage + '"';
					var pos = css_bg_img_arr.indexOf(add_bg);
					var execut = false;
					if(newValue){
						if(pos < 0){
							css_bg_img_arr.splice(0, 0, add_bg);
							execut = true;
						} else {
							if(pos > 0){
								css_bg_img_arr.splice(pos, 1)
								css_bg_img_arr.push(add_bg);
								execut = true;
							}
						}
						if(execut){
							$do_element.css('background-image', css_bg_img_arr.map(c => 'url(' + c + ')').join(','));
						}
					}
					else {
						return;
						if(pos < 0){
							css_bg_img_arr.push(add_bg);
							execut = true;
						}
						// console.error('menu.backgroundImage', this.menu.backgroundImage, css_bg_img_arr);
					}
					if(execut){
						// console.warn('menu.backgroundImage', css_bg_img_arr.map(c => 'url(' + c + ')').join(','));
						$do_element.css('background-image', css_bg_img_arr.map(c => 'url(' + c + ')').join(','));
					}
				}
			},
			immediate: true,
		},
	},
	provide() {
		return {
		}
	}
}
