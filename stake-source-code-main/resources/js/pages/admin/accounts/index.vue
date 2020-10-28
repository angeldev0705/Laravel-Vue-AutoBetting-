<template>
  <v-container>
    <v-row>
      <v-col cols="12">
        <v-card>
          <v-card-title>
            {{ $t('Accounts') }}
          </v-card-title>
          <v-card-text>
            <data-table
              api="/api/admin/accounts"
              :headers="headers"
              search-enabled
              :search-label="$t('Search by user ID or name')"
            >
              <template v-slot:item.name="{ item }">
                 <user-link :user="item.user" />
              </template>
              <template v-slot:item.actions="{ item }">
                <account-menu :id="item.id" small />
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
import UserLink from '~/components/Admin/UserLink'
import AccountMenu from '~/components/Admin/AccountMenu'

export default {
  middleware: ['auth', 'verified', '2fa_passed', 'admin'],

  components: { DataTable, UserLink, AccountMenu },

  metaInfo () {
    return { title: this.$t('Accounts') }
  },

  computed: {
    headers () {
      return [
        { text: this.$t('ID'), value: 'id' },
        { text: this.$t('Name'), value: 'name', sortable: false },
        { text: this.$t('Balance'), value: 'balance', align: 'right', format: 'decimal' },
        { text: this.$t('Created at'), value: 'created_at', align: 'right' },
        { value: 'actions', sortable: false, align: 'right' }
      ]
    }
  }
}
</script>
