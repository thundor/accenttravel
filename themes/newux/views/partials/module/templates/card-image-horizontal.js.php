import BaseTemplate from './card-image-vertical.js?newux=<?php echo !empty($_GET['newux']) ? (string)$_GET['newux'] : 1 ; ?>';
export default {
	extends: BaseTemplate,
	name: <?php echo json_encode($a, JSON_UNESCAPED_SLASHES); ?>,
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
			<v-toolbar v-if="output.header_title" color="transparent" :class="{['text-' + (output.header_color && !/[^a-z]/.test(output.header_color) || 'default')] : true}" density="compact" :style="{color: output.header_color && /[^a-z]/.test(output.header_color) && (output.header_color + ' !important') }">
			  <v-toolbar-title v-text="output.header_title"></v-toolbar-title>

			  <template v-slot:prepend>
				<v-divider :thickness="6" class="border-opacity-100 border-lg rounded-xl my-0" width="20" style="border-color: currentColor !important;"></v-divider>
			  </template>
			</v-toolbar>
			<div class="d-flex flex-wrap">
				<component :is="loadViewAsync('partials/module/templates/image')" :custom="custom" v-model="final.image" :default="this.default?.image" :editable="editable" :designParent="designerMode"></component>
				<div class="d-flex flex-fill flex-column justify-center" style="width:min-content; min-width: 50%;">
					<div class="" v-if="output.title || output.subtitle">
						<v-card-title v-if="output.title" v-text="output.title" class="text-wrap"></v-card-title>
						<v-card-subtitle v-if="output.subtitle" v-text="output.subtitle" class="text-wrap"></v-card-subtitle>
					</div>
					<div v-if="output.button_title" class="d-flex align-center ga-2 justify-end pe-4 my-2" :style="{
					  transform: !designerMode && isHovering ? 'translateX(10px)' : 'translateX(0)',
					  transition: 'transform 0.3s ease'
					}">
						<span v-text="output.button_title"></span>
						<v-icon icon="mdi-chevron-right-circle" :color="!isHovering && 'primary' || 'warning'" size="28"></v-icon>
					</div>
				</div>
			</div>
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
