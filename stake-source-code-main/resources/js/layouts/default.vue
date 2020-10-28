<template>
  <v-app>
    <v-navigation-drawer v-model="navigationDrawer" app temporary>
      <v-list-item>
        <v-list-item-content>
          <v-list-item-title class="title">
            {{ $t('Navigation') }}
          </v-list-item-title>
        </v-list-item-content>
      </v-list-item>
      <v-divider />
      <template v-if="user && user.is_admin">
        <v-list dense>
          <v-list-item :to="{ name: 'admin.dashboard.index' }" link exact>
            <v-list-item-action>
              <v-icon>mdi-view-dashboard</v-icon>
            </v-list-item-action>
            <v-list-item-content>
              <v-list-item-title>{{ $t('Dashboard') }}</v-list-item-title>
            </v-list-item-content>
          </v-list-item>
          <v-list-item :to="{ name: 'admin.users.index' }" link exact>
            <v-list-item-action>
              <v-icon>mdi-account-multiple</v-icon>
            </v-list-item-action>
            <v-list-item-content>
              <v-list-item-title>{{ $t('Users') }}</v-list-item-title>
            </v-list-item-content>
          </v-list-item>
          <v-list-item :to="{ name: 'admin.accounts.index' }" link exact>
            <v-list-item-action>
              <v-icon>mdi-account-cash</v-icon>
            </v-list-item-action>
            <v-list-item-content>
              <v-list-item-title>{{ $t('Accounts') }}</v-list-item-title>
            </v-list-item-content>
          </v-list-item>
          <v-list-item v-if="paymentsPackageEnabled" :to="{ name: 'admin.deposits.index' }" link exact>
            <v-list-item-action>
              <v-icon>mdi-cash-plus</v-icon>
            </v-list-item-action>
            <v-list-item-content>
              <v-list-item-title>{{ $t('Deposits') }}</v-list-item-title>
            </v-list-item-content>
          </v-list-item>
          <v-list-item v-if="paymentsPackageEnabled" :to="{ name: 'admin.withdrawals.index' }" link exact>
            <v-list-item-action>
              <v-icon>mdi-cash-minus</v-icon>
            </v-list-item-action>
            <v-list-item-content>
              <v-list-item-title>{{ $t('Withdrawals') }}</v-list-item-title>
            </v-list-item-content>
          </v-list-item>
          <v-list-item :to="{ name: 'admin.affiliate.index' }" link exact>
            <v-list-item-action>
              <v-icon>mdi-link</v-icon>
            </v-list-item-action>
            <v-list-item-content>
              <v-list-item-title>{{ $t('Affiliates') }}</v-list-item-title>
            </v-list-item-content>
          </v-list-item>
          <v-list-item :to="{ name: 'admin.games.index' }" link exact>
            <v-list-item-action>
              <v-icon>mdi-cards-playing-outline</v-icon>
            </v-list-item-action>
            <v-list-item-content>
              <v-list-item-title>{{ $t('Games') }}</v-list-item-title>
            </v-list-item-content>
          </v-list-item>
          <v-list-item :to="{ name: 'admin.raffles.index' }" link exact>
            <v-list-item-action>
              <v-icon>mdi-ticket</v-icon>
            </v-list-item-action>
            <v-list-item-content>
              <v-list-item-title>{{ $t('Raffles') }}</v-list-item-title>
            </v-list-item-content>
          </v-list-item>
          <v-list-item :to="{ name: 'admin.chat.index' }" link exact>
            <v-list-item-action>
              <v-icon>mdi-message-outline</v-icon>
            </v-list-item-action>
            <v-list-item-content>
              <v-list-item-title>{{ $t('Chat') }}</v-list-item-title>
            </v-list-item-content>
          </v-list-item>
          <v-list-item v-if="paymentsPackageEnabled" :to="{ name: 'admin.deposit-methods.index' }" link exact>
            <v-list-item-action>
              <v-icon>mdi-cash-marker</v-icon>
            </v-list-item-action>
            <v-list-item-content>
              <v-list-item-title>{{ $t('Deposit methods') }}</v-list-item-title>
            </v-list-item-content>
          </v-list-item>
          <v-list-item v-if="paymentsPackageEnabled" :to="{ name: 'admin.withdrawal-methods.index' }" link exact>
            <v-list-item-action>
              <v-icon>mdi-cash-marker</v-icon>
            </v-list-item-action>
            <v-list-item-content>
              <v-list-item-title>{{ $t('Withdrawal methods') }}</v-list-item-title>
            </v-list-item-content>
          </v-list-item>
          <v-list-item :to="{ name: 'admin.settings.index' }" link exact>
            <v-list-item-action>
              <v-icon>mdi-cog</v-icon>
            </v-list-item-action>
            <v-list-item-content>
              <v-list-item-title>{{ $t('Settings') }}</v-list-item-title>
            </v-list-item-content>
          </v-list-item>
          <v-list-item :to="{ name: 'admin.maintenance.index' }" link exact>
            <v-list-item-action>
              <v-icon>mdi-server</v-icon>
            </v-list-item-action>
            <v-list-item-content>
              <v-list-item-title>{{ $t('Maintenance') }}</v-list-item-title>
            </v-list-item-content>
          </v-list-item>
          <v-list-item :to="{ name: 'admin.add-ons.index' }" link exact>
            <v-list-item-action>
              <v-icon>mdi-shape</v-icon>
            </v-list-item-action>
            <v-list-item-content>
              <v-list-item-title>{{ $t('Add-ons') }}</v-list-item-title>
            </v-list-item-content>
          </v-list-item>
          <v-list-item :to="{ name: 'admin.license.index' }" link exact>
            <v-list-item-action>
              <v-icon>mdi-license</v-icon>
            </v-list-item-action>
            <v-list-item-content>
              <v-list-item-title>{{ $t('License registration') }}</v-list-item-title>
            </v-list-item-content>
          </v-list-item>
        </v-list>
        <v-divider />
      </template>
      <v-list dense>
        <v-list-item :to="{ name: 'home' }" link exact>
          <v-list-item-action>
            <v-icon>mdi-home</v-icon>
          </v-list-item-action>
          <v-list-item-content>
            <v-list-item-title>{{ $t('Home') }}</v-list-item-title>
          </v-list-item-content>
        </v-list-item>
        <template v-if="authenticated">
          <v-list-item v-if="paymentsPackageEnabled" :to="{ name: 'user.account.deposits.index' }" link exact>
            <v-list-item-action>
              <v-icon>mdi-cash-plus</v-icon>
            </v-list-item-action>
            <v-list-item-content>
              <v-list-item-title>{{ $t('Deposits') }}</v-list-item-title>
            </v-list-item-content>
          </v-list-item>
          <v-list-item v-if="paymentsPackageEnabled" :to="{ name: 'user.account.withdrawals.index' }" link exact>
            <v-list-item-action>
              <v-icon>mdi-cash-minus</v-icon>
            </v-list-item-action>
            <v-list-item-content>
              <v-list-item-title>{{ $t('Withdrawals') }}</v-list-item-title>
            </v-list-item-content>
          </v-list-item>
          <v-list-group
            prepend-icon="mdi-cards-playing-outline"
            no-action
          >
            <template v-slot:activator>
              <v-list-item-title>{{ $t('Games') }}</v-list-item-title>
            </template>
            <template v-for="(game, id) in games">
              <v-list-item v-if="!config(id + '.variations')" :key="id" :to="{ name: 'game', params: { game: id }}" link exact>
                <v-list-item-content>
                  <v-list-item-title>{{ game.name }}</v-list-item-title>
                </v-list-item-content>
              </v-list-item>
              <template v-else>
                <template v-for="variation in config(id + '.variations')">
                  <v-list-item :key="variation.slug" :to="{ name: 'game', params: { game: id, slug: variation.slug }}" link exact>
                    <v-list-item-content>
                      <v-list-item-title>{{ variation.title }}</v-list-item-title>
                    </v-list-item-content>
                  </v-list-item>
                </template>
              </template>
            </template>
          </v-list-group>
          <v-list-item v-if="rafflePackageEnabled" :to="{ name: 'raffles' }" link exact>
            <v-list-item-action>
              <v-icon>mdi-ticket</v-icon>
            </v-list-item-action>
            <v-list-item-content>
              <v-list-item-title>{{ $t('Raffles') }}</v-list-item-title>
            </v-list-item-content>
          </v-list-item>
          <v-list-item :to="{ name: 'history' }" link exact>
            <v-list-item-action>
              <v-icon>mdi-history</v-icon>
            </v-list-item-action>
            <v-list-item-content>
              <v-list-item-title>{{ $t('History') }}</v-list-item-title>
            </v-list-item-content>
          </v-list-item>
          <v-list-item :to="{ name: 'leaderboard' }" link exact>
            <v-list-item-action>
              <v-icon>mdi-star</v-icon>
            </v-list-item-action>
            <v-list-item-content>
              <v-list-item-title>{{ $t('Leaderboard') }}</v-list-item-title>
            </v-list-item-content>
          </v-list-item>
        </template>
      </v-list>
    </v-navigation-drawer>

    <chat v-if="authenticated && chatEnabled" v-model="chatDrawer" @message="setUnreadChatMessagesCount" />

    <v-app-bar app clipped-left>
      <v-app-bar-nav-icon @click.stop="navigationDrawer = !navigationDrawer" />

      <v-toolbar-title align="center">
        <router-link :to="{ name: 'home' }">
          <v-avatar size="40" tile>
            <v-img :src="appLogoUrl" :alt="appName" />
          </v-avatar>
        </router-link>
        <span class="ml-2 d-none d-sm-inline">
          {{ appName }}
        </span>
      </v-toolbar-title>

      <v-spacer />

      <template v-if="!token && !authenticated">
        <v-btn :to="{ name: 'login' }" class="secondary">{{ $t('Log in') }}</v-btn>
        <v-btn :to="{ name: 'register' }" class="primary ml-2">{{ $t('Sign up') }}</v-btn>
      </template>
      <template v-else-if="authenticated">
        <v-menu v-model="accountMenu" offset-y left>
          <template v-slot:activator="{ on }">
            <v-btn
              icon
              rounded
              class="px-2 width-auto"
              v-on="on"
            >
              <v-icon>mdi-currency-usd-circle-outline</v-icon>
              <animated-number :number="account.balance" />
            </v-btn>
          </template>
          <v-list>
            <v-subheader class="text-uppercase">{{ $t('Account') }}</v-subheader>
            <v-list-item :to="{ name: 'user.account.transactions' }" link exact>
              <v-list-item-icon>
                <v-icon>mdi-format-list-bulleted</v-icon>
              </v-list-item-icon>
              <v-list-item-content>
                <v-list-item-title>{{ $t('Transactions') }}</v-list-item-title>
              </v-list-item-content>
            </v-list-item>
            <v-list-item v-if="paymentsPackageEnabled" :to="{ name: 'user.account.deposits.index' }" link exact>
              <v-list-item-icon>
                <v-icon>mdi-cash-plus</v-icon>
              </v-list-item-icon>
              <v-list-item-content>
                <v-list-item-title>{{ $t('Deposits') }}</v-list-item-title>
              </v-list-item-content>
            </v-list-item>
            <v-list-item v-if="paymentsPackageEnabled" :to="{ name: 'user.account.withdrawals.index' }" link exact>
              <v-list-item-icon>
                <v-icon>mdi-cash-minus</v-icon>
              </v-list-item-icon>
              <v-list-item-content>
                <v-list-item-title>{{ $t('Withdrawals') }}</v-list-item-title>
              </v-list-item-content>
            </v-list-item>
          </v-list>
        </v-menu>

        <v-menu v-model="settingsMenu" :close-on-content-click="false" offset-y left>
          <template v-slot:activator="{ on }">
            <v-btn icon v-on="on">
              <v-icon>mdi-cog</v-icon>
            </v-btn>
          </template>
          <v-card>
            <v-list>
              <v-subheader class="text-uppercase">
                {{ $t('Settings') }}
              </v-subheader>
              <v-list-item>
                <v-list-item-action>
                  <v-switch
                    :input-value="settings.sounds"
                    :value="settings.sounds"
                    color="primary"
                    @change="saveSettings('sounds', $event)"
                  />
                </v-list-item-action>
                <v-list-item-title>{{ $t('Enable sounds') }}</v-list-item-title>
              </v-list-item>
            </v-list>
          </v-card>
        </v-menu>

        <v-menu v-model="userMenu" :close-on-content-click="false" offset-y>
          <template v-slot:activator="{ on }">
            <v-btn icon v-on="on">
              <v-avatar size="40">
                <img :src="user.avatar_url" :alt="user.name">
              </v-avatar>
            </v-btn>
          </template>

          <v-card>
            <v-list>
              <v-list-item>
                <v-list-item-avatar>
                  <img :src="user.avatar_url" :alt="user.name">
                </v-list-item-avatar>

                <v-list-item-content>
                  <v-list-item-title>{{ user.name }}</v-list-item-title>
                  <v-list-item-subtitle>{{ user.email }}</v-list-item-subtitle>
                </v-list-item-content>

                <v-list-item-action class="flex-row">
                  <v-tooltip bottom>
                    <template v-slot:activator="{ on }">
                      <v-btn icon :to="{ name: 'users.show', params: { id: user.id } }" @click="userMenu = false" v-on="on">
                        <v-icon>mdi-account</v-icon>
                      </v-btn>
                    </template>
                    <span>{{ $t('Profile') }}</span>
                  </v-tooltip>
                  <v-tooltip bottom>
                    <template v-slot:activator="{ on }">
                      <v-btn icon :to="{ name: 'user.security' }" @click="userMenu = false" v-on="on">
                        <v-icon>mdi-shield-lock</v-icon>
                      </v-btn>
                    </template>
                    <span>{{ $t('Security') }}</span>
                  </v-tooltip>
                  <v-tooltip bottom>
                    <template v-slot:activator="{ on }">
                      <v-btn icon :to="{ name: 'user.affiliate' }" @click="userMenu = false" v-on="on">
                        <v-icon>mdi-link</v-icon>
                      </v-btn>
                    </template>
                    <span>{{ $t('Affiliate program') }}</span>
                  </v-tooltip>
                </v-list-item-action>
              </v-list-item>
            </v-list>

            <v-divider></v-divider>

            <v-card-actions>
              <v-spacer />
              <v-btn text @click="logout">
                <v-icon>mdi-logout</v-icon>
                {{ $t('Log out') }}
              </v-btn>
            </v-card-actions>
          </v-card>
        </v-menu>

        <v-btn v-if="chatEnabled" icon @click="chatDrawer = !chatDrawer">
          <v-badge :content="unreadChatMessagesCount" :value="unreadChatMessagesCount" overlap>
            <v-icon>{{ chatDrawer ? 'mdi-message' : 'mdi-message-outline' }}</v-icon>
          </v-badge>
        </v-btn>
      </template>
      <preloader :active="loading" />
    </v-app-bar>

    <v-main>
      <message />
      <router-view />
    </v-main>

    <footer-menu />
  </v-app>
</template>

<script>
import { config } from '~/plugins/config'
import { mapState, mapGetters } from 'vuex'
import Message from '~/components/Message'
import Chat from '~/components/Chat'
import Preloader from '~/components/Preloader'
import FooterMenu from '~/components/FooterMenu'
import AnimatedNumber from '~/components/AnimatedNumber'

export default {
  name: 'DefaultLayout',

  components: { Message, Chat, Preloader, FooterMenu, AnimatedNumber },

  data () {
    return {
      navigationDrawer: false,
      chatDrawer: false,
      accountMenu: false,
      settingsMenu: false,
      userMenu: false,
      unreadChatMessagesCount: 0
    }
  },

  computed: {
    ...mapState('auth', ['user', 'account', 'token']),
    ...mapState('settings', ['settings']),
    ...mapState('progress', ['loading']),
    ...mapGetters({
      authenticated: 'auth/check',
      games: 'package-manager/games'
    }),
    appName () {
      return config('app.name')
    },
    appLogoUrl () {
      return config('app.logo')
    },
    chatEnabled () {
      return config('settings.chat.enabled')
    },
    rafflePackageEnabled () {
      return this.$store.getters['package-manager/get']('raffle')
    },
    paymentsPackageEnabled () {
      return this.$store.getters['package-manager/get']('payments')
    }
  },

  methods: {
    config,
    setUnreadChatMessagesCount (count) {
      this.unreadChatMessagesCount = count
    },
    async logout () {
      // Log out
      await this.$store.dispatch('auth/logout')

      // Redirect to home page
      if (this.$route.name !== 'home') {
        this.$router.push({ name: 'home' })
      }
    },
    saveSettings (key, value) {
      // value is null when switch is turned off
      this.$store.dispatch('settings/set', { key, value: !!value })
    }
  }
}
</script>
