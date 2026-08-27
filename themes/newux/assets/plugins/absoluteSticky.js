// absoluteSticky.js
import { getCurrentInstance, onMounted, onUnmounted, watchEffect, nextTick } from 'vue'

export default {
  name: 'AbsoluteSticky',
  setup(_, { slots }) {
    const instance = getCurrentInstance()
    const offset = { value: 0 }

    let el = null
    let timeout = null
    const delay = 1000 // Adjust delay as needed (ms)

    const updateOffset = (e) => {
		if (timeout) return // Already scheduled
		timeout = setTimeout(() => {
			timeout = null
		  if (el) {
			  console.warn('changing', e);
			const top = el.getBoundingClientRect().top
			el.style.setProperty('--element-offset-top', top < 0 ? (-top + `px`) : 0);
		  }
		}, delay)
    }

    onMounted(async () => {
      await nextTick()

      // Get slot's first element
      el = instance?.subTree?.children?.[0]?.el

      updateOffset()
      window.addEventListener('scroll', updateOffset)
      window.addEventListener('resize', updateOffset)
    })

    onUnmounted(() => {
		clearTimeout(timer);
      window.removeEventListener('scroll', updateOffset)
      window.removeEventListener('resize', updateOffset)
    })

    return () => slots.default?.()
  },
}