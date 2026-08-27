import BaseTemplate from './item.js?newux=<?php echo !empty($_GET['newux']) ? (string)$_GET['newux'] : 1 ; ?>';
import DOMPurify from 'dompurify';
export default {
	extends: BaseTemplate,
	name: <?php echo json_encode($a, JSON_UNESCAPED_SLASHES); ?>,
	data: () => ({
		template: '<?php echo basename(__DIR__,'.js.php'); ?>',
		path: '<?php echo basename(__FILE__,'.js.php'); ?>',
		form: {
			default: {status: true},
			edit: false,
			data: {},
		},
		data: {
		}
	}),
	beforeMount() {
	},
	computed: {
	},
	watch:{
	},
	methods:{
	},
	template : `
	<?php if($this->theme->_can_edit){ ?>
	<v-container v-if="designerMode" :class="{'bg-grey-lighten-3': !output.status}" v-bind="{...(output.props || {}), class:null, style:null}" class="border-opacity-50 border-lg border-error pa-1 d-flex flex-column ga-1">
		<v-list-item lines="none" :active="form.data.index == index" class="pa-2" >
			<template v-slot:prepend>
				<v-menu
				  open-on-hover
				  open-on-click
				>
				  <template v-slot:activator="{ props }">
					<v-btn
					  color="error"
					  size="xsmall"
					  class="px-1 me-2"
					  v-bind="props"
					>
					  Sectiune {{ index+1 }} {{ final.name }}
					</v-btn>
				  </template>

				  <v-list>
					<v-list-item  @click.stop="((form.data = JSON.parse(JSON.stringify({...final, sort_order: index+1, index: index}))), dialog = true)">
					  <v-list-item-title><v-icon icon="mdi-pencil"></v-icon> Modifica</v-list-item-title>
					</v-list-item>
					<v-list-item  @click.stop="parent.children.splice(index, 1)">
					  <v-list-item-title><v-icon icon="mdi-delete-forever-outline"></v-icon> Sterge</v-list-item-title>
					</v-list-item>
				  </v-list>
				</v-menu>
			
			  
			</template>
			<template v-slot:append>
				<div class="d-flex ga-2">
					<v-btn
					  color="error"
					  size="xsmall"
					  class=""
					  icon="mdi-content-duplicate"
					  @click.stop="duplicate(index)"
					>
					</v-btn>
					<v-btn
					  color="primary"
					  size="xsmall"
					  class=""
					  icon="mdi-plus"
					  @click.stop="addChild()"
					>
					</v-btn>
				</div>
			</template>
			<v-list-item-title>{{ final.text }}</v-list-item-title>
		</v-list-item>
		<template v-if="final.children && final.children.length || (final.children = [{}])">
			<component v-for="(row, rowIndex) in final.children" :key="rowIndex" :is="loadViewAsync('partials/module/templates/grid/row')" :parent="final" v-model="row" :grid="parent" :index="rowIndex" :default="{status: output.status}" :custom="custom" v-on:custom="(...arguments) => $emit('custom', ...arguments)" :designParent="designerMode"></component>
		</template>
		
		<v-dialog v-model="dialog">
			<template v-slot:default="{ isActive }">
				<v-card class="align-self-center" style="max-width: min(95vw, 630px);width:630px">
					<v-card-title v-text="'Sectiune'"></v-card-title>
					<v-card-text class="px-0 max-height overflow-y-auto">
						<v-form ref="form">
							<v-layout>
							<v-navigation-drawer v-if="!form.data.template"
								expand-on-hover
								rail
							  >
								<v-list density="compact" nav>
								  <v-list-item prepend-icon="mdi-cog-outline" title="General" :key="0" :value="0" @click.stop="wind=0" :active="wind==0"></v-list-item>
								  <v-list-item prepend-icon="mdi-application-edit-outline" title="Props" :key="1" :value="1" @click.stop="wind=1" :active="wind==1"></v-list-item>
								  <v-list-item prepend-icon="mdi-code-block-braces" title="Sablon" :key="2" :value="2" @click.stop="wind=2" :active="wind==2"></v-list-item>
								</v-list>
							  </v-navigation-drawer>
							<v-main>
							<v-window v-model="wind" class="px-2" style="min-height: 150px;">
								<v-window-item v-if="!form.data.template" :value="0">
									<v-text-field v-model="form.data.text" label="Titlu administrativ"></v-text-field>
									<v-switch
										:indeterminate="'boolean' != typeof form.data.status"
										color="primary"
										v-model="form.data.status"
										density="compact"
										hide-details
										label="Status"
									></v-switch>
									<v-switch
										:indeterminate="'boolean' != typeof form.data.carousel"
										color="primary"
										v-model="form.data.carousel"
										density="compact"
										hide-details
										label="Carusel"
									></v-switch>
									<v-text-field v-model="form.data.sort_order" :rules="rules.number" label="Sort" :placeholder="'' + (index + 1)"></v-text-field>
									<?php /* <pre v-text="JSON.stringify(form.data.default_mod_data || {}, 2)"></pre> */ ?>
								</v-window-item>
								<v-window-item v-if="!form.data.template" :value="1">
									<Props v-model="form.data.props" type="section"></Props>
								</v-window-item>
								<v-window-item :value="2">
									<v-select clearable label="Incarca Sablon Sectiune" v-model="form.data.template" :items="templates[path]"></v-select>
									<template v-if="!form.data.template">
										<v-text-field v-model="form.data.name" :rules="rules.filename" label="Salvare ca sablon cu numele:"></v-text-field>
										<v-select clearable label="Sablon Implicit de modul" v-model="form.data.default_mod_template" :items="templates.mod"></v-select>
										<component v-if="form.data.default_mod_template" :is="loadViewAsync('partials/module/templates/' + form.data.default_mod_template)" v-model="form.data.default_mod_data" :designParent="designerMode"></component>
									</template>
								</v-window-item>
							</v-window>
							</v-main>
							</v-layout>
						</v-form>
					</v-card-text>
					<v-card-actions>
						<v-spacer></v-spacer>
						<v-btn class="d-flex text-none font-weight-normal cancel-button" size="large" variant="outlined" @click.stop="isActive.value = false"><v-icon icon="mdi-arrow-left"></v-icon> Inchide</v-btn>
						<v-btn class="d-flex text-none font-weight-normal save-button" size="large" variant="outlined" @click.stop="$refs.form.validate().then(r => r.valid && (addItem(form.data, parent.children, '<?php echo basename(__FILE__,'.js.php'); ?>'), $refs.form.reset(), (isActive.value = false)))"><v-icon icon="mdi-content-save"></v-icon> Salveaza</v-btn>
					</v-card-actions>
				</v-card>
			</template>
		</v-dialog>
	</v-container>
	<template v-else>
	<?php } ?>
	<pre v-if="0" v-text="JSON.stringify(output.children, null, 2)"></pre>
		<v-container v-bind="output.props || {}" v-if="output.children && output.status" :class="{'px-0': output.carousel}">
			<template v-if="output.carousel">
				<v-carousel hide-delimiters :model-value="0" class="h-auto">
					<component v-if="output.children" v-for="(row, index) in output.children" :key="index" :is="loadViewAsync('partials/module/templates/grid/row')" :parent="output" v-model="row" :grid="parent" :index="index" :default="{status: output.status}" :custom="custom" v-on:custom="(...arguments) => $emit('custom', ...arguments)" :designParent="designerMode"></component>
				</v-carousel>
			</template>
			<component v-else v-if="output.children" v-for="(row, index) in output.children" :key="index" :is="loadViewAsync('partials/module/templates/grid/row')" :parent="output" v-model="row" :grid="parent" :index="index" :default="{status: output.status}" :custom="custom" v-on:custom="(...arguments) => $emit('custom', ...arguments)" :designParent="designerMode"></component>
		</v-container>
	<?php if($this->theme->_can_edit){ ?>
	</template>
	<?php } ?>
	`,
}
