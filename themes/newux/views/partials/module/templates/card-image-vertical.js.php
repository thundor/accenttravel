import BaseTemplate from './base.js?newux=<?php echo !empty($_GET['newux']) ? (string)$_GET['newux'] : 1 ; ?>';
import DOMPurify from 'dompurify';
export default {
	extends: BaseTemplate,
	name: <?php echo json_encode($a, JSON_UNESCAPED_SLASHES); ?>,
	inheritAttrs: false,
	data: () => ({
		template: '<?php echo basename(__FILE__,'.js.php'); ?>',
		dialog: false,
		show: false,
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
			image: {},
			props:{
			}
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
						  <v-list-item prepend-icon="mdi-cog-outline" title="Card" :key="0" :value="0" @click="wind=0" :active="wind==0"></v-list-item>
						  <v-list-item prepend-icon="mdi-image" title="Image" :key="'img'" :value="'img'" @click="wind='img'" :active="wind=='img'"></v-list-item>
						</v-list>
					  </v-navigation-drawer>
					<v-main>
					<v-window v-model="wind" style="min-height: 150px;">
						<pre v-if="0" v-html="JSON.stringify(this.default)"></pre>
						<v-window-item v-if="!data.template" :value="0" class="max-height overflow-y-auto pa-2" eager>
							<v-text-field v-model="data.title" label="Titlu"></v-text-field>
							<v-text-field v-model="data.subtitle" label="Sub titlu"></v-text-field>
							<v-text-field v-model="data.button_title" label="Text Buton"></v-text-field>
							<v-text-field v-model="data.header_title" label="Text Antet"></v-text-field>
							<v-text-field v-model="data.header_color" label="Culoare Antet"></v-text-field>
							<template v-if="(data.props = data.props || {})" v-for="props in [data.props]">
								<v-select label="Tip" v-model="props.tag" clearable :items="[{value: null, title: '- Normal -'}, {value: 'a', title: 'Link'}]"></v-select>
								<v-text-field v-if="'a' == (props.tag || this.default?.props?.tag)" v-model="props.href" label="URL"></v-text-field>
								<v-text-field v-model="props.target" label="Target"></v-text-field>
								<v-text-field v-model="props.class" label="Clase"></v-text-field>
								<v-text-field v-model="props.height" label="Inaltime"></v-text-field>
								<v-text-field v-model="props.width" label="Latime"></v-text-field>
								<v-text-field v-model="props.minHeight" label="Inaltime minima"></v-text-field>
								<v-text-field v-model="props.minWidth" label="Latime minima"></v-text-field>
								<v-text-field v-model="props.maxHeight" label="Inaltime maxima"></v-text-field>
								<v-text-field v-model="props.maxWidth" label="Latime maxima"></v-text-field>
								<v-select label="Rotunjire" v-model="props.rounded" :items="['0', true, 'xs', 'sm', 'lg', 'xl', 'pill', 'circle', 'shaped'].map(v => ({title: '' + v, value: v}))" hide-details></v-select>
							</template>
						</v-window-item>
						<v-window-item :value="'img'" class="max-height overflow-y-auto pa-2" eager>
							<component :is="loadViewAsync('partials/module/templates/image')" v-model="data.image" inline :default="this.default?.image" :designParent="true"></component>
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
		dialog: {
			handler(newValue, oldValue){
				if(newValue){
					this.form.data = JSON.parse(JSON.stringify(this.data));
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
	template : `
	<v-hover><template v-slot:default="{ isHovering, props }">
		<v-card
			v-bind="{...props, ...(output.props || {})}"
			class="lis-card-image lis-card-image-horizontal"
			<?php if($this->theme->_can_edit){ ?>
			@click="designerMode && $event.preventDefault()"
			<?php } ?>
			:style="{
			  transform: !designerMode && isHovering ? 'scale(1.05)' : 'scale(1)',
			  transition: 'transform 0.3s ease'
			}"
		>
			<component :is="loadViewAsync('partials/module/templates/image')" v-model="final.image" :custom="custom" :default="this.default?.image" :editable="editable" :designParent="designerMode"
			></component>
			<v-card-item>
				<v-card-title v-if="output.title" v-text="output.title" class="text-wrap"></v-card-title>
				<v-card-subtitle v-if="output.subtitle" v-text="output.subtitle" class="text-wrap"></v-card-subtitle>
				<template v-slot:append v-if="output.button_title">
					<div class="d-flex align-center ga-2" :style="{
					  transform: !designerMode && isHovering ? 'translateX(10px)' : 'translateX(0)',
					  transition: 'transform 0.3s ease'
					}">
						<span v-text="output.button_title"></span>
						<v-icon icon="mdi-chevron-right-circle" :color="!isHovering && 'primary' || 'warning'" size="28"></v-icon>
					</div>
				</template>
			</v-card-item>
		</v-card>
	</template></v-hover>
	<?php if($this->theme->_can_edit){ ?>
	<template v-if="designerMode">
		<template v-if="editable && inline">
			<Edit ref="form" v-model="data"></Edit>
		</template>
		<template v-else>
			<v-toolbar v-if="editable" color="transparent">
				<template v-slot:append>
					<v-btn
						primary
						@click="dialog = true"
						icon="mdi-pencil"
					></v-btn>
				</template>
			</v-toolbar>
			<v-dialog v-model="dialog">
				<template v-slot:default="{ isActive }">
					<v-card class="align-self-center" style="max-width: min(95vw, 630px);width:630px">
						<v-card-title v-text="'Editare card'"></v-card-title>
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
	`,
}
