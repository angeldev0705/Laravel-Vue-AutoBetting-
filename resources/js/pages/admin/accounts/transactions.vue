<template>
  <v-container>
    <v-row align="center" justify="center">
      <v-col cols="12" md="8">
        <v-card>
          <v-toolbar>
            <v-btn @click="$router.go(-1)" icon>
              <v-icon>mdi-arrow-left</v-icon>
            </v-btn>
            <v-toolbar-title>
              {{ $t('Account {0}', [id]) }}
            </v-toolbar-title>
            <v-spacer />
            <account-menu :id="id" />
          </v-toolbar>
          <v-card-text>
            <data-table
              :api="`/api/admin/accounts/${id}/transactions`"
              :headers="headers"
              :search-enabled="false"
            >
              <template v-slot:item.transactionable="{ item : { transactionable } }">
                <template v-if="transactionable">
                  <template v-if="transactionable.bet >=0 && transactionable.win >= 0">
                    <router-link :to="{ name: 'admin.games.show', params: { id: transactionable.id} }">
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
import AccountMenu from '~/components/Admin/AccountMenu'

export default {
  middleware: ['auth', 'verified', '2fa_passed', 'admin'],

  components: { DataTable, AccountMenu },

  props: ['id'],

  metaInfo () {
    return { title: this.$t('Account {0}', [this.id]) }
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
