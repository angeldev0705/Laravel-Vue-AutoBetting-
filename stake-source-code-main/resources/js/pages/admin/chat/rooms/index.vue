<template>
  <v-container>
    <v-row>
      <v-col cols="12">
        <v-card>
          <v-card-title>
            {{ $t('Chat rooms') }}
          </v-card-title>
          <v-card-text>
            <v-btn color="primary" :to="{ name: 'admin.chat.rooms.create' }" class="mb-3">
              {{ $t('Create chat room') }}
            </v-btn>
            <data-table
              api="/api/admin/chat/rooms"
              :headers="headers"
            >
              <template v-slot:item.actions="{ item }">
                <chat-room-menu :id="item.id" small />
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
import ChatRoomMenu from '~/components/Admin/ChatRoomMenu'

export default {
  middleware: ['auth', 'verified', '2fa_passed', 'admin'],

  components: { ChatRoomMenu, DataTable },

  metaInfo () {
    return { title: this.$t('Chat rooms') }
  },

  computed: {
    headers () {
      return [
        { text: this.$t('ID'), value: 'id' },
        { text: this.$t('Name'), value: 'name' },
        { text: this.$t('Status'), value: 'status_title' },
        { text: this.$t('Messages count'), value: 'messages_count', align: 'right' },
        { text: this.$t('Created at'), value: 'created_at', align: 'right' },
        { text: this.$t('Updated at'), value: 'updated_at', align: 'right' },
        { value: 'actions', sortable: false, align: 'right' }
      ]
    }
  }
}
</script>
