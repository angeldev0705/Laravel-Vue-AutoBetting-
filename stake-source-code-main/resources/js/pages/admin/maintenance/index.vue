<template>
  <v-container>
    <v-row>
      <v-col cols="12">
        <v-card>
          <v-card-title>
            {{ $t('Maintenance') }}
          </v-card-title>
          <v-card-text>
            <v-row>
              <v-col>
                <v-card outlined>
                  <v-card-title class="font-weight-thin">
                    {{ $t('System info') }}
                  </v-card-title>
                  <v-card-text>
                    <v-skeleton-loader type="text" :loading="!data.system_info">
                      {{ data.system_info }}
                    </v-skeleton-loader>
                  </v-card-text>
                </v-card>
              </v-col>
            </v-row>
            <v-row>
              <v-col>
                <v-card outlined>
                  <v-card-title class="font-weight-thin">
                    {{ $t('Maintenance mode') }}
                  </v-card-title>
                  <v-card-text>
                    <p>
                    {{ $t('You can enable maintenance mode during application upgrade or when doing other service tasks, so nobody except administrators can use the website.') }}
                    </p>
                    <v-form v-model="maintenanceFormIsValid" @submit.prevent="switchMaintenanceMode" class="mt-5">
                      <v-text-field
                        v-model="maintenanceForm.message"
                        :label="$t('Message')"
                        :rules="[validationRequired]"
                        :error="maintenanceForm.errors.has('message')"
                        :error-messages="maintenanceForm.errors.get('message')"
                        :disabled="typeof data.maintenance_mode === 'undefined' || data.maintenance_mode"
                        outlined
                      />

                      <v-skeleton-loader type="button" :loading="typeof data.maintenance_mode === 'undefined'">
                        <v-btn type="submit" color="primary" :disabled="!maintenanceFormIsValid || maintenanceForm.busy" :loading="maintenanceForm.busy">
                          <template v-if="data.maintenance_mode">
                            {{ $t('Disable maintenance mode') }}
                          </template>
                          <template v-else>
                            {{ $t('Enable maintenance mode') }}
                          </template>
                        </v-btn>
                      </v-skeleton-loader>
                    </v-form>
                  </v-card-text>
                </v-card>
              </v-col>
            </v-row>
            <v-row>
              <v-col>
                <v-card outlined>
                  <v-card-title class="font-weight-thin">
                    {{ $t('Tasks') }}
                  </v-card-title>
                  <v-card-text>
                    <p>
                      {{ $t('The app provides a number of service tasks, which can be executed on demand.') }}
                    </p>
                    <v-form v-model="commandFormIsValid" @submit.prevent="executeCommand" class="mt-5">
                      <v-select
                        v-model="commandForm.command"
                        :items="commands"
                        :label="$t('Task')"
                        :error="commandForm.errors.has('command')"
                        :error-messages="commandForm.errors.get('command')"
                        outlined
                        :disabled="!data.commands"
                      />

                      <template v-if="commandForm.command">
                        <template v-for="command in data.commands">
                          <template v-if="command.class === commandForm.command">
                            <v-text-field
                              v-for="(arg, i) in command.arguments"
                              :key="i"
                              v-model="commandForm.arguments[arg.id]"
                              :label="$t(arg.title)"
                              :rules="[validationRequired]"
                              :placeholder="arg.default"
                              :error="commandForm.errors.has('arguments')"
                              :error-messages="commandForm.errors.get('arguments')"
                              outlined
                            />
                          </template>
                        </template>
                      </template>

                      <v-skeleton-loader type="button" :loading="typeof data.commands === 'undefined'">
                        <v-btn type="submit" color="primary" :disabled="!commandForm.command || !commandFormIsValid || commandForm.busy" :loading="commandForm.busy">
                          {{ $t('Execute') }}
                        </v-btn>
                      </v-skeleton-loader>
                    </v-form>
                  </v-card-text>
                </v-card>
              </v-col>
            </v-row>
            <v-row>
              <v-col>
                <v-card outlined>
                  <v-card-title class="font-weight-thin">
                    {{ $t('Database') }}
                  </v-card-title>
                  <v-card-text>
                    <p>
                      {{ $t('After upgrading to a new version of the application it is necessary to update the database objects.') }}
                      {{ $t('All current data will be preserved.') }}
                    </p>
                    <v-form @submit.prevent="migrate">
                      <v-btn type="submit" color="primary" :disabled="migrationForm.busy" :loading="migrationForm.busy">
                        {{ $t('Update database') }}
                      </v-btn>
                    </v-form>
                  </v-card-text>
                </v-card>
              </v-col>
            </v-row>
            <v-row>
              <v-col>
                <v-card outlined>
                  <v-card-title class="font-weight-thin">
                    {{ $t('Cache') }}
                  </v-card-title>
                  <v-card-text>
                    <p>
                    {{ $t('The application caches templates, configuration, translation strings, aggregated data etc to improve performance.') }}
                    {{ $t('To clear all caches at once click the button below.') }}
                    </p>
                    <v-form @submit.prevent="clearCache">
                      <v-btn type="submit" color="primary" :disabled="cacheForm.busy" :loading="cacheForm.busy">
                        {{ $t('Clear cache') }}
                      </v-btn>
                    </v-form>
                  </v-card-text>
                </v-card>
              </v-col>
            </v-row>
            <v-row>
              <v-col>
                <v-card outlined>
                  <v-card-title class="font-weight-thin">
                    {{ $t('Cron') }}
                  </v-card-title>
                  <v-card-text>
                    <p>
                      {{ $t('Certain application tasks are supposed to run automatically on a regular basis.') }}
                      {{ $t('To make it work please add the following system cron job.') }}
                      {{ $t('Please note that the command-line (cli) PHP version on your server should also meet the minimum PHP version requirements, otherwise the cron job will fail to execute.') }}
                      {{ $t('If you add the cron job via cPanel you need to omit the leading asteriks symbols.') }}
                    </p>
                    <v-form @submit.prevent="cron">
                      <v-text-field
                        ref="cron"
                        dense
                        outlined
                        readonly
                        append-icon="mdi-content-copy"
                        @click:append="copyToClipboard($refs.cron)"
                        :value="data.cron_job"
                        :disabled="!data.cron_job"
                      />

                      <v-btn type="submit" color="primary" :disabled="cronForm.busy" :loading="cronForm.busy">
                        {{ $t('Execute cron job manually') }}
                      </v-btn>
                    </v-form>
                  </v-card-text>
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
import { copyToClipboard } from '~/plugins/utils'

export default {
  middleware: ['auth', 'verified', '2fa_passed', 'admin'],

  metaInfo () {
    return { title: this.$t('Maintenance') }
  },

  mixins: [FormMixin],

  computed: {
    commands () {
      return this.data.commands
        ? this.data.commands.map(command => ({
          value: command.class,
          text: command.description
        }))
        : []
    }
  },

  data () {
    return {
      data: {},
      maintenanceFormIsValid: null,
      maintenanceForm: new Form({
        message: this.$t('Sorry, we are doing some maintenance. Please check back soon.')
      }),
      commandFormIsValid: null,
      commandForm: new Form({
        command: null,
        arguments: {}
      }),
      migrationForm: new Form(),
      cacheForm: new Form(),
      cronForm: new Form(),
    }
  },

  async created () {
    const { data } = await axios.get('/api/admin/maintenance')

    this.data = data
  },

  methods: {
    async switchMaintenanceMode () {
      const action = this.data.maintenance_mode ? 'up' : 'down'

      const { data } = await this.maintenanceForm.post(`/api/admin/maintenance/${action}`)

      this.data.maintenance_mode = !this.data.maintenance_mode

      this.$store.dispatch('message/success', { text: data.message })
    },
    async executeCommand () {
      const { data } = await this.commandForm.post('/api/admin/maintenance/command')

      this.$store.dispatch('message/success', { text: data.message })
    },
    async migrate () {
      const { data } = await this.migrationForm.post('/api/admin/maintenance/migrate')

      this.$store.dispatch('message/success', { text: data.message })
    },
    async clearCache () {
      const { data } = await this.cacheForm.post('/api/admin/maintenance/cache')

      this.$store.dispatch('message/success', { text: data.message })
    },
    async cron () {
      const { data } = await this.cronForm.post('/api/admin/maintenance/cron')

      this.$store.dispatch('message/success', { text: data.message })
    },
    copyToClipboard (ref) {
      return copyToClipboard(ref.$el.querySelector('input'))
    }
  }
}
</script>
