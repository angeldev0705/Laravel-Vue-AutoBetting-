<script>
import { config } from '~/plugins/config'
import { route } from '~/plugins/route'

export default {
  computed: {
    gamePackageId () {
      return this.$route.params.game
    },
    gameSlug () {
      return this.$route.params.slug
    },
    config () {
      return this.gameSlug
        ? config(`${this.gamePackageId}`).variations.find(o => o.slug === this.gameSlug)
        : config(`${this.gamePackageId}`)
    },
    provablyFairGame () {
      return this.$store.getters['provably-fair/get'](this.gamePackageId) || {}
    }
  },

  methods: {
    getRoute (action) {
      return route(`games.${this.gamePackageId}.${action}`)
    }
  }
}
</script>
