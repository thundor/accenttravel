import { components } from 'vuetify';
export default {
	extends: components.VImg,
	components:{
		'v-img': components.VImg
	},
	computed: {
		finalProps:{
			get() {
                const src = (this.$props.src || '').trim();

                  const isExternal =
                    /^(https?:)?\/\//i.test(src) || /^(data:|blob:)/i.test(src);

                  const isInternal = src && !isExternal; // include si relative

                  const isWebp = /\.webp(\?.*)?$/i.test(src);

                  // daca vrei strict doar png/jpg/jpeg:
                  const isConvertible = /\.(png|jpe?g)(\?.*)?$/i.test(src);

                  let newSrc = src;

                if (isInternal && !isWebp && isConvertible) {
                    let ext = src.match(/\.(png|jpe?g)(\?.*)?$/i)[1];
                    // separa query/hash ca sa nu le pierzi
                    const m = src.match(/^([^?#]+)(\?[^#]*)?(#.*)?$/);
                    const path = m?.[1] || src;
                    const query = m?.[2] || '';
                    const hash = m?.[3] || '';

                    // scoate extensia si pune .webp
                    const withoutExt = path.replace(/\.[^/.]+$/, '');

                    // prefix + extensie webp
                    newSrc = `/optimized/${ext}${withoutExt}.webp${query}${hash}`;
                }
				return {
					...this.$props,
					alt: this.$props.alt?.trim() || (this.$props.src || '').replace(/.*\//,'').replace(/(.*)\..*/, '$1') || 'Image',
					// title: this.$props.title?.trim() || this.$props.alt?.trim() || (this.$props.src || '').replace(/.*\//,'').replace(/(.*)\..*/, '$1') || '',
                    src: newSrc,
				}
			},
		},
	},
	template: `
	<v-img v-bind="finalProps">
		<!-- Default slot -->
		<slot />

		<!-- Named slots -->
		<template #placeholder>
		  <slot name="placeholder" />
		</template>

		<template #error>
		  <slot name="error" />
		</template>

		<template #sources>
		  <slot name="sources" />
		</template>
	  </v-img>
	`
};