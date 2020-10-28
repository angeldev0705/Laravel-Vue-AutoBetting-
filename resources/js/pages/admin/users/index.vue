<template>
  <v-container>
    <v-row>
      <v-col cols="12">
        <v-card>
          <v-card-title>
            {{ $t('Users') }}
          </v-card-title>
          <v-card-text>
            <data-table
              api="/api/admin/users"
              :headers="headers"
              search-enabled
            >
              <template v-slot:item.name="{ item }">
                 <user-link :user="item" />
              </template>
              <template v-slot:item.email="{ item }">
                <a :href="'mailto:' + item.email">{{ item.email }}</a>
                <v-tooltip v-if="item.email_verified_at" bottom>
                  <template v-slot:activator="{ on }">
                    <v-icon color="success" v-on="on" small>mdi-check</v-icon>
                  </template>
                  <span>{{ $t('Email verified') }}</span>
                </v-tooltip>
              </template>
              <template v-slot:item.actions="{ item }">
                <user-menu :id="item.id" small />
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
import UserMenu from '~/components/Admin/UserMenu'

export default {
  middleware: ['auth', 'verified', '2fa_passed', 'admin'],

  components: { DataTable, UserLink, UserMenu },

  metaInfo () {
    return { title: this.$t('Users') }
  },

  computed: {
    headers () {
      return [
        { text: this.$t('ID'), value: 'id' },
        { text: this.$t('Name'), value: 'name' },
        { text: this.$t('Email'), value: 'email' },
        { text: this.$t('Created at'), value: 'created_at', align: 'right' },
        { value: 'actions', sortable: false, align: 'right' }
      ]
    }
  }
}
</script>
