import BaseTemplate from './base.js?newux=<?php echo !empty($_GET['newux']) ? (string)$_GET['newux'] : 1 ; ?>';
import DOMPurify from 'dompurify';
export default {
	extends: BaseTemplate,
	name: <?php echo json_encode($a, JSON_UNESCAPED_SLASHES); ?>,
	props: {
	},
	data: () => ({
		template: '<?php echo basename(__FILE__,'.js.php'); ?>',
		data: {
		}
	}),
	beforeMount() {
		console.warn('GRID', this.data);
	},
	computed: {
	},
	watch:{},
	methods:{},
	template: `
		<component :is="loadViewAsync('partials/search-wrapper')">
			<template #before>
				<component :is="loadViewAsync('partials/module/templates/hero-banner')" v-model="data" :designParent="designerMode"></component>
				<?php if($this->theme->_can_edit){ ?>
				<v-btn v-if="name && !designGlobal" class="design-this px-2 position-absolute right-0" icon="mdi-pencil" size="sm" @click="designThis = !designThis"><v-icon icon="mdi-pencil"></v-icon> Edit {{ name }}</v-btn>
				<?php } ?>
			</template>
			<template #default>
			<slot name="default"></slot>
			</template>
		</component>
	`
}
