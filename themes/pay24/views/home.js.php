export default {
	data: () => {
		return {
			menu: {
				items: [
					{
						key: 'flights',
						icon: 'mdi-airplane',
						text: 'Bilete avion',
					},
					{
						key: 'hotels',
						icon: 'mdi-home-assistant',
						text: 'Rezervare hotel',
					},
					{
						key: 'packages',
						icon: 'mdi-plane-car',
						text: 'Oferte vacanta',
					},
				]
			}
		}
	},
	template : `
		<v-window v-model="step" class="w-100 fill-height">
			<v-window-item :value="0" class="w-100 fill-height">
				<v-card
					class="w-100 fill-height d-flex flex-column"
				>
					<v-list>
					<v-list-subheader>Posibilitati Vacanta</v-list-subheader>
		
					<v-list-item
						v-for="(item, i) in menu.items"
						:to="item.key"
						:key="item.key"
						:value="item"
						active-color="primary"
						rounded="xl"
					>
						<template v-slot:prepend>
						<v-icon :icon="item.icon"></v-icon>
						</template>
		
						<v-list-item-title v-text="item.text"></v-list-item-title>
					</v-list-item>
					</v-list>
				</v-card>
			</v-window-item>
			<v-window-item :value="1" class="w-100 fill-height">
				<router-view v-if="step"></router-view>
			</v-window-item>
		</v-window>
	`,
	beforeCreate() {
		['flights', 'hotels', 'packages'].forEach((v, i) => {
			var d = {
				path: '/' + v,
				name: v,
				displayName: v,
				component: () => loadView(v),
			};
			console.log(this);
			router.addRoute(d)

		})
		// console.warn('router', router);
	},
	computed: {
		step(){
			return router.currentRoute.name == 'home' ? 0 : 1
		}
	}
}
