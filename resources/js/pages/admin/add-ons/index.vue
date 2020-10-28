<template>
  <v-container>
    <v-row>
      <v-col cols="12">
        <v-card :loading="!data.packages">
          <v-card-title>
            {{ $t('Add-ons') }}
          </v-card-title>
          <v-card-text>
            <v-row v-for="(pkg, id) in data.packages" :key="id">
              <v-col>
                <v-card outlined>
                  <v-card-title class="font-weight-thin">
                    {{ pkg.name }}
                    <template v-if="pkg.enabled">
                      v{{ pkg.version }}
                    </template>
                  </v-card-title>
                  <v-card-text>
                    {{ pkg.description }}
                  </v-card-text>
                  <v-card-actions>
                    <template v-if="pkg.installed && pkg.min_app_version_installed">
                      <template v-if="data.releases['add-ons'][id] && data.releases['add-ons'][id].version !== pkg.version">
                        <v-btn v-if="pkg.hash" text color="primary" :to="{ name: 'admin.add-ons.install', params: { id } }">
                          {{ $t('Upgrade to v{0}', [data.releases['add-ons'][id].version]) }}
                        </v-btn>
                      </template>
                      <template v-else>
                        <v-btn v-if="pkg.hash" text color="primary" :to="{ name: 'admin.add-ons.install', params: { id } }">
                          {{ $t('Re-install') }}
                        </v-btn>
                      </template>
                      <v-btn v-if="pkg.enabled" text color="primary" @click="disable(id)">
                        {{ $t('Disable') }}
                      </v-btn>
                      <v-btn v-else text color="primary" @click="enable(id)">
                        {{ $t('Enable') }}
                      </v-btn>
                      <v-btn text color="primary" :to="{ name: 'admin.add-ons.changelog', params: { id } }">
                        {{ $t('Changelog') }}
                      </v-btn>
                    </template>
                    <template v-else-if="pkg.installed && !pkg.min_app_version_installed">
                      <span class="pl-2 error--text">{{ $t('App v{0} is required to enable this add-on', [pkg.min_app_version]) }}</span>
                    </template>
                    <template v-else>
                      <v-btn v-if="pkg.purchase_url" text color="primary" :href="pkg.purchase_url" target="_blank">
                        {{ $t('Purchase') }}
                      </v-btn>
                      <v-btn v-if="pkg.hash" text color="primary" :to="{ name: 'admin.add-ons.install', params: { id } }">
                        {{ $t('Install') }}
                      </v-btn>
                    </template>
                  </v-card-actions>
                </v-card>
              </v-col>
            </v-row>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import axios from 'axios'
import Form from 'vform'
import FormMixin from '~/mixins/Form'

export default {
  middleware: ['auth', 'verified', '2fa_passed', 'admin'],

  metaInfo () {
    return { title: this.$t('Add-ons') }
  },

  mixins: [FormMixin],

  computed: {
    //
  },

  data () {
    return {
      form: new Form(),
      data: {}
    }
  },

  async created () {
    const { data } = await axios.get('/api/admin/add-ons')

    this.data = data
  },

  methods: {
    async disable (packageId) {
      const { data } = await this.form.post(`/api/admin/add-ons/${packageId}/disable`)

      this.$store.dispatch('message/' + (data.success ? 'success' : 'error'), { text: data.message })

      if (data.success) {
        this.data.packages[packageId].enabled = false
      }
    },
    async enable (packageId) {
      const { data } = await this.form.post(`/api/admin/add-ons/${packageId}/enable`)

      this.$store.dispatch('message/' + (data.success ? 'success' : 'error'), { text: data.message })

      if (data.success) {
        this.data.packages[packageId].enabled = true
      }
    }
  }
}
</script>
