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
			<v-form ref="form">
				<template v-if="(data.props = data.props || {})" v-for="props in [data.props]">
					<v-text-field v-model="props.color" label="Culoare"></v-text-field>
					<v-text-field v-model="props.icon" label="Iconita"></v-text-field>
					<v-text-field v-model="props.class" label="Clase"></v-text-field>
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
	<v-icon
		v-bind="output.props || {}"
		<?php if($this->theme->_can_edit){ ?>
		@click.prevent="designerMode && (dialog = true)"
		<?php } ?>
	>
	</v-icon>
	<?php if($this->theme->_can_edit){ ?>
	<template v-if="designerMode">
		<template v-if="editable && inline">
			<Edit ref="form" v-model="data"></Edit>
		</template>
		<template v-else>
			<v-dialog v-model="dialog">
				<template v-slot:default="{ isActive }">
					<v-card class="align-self-center" style="max-width: min(95vw, 630px);width:630px">
						<v-card-title v-text="'Editare buton'"></v-card-title>
						<v-card-text class="max-height overflow-y-auto">
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
	<pre v-if="0" v-html="JSON.stringify(output.props, 2)"></pre>
	`,
}
