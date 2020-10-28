<template>
  <v-container>
    <v-row align="center" justify="center">
      <v-col cols="12">
        <v-card>
          <v-toolbar>
            <v-toolbar-title>
              {{ $t('Biggest wins') }}
            </v-toolbar-title>
            <v-spacer />
          </v-toolbar>
          <v-card-text>
            <data-table
              api="/api/history/wins"
              :headers="headers"
              :search-enabled="false"
              hide-footer
            >
              <template v-slot:item.account.user.name="{ item : { account : { user } } }">
                <v-avatar size="25">
                  <v-img :src="user.avatar_url" />
                </v-avatar>
                <user-profile-modal :user="user" />
              </template>
              <template v-slot:item.actions="{ item }">
                <game-menu :id="item.id" small />
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
import GameMenu from '~/components/GameMenu'
import UserProfileModal from '~/components/UserProfileModal'

export default {
  middleware: ['auth', 'verified', '2fa_passed'],

  components: { DataTable, GameMenu, UserProfileModal },

  metaInfo () {
    return { title: this.$t('Biggest wins') }
  },

  computed: {
    headers () {
      return [
        { text: this.$t('Player'), value: 'account.user.name', sortable: false },
        { text: this.$t('Game'), value: 'title', sortable: false },
        { text: this.$t('Bet'), value: 'bet', align: 'right', format: 'decimal', sortable: false },
        { text: this.$t('Win'), value: 'win', align: 'right', format: 'decimal', sortable: false },
        { text: this.$t('Profit'), value: 'profit', align: 'right', format: 'decimal', sortable: false },
        { text: this.$t('Created at'), value: 'created_at', align: 'right', sortable: false },
        { value: 'actions', align: 'right', sortable: false }
      ]
    }
  }
}
</script>
