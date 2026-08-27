<?php defined('ENVIRONMENT') OR die('Invalid access'); ?>
<?php themeFunctions::loadAddons(__FILE__); ?>
<?php themeFunctions::debugFileLine('start'); ?>
<v-container class="pa-0" fluid>
<v-window class="" :touch="false">
	<v-window-item :value="0" class="w-100 fill-height">
		<v-card
			class="w-100 fill-height d-flex flex-column"
		>
			<component :is="loadViewAsync('partials/module/searcher')">
				<component :is="loadViewAsync('partials/module/home-modules', true)"></component>
			</component>
		</v-card>
		
	</v-window-item>
	<v-window-item :value="1" class="w-100 fill-height">
		
	</v-window-item>
</v-window>
</v-container>
<?php themeFunctions::debugFileLine('end'); ?>