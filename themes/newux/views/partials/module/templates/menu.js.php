import BaseTemplate from './base.js?newux=<?php echo !empty($_GET['newux']) ? (string)$_GET['newux'] : 1 ; ?>';
import DOMPurify from 'dompurify';
export default {
	extends: BaseTemplate,
	name: <?php echo json_encode($a, JSON_UNESCAPED_SLASHES); ?>,
	data: () => ({
		template: '<?php echo basename(__FILE__,'.js.php'); ?>',
		dialog: false,
		drawer: false,
		form: {
			
		},
		data: {
			logo: {
				props:{
					src: '/themes/newux/assets/images/logo.svg',
					width: '176',
					height: '60',
					class: 'mx-auto',
					cover: true,
				}
			},
			list: {
				items: [
					{text:"Demo list item", href: '//google.ro'},
				],
			},
			list2: {
				items: [
					{text:"Demo list item", href: '//google.ro'},
				],
			},
		}
	}),
	beforeMount() {
		// console.warn('GRID', this.data);
	},
	computed: {
	},
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
			},
			data: () => ({
				wind: 0,
				CKEditorFuncNum: undefined,
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
						  <v-list-item prepend-icon="mdi-cog-outline" title="Meniu" :key="0" :value="0" @click="wind=0" :active="wind==0"></v-list-item>
						  <v-list-item prepend-icon="mdi-image" title="Image" :key="'img'" :value="'img'" @click="wind='img'" :active="wind=='img'"></v-list-item>
						  <v-list-item prepend-icon="mdi-list-box-outline" title="List" :key="'list'" :value="'list'" @click="wind='list'" :active="wind=='list'"></v-list-item>
						  <v-list-item prepend-icon="mdi-list-box" title="List 2" :key="'list2'" :value="'list2'" @click="wind='list2'" :active="wind=='list2'"></v-list-item>
						  <v-list-item prepend-icon="mdi-arrow-collapse-down" title="List 3" :key="'list3'" :value="'list3'" @click="wind='list3'" :active="wind=='list3'"></v-list-item>
						</v-list>
					  </v-navigation-drawer>
					<v-main>
					<v-window v-model="wind" style="min-height: 150px;">
						<pre v-if="0" v-html="JSON.stringify(this.default)"></pre>
						<v-window-item v-if="!data.template" :value="0" class="max-height overflow-y-auto pa-2">
							<template v-if="(data.props = data.props || {})" v-for="props in [data.props]">
								<v-text-field v-model="props.class" label="Clase"></v-text-field>
								<v-text-field v-model="props.height" label="Inaltime"></v-text-field>
							</template>
						</v-window-item>
						<v-window-item :value="'img'" class="max-height overflow-y-auto pa-2">
							<component :is="loadViewAsync('partials/module/templates/image')" v-model="data.logo" inline :default="this.default?.logo || {}" :designParent="true"></component>
						</v-window-item>
						<v-window-item :value="'list'" class="max-height overflow-y-auto pa-2">
							<pre v-if="0" v-html="JSON.stringify(data)"></pre>
							<component :is="loadViewAsync('partials/module/templates/vertical-list')" v-model="data.list" inline :default="this.default?.list || {}" :designParent="true" :can-mega-menu="true"></component>
						</v-window-item>
						<v-window-item :value="'list2'" class="max-height overflow-y-auto pa-2">
							<pre v-if="0" v-html="JSON.stringify(data)"></pre>
							<component :is="loadViewAsync('partials/module/templates/vertical-list')" v-model="data.list2" inline :default="this.default?.list2 || {}" :designParent="true" :can-mega-menu="true"></component>
						</v-window-item>
						<v-window-item :value="'list3'" class="max-height overflow-y-auto pa-2">
							<pre v-if="0" v-html="JSON.stringify(data)"></pre>
							<component :is="loadViewAsync('partials/module/templates/vertical-list')" v-model="data.list3" inline :default="this.default?.list3 || {}" :designParent="true" :can-mega-menu="true"></component>
						</v-window-item>
					</v-window>
					
					</v-main>
					</v-layout>
			
			</v-form>
			`,
			watch: {
				'modelValue': {
					handler(newValue, oldValue){
						this.data = newValue || {};
						// console.warn('this.data = newValue || {}', this.data);
					},
					immediate: true,
					deep: true,
				},
				data: {
					handler(newValue, oldValue){
						var d = newValue && Object.keys(newValue).length && newValue || null;
						if(d){
							// console.error('this.$emit(\'update:modelValue\', d)', d);
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
		dialog: {
			handler(newValue, oldValue){
				if(newValue){
					this.form.data = JSON.parse(JSON.stringify(this.data));
					console.warn('this.form.data', this.form.data);
				}
			},
		},
		data: {
			handler(newValue, oldValue){
				this.save();
			},
			deep: true,
		}
	},
	methods:{},
	template : `
	<v-navigation-drawer temporary v-model="drawer">
		<component :is="loadViewAsync('partials/module/templates/vertical-list')" v-model="data.list3" :custom="custom" :default="this.default?.list" :editable="editable" :designParent="designerMode" :can-mega-menu="true"></component>
		<component :is="loadViewAsync('partials/module/templates/vertical-list')" v-model="data.list" :custom="custom" :default="this.default?.list" :editable="editable" :designParent="designerMode" :can-mega-menu="true"></component>
		<component :is="loadViewAsync('partials/module/templates/vertical-list')" v-model="data.list2" :custom="custom" :default="this.default?.list" :editable="editable" :designParent="designerMode" :can-mega-menu="true"></component>
	</v-navigation-drawer>
	<v-app-bar :elevation="0" :height="useDisplay.mdAndUp.value ? 100 : 76" <?php if($this->theme->_can_edit){ ?> absolute <?php } ?>>
		<v-container class="pb-0 pt-1 pb-0" ref="mega_menu">
		<div class="d-flex ga-4 border-b-md border-info border-opacity-50 align-center">
		<?php /* <v-app-bar-title>Title</v-app-bar-title> */ ?>
		<?php if($this->theme->_can_edit){ ?>
		<v-hover v-slot="{ isHovering, props }">
			<v-btn v-if="name && !designGlobal" v-bind="props" class="design-this px-2 position-absolute bg-white" icon="mdi-pencil" color="primary" size="sm" @click="designThis = !designThis" style="z-index:1;" density="compact" variant="outlined" rounded="lg"> <v-icon icon="mdi-pencil"></v-icon> <span v-if="isHovering">Edit {{ name }}</span></v-btn>
		</v-hover>
		<?php } ?>
		<v-btn @click.stop="drawer = !drawer;" class="d-md-none text-h4" density="compact" variant="text" rounded="lg" style="min-height: 45px;">
			<v-icon icon="mdi-menu"></v-icon>
		</v-btn>
		<v-app-bar-title id="logo-wrapper" class=" align-center justify-center justify-md-start d-flex flex-fill flex-md-grow-0 v-col-lg-3" v-if="final?.logo?.props?.src">
			<a href="/"><component :is="loadViewAsync('partials/module/templates/image')" v-model="final.logo" :custom="custom" :default="this.default?.logo" :editable="editable" :designParent="designerMode"
			></component></a>
		</v-app-bar-title>
		
		<div class="d-flex flex-column flex-md-fill">
			<div class="d-flex flex-fill align-center">
		<div class="d-flex border-info border-opacity-50 flex-fill list-1-2-wrapper" :class="{'border-b-md': useDisplay.mdAndUp.value}">
			<div class="d-none d-md-flex menu-list-1-wrapper" v-bind="finalOrOutput?.list?.props">
			<template v-for="(item, i) in finalOrOutput?.list?.items || []">
				<?php ob_start(); ?>
				<v-menu v-if="item.list" :class="{'is-mega-menu': item.mega_menu && $refs.mega_menu && true}" :target="item.mega_menu ? $refs.mega_menu : undefined" <?php if(!$this->theme->_can_edit){ ?> open-on-hover <?php } ?> open-on-click :close-on-content-click="false" :close-on-back="false">
					<template v-slot:activator="{ isActive, props }">
					<v-btn class="text-none text-decoration-none" style="min-height: 45px;" v-bind="{ ...props, onClick: (...args) => (isActive && !('button' == args?.[0]?.shadowTarget.localName && args?.[0]?.shadowTarget || args?.[0]?.shadowTarget?.offsetParent)?.ariaExpanded == 'false' ? (args[0].preventDefault(), args[0].stopPropagation(), args[0].stopImmediatePropagation()) : props.onClick(...args), console.warn('onClick', ('button' == args?.[0]?.shadowTarget.localName && args?.[0]?.shadowTarget || args?.[0]?.shadowTarget?.offsetParent).ariaExpanded, args)) }" variant="text" density="compact" :href="item.href">
						<template v-if="item.icon">
							<v-icon :icon="item.icon"></v-icon>
							<span class="d-none d-lg-flex" v-html="item.text"></span>
						</template>
						<template v-else>
							<span v-html="item.text"></span>
						</template>
					</v-btn>
					</template>
					<template v-if="'vertical-list' == (item.mod_template || 'vertical-list')">
					  <component :is="loadViewAsync('partials/module/templates/' + (item.mod_template || 'vertical-list'))" v-model="item.list" :designParent="designerMode"></component>
					</template>
					<template v-else>
						<v-list class="pa-0">
							<v-list-item class="pa-0">
							<component :is="loadViewAsync('partials/module/templates/' + (item.mod_template || 'vertical-list'))" v-model="item.list" :designParent="designerMode"></component>
							</v-list-item>
						</v-list>
					</template>
				</v-menu>
				<v-btn v-else class="text-none text-decoration-none" style="min-height: 45px;" variant="text" density="compact" :href="item.href">
					<template v-if="item.icon">
						<v-icon :icon="item.icon"></v-icon>
						<span class="d-none d-lg-flex" v-html="item.text"></span>
					</template>
					<template v-else>
						<span v-html="item.text"></span>
					</template>
				</v-btn>
				<?php $sub_menu = ob_get_flush(); ?>
			</template>
			</div>
			<v-spacer></v-spacer>
			<div class="d-flex menu-list-2-wrapper" v-bind="finalOrOutput?.list2?.props">
			<template v-for="(item, i) in finalOrOutput?.list2?.items || []">
				<?php echo $sub_menu; ?>
			</template>
			</div>
			<?php if($this->theme->_can_edit){ ?>
		<template v-if="designerMode">
			<template v-if="editable && inline">
				<Edit ref="form" v-model="data"></Edit>
			</template>
			<template v-else>
				<v-btn
							primary
							@click="dialog = true"
							icon="mdi-pencil"
						></v-btn>
				<v-dialog v-model="dialog">
					<template v-slot:default="{ isActive }">
						<v-card class="align-self-center" style="width:95vw;max-width:100%">
							<v-card-title v-text="'Editare meniu'"></v-card-title>
							<v-card-text class="max-height overflow-y-auto pa-0">
								<Edit ref="form" v-model="form.data" :default="this.default"></Edit>
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
		</div>
		
		<div v-if="finalOrOutput?.list3?.items?.length" class="d-none d-md-flex flex-fill menu-list-3-wrapper" v-bind="finalOrOutput?.list3?.props">
			<template v-for="(item, i) in finalOrOutput?.list3?.items || []">
				<?php echo $sub_menu; ?>
			</template>
		</div>
		</div>
		</div>
		<pre v-if="0" v-html="JSON.stringify(output)"></pre>
		</v-container>
	</v-app-bar>
	`,
}
