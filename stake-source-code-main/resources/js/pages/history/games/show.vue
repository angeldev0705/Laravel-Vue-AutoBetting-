<template>
  <v-container>
    <v-row align="center" justify="center">
      <v-col cols="12" lg="8">
        <v-card>
          <v-toolbar>
            <v-btn @click="$router.go(-1)" icon>
              <v-icon>mdi-arrow-left</v-icon>
            </v-btn>
            <v-toolbar-title>
              {{ $t('Game {0}', [id]) }}
            </v-toolbar-title>
            <v-spacer />
            <game-menu :id="id" />
          </v-toolbar>
          <v-card-text>
            <key-value-table
              name="game"
              :api="`/api/history/games/${id}`"
              :headers="headers"
            >
              <template v-slot:account.user.name="{ item: { account : { user } } }">
                <user-profile-modal :user="user" />
              </template>
            </key-value-table>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import KeyValueTable from '~/components/KeyValueTable'
import GameMenu from '~/components/GameMenu'
import UserProfileModal from '~/components/UserProfileModal'

export default {
  middleware: ['auth', 'verified', '2fa_passed'],

  components: { KeyValueTable, GameMenu, UserProfileModal },

  props: ['id'],

  metaInfo () {
    return { title: this.$t('Game {0}', [this.id]) }
  },

  computed: {
    headers () {
      return [
        { text: this.$t('Player'), value: 'account.user.name' },
        { text: this.$t('Game'), value: 'title' },
        { text: this.$t('Bet'), value: 'bet', format: 'decimal' },
        { text: this.$t('Win'), value: 'win', format: 'decimal' },
        { text: this.$t('Profit'), value: 'profit', format: 'decimal' },
        { text: this.$t('Created at'), value: 'created_at' },
        { text: this.$t('Updated at'), value: 'updated_at' }
      ]
    }
  }
}
</script>
