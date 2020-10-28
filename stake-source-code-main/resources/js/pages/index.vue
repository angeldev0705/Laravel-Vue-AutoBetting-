<template>
  <div>
    <v-carousel
      :cycle="slider.cycle && slidesCount > 1"
      :hide-delimiters="!slider.pagination || slidesCount === 1"
      :show-arrows="slider.navigation && slidesCount > 1"
      :interval="slider.interval * 1000"
      hide-delimiter-background
      :height="slider.height"
    >
      <v-carousel-item
        v-for="(slide, i) in slider.slides"
        :key="i"
      >
        <v-parallax :src="slide.image.url" :height="slider.height">
          <v-row align="center" justify="center" class="darken-60">
            <v-col class="text-center" cols="12">
              <h2 v-if="slide.title" class="display-2">
                {{ slide.title }}
              </h2>
              <h3 v-if="slide.subtitle" class="display-1 font-weight-thin mt-5">
                {{ slide.subtitle }}
              </h3>
              <div v-if="slide.link.title" class="mt-5">
                <v-btn color="primary" large :href="slide.link.url">
                  {{ slide.link.title }}
                </v-btn>
              </div>
            </v-col>
          </v-row>
        </v-parallax>
      </v-carousel-item>
    </v-carousel>

    <v-container class="mt-10">
      <v-row>
        <v-col class="text-center">
          <h3 class="display-1 font-weight-thin">
            {{ $t('Enjoy our exciting games') }}
          </h3>
        </v-col>
      </v-row>
      <v-row v-if="categories.length > 1">
        <v-col>
          <v-chip-group
            v-model="selectedCategory"
            active-class="primary"
            mandatory
          >
            <v-chip label active @click="filterByCategory('')">
              {{ $t('All') }}
            </v-chip>
            <v-chip v-for="category in categories" :key="category" label @click="filterByCategory(category)">
              {{ category }}
            </v-chip>
          </v-chip-group>
        </v-col>
      </v-row>
      <v-row ref="games" class="justify-center">
        <template v-for="(game, id) in games">
          <v-col
            v-if="!config(id + '.variations')"
            :key="id"
            cols="12"
            md="6"
            lg="3"
            :data-groups="JSON.stringify(config(id + '.categories') || [])"
            class="game-card"
          >
            <game-card :id="id" :name="game.name" :banner="config(id + '.banner')" />
          </v-col>
          <template v-else>
            <v-col
              v-for="variation in config(id + '.variations')"
              :key="variation.slug"
              cols="12"
              md="6"
              lg="3"
              :data-groups="JSON.stringify(variation.categories || [])" class="game-card"
            >
              <game-card :id="id" :name="variation.title" :slug="variation.slug" :banner="variation.banner" />
            </v-col>
          </template>
        </template>
      </v-row>
    </v-container>

    <v-parallax src="/images/provably-fair.jpg" height="350" class="mt-10">
      <v-row align="center" justify="center" class="darken-50">
        <v-col class="text-center">
          <h3 class="display-1 mb-5">
            <v-icon class="display-1" large>mdi-check-decagram</v-icon>
            {{ $t('Provably fair') }}
          </h3>
          <div class="title font-weight-thin mb-5">
            {{ $t('All our games are provably fair.') }}
            {{ $t('We do not cheat.') }}
          </div>
          <div class="text-center">
            <v-btn
              :to="{ name: 'page', params: { id: 'provably-fair' } }"
              outlined
              large
              dark
            >
              {{ $t('Learn more') }}
            </v-btn>
          </div>
        </v-col>
      </v-row>
    </v-parallax>

    <v-container class="mt-10">
      <v-row>
        <v-col class="text-center">
          <h3 class="display-1 font-weight-thin">{{ $t('Earn free credits and bonuses') }}</h3>
        </v-col>
      </v-row>
      <v-row class="justify-center">
        <v-col cols="12" md="6" lg="3" class="text-center">
          <v-card elevation="6" outlined shaped style="min-height: 220px">
            <div class="mt-5">
              <v-avatar color="primary" size="60">
                <v-icon color="white" large>
                  mdi-account-plus
                </v-icon>
              </v-avatar>
            </div>
            <v-card-title class="d-block">
              {{ $t('Sign up bonus') }}
            </v-card-title>

            <v-card-text>
              <p>
                {{ $t('Sign up and get {0} free credits to play.', [config('settings.bonuses.sign_up')] ) }}
              </p>
            </v-card-text>
          </v-card>
        </v-col>
        <v-col v-if="paymentsPackageEnabled" cols="12" md="6" lg="3" class="text-center">
          <v-card elevation="6" outlined shaped style="min-height: 220px">
            <div class="mt-5">
              <v-avatar color="primary" size="60">
                <v-icon color="white" large>
                  mdi-cash-plus
                </v-icon>
              </v-avatar>
            </div>
            <v-card-title class="d-block">
              {{ $t('Deposit bonus') }}
            </v-card-title>

            <v-card-text>
              <p>
                {{ $t('Get {0}% on top when you deposit {1} credits or more.', [config('settings.bonuses.deposit.pct'), config('settings.bonuses.deposit.threshold')] ) }}
              </p>
            </v-card-text>
          </v-card>
        </v-col>
        <v-col cols="12" md="6" lg="3" class="text-center">
          <v-card elevation="6" outlined shaped style="min-height: 220px">
            <div class="mt-5">
              <v-avatar color="primary" size="60">
                <v-icon color="white" large>
                  mdi-link
                </v-icon>
              </v-avatar>
            </div>
            <v-card-title class="d-block">
              {{ $t('Affiliate program') }}
            </v-card-title>

            <v-card-text>
              <p>
                {{ $t('Refer your friends to the casino and get bonuses when they play games.') }}
              </p>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>
    </v-container>

    <v-container class="mt-10">
      <v-row>
        <v-col class="text-center">
          <h3 class="display-1 font-weight-thin">{{ $t('Recent games') }}</h3>
        </v-col>
      </v-row>
      <v-row class="justify-center">
        <v-col cols="12" lg="8">
          <v-list v-if="recentGames === null">
            <v-skeleton-loader v-for="(v,i) in Array(10).fill(0)" :key="i" type="list-item-avatar-two-line" />
          </v-list>
          <p v-else-if="recentGames.length === 0" class="text-center">
            {{ $t('No games found') }}
          </p>
          <v-list v-else subheader>
            <v-list-item v-for="game in recentGames" :key="game.id" :to="{ name: 'history.games.show', params: { id: game.id } }">
              <v-list-item-avatar>
                <v-img :src="game.account.user.avatar_url" />
              </v-list-item-avatar>

              <v-list-item-content>
                <v-list-item-title>
                  {{ game.account.user.name }}
                </v-list-item-title>
                <v-list-item-subtitle>
                  {{ game.title }}
                </v-list-item-subtitle>
              </v-list-item-content>

              <v-list-item-icon>
                <v-chip v-if="game.win > game.bet" color="success">
                  <v-icon left small>mdi-thumb-up</v-icon>
                  {{ $t('Won') }} {{ decimal(game.win - game.bet, 2) }}
                </v-chip>
                <v-chip v-else-if="game.win < game.bet" color="error">
                  <v-icon left small>mdi-thumb-down</v-icon>
                  {{ $t('Lost') }} {{ decimal(game.bet - game.win) }}
                </v-chip>
                <v-chip v-else color="warning">
                  <v-icon left small>mdi-thumbs-up-down</v-icon>
                  {{ $t('Tie') }}
                </v-chip>
              </v-list-item-icon>
            </v-list-item>
          </v-list>
        </v-col>
      </v-row>
    </v-container>
  </div>
</template>
<script>
import axios from 'axios'
import { decimal } from '~/plugins/format'
import { mapGetters } from 'vuex'
import { config } from '~/plugins/config'
import GameCard from '~/components/GameCard'
import Shuffle from 'shufflejs'

export default {
  components: { GameCard },

  metaInfo () {
    return {
      title: this.$t('Fair online casino games')
    }
  },

  data () {
    return {
      selectedCategory: null,
      shuffle: null,
      recentGames: null
    }
  },

  computed: {
    ...mapGetters({
      games: 'package-manager/games'
    }),
    appName () {
      return config('app.name')
    },
    appLogo () {
      return config('app.logo')
    },
    appBanner () {
      return config('app.banner')
    },
    categories () {
      let categories = []

      Object.keys(this.games).forEach(id => {
        const variations = this.config(id + '.variations')

        if (variations) {
          variations.forEach(variation => {
            categories = categories.concat(variation.categories)
          })
        } else {
          categories = categories.concat(this.config(id + '.categories'))
        }
      })

      // remove duplicates
      return categories.filter((category, i) => category && categories.indexOf(category) === i)
    },
    paymentsPackageEnabled () {
      return this.$store.getters['package-manager/get']('payments')
    },
    slider () {
      return config('settings.content.home.slider')
    },
    slidesCount () {
      return this.slider ? this.slider.slides.length : 0
    }
  },

  async created () {
    const { data } = await axios.get('/api/public/games/recent')

    this.recentGames = data
  },

  methods: {
    config,
    decimal,
    filterByCategory (category) {
      if (!this.shuffle) {
        this.shuffle = new Shuffle(this.$refs.games, { itemSelector: '.game-card' })
      }

      this.shuffle.filter(category)
    }
  }
}
</script>
<style lang="scss" scoped>
.v-chip-group::v-deep {
  .v-slide-group__content {
    justify-content: center;
  }
}

.darken-50 {
  background: rgba(0, 0, 0, 0.5);
}

.darken-60 {
  background: rgba(0, 0, 0, 0.6);
}
</style>
