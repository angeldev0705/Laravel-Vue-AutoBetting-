<template>
  <v-card>
    <template>
      <v-card-title class="flex-column justify-center">
        <v-avatar v-if="user" size="50">
          <img :src="user.avatar_url" :alt="user.name">
        </v-avatar>
        <v-skeleton-loader v-else type="avatar" class="" width="50" />
        <div v-if="user" class="mt-2">
          {{ user.name }}
          <v-btn v-if="currentUser.id === user.id" :to="{ name: 'user.edit' }" class="ml-2" small fab>
            <v-icon>mdi-pencil</v-icon>
          </v-btn>
        </div>
        <v-skeleton-loader v-else type="text" width="50%" class="mt-5" />
      </v-card-title>
      <v-card-subtitle class="text-center mt-n2">
        <template v-if="user">
          {{ $t('Registered') }} {{ user.created_ago }}
        </template>
        <v-skeleton-loader v-else type="text" width="50%" class="mx-auto mt-2" />
      </v-card-subtitle>
      <v-card-text>
        <v-row>
          <v-col cols="12" md="6" lg="4">
            <v-card outlined>
              <v-card-subtitle class="text-center">
                {{ $t('Bets') }}
              </v-card-subtitle>
              <v-card-title class="justify-center pt-0 pb-2">
                <template v-if="stats">
                  <v-icon class="text--secondary">
                    mdi-dice-3-outline
                  </v-icon>
                  {{ integer(stats.bet_count) }}
                </template>
                <v-skeleton-loader v-else type="text" width="50" height="32" class="d-flex align-center" />
              </v-card-title>
            </v-card>
          </v-col>
          <v-col cols="12" md="6" lg="4">
            <v-card outlined>
              <v-card-subtitle class="text-center">
                {{ $t('Wins') }}
              </v-card-subtitle>
              <v-card-title class="justify-center pt-0 pb-2">
                <template v-if="stats">
                  <v-icon class="green--text">
                    mdi-chevron-double-up
                  </v-icon>
                  {{ integer(stats.win_count) }}
                </template>
                <v-skeleton-loader v-else type="text" width="50" height="32" class="d-flex align-center" />
              </v-card-title>
            </v-card>
          </v-col>
          <v-col cols="12" md="6" lg="4">
            <v-card outlined>
              <v-card-subtitle class="text-center">
                {{ $t('Losses') }}
              </v-card-subtitle>
              <v-card-title class="justify-center pt-0 pb-2">
                <template v-if="stats">
                  <v-icon class="red--text">
                    mdi-chevron-double-down
                  </v-icon>
                  {{ integer(stats.bet_count - stats.win_count) }}
                </template>
                <v-skeleton-loader v-else type="text" width="50" height="32" class="d-flex align-center" />
              </v-card-title>
            </v-card>
          </v-col>
        </v-row>
        <v-row class="justify-center">
          <v-col cols="12" md="6" lg="4">
            <v-card outlined>
              <v-card-subtitle class="text-center">
                {{ $t('Wagered') }}
              </v-card-subtitle>
              <v-card-title class="justify-center pt-0 pb-2">
                <template v-if="stats">
                  <v-icon class="text--secondary">
                    mdi-currency-usd-circle-outline
                  </v-icon>
                  {{ integer(stats.bet_total) }}
                </template>
                <v-skeleton-loader v-else type="text" width="50" height="32" class="d-flex align-center" />
              </v-card-title>
            </v-card>
          </v-col>
          <v-col cols="12" md="6" lg="4">
            <v-card outlined>
              <v-card-subtitle class="text-center">
                {{ $t('Profit') }}
              </v-card-subtitle>
              <v-card-title class="justify-center pt-0 pb-2">
                <template v-if="stats">
                  <span v-if="typeof stats.profit_total !== 'undefined'">
                    <v-icon v-if="stats.profit_total > 0" class="green--text">
                      mdi-emoticon-happy-outline
                    </v-icon>
                    <v-icon v-else-if="stats.profit_total < 0" class="red--text">
                      mdi-emoticon-sad-outline
                    </v-icon>
                    {{ integer(stats.profit_total) }}
                  </span>
                  <span v-else>
                    {{ $t('hidden') }}
                  </span>
                </template>
                <v-skeleton-loader v-else type="text" width="50" height="32" class="d-flex align-center" />
              </v-card-title>
            </v-card>
          </v-col>
          <v-col cols="12" md="6" lg="4">
            <v-card outlined>
              <v-card-subtitle class="text-center">
                {{ $t('Max profit') }}
              </v-card-subtitle>
              <v-card-title class="justify-center pt-0 pb-2">
                <template v-if="stats">
                  <span v-if="typeof stats.profit_max !== 'undefined'">
                    <v-icon class="text--secondary">
                      mdi-arrow-collapse-up
                    </v-icon>
                    {{ integer(stats.profit_max) }}
                  </span>
                  <span v-else>
                    {{ $t('hidden') }}
                  </span>
                </template>
                <v-skeleton-loader v-else type="text" width="50" height="32" class="d-flex align-center" />
              </v-card-title>
            </v-card>
          </v-col>
        </v-row>
      </v-card-text>
    </template>
  </v-card>
</template>

<script>
import axios from 'axios'
import { mapState } from 'vuex'
import { integer } from '~/plugins/format'

export default {
  props: ['id'],

  metaInfo () {
    return { title: this.$t('User profile', [this.id]) }
  },

  data () {
    return {
      user: null,
      stats: null
    }
  },

  computed: {
    ...mapState('auth', { currentUser: 'user' })
  },

  async created () {
    const { data } = await axios.get(`/api/users/${this.id}`)

    this.user = data.user
    this.stats = data.stats
  },

  methods: {
    integer
  }
}
</script>
