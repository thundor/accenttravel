import BaseTemplate from './base.js?newux=<?php echo !empty($_GET['newux']) ? (string)$_GET['newux'] : 1 ; ?>';
import DOMPurify from 'dompurify';
export default {
	extends: BaseTemplate,
	name: <?php echo json_encode($a, JSON_UNESCAPED_SLASHES); ?>,
	props: {
		canMegaMenu: {
		  type: Boolean,
		  default: () => (false),
		},
	},
	data: () => ({
		activatorProps: {},
		transitions: [
		  "fade-transition",
		  "scale-transition",
		  "scroll-x-transition",
		  "scroll-x-reverse-transition",
		  "scroll-y-transition",
		  "scroll-y-reverse-transition",
		  "slide-x-transition",
		  "slide-x-reverse-transition",
		  "slide-y-transition",
		  "slide-y-reverse-transition",
		  "tab-transition",
		  "tab-reverse-transition"
		],
		template: '<?php echo basename(__FILE__,'.js.php'); ?>',
		slide: 0,
		slide2: -1,
		edit_title_dialog: false,
		edit_title: undefined,
		dialog: false,
		dialog2: false,
		form: {
			edit: false,
			data: {
			},
			rules: {
				required: [
				  v => !!v && !!(v || '').trim() || 'Necesar',
				],
			}
			
		},
		data: {
			title: 'VList Module title',
			items: [
				{html:"Slide 1", src: 'https://accenttravel.ro/resources/images/Tema/turcia.webp'},
				{html:"Slide 2", src: 'https://accenttravel.ro/resources/images/Tema/barca_plutind.webp'},
			],
		}
	}),
	components: {
		<?php if($this->theme->_can_edit){ ?>
		Edit: {
			emits: ['update:modelValue'],
			props: {
				modelValue: {
				  type: Object,
				  default: () => ({}),
				},
				default: {
				  type: Object,
				  default: () => ({}),
				},
				type: {
				  type: String,
				  default: () => (''),
				},
				canMegaMenu: {
				  type: Boolean,
				  default: () => (false),
				},
			},
			data: () => ({
				dialog: false,
				wind: 0,
				CKEditorFuncNum: undefined,
				form: {
					data: {
					},
					rules: {
						required: [
						  v => !!v && !!(v || '').trim() || 'Necesar',
						],
					}
				},
				transitions: [
				  "fade-transition",
				  "scale-transition",
				  "scroll-x-transition",
				  "scroll-x-reverse-transition",
				  "scroll-y-transition",
				  "scroll-y-reverse-transition",
				  "slide-x-transition",
				  "slide-x-reverse-transition",
				  "slide-y-transition",
				  "slide-y-reverse-transition",
				  "tab-transition",
				  "tab-reverse-transition"
				],
				data: {
				},
			}),
			mounted() {
				this.CKEditorFuncNum = CKEDITOR.tools.addFunction(this.setImage);
			},
			unmounted() {
				CKEDITOR.tools.removeFunction(this.CKEditorFuncNum);
			},
			methods:{
				validate(){
					return this.$refs.form.validate();
				},
				reset(){
					return this.$refs.form.reset();
				},
				openImageFilemanager(){
					this.openFilemanager(this.CKEditorFuncNum);
				},
				setImage(path){
					this.data.props.src = path;
				},
				addItem(r){
					console.warn('addItem', r);
					var obj = Object.keys(this.form.data).reduce((carry, dataIndex) => {
						if(-1 === ['index', 'sort_order', 'template'].indexOf(dataIndex)){
							carry[dataIndex] = this.form.data[dataIndex];
						}
						return carry;
					}, {});
					var remove_index = this.form.data.index;
					this.data.items = this.data.items || [];
					var sort_order = parseInt('' === this.form.data.sort_order ? this.data.items.length + 1 : this.form.data.sort_order);
					if(isNaN(sort_order)){
						sort_order = this.data.items.length + 1;
					}
					console.warn('adding item 2', sort_order);
					sort_order --;
					if(sort_order > this.data.items.length) sort_order = this.data.items.length;
					if(sort_order < 0) sort_order = 0;
					obj = JSON.parse(JSON.stringify(obj));
					if(undefined !== remove_index){
						console.warn('removing item', remove_index, JSON.parse(JSON.stringify(this.data.items[remove_index])));
						this.data.items.splice(remove_index, 1);
					}
					console.warn('adding item', sort_order, obj);
					this.data.items.splice(sort_order, 0, obj);
				},
			},
			components: {
				EditItem: {
					emits: ['update:modelValue'],
					props: {
						modelValue: {
						  type: Object,
						  default: () => ({}),
						},
					},
					data: () => ({
						item_html: null,
						item_image: null,
						data: {
						},
					}),
					template: `
						<pre v-if="0" v-html="JSON.stringify(item_html)"></pre>
						<component :is="loadViewAsync('partials/module/templates/html')" v-model="item_html" :designParent="true"></component>
						<pre v-if="0" v-html="JSON.stringify(item_image)"></pre>
						<component :is="loadViewAsync('partials/module/templates/image')" v-model="item_image" :designParent="true" inline editable></component>
					`,
					watch: {
						'modelValue': {
							handler(newValue, oldValue){
								// console.warn('modelValue', newValue);
								this.data = newValue || {};
								this.item_html = JSON.parse(JSON.stringify({html: this.data?.html || ''}));
								this.item_image = JSON.parse(JSON.stringify(this.data?.image || {}));
							},
							immediate: true,
						},
						item_html: {
							handler(newValue, oldValue){
								this.data.html = newValue.html;
							},
						},
						item_image: {
							handler(newValue, oldValue){
								this.data.image = newValue;
							},
							deep: true,
						},
						data: {
							handler(newValue, oldValue){
								if(this.removeEmptyObjectsJson(newValue) === this.removeEmptyObjectsJson(this.data)){
									console.warn('preventsave', this.removeEmptyObjectsJson(newValue));
									return;
								}
								console.warn('SAVING', this.removeEmptyObjectsJson(newValue), JSON.parse(JSON.stringify(newValue)));
								/* if(data.items && data.items.length){
									data.items = data.items.map(i => {
										i.html = i.html && DOMPurify.sanitize(i.html) || i.html;
										return i;
									});
								} */
								newValue = this.removeEmptyObjects(newValue);
								var d = newValue && Object.keys(newValue).length && newValue || null;
								if(d){
									this.$emit('update:modelValue', d);
								}
							},
							deep: true,
						},
					}
				}
			},
			template: `
			<v-form ref="form">
				<v-layout style="min-height:500px;">
					<v-navigation-drawer v-if="!data.template"
						expand-on-hover
						rail
					  >
						<v-list density="compact" nav>
						  <v-list-item prepend-icon="mdi-cog-outline" title="BG slider" :key="0" :value="0" @click.stop="wind=0" :active="wind==0"></v-list-item>
						  <v-list-item prepend-icon="mdi-cog-play-outline" title="HTML slider" :key="1" :value="1" @click.stop="wind=1" :active="wind==1"></v-list-item>
						  <v-list-item prepend-icon="mdi-format-list-text" title="Items" :key="'list'" :value="'list'" @click.stop="wind='list'" :active="wind=='list'"></v-list-item>
						</v-list>
					  </v-navigation-drawer>
					<v-main>
					<v-window v-model="wind" style="min-height: 150px;">
						<pre v-if="0" v-html="JSON.stringify(this.default)"></pre>
						<v-window-item :value="0" class="max-height overflow-y-auto pa-2">
							Carousel:
							<template v-if="(data.bg_props = data.bg_props || {})" v-for="props in [data.bg_props]">
								<v-text-field v-model="props.class" label="Clase"></v-text-field>
								<v-textarea v-model="props.style" hide-details label="Stil CSS"></v-textarea>
							</template>
							Items:
							<template v-if="(data.bg_item_props = data.bg_item_props || {})" v-for="props in [data.bg_item_props]">
								<v-text-field v-model="props.class" label="Clase"></v-text-field>
								<v-textarea v-model="props.style" hide-details label="Stil CSS"></v-textarea>
							</template>
						</v-window-item>
						<v-window-item :value="1" class="max-height overflow-y-auto pa-2">
							Carousel:
							<template v-if="(data.props = data.props || {})" v-for="props in [data.props]">
								<v-text-field v-model="props.interval" label="Interval"></v-text-field>
								<v-text-field v-model="props.class" label="Clase"></v-text-field>
								<v-textarea v-model="props.style" hide-details label="Stil CSS"></v-textarea>
							</template>
							Items:
							<template v-if="(data.item_props = data.item_props || {})" v-for="props in [data.item_props]">
								<v-text-field v-model="props.class" label="Clase"></v-text-field>
								<v-textarea v-model="props.style" hide-details label="Stil CSS"></v-textarea>
							</template>
						</v-window-item>
						<v-window-item :value="'list'" class="max-height overflow-y-auto pa-2">
							<v-list>
								<v-list-item v-for="(item, i) in (data.items || [])">
									<template v-slot:prepend>
										<v-menu
										  open-on-hover
										>
										  <template v-slot:activator="{ props }">
											<v-btn
											  color="primary"
											  size="xsmall"
											  class="px-1 me-2"
											  v-bind="props"
											>
											  {{ i+1 }}
											</v-btn>
										  </template>

										  <v-list>
											<v-list-item  @click.stop="(this.form.data = {...JSON.parse(JSON.stringify(item)), sort_order: i + 1, index: i}, dialog = true)">
											  <v-list-item-title><v-icon icon="mdi-pencil"></v-icon> Modifica</v-list-item-title>
											</v-list-item>
											<v-list-item  @click.stop="data.items.splice(i, 1)">
											  <v-list-item-title><v-icon icon="mdi-delete-forever-outline"></v-icon> Sterge</v-list-item-title>
											</v-list-item>
										  </v-list>
										</v-menu>
									</template>
									<v-list-item-title v-text="item.title"></v-list-item-title>
									<template v-slot:append>
										<v-img v-if="item?.image?.props?.src" :src="item?.image?.props?.src" height="40" min-width="40" aspect-ratio="16/9"></v-img>
									</template>
								</v-list-item>
								<v-dialog v-model="dialog">
									<template v-slot:default="{ isActive }">
										<v-card class="align-self-center pa-0" style="max-width: 95vw;width:630px">
											<v-card-title v-text="'Editare Hero-Banner'"></v-card-title>
											<v-card-text class="max-height overflow-y-auto">
												<?php /* <v-switch
													:indeterminate="'boolean' != typeof form.data.status"
													color="primary"
													v-model="form.data.status"
													density="compact"
													hide-details
													label="Status"
												></v-switch> */ ?>
												<v-text-field v-model="form.data.title" label="Titlu"></v-text-field>
												<v-text-field v-model="form.data.sort_order" :rules="form.rules.number" label="Sort" :placeholder="'' + ((data?.items?.length || 0) + 1)"></v-text-field>
												<v-select label="Tranzitie text" v-model="form.data.transition" clearable :items="transitions"></v-select>
												<EditItem v-model="form.data"></EditItem>
											</v-card-text>
											<v-card-actions>
												<v-btn class="d-flex text-none font-weight-normal cancel-button" size="large" variant="outlined" @click="isActive.value = false"><v-icon icon="mdi-arrow-left"></v-icon> Inchide</v-btn>
												<v-spacer></v-spacer>
												<v-btn class="d-flex text-none font-weight-normal save-button" size="large" variant="outlined" @click="$refs.form.validate().then(r => r.valid && (this.addItem(), $refs.form.reset(), (isActive.value = false)))"><v-icon icon="mdi-content-save"></v-icon> Salveaza</v-btn>
											</v-card-actions>
										</v-card>
									</template>
								</v-dialog>
							</v-list>
							<v-btn @click="(!data.items && (data.items = []), (this.form.data = {}), this.dialog = true)">+</v-btn>
						</v-window-item>
					</v-window>
					
					</v-main>
					</v-layout>
			
			</v-form>
			`,
			watch: {
				'modelValue': {
					handler(newValue, oldValue){
						console.warn('modelValue', newValue);
						this.data = newValue || {};
					},
					immediate: true,
				},
				data: {
					handler(newValue, oldValue){
						if(this.removeEmptyObjectsJson(newValue) === this.removeEmptyObjectsJson(this.data)){
							// console.warn('preventsave', this.removeEmptyObjectsJson(newValue));
							return;
						}
						// console.warn('SAVING', this.removeEmptyObjectsJson(newValue), JSON.parse(JSON.stringify(newValue)));
						/* if(data.items && data.items.length){
							data.items = data.items.map(i => {
								i.html = i.html && DOMPurify.sanitize(i.html) || i.html;
								return i;
							});
						} */
						newValue = this.removeEmptyObjects(newValue);
						var d = newValue && Object.keys(newValue).length && newValue || null;
						if(d){
							this.$emit('update:modelValue', d);
						}
					},
					deep: true,
				},
			}
		},
		<?php } ?>
	},
	watch:{
		slide: {
			handler(newValue, oldValue){
				setTimeout(() => {
					this.slide2 = this.slide;
				},500)
			},
			immediate: true
		},
		dialog2: {
			handler(newValue, oldValue){
				if(!newValue){
					this.form.data = {};
				}
			},
		},
		dialog: {
			handler(newValue, oldValue){
				if(newValue){
					this.form.data = JSON.parse(JSON.stringify(this.final));
				}
			},
		},
	},
	methods:{
	},
	template : `
	<v-carousel class="hero-banner-bg fill-height" v-bind="data.bg_props" tab-index="-1"
		hide-delimiters
		v-model="slide"
		:show-arrows="false"
		continuous
	>
	  <v-carousel-item v-for="(item, item_index) in finalOrOutput.items"
		:eager="item_index == slide || item_index == slide+1"
		v-bind="item?.image?.props" class="fill-height"
		cover
	  >
		<div class="fill-height"></div>
	  </v-carousel-item>
	</v-carousel>
	<div class="hero-banner-content d-flex flex-fill justify-center align-center">
		<v-carousel 
			v-model="slide"
			hide-delimiters
			:show-arrows="(final.items || []).length > 1 ? 'hover' : false"
			<?php if(!$this->theme->_can_edit){ ?>
			:cycle="!designerMode"
			interval="5000"
			<?php } ?>
			v-bind="data.props"
		>
		  <v-carousel-item v-for="(item, item_index) in final.items"class="fill-height">
			<div class="fill-height">
				<transition :name="item.transition || undefined" mode="in-out">
					<div v-if="!item.transition || slide2 == item_index" class="d-flex fill-height justify-center align-center">
						<div v-html="item.html"></div>
					</div>
				</transition>
			</div>
		  </v-carousel-item>
		</v-carousel>
		
	<?php if($this->theme->_can_edit){ ?>
	<template v-if="designerMode">
		<template v-if="editable && inline">
			<Edit ref="form" v-model="data"></Edit>
		</template>
		<template v-else>
			<v-dialog v-model="dialog">
				<template v-slot:activator="{ props }">
					<v-btn
						primary
						v-bind="props"
						icon="mdi-cogs"
					></v-btn>
				</template>
				<template v-slot:default="{ isActive }">
					<v-card class="align-self-center" style="max-width: 630px;width:630px">
						<v-card-title v-text="'Editare Hero-Banner'"></v-card-title>
						<v-card-text class="max-height overflow-y-auto pa-0">
							<Edit ref="form" v-model="form.data"></Edit>
						</v-card-text>
						<v-card-actions>
							<v-spacer></v-spacer>
							<v-btn class="d-flex text-none font-weight-normal cancel-button" size="large" variant="outlined" @click="isActive.value = false"><v-icon icon="mdi-arrow-left"></v-icon> Inchide</v-btn>
							<v-btn class="d-flex text-none font-weight-normal save-button" size="large" variant="outlined" @click="$refs.form.validate().then(r => r.valid && ((data = JSON.parse(JSON.stringify(form.data))), $refs.form.reset(), (isActive.value = false)))"><v-icon icon="mdi-content-save"></v-icon> Salveaza</v-btn>
						</v-card-actions>
					</v-card>
				</template>
			</v-dialog>
		</template>
	</template>
	<?php } ?>
	</div>
	`,
}
