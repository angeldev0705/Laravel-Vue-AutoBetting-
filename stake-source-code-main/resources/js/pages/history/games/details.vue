<template>
  <component :id="id" :is="component">
    <template v-slot:menu>
      <game-menu :id="id" />
    </template>
  </component>
</template>

<script>
import axios from 'axios'
import GameMenu from '~/components/GameMenu'

export default {
  middleware: ['auth', 'verified', '2fa_passed'],

  components: { GameMenu },

  props: ['id'],

  metaInfo () {
    return { title: this.$t('Game {0}', [this.id]) }
  },

  data () {
    return {
      gamePackageId: null
    }
  },

  computed: {
    component () {
      return this.gamePackageId
        ? () => import(/* webpackChunkName: 'js/games/[request]' */ `packages/${this.gamePackageId}/resources/js/pages/details`)
        : null
    }
  },

  async created () {
    const { data } = await axios.get(`/api/history/games/${this.id}/package`)

    this.gamePackageId = data.id
  }
}
</script>
