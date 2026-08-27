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
		template: '<?php echo basename(__FILE__,'.js.php'); ?>',
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
				{text:"Demo list item", href: '//google.ro'},
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
						  <v-list-item prepend-icon="mdi-cog-outline" title="List" :key="0" :value="0" @click.stop="wind=0" :active="wind==0"></v-list-item>
						  <v-list-item prepend-icon="mdi-cogs" title="Items" :key="'item'" :value="'item'" @click.stop="wind='item'" :active="wind=='item'"></v-list-item>
						</v-list>
					  </v-navigation-drawer>
					<v-main>
					<v-window v-model="wind" style="min-height: 150px;">
						<pre v-if="0" v-html="JSON.stringify(this.default)"></pre>
						<v-window-item :value="0" class="max-height overflow-y-auto pa-2">
							<v-textarea v-model="data.title" label="Titlu"></v-textarea>
							<v-switch v-model="data.prepend_icon" label="Iconita in fata"></v-switch>
							<template v-if="(data.props = data.props || {})" v-for="props in [data.props]">
								<v-text-field v-model="props.class" label="Clase"></v-text-field>
								<v-textarea v-model="props.style" hide-details label="Stil CSS"></v-textarea>
							</template>
						</v-window-item>
						<v-window-item :value="'item'" class="max-height overflow-y-auto pa-2">
							<template v-if="(data.item_props = data.item_props || {})" v-for="props in [data.item_props]">
								<v-text-field v-model="props.class" label="Clase"></v-text-field>
								<v-textarea v-model="props.style" hide-details label="Stil CSS"></v-textarea>
							</template>
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
					},
					immediate: true,
				},
				data: {
					handler(newValue, oldValue){
						if(this.removeEmptyObjectsJson(newValue) === this.removeEmptyObjectsJson(this.data)){
							// console.warn('preventsave', newValue);
							return;
						}
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
		edit_title_dialog: {
			handler(newValue, oldValue){
				// console.warn('edit_title_dialog', newValue, this.final.title);
				if(!newValue){
					this.final.title = DOMPurify.sanitize(this.edit_title);
				}
				this.edit_title = newValue ? this.final.title : undefined;
			},
		},
	},
	methods:{
		addItem(r){
			// console.warn(r);
			var obj = Object.keys(this.form.data).reduce((carry, dataIndex) => {
				if(-1 === ['index', 'sort_order', 'template'].indexOf(dataIndex)){
					carry[dataIndex] = this.form.data[dataIndex];
				}
				return carry;
			}, {});
			var remove_index = this.form.data.index;
			this.final.items = this.final.items || [];
			var sort_order = parseInt('' === this.form.data.sort_order ? this.final.items.length + 1 : this.form.data.sort_order);
			if(isNaN(sort_order)){
				sort_order = this.final.items.length + 1;
			}
			console.warn('adding item 2', sort_order);
			sort_order --;
			if(sort_order > this.final.items.length) sort_order = this.final.items.length;
			if(sort_order < 0) sort_order = 0;
			obj = JSON.parse(JSON.stringify(obj));
			if(undefined !== remove_index){
				console.warn('removing item', remove_index, JSON.parse(JSON.stringify(this.final.items[remove_index])));
				this.final.items.splice(remove_index, 1);
			}
			console.warn('adding item', sort_order, obj);
			this.final.items.splice(sort_order, 0, obj);
		},
	},
	template : `
	<v-list v-if="(final.items && final.items.length) || designerMode"
			v-bind="(output.props || {})">
		<v-list-subheader v-if="designerMode || output.title">
			<div class="d-flex">
			<?php if($this->theme->_can_edit){ ?>
			<v-menu v-if="designerMode"
			  open-on-hover
			  v-model="edit_title_dialog"
			>
				<template v-slot:activator="{ props }">
					<v-icon v-bind="props" icon="mdi-pencil"></v-icon>
				</template>
				<v-card min-width="300">
				  <v-textarea v-model="edit_title" :rules="form.rules.required" hide-details label="Titlu"></v-textarea>
				</v-card>
			</v-menu>
			<?php } ?>
			<div v-html="edit_title || output.title"></div>
			</div>
		</v-list-subheader>
		<template v-for="(item, i) in <?php if($this->theme->_can_edit){ ?>(!designerMode && (output?.items || []).filter(i => !i.auth || i.auth == auth) || final?.items || [])<?php } else { ?>((output?.items || []).filter(i => !i.auth || i.auth == auth) || final?.items || [])<?php } ?>" :key="i">
		<template v-if="item.list?.children?.length || item.list?.items?.length">
			<v-menu v-if="item.menu" location="end">
				<template v-slot:activator="{ props: activatorProps }">
				<?php ob_start(); ?>
				<v-hover v-slot="{ isHovering, props: hoverProps }">
				<v-list-item lines="none" class="text-decoration-none" :value="i" :active="form.data.index == i"
					:href="item.href && item.href || undefined"
					tag="a"
					v-bind="{ ...activatorProps, ...hoverProps, ...(output.item_props || {})}"
					<?php if($this->theme->_can_edit){ ?>
					@click.stop="designerMode && $event.preventDefault()"
					<?php } ?>
					>
					<template v-slot:prepend>
					<?php if($this->theme->_can_edit){ ?>
						<v-menu v-if="designerMode"
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
							<v-list-item  @click.stop="((form.data = {...item, sort_order: i+1, index: i}), dialog2 = true)">
							  <v-list-item-title><v-icon icon="mdi-pencil"></v-icon> Modifica</v-list-item-title>
							</v-list-item>
							<v-list-item  @click.stop="final.items.splice(i, 1)">
							  <v-list-item-title><v-icon icon="mdi-delete-forever-outline"></v-icon> Sterge</v-list-item-title>
							</v-list-item>
						  </v-list>
						</v-menu>
					<?php } ?>
						<v-icon class="elevation-2 rounded-pill" :icon="item.icon || 'mdi-chevron-right'" v-if="final.prepend_icon || item.icon" :style="{
							  transform: !designerMode && isHovering ? 'translateX(10px)' : 'translateX(0)',
							  transition: 'transform 0.3s ease'
							}" size="22"></v-icon>
					</template>
					<template v-slot:append v-if="item.menu && item.list">
						<v-icon :icon="'mdi-chevron-right'" :style="{
							  transform: isHovering ? 'translateX(10px)' : 'translateX(0)',
							  transition: 'transform 0.3s ease'
							}" density="default"></v-icon>
					</template>
					<v-list-item-title v-html="item.text"></v-list-item-title>
				</v-list-item>
				</v-hover>
				<?php $v_list_item = ob_get_flush(); ?>
				</template>
				<?php ob_start(); ?>
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
				<?php $v_list_children = ob_get_flush(); ?>
			</v-menu>
			<v-list-group v-else
			  no-action
			  sub-group
			>
			  <template v-slot:activator="{ props: activatorProps }">
				<?php echo $v_list_item; ?>
			  </template>
			  <?php echo $v_list_children; ?>
			</v-list-group>
		</template>
		<template v-else>
		<?php echo $v_list_item; ?>
		</template>
		</template>
	</v-list>
	
	<?php if($this->theme->_can_edit){ ?>
	<v-dialog v-if="designerMode" v-model="dialog2">
		<template v-slot:activator="{ props }">
		  <v-btn v-bind="props" class="buton-add-modul" @click.stop="(form.data = {})" size="small" icon="mdi-plus"></v-btn>
		</template>
		<template v-slot:default="{ isActive }">
			<v-card class="align-self-center" style="max-width: 630px;width:630px">
				<v-card-title v-text="(form.data.index ? 'Editare' : 'Adaugare') + ' item lista'"></v-card-title>
				<v-card-text>
					<v-form ref="form">
						<v-text-field v-model="form.data.text" :rules="form.rules.required" label="Titlu"></v-text-field>
						<v-text-field v-model="form.data.href" label="URL"></v-text-field>
						<v-text-field v-model="form.data.icon" label="Iconita"></v-text-field>
						<v-switch v-if="canMegaMenu" v-model="form.data.mega_menu" label="Mega Menu"></v-switch>
						<v-switch v-else v-model="form.data.menu" label="Dropdown"></v-switch>
						<v-select label="Tip" v-model="form.data.auth" clearable :items="[{value: null, title: '- Normal -'}, {value: 'logged-in', title: 'Logat'}, {value: 'logged-out', title: 'Nelogat'}]"></v-select>
						<v-text-field v-model="form.data.sort_order" :rules="form.rules.number" label="Sort" :placeholder="'' + ((data?.items?.length || 0) + 1)"></v-text-field>
						
						<v-select clearable label="Tip submeniu" v-model="form.data.mod_template" :items="templates.mod" @change="form.data.list = null"></v-select>
						<component :is="loadViewAsync('partials/module/templates/' + (form.data.mod_template || 'vertical-list'))" v-model="form.data.list" :default="this.default?.list || {}" :designParent="designerMode"></component>
					</v-form>
				</v-card-text>
				<v-card-actions>
					<v-spacer></v-spacer>
					<v-btn class="d-flex text-none font-weight-normal cancel-button" size="large" variant="outlined" @click.stop="isActive.value = false"><v-icon icon="mdi-arrow-left"></v-icon> Inchide</v-btn>
					<v-btn class="d-flex text-none font-weight-normal save-button" size="large" variant="outlined" @click.stop="$refs.form.validate().then(r => r.valid && (addItem(r), $refs.form.reset(), (isActive.value = false)))"><v-icon icon="mdi-content-save"></v-icon> Salveaza</v-btn>
				</v-card-actions>
			</v-card>
		</template>
	</v-dialog>
	<template v-if="designerMode">
		<template v-if="editable && inline">
			<Edit ref="form" v-model="data" :can-mega-menu="canMegaMenu"></Edit>
		</template>
		<template v-else>
			<v-toolbar v-if="editable" color="transparent">
				<template v-slot:append>
					<v-btn
						primary
						@click.stop="dialog = true"
						icon="mdi-pencil"
					></v-btn>
				</template>
			</v-toolbar>
			<v-dialog v-model="dialog">
				<template v-slot:default="{ isActive }">
					<v-card class="align-self-center" style="max-width: 630px;width:630px">
						<v-card-title v-text="'Editare item lista'"></v-card-title>
						<v-card-text class="max-height overflow-y-auto pa-0">
							<Edit ref="form" v-model="form.data" :default="this.default" :can-mega-menu="canMegaMenu"></Edit>
						</v-card-text>
						<v-card-actions>
							<v-spacer></v-spacer>
							<v-btn class="d-flex text-none font-weight-normal cancel-button" size="large" variant="outlined" @click.stop="isActive.value = false"><v-icon icon="mdi-arrow-left"></v-icon> Inchide</v-btn>
							<v-btn class="d-flex text-none font-weight-normal save-button" size="large" variant="outlined" @click.stop="$refs.form.validate().then(r => r.valid && ((data = JSON.parse(JSON.stringify(form.data))), $refs.form.reset(), (isActive.value = false)))"><v-icon icon="mdi-content-save"></v-icon> Salveaza</v-btn>
						</v-card-actions>
					</v-card>
				</template>
			</v-dialog>
		</template>
	</template>
	<?php } ?>
	`,
}
