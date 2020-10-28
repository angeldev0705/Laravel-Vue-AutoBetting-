<template>
  <v-footer>
    <v-menu
      offset-y
      top
      right
      max-height="300"
    >
      <template v-slot:activator="{ on }">
        <v-btn icon v-on="on">
          <country-flag :country="flag(locale)" size="small" />
        </v-btn>
      </template>

      <v-list>
        <v-list-item
          v-for="(lang,locale) in locales"
          :key="locale"
          @click="setLocale(locale)"
        >
          <v-list-item-icon class="mx-0 my-2">
            <country-flag :country="flag(locale)" size="small" />
          </v-list-item-icon>
          <v-list-item-content>
            <v-list-item-title>
              {{ lang.title }}
            </v-list-item-title>
          </v-list-item-content>
        </v-list-item>
      </v-list>
    </v-menu>
    <v-spacer class="flex-grow-0 flex-sm-grow-1" />
    <v-btn v-for="page in pages" :key="page.id" :to="{ name: 'page', params: { id: page.id } }" small text rounded color="primary">
      {{ page.title }}
    </v-btn>
  </v-footer>
</template>

<script>
import { config } from '~/plugins/config'
import { mapState } from 'vuex'
import { loadMessages } from '~/plugins/i18n'
import CountryFlag from 'vue-country-flag'

export default {
  name: 'FooterMenu',

  components: { CountryFlag },

  data () {
    return {
      pages: config('settings.content.footer.menu')
    }
  },

  computed: {
    ...mapState('lang', [
      'locale',
      'locales'
    ])
  },

  methods: {
    flag (locale) {
      return this.locales[locale].flag || locale
    },
    setLocale (locale) {
      if (this.$i18n.locale !== locale) {
        loadMessages(locale)

        this.$store.dispatch('lang/setLocale', { locale })
      }
    }
  }
}
</script>
