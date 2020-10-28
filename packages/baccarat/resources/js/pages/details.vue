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
            <slot name="menu" />
          </v-toolbar>
          <v-card-text>
            <key-value-table
              name="game"
              :api="`/api/admin/games/${id}/details`"
              :headers="headers"
            >
              <template v-slot:gameable.deck="{ item: { gameable } }">
                <card v-for="card in gameable.deck.slice(0,8)" :key="`deck-${card}`" :card="card" />
                ...
              </template>
              <template v-slot:gameable.player_hand="{ item: { gameable } }">
                <card v-for="card in gameable.player_hand" :key="`player-${card}`" :card="card" />
              </template>
              <template v-slot:gameable.player_score="{ item: { gameable } }">
                {{ gameable.player_score }}
              </template>
              <template v-slot:gameable.banker_hand="{ item: { gameable } }">
                <card v-for="card in gameable.banker_hand" :key="`banker-${card}`" :card="card" />
              </template>
              <template v-slot:gameable.banker_score="{ item: { gameable } }">
                {{ gameable.banker_score }}
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
import PlayingCardAbbreviation from '~/components/Games/Cards/PlayingCardAbbreviation'

export default {
  components: { KeyValueTable, Card: PlayingCardAbbreviation },

  props: ['id'],

  computed: {
    headers () {
      return [
        { text: this.$t('Deck'), value: 'gameable.deck' },
        { text: this.$t('Bet'), value: 'gameable.bet_type_title' },
        { text: this.$t('Win'), value: 'gameable.win_type_title' },
        { text: this.$t('Player hand'), value: 'gameable.player_hand' },
        { text: this.$t('Player score'), value: 'gameable.player_score' },
        { text: this.$t('Banker hand'), value: 'gameable.banker_hand' },
        { text: this.$t('Banker score'), value: 'gameable.banker_score' },
        { text: this.$t('Created at'), value: 'gameable.created_at' },
        { text: this.$t('Updated at'), value: 'gameable.updated_at' }
      ]
    }
  }
}
</script>
