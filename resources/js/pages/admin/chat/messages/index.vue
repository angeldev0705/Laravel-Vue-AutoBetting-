<template>
  <v-container>
    <v-row>
      <v-col cols="12">
        <v-card>
          <v-card-title>
            {{ $t('Chat messages') }}
          </v-card-title>
          <v-card-text>
            <data-table
              api="/api/admin/chat/messages"
              :headers="headers"
              search-enabled
              :search-label="$t('Search by user ID or name')"
            >
              <template v-slot:item.name="{ item: { user } }">
                <user-link :user="user" />
              </template>
              <template v-slot:item.actions="{ item }">
                <chat-message-menu :id="item.id" small />
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
import ChatMessageMenu from '~/components/Admin/ChatMessageMenu'

export default {
  middleware: ['auth', 'verified', '2fa_passed', 'admin'],

  components: { ChatMessageMenu, DataTable, UserLink },

  metaInfo () {
    return { title: this.$t('Chat messages') }
  },

  computed: {
    headers () {
      return [
        { text: this.$t('ID'), value: 'id' },
        { text: this.$t('Name'), value: 'name', sortable: false },
        { text: this.$t('Room'), value: 'room.name', sortable: false },
        { text: this.$t('Message'), value: 'message', sortable: false },
        { text: this.$t('Created at'), value: 'created_at', align: 'right' },
        { value: 'actions', sortable: false, align: 'right' }
      ]
    }
  }
}
</script>
