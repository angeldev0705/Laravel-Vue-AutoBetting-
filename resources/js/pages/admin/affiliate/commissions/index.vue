<template>
  <v-container>
    <v-row>
      <v-col cols="12">
        <v-card>
          <v-card-title>
            {{ $t('Affiliates commissions') }}
          </v-card-title>
          <v-card-text>
            <data-table
              api="/api/admin/affiliate/commissions"
              :headers="headers"
              search-enabled
              :search-label="$t('Search by user ID or name')"
            >
              <template v-slot:item.name="{ item: { account: { user } } }">
                <user-link :user="user" />
              </template>
              <template v-slot:item.status="{ item }">
                <span :class="getStatusClass(item)">{{ item.status_title }}</span>
              </template>
              <template v-slot:item.actions="{ item }">
                <affiliate-commission-menu :id="item.id" small />
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
import AffiliateCommissionMenu from '~/components/Admin/AffiliateCommissionMenu'

export default {
  middleware: ['auth', 'verified', '2fa_passed', 'admin'],

  components: { DataTable, UserLink, AffiliateCommissionMenu },

  metaInfo () {
    return { title: this.$t('Affiliates commissions') }
  },

  computed: {
    headers () {
      return [
        { text: this.$t('ID'), value: 'id' },
        { text: this.$t('Name'), value: 'name', sortable: false },
        { text: this.$t('Type'), value: 'title', sortable: false },
        { text: this.$t('Status'), value: 'status', sortable: false },
        { text: this.$t('Amount'), value: 'amount', align: 'right', format: 'decimal' },
        { text: this.$t('Created at'), value: 'created_at', align: 'right' },
        { value: 'actions', sortable: false, align: 'right' }
      ]
    }
  },

  methods: {
    getStatusClass (item) {
      if (item.status === 1) {
        return 'green--text'
      } else if (item.status === 2) {
        return 'error--text'
      }
    }
  }
}
</script>
