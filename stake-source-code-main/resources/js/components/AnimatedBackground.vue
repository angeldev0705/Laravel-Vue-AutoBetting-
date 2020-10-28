<template>
  <component v-if="background" :is="backgroundComponent" />
</template>

<script>
import { config } from '~/plugins/config'

export default {
  data () {
    return {
      backgroundComponent: null
    }
  },

  computed: {
    background () {
      return config('settings.theme.background')
    }
  },

  async created () {
    // dynamically load background component
    if (this.background) {
      const module = await import(/* webpackChunkName: 'js/[request]' */`./Backgrounds/${this.background}`)
      this.backgroundComponent = module.default
    }
  }
}
</script>
