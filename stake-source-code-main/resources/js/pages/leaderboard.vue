<template>
  <v-container>
    <v-row align="center" justify="center">
      <v-col cols="12">
        <v-card>
          <v-toolbar>
            <v-toolbar-title>
              {{ $t('Leaderboard') }}
            </v-toolbar-title>
            <v-spacer />
            <filter-menu @apply="filters = $event" />
          </v-toolbar>
          <v-card-text>
            <data-table
              api="/api/leaderboard"
              :filters="filters"
              :headers="headers"
              sort-by="bet_total"
            >
              <template v-slot:item.name="{ item }">
                <v-avatar size="25">
                  <v-img :src="item.avatar_url" />
                </v-avatar>
                <user-profile-modal :user="{ id: item.id, name: item.name }" />
              </template>
            </data-table>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import DataTable from '~/components/DataTable'
import UserProfileModal from '~/components/UserProfileModal'
import FilterMenu from '~/components/Filters/FilterMenu'

export default {
  middleware: ['auth', 'verified', '2fa_passed'],

  components: { FilterMenu, DataTable, UserProfileModal },

  metaInfo () {
    return { title: this.$t('Leaderboard') }
  },

  data () {
    return {
      filters: {}
    }
  },

  computed: {
    headers () {
      return [
        { text: this.$t('Player'), value: 'name' },
        { text: this.$t('Bets'), value: 'bet_count', format: 'integer', align: 'right' },
        { text: this.$t('Wagered'), value: 'bet_total', format: 'decimal', align: 'right' },
        { text: this.$t('Profit'), value: 'profit_total', format: 'decimal', align: 'right' },
        { text: this.$t('Max profit'), value: 'profit_max', format: 'decimal', align: 'right' }
      ]
    }
  }
}
</script>
