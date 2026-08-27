export default {
	template : `
	<v-card>
		<router-link :to="{ name: 'home'}">Home</router-link>
		<div>Oferte vacanta</div>
	</v-card>
	`,
	beforeCreate: () => {
		
		console.warn('router', router);
		['flights'].forEach((v, i) => {
			var d = {
				path: '/' + v,
				name: v,
				displayName: v,
				component: () => loadView(v),
			};
			router.addRoute(d)

		})
		// console.warn('router', router);
	}
}
