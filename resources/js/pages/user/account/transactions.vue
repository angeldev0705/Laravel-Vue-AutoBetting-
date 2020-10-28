<template>
  <v-container>
    <v-row align="center" justify="center">
      <v-col cols="12">
        <v-card>
          <v-toolbar>
            <v-toolbar-title>
              {{ $t('Account transactions') }}
            </v-toolbar-title>
            <v-spacer />
          </v-toolbar>
          <v-card-text>
            <data-table
              api="/api/user/account/transactions"
              :headers="headers"
              :search-enabled="false"
            >
              <template v-slot:item.transactionable="{ item : { transactionable } }">
                <template v-if="transactionable">
                  <template v-if="transactionable.bet >=0 && transactionable.win >= 0">
                    <router-link :to="{ name: 'history.games.show', params: { id: transactionable.id} }">
                      {{ transactionable.title }} #{{ transactionable.id }}
                    </router-link>
                  </template>
                  <template v-else>
                    {{ transactionable.title }}
                  </template>
                </template>
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

export default {
  middleware: ['auth', 'verified', '2fa_passed'],

  components: { DataTable },

  metaInfo () {
    return { title: this.$t('Account transactions') }
  },

  computed: {
    headers () {
      return [
        { text: this.$t('ID'), value: 'id' },
        { text: this.$t('Type'), value: 'transactionable', sortable: false },
        { text: this.$t('Amount'), value: 'amount', format: 'decimal', align: 'right' },
        { text: this.$t('Balance'), value: 'balance', format: 'decimal', align: 'right' },
        { text: this.$t('Created at'), value: 'created_at', align: 'right' }
      ]
    }
  }
}
</script>
