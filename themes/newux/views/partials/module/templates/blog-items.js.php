<?php
$this->load->model('CMS_Pages_model');
$filters = [];
$filters['join_content'] = true;
$filters['limit'] = 6;
$filters['status'] = 1;
$filters['blog'] = 1;
$filters['lang'] = 'ro';
$filters['ordering'] = 'sort_order ASC';
$pages = [];
foreach($this->CMS_Pages_model->getPages($filters) as $page){
	$images = array_filter($page->images, function($image){
		if(!empty($image['hide'])) return false;
		return true;
	});
	$allimages = [];
	foreach($images as $image=>$image_details){
		$image_details['src'] = $image;
		$allimages[] = $image_details;
	}
	$page->image = array_shift($allimages);
	$page->images = $allimages;
	$page = (array)$page;
	$page = array_intersect_key($page, array_flip(['page_id', 'image', 'description', 'title', 'slug']));
	$p = [
		'children' => [[
			"mod_data" => [
				"image" => [
					"props" => [
						"src" => $page['image']['src'] ?? ''
					]
				],
				"props" => [
					"href" => $page['slug'] ? '/' . $page['slug'] : '/cms/page/' . $page['page_id'],
				],
				'title' => $page['title'],
				'subtitle' => $page['description'],
			]
		]]
	];
	$pages[] = $p;
}
?>
import BaseTemplate from './base.js?newux=<?php echo !empty($_GET['newux']) ? (string)$_GET['newux'] : 1 ; ?>';
import DOMPurify from 'dompurify';
export default {
	extends: BaseTemplate,
	name: <?php echo json_encode($a, JSON_UNESCAPED_SLASHES); ?>,
	data: () => ({
		template: '<?php echo basename(__FILE__,'.js.php'); ?>',
		fdata: {
		"children": [{
			status: true,
			"children": [{
				"children": <?php echo json_encode($pages, JSON_PRETTY_PRINT); ?>,
				"default_mod_template": "card-image-horizontal",
				"default_mod_data": {
					"image": {
						"props": {
							"src": "/resources/images/antalya.jpg",
							"cover": true,
							"rounded": "lg",
							"height": "",
							"minHeight": "",
							"maxWidth": "",
							"maxHeight": "",
							"aspectRatio": "1.52",
							"class": "ma-auto",
							"minWidth": "250"
						}
					},
					"props": {
						"rounded": "lg",
						"minWidth": "min(calc(100vw - 100px), 460px)",
						"maxWidth": "min(100%, 684px)",
						"tag": "a",
						"class": "text-decoration-none",
					},
					"title": "",
					"subtitle": "",
					"header_title": "",
					"button_title": "Citeste si inspira-te"
				},
				"status": true,
				"props": {
					"class": ""
				},
				"carousel": false
			}]
			}]
		}
	}),
	beforeMount() {
	},
	computed: {
	},
	watch:{},
	methods:{},
	template : `
	<component :is="loadViewAsync('partials/module/templates/grid')" v-on:custom="(...arguments) => $emit('custom', ...arguments)" v-model="fdata" :designParent="false" ></component>
	`,
}
