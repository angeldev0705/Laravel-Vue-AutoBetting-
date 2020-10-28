<template>
  <v-container>
    <v-row align="center" justify="center">
      <v-col cols="12" md="6">
        <v-card>
          <v-toolbar>
            <v-btn @click="$router.go(-1)" icon>
              <v-icon>mdi-arrow-left</v-icon>
            </v-btn>
            <v-toolbar-title>
              {{ $t('User {0}', [id]) }}
            </v-toolbar-title>
            <v-spacer />
            <user-menu :id="id" />
          </v-toolbar>
          <v-card-text>
            <key-value-table
              name="user"
              :api="`/api/admin/users/${id}`"
              :headers="headers"
            >
              <template v-slot:referrer="{ item: user }">
                <router-link v-if="user.referrer" :to="{ name: 'admin.users.show', params: { id: user.referrer.id } }">{{ user.referrer.name }}</router-link>
              </template>
              <template v-slot:avatar="{ item: user }">
                <v-avatar size="40">
                  <img :src="user.avatar_url" :alt="user.name">
                </v-avatar>
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
import UserMenu from '~/components/Admin/UserMenu'

export default {
  middleware: ['auth', 'verified', '2fa_passed', 'admin'],

  components: { KeyValueTable, UserMenu },

  props: ['id'],

  metaInfo () {
    return { title: this.$t('User {0}', [this.id]) }
  },

  computed: {
    headers () {
      return [
        { text: this.$t('ID'), value: 'id' },
        { text: this.$t('Referrer'), value: 'referrer' },
        { text: this.$t('Avatar'), value: 'avatar' },
        { text: this.$t('Name'), value: 'name' },
        { text: this.$t('Email'), value: 'email' },
        { text: this.$t('Role'), value: 'role_title' },
        { text: this.$t('Status'), value: 'status_title' },
        { text: this.$t('Banned from chat'), value: 'banned_from_chat' },
        { text: this.$t('Created at'), value: 'created_at' },
        { text: this.$t('Updated at'), value: 'updated_at' },
        { text: this.$t('Email verified at'), value: 'email_verified_at' },
        { text: this.$t('Last login at'), value: 'last_login_at' },
        { text: this.$t('Last login from'), value: 'last_login_from' },
        { text: this.$t('Two-factor authentication'), value: 'two_factor_auth_enabled' }
      ]
    }
  }
}
</script>
