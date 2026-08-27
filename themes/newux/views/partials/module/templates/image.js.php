import BaseTemplate from './base.js?newux=<?php echo !empty($_GET['newux']) ? (string)$_GET['newux'] : 1 ; ?>';
import DOMPurify from 'dompurify';
export default {
	extends: BaseTemplate,
	name: <?php echo json_encode($a, JSON_UNESCAPED_SLASHES); ?>,
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
			props:{
				src: '/themes/newux/assets/images/placeholder.webp',
				aspectRatio: '16/9',
				cover: true,
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
				type: {
				  type: String,
				  default: () => (''),
				},
			},
			data: () => ({
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
			<v-form ref="form" class="my-2">
				<v-text-field v-model="data.chip_title" label="Titlu flotant"></v-text-field>
				<template v-if="(data.a_props = data.a_props || {})" v-for="a_props in [data.a_props]">
					<v-text-field v-model="a_props.href" label="URL"></v-text-field>
					<v-text-field v-model="a_props.target" label="Target"></v-text-field>
				</template>
				<template v-if="(data.props = data.props || {})" v-for="props in [data.props]">
					<v-text-field v-model="props.class" label="Clase"></v-text-field>
					<v-text-field v-model="props.alt" label="Titlu alternativ"></v-text-field>
					<v-text-field v-model="props.src" label="Image" @click:append="openImageFilemanager" append-icon="mdi-image-check-outline"></v-text-field>
					<v-switch :indeterminate="'boolean' != typeof props.cover" v-model="props.cover" density="compact" hide-details label="Coperta"></v-switch>
					<v-text-field v-model="props.height" label="Inaltime"></v-text-field>
					<v-text-field v-model="props.width" label="Latime"></v-text-field>
					<v-text-field v-model="props.minHeight" label="Inaltime minima"></v-text-field>
					<v-text-field v-model="props.minWidth" label="Latime minima"></v-text-field>
					<v-text-field v-model="props.maxHeight" label="Inaltime maxima"></v-text-field>
					<v-text-field v-model="props.maxWidth" label="Latime maxima"></v-text-field>
					<v-text-field v-model="props.aspectRatio" label="Aspect"></v-text-field>
					<v-text-field v-model="props.position" label="Pozitie"></v-text-field>
					<v-select label="Rotunjire" v-model="props.rounded" :items="['0', true, 'xs', 'sm', 'lg', 'xl', 'pill', 'circle', 'shaped'].map(v => ({title: '' + v, value: v}))"></v-select>
				</template>
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
	<v-img eager
		v-bind="extendObj({src: '/themes/newux/assets/images/placeholder.webp'}, output.props || {})"
		<?php if($this->theme->_can_edit){ ?>
		@click="designerMode && ($event.preventDefault(), $event.stopImmediatePropagation(),  $event.stopPropagation(), (dialog = true))"
		<?php } ?>
	>
		<template v-if="output.a_props?.href">
			<a v-bind="output.a_props" class="position-absolute top-0 left-0 right-0 bottom-0"></a>
		</template>
		<v-chip class="lis-image-chip" variant="elevated" rounded="xl" v-if="output.chip_title">
			<span v-html="output.chip_title"></span>
		</v-chip>
	</v-img>
	<?php if($this->theme->_can_edit){ ?>
	<template v-if="designerMode">
		<template v-if="editable && inline">
			<Edit ref="form" v-model="data"></Edit>
		</template>
		<template v-else>
			<v-dialog v-model="dialog">
				<template v-slot:default="{ isActive }">
					<v-card class="align-self-center" style="max-width: min(95vw, 630px);width:630px">
						<v-card-title v-text="'Editare imagine'"></v-card-title>
						<v-card-text class="max-height overflow-y-auto px-2 py-0">
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
	`,
}
