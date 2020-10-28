<template>
  <v-container>
    <v-card>
      <v-card-title>
        {{ $t('Settings') }}
      </v-card-title>
      <v-card-text>
        <v-form v-model="formIsValid" @submit.prevent="save">
          <v-tabs vertical>
            <v-tab>
              {{ $t('General') }}
            </v-tab>
            <v-tab>
              {{ $t('Theme') }}
            </v-tab>
            <v-tab>
              {{ $t('Content') }}
            </v-tab>
            <v-tab>
              {{ $t('Security') }}
            </v-tab>
            <v-tab>
              {{ $t('Games') }}
            </v-tab>
            <v-tab>
              {{ $t('Chat') }}
            </v-tab>
            <v-tab>
              {{ $t('Bots') }}
            </v-tab>
            <v-tab>
              {{ $t('Bonuses') }}
            </v-tab>
            <v-tab>
              {{ $t('Affiliate program') }}
            </v-tab>
            <v-tab>
              {{ $t('Services') }}
            </v-tab>
            <v-tab>
              {{ $t('OAuth providers') }}
            </v-tab>
            <v-tab class="text-left">
              {{ $t('Formatting') }}
            </v-tab>
            <v-tab class="text-left">
              {{ $t('E-mail') }}
            </v-tab>
            <v-tab>
              {{ $t('Logging') }}
            </v-tab>
            <template v-for="(pkg, id) in packages">
              <v-tab v-if="packageComponents[id]" :key="id">
                {{ $t('Add-on') }}: {{ pkg.name }}
              </v-tab>
            </template>

            <v-tab-item>
              <v-card flat>
                <v-card-text>
                  <v-text-field
                    v-model="form.APP_NAME"
                    :label="$t('App name')"
                    :rules="[validationRequired]"
                    :error="form.errors.has('APP_NAME')"
                    :error-messages="form.errors.get('APP_NAME')"
                    outlined
                    @keydown="clearFormErrors($event,'APP_NAME')"
                  />

                  <v-select
                    v-model="form.LOCALE"
                    :items="languages"
                    :label="$t('Default language')"
                    :error="form.errors.has('LOCALE')"
                    :error-messages="form.errors.get('LOCALE')"
                    outlined
                  />

                  <file-upload
                    v-model="form.APP_LOGO"
                    :label="$t('Logo')"
                    name="logo"
                    folder="images"
                  />
                </v-card-text>
              </v-card>
            </v-tab-item>
            <v-tab-item>
              <v-card flat>
                <v-card-text>
                  <v-select
                    v-model="form.THEME_MODE"
                    :items="modes"
                    :label="$t('Mode')"
                    :error="form.errors.has('THEME_MODE')"
                    :error-messages="form.errors.get('THEME_MODE')"
                    outlined
                  />

                  <template v-for="color in colors">
                    <color-picker :key="color" v-model="form[color]" :label="colorText(color)" />
                  </template>

                  <v-select
                    v-model="form.THEME_BACKGROUND"
                    :items="backgrounds"
                    :label="$t('Background')"
                    :error="form.errors.has('THEME_BACKGROUND')"
                    :error-messages="form.errors.get('THEME_BACKGROUND')"
                    outlined
                  />
                </v-card-text>
              </v-card>
            </v-tab-item>
            <v-tab-item>
              <v-card flat>
                <v-card-text>
                  <v-expansion-panels>
                    <v-expansion-panel>
                      <v-expansion-panel-header>{{ $t('Home page slider') }}</v-expansion-panel-header>
                      <v-expansion-panel-content>
                        <v-text-field
                          v-model.number="form.CONTENT_HOME_SLIDER.height"
                          :label="$t('Height')"
                          :rules="[v => validationInteger(parseInt(v, 10)), validationPositiveNumber]"
                          :suffix="$t('px')"
                          outlined
                          class="mt-3"
                        />

                        <v-switch
                          v-model="form.CONTENT_HOME_SLIDER.navigation"
                          :label="$t('Navigation')"
                          color="primary"
                          :false-value="false"
                          :true-value="true"
                        />

                        <v-switch
                          v-model="form.CONTENT_HOME_SLIDER.pagination"
                          :label="$t('Pagination')"
                          color="primary"
                          :false-value="false"
                          :true-value="true"
                        />

                        <v-switch
                          v-model="form.CONTENT_HOME_SLIDER.cycle"
                          :label="$t('Auto rotate slides')"
                          color="primary"
                          :false-value="false"
                          :true-value="true"
                        />

                        <v-text-field
                          v-show="form.CONTENT_HOME_SLIDER.cycle"
                          v-model.number="form.CONTENT_HOME_SLIDER.interval"
                          :label="$t('Interval')"
                          :rules="[v => validationInteger(parseInt(v, 10)), validationPositiveNumber]"
                          :suffix="$t('seconds')"
                          outlined
                          class="mt-3"
                        />

                        <v-expansion-panels>
                          <v-expansion-panel
                            v-for="(slide, i) in form.CONTENT_HOME_SLIDER.slides"
                            :key="i"
                          >
                            <v-expansion-panel-header>
                              {{ $t('Slide {0}', [i+1]) }}
                            </v-expansion-panel-header>
                            <v-expansion-panel-content>
                              <v-text-field
                                v-model="form.CONTENT_HOME_SLIDER.slides[i].title"
                                :label="$t('Title')"
                                outlined
                              />

                              <v-text-field
                                v-model="form.CONTENT_HOME_SLIDER.slides[i].subtitle"
                                :label="$t('Subtitle')"
                                outlined
                              />

                              <file-upload
                                v-model="form.CONTENT_HOME_SLIDER.slides[i].image.url"
                                :label="$t('Image')"
                                :name="`home-slide-${i}`"
                                folder="images"
                              />

                              <v-text-field
                                v-model="form.CONTENT_HOME_SLIDER.slides[i].link.title"
                                :label="$t('Action button title')"
                                outlined
                              />

                              <v-text-field
                                v-model="form.CONTENT_HOME_SLIDER.slides[i].link.url"
                                :label="$t('Action button URL')"
                                outlined
                              />

                              <v-btn v-if="form.CONTENT_HOME_SLIDER.slides.length > 1" color="primary" @click="deleteSlide(i)">
                                {{ $t('Delete slide') }}
                              </v-btn>
                            </v-expansion-panel-content>
                          </v-expansion-panel>
                        </v-expansion-panels>

                        <v-btn color="primary" class="mt-5" @click="addSlide">
                          {{ $t('Add slide') }}
                        </v-btn>

                      </v-expansion-panel-content>
                    </v-expansion-panel>
                    <v-expansion-panel>
                      <v-expansion-panel-header>{{ $t('Footer menu') }}</v-expansion-panel-header>
                      <v-expansion-panel-content>
                        <template v-for="(page, i) in form.CONTENT_FOOTER_MENU">
                          <v-row :key="i">
                            <v-col cols="12" md="4">
                              <v-text-field
                                v-model="form.CONTENT_FOOTER_MENU[i].id"
                                :label="$t('Page ID')"
                                :rules="[validationRequired]"
                                outlined
                                :hide-details="true"
                                append-icon="mdi-open-in-new"
                                @click:append="openLink(`/pages/${form.CONTENT_FOOTER_MENU[i].id}`)"
                              />
                            </v-col>
                            <v-col cols="12" md="8">
                              <v-text-field
                                v-model="form.CONTENT_FOOTER_MENU[i].title"
                                :label="$t('Page title')"
                                :rules="[validationRequired]"
                                outlined
                                :hide-details="true"
                                append-outer-icon="mdi-delete"
                                @click:append-outer="form.CONTENT_FOOTER_MENU.splice(i, 1)"
                              />
                            </v-col>
                            <v-col cols="12">
                              <p>
                                {{ $t('The HTML page content should be placed to {0}.', [`storage/app/public/html/${form.CONTENT_FOOTER_MENU[i].id}.html`]) }}
                              </p>
                              <v-divider class="mt-7 mb-4" />
                            </v-col>
                          </v-row>
                        </template>
                        <v-btn color="primary" @click="form.CONTENT_FOOTER_MENU.push({ id: 'new-page', title: 'New page title' })">
                          {{ $t('Add') }}
                        </v-btn>
                      </v-expansion-panel-content>
                    </v-expansion-panel>
                  </v-expansion-panels>
                </v-card-text>
              </v-card>
            </v-tab-item>
            <v-tab-item>
              <v-card flat>
                <v-card-text>
                  <v-text-field
                    v-model.number="form.SESSION_LIFETIME"
                    :label="$t('Session lifetime')"
                    :rules="[v => validationInteger(parseInt(v, 10)), validationPositiveNumber]"
                    :error="form.errors.has('SESSION_LIFETIME')"
                    :error-messages="form.errors.get('SESSION_LIFETIME')"
                    :suffix="$t('minutes')"
                    outlined
                  />

                  <v-switch
                    v-model="form.EMAIL_VERIFICATION"
                    :label="$t('Require email verification')"
                    color="primary"
                    false-value="false"
                    true-value="true"
                  />
                </v-card-text>
              </v-card>
            </v-tab-item>
            <v-tab-item>
              <v-card flat>
                <v-card-text>
                  <file-upload
                    v-model="form.GAMES_PLAYING_CARDS_FRONT_IMAGE"
                    :label="$t('Playing card front background image')"
                    name="card-front"
                    folder="images"
                  />

                  <file-upload
                    v-model="form.GAMES_PLAYING_CARDS_BACK_IMAGE"
                    :label="$t('Playing card back background image')"
                    name="card-back"
                    folder="images"
                  />
                </v-card-text>
              </v-card>
            </v-tab-item>
            <v-tab-item>
              <v-card flat>
                <v-card-text>
                  <v-switch
                    v-model="form.CHAT_ENABLED"
                    :label="$t('Enable chat')"
                    color="primary"
                    false-value="false"
                    true-value="true"
                    :disabled="!pusherEnabled"
                    :hint="$t('Please fill Pusher settings first under Services tab.')"
                    :persistent-hint="!pusherEnabled"
                    class="mb-5"
                  />

                  <v-text-field
                    v-if="form.CHAT_ENABLED === 'true'"
                    v-model.number="form.CHAT_MESSAGE_MAX_LENGTH"
                    :label="$t('Chat message max length')"
                    :rules="[v => validationInteger(parseInt(v, 10)), validationPositiveNumber]"
                    :error="form.errors.has('CHAT_MESSAGE_MAX_LENGTH')"
                    :error-messages="form.errors.get('CHAT_MESSAGE_MAX_LENGTH')"
                    outlined
                    @keydown="clearFormErrors($event,'CHAT_MESSAGE_MAX_LENGTH')"
                  />
                </v-card-text>
              </v-card>
            </v-tab-item>
            <v-tab-item>
              <v-card flat>
                <v-card-text>
                  <v-expansion-panels>
                    <v-expansion-panel>
                      <v-expansion-panel-header>{{ $t('Games') }}</v-expansion-panel-header>
                      <v-expansion-panel-content>
                        <v-select
                          v-model="form.BOTS_GAMES_FREQUENCY"
                          :items="frequencies"
                          :label="$t('Frequency')"
                          :error="form.errors.has('BOTS_GAMES_FREQUENCY')"
                          :error-messages="form.errors.get('BOTS_GAMES_FREQUENCY')"
                          :hint="$t('How often bots will awake.')"
                          persistent-hint
                          outlined
                        />

                        <v-text-field
                          v-model.number="form.BOTS_GAMES_MIN_COUNT"
                          :label="$t('Min bots')"
                          :rules="[v => validationInteger(parseInt(v, 10)), validationPositiveNumber]"
                          :error="form.errors.has('BOTS_GAMES_MIN_COUNT')"
                          :error-messages="form.errors.get('BOTS_GAMES_MIN_COUNT')"
                          outlined
                          :hint="$t('Minimum number of bots to play a game during each cycle.')"
                          persistent-hint
                          @keydown="clearFormErrors($event,'BOTS_GAMES_MIN_COUNT')"
                        />

                        <v-text-field
                          v-model.number="form.BOTS_GAMES_MAX_COUNT"
                          :label="$t('Max bots')"
                          :rules="[v => validationInteger(parseInt(v, 10)), validationPositiveNumber]"
                          :error="form.errors.has('BOTS_GAMES_MAX_COUNT')"
                          :error-messages="form.errors.get('BOTS_GAMES_MAX_COUNT')"
                          outlined
                          :hint="$t('Maximum number of bots to play a game during each cycle.')"
                          persistent-hint
                          @keydown="clearFormErrors($event,'BOTS_GAMES_MAX_COUNT')"
                        />

                        <v-text-field
                          v-model.number="form.BOTS_GAMES_MIN_BET"
                          :label="$t('Min bet')"
                          :error="form.errors.has('BOTS_GAMES_MIN_BET')"
                          :error-messages="form.errors.get('BOTS_GAMES_MIN_BET')"
                          outlined
                          :suffix="$t('credits')"
                          :hint="$t('Minimum bet a bot is allowed to make.') + ' ' + $t('Leave empty to use the limit specified in the game settings.')"
                          persistent-hint
                          @keydown="clearFormErrors($event,'BOTS_GAMES_MIN_BET')"
                        />

                        <v-text-field
                          v-model.number="form.BOTS_GAMES_MAX_BET"
                          :label="$t('Max bet')"
                          :error="form.errors.has('BOTS_GAMES_MAX_BET')"
                          :error-messages="form.errors.get('BOTS_GAMES_MAX_BET')"
                          outlined
                          :suffix="$t('credits')"
                          :hint="$t('Minimum bet a bot is allowed to make.') + ' ' + $t('Leave empty to use the limit specified in the game settings.')"
                          persistent-hint
                          @keydown="clearFormErrors($event,'BOTS_GAMES_MAX_BET')"
                        />
                      </v-expansion-panel-content>
                    </v-expansion-panel>
                  </v-expansion-panels>
                </v-card-text>
              </v-card>
            </v-tab-item>
            <v-tab-item>
              <v-card flat>
                <v-card-text>
                  <v-text-field
                    v-model.number="form.BONUSES_SIGN_UP"
                    :label="$t('Sign up bonus')"
                    :error="form.errors.has('BONUSES_SIGN_UP')"
                    :error-messages="form.errors.get('BONUSES_SIGN_UP')"
                    :suffix="$t('credits')"
                    outlined
                  />

                  <v-row>
                    <v-col cols="12" md="6">
                      <v-text-field
                        v-model.number="form.BONUSES_DEPOSIT_THRESHOLD"
                        :label="$t('Threshold')"
                        :prefix="$t('If deposit amount') + ' >= '"
                        :suffix="$t('credits')"
                        :rules="[validationPositiveNumber]"
                        :error="form.errors.has('BONUSES_DEPOSIT_THRESHOLD')"
                        :error-messages="form.errors.get('BONUSES_DEPOSIT_THRESHOLD')"
                        class="text-center"
                        outlined
                      />
                    </v-col>
                    <v-col cols="12" md="6">
                      <v-text-field
                        v-model.number="form.BONUSES_DEPOSIT_PCT"
                        :label="$t('Deposit bonus')"
                        :prefix="$t('give back')"
                        :suffix="$t('% of deposit amount')"
                        :rules="[validationNumeric]"
                        :error="form.errors.has('BONUSES_DEPOSIT_PCT')"
                        :error-messages="form.errors.get('BONUSES_DEPOSIT_PCT')"
                        class="text-center"
                        outlined
                      />
                    </v-col>
                  </v-row>
                </v-card-text>
              </v-card>
            </v-tab-item>
            <v-tab-item>
              <v-card flat>
                <v-card-text>
                  <v-expansion-panels>
                    <v-expansion-panel>
                      <v-expansion-panel-header>{{ $t('General') }}</v-expansion-panel-header>
                      <v-expansion-panel-content>
                        <v-select
                          v-model="form.AFFILIATE_AUTO_APPROVAL_FREQUENCY"
                          :items="commissionsApprovalFrequencies"
                          :label="$t('Commissions auto approval frequency')"
                          :error="form.errors.has('AFFILIATE_AUTO_APPROVAL_FREQUENCY')"
                          :error-messages="form.errors.get('AFFILIATE_AUTO_APPROVAL_FREQUENCY')"
                          outlined
                        />

                        <v-switch
                          v-model="form.AFFILIATE_ALLOW_SAME_IP"
                          :label="$t('Do not validate IP address when creating commissions')"
                          color="primary"
                          false-value="false"
                          true-value="true"
                        />
                      </v-expansion-panel-content>
                    </v-expansion-panel>
                    <v-expansion-panel>
                      <v-expansion-panel-header>{{ $t('Commissions') }}</v-expansion-panel-header>
                      <v-expansion-panel-content>
                        <template v-if="form.AFFILIATE_COMMISSIONS_SIGN_UP">
                          <div v-for="tier in [0,1,2]" :key="'tier-' + tier">
                            <h4 class="subtitle-1 mb-3">{{ $t('Tier {0}', [tier+1]) }}</h4>

                            <v-text-field
                              v-model.number="form.AFFILIATE_COMMISSIONS_SIGN_UP[tier]"
                              :label="$t('Referrer sign up bonus')"
                              :error="form.errors.has('AFFILIATE_COMMISSIONS_SIGN_UP')"
                              :error-messages="form.errors.get('AFFILIATE_COMMISSIONS_SIGN_UP')"
                              :suffix="$t('credits')"
                              outlined
                            />

                            <v-text-field
                              v-model.number="form.AFFILIATE_COMMISSIONS_GAME_LOSS[tier]"
                              :label="$t('Referrer game loss bonus')"
                              :error="form.errors.has('AFFILIATE_COMMISSIONS_GAME_LOSS')"
                              :error-messages="form.errors.get('AFFILIATE_COMMISSIONS_GAME_LOSS')"
                              :suffix="$t('% of bet amount')"
                              outlined
                            />

                            <v-text-field
                              v-model.number="form.AFFILIATE_COMMISSIONS_GAME_WIN[tier]"
                              :label="$t('Referrer game win bonus')"
                              :error="form.errors.has('AFFILIATE_COMMISSIONS_GAME_WIN')"
                              :error-messages="form.errors.get('AFFILIATE_COMMISSIONS_GAME_WIN')"
                              :suffix="$t('% of bet amount')"
                              outlined
                            />

                            <v-text-field
                              v-model.number="form.AFFILIATE_COMMISSIONS_DEPOSIT[tier]"
                              :label="$t('Referrer deposit bonus')"
                              :error="form.errors.has('AFFILIATE_COMMISSIONS_DEPOSIT')"
                              :error-messages="form.errors.get('AFFILIATE_COMMISSIONS_DEPOSIT')"
                              :suffix="$t('% of deposit amount')"
                              outlined
                            />
                          </div>
                        </template>
                      </v-expansion-panel-content>
                    </v-expansion-panel>
                  </v-expansion-panels>
                </v-card-text>
              </v-card>
            </v-tab-item>
            <v-tab-item>
              <v-card flat>
                <v-card-text>
                  <v-expansion-panels>
                    <v-expansion-panel>
                      <v-expansion-panel-header>{{ $t('Google Tag Manager') }}</v-expansion-panel-header>
                      <v-expansion-panel-content>
                        <v-text-field
                          v-model="form.GTM_CONTAINER_ID"
                          :label="$t('Container ID')"
                          :error="form.errors.has('GTM_CONTAINER_ID')"
                          :error-messages="form.errors.get('GTM_CONTAINER_ID')"
                          outlined
                          @keydown="clearFormErrors($event,'GTM_CONTAINER_ID')"
                        />
                      </v-expansion-panel-content>
                    </v-expansion-panel>
                    <v-expansion-panel>
                      <v-expansion-panel-header>{{ $t('Google reCaptcha') }}</v-expansion-panel-header>
                      <v-expansion-panel-content>
                        <v-text-field
                          v-model="form.RECAPTCHA_PUBLIC_KEY"
                          :label="$t('Public key')"
                          :error="form.errors.has('RECAPTCHA_PUBLIC_KEY')"
                          :error-messages="form.errors.get('RECAPTCHA_PUBLIC_KEY')"
                          outlined
                          @keydown="clearFormErrors($event,'RECAPTCHA_PUBLIC_KEY')"
                        />

                        <v-text-field
                          v-model="form.RECAPTCHA_SECRET_KEY"
                          :label="$t('Secret key')"
                          :error="form.errors.has('RECAPTCHA_SECRET_KEY')"
                          :error-messages="form.errors.get('RECAPTCHA_SECRET_KEY')"
                          outlined
                          @keydown="clearFormErrors($event,'RECAPTCHA_SECRET_KEY')"
                        />
                      </v-expansion-panel-content>
                    </v-expansion-panel>
                    <v-expansion-panel>
                      <v-expansion-panel-header>{{ $t('Pusher') }}</v-expansion-panel-header>
                      <v-expansion-panel-content>
                        <v-text-field
                          v-model="form.PUSHER_APP_ID"
                          :label="$t('Pusher app ID')"
                          :error="form.errors.has('PUSHER_APP_ID')"
                          :error-messages="form.errors.get('PUSHER_APP_ID')"
                          outlined
                          @keydown="clearFormErrors($event,'PUSHER_APP_ID')"
                        />

                        <v-text-field
                          v-model="form.PUSHER_APP_KEY"
                          :label="$t('Pusher app key')"
                          :error="form.errors.has('PUSHER_APP_KEY')"
                          :error-messages="form.errors.get('PUSHER_APP_KEY')"
                          outlined
                          @keydown="clearFormErrors($event,'PUSHER_APP_KEY')"
                        />

                        <v-text-field
                          v-model="form.PUSHER_APP_SECRET"
                          :label="$t('Pusher app secret')"
                          :error="form.errors.has('PUSHER_APP_SECRET')"
                          :error-messages="form.errors.get('PUSHER_APP_SECRET')"
                          outlined
                          @keydown="clearFormErrors($event,'PUSHER_APP_SECRET')"
                        />

                        <v-text-field
                          v-model="form.PUSHER_APP_CLUSTER"
                          :label="$t('Pusher app cluster')"
                          :error="form.errors.has('PUSHER_APP_CLUSTER')"
                          :error-messages="form.errors.get('PUSHER_APP_CLUSTER')"
                          outlined
                          @keydown="clearFormErrors($event,'PUSHER_APP_CLUSTER')"
                        />
                      </v-expansion-panel-content>
                    </v-expansion-panel>
                  </v-expansion-panels>
                </v-card-text>
              </v-card>
            </v-tab-item>
            <v-tab-item>
              <v-card flat>
                <v-card-text>
                  <v-expansion-panels>
                    <v-expansion-panel v-for="provider in providers" :key="provider">
                      <v-expansion-panel-header>{{ ucfirst(provider) }}</v-expansion-panel-header>
                      <v-expansion-panel-content>
                        <v-text-field
                          v-model="form[providerClientIdVariable(provider)]"
                          :label="$t('Client ID')"
                          :error="form.errors.has(providerClientIdVariable(provider))"
                          :error-messages="form.errors.get(providerClientIdVariable(provider))"
                          outlined
                        />

                        <v-text-field
                          v-model="form[providerClientSecretVariable(provider)]"
                          :label="$t('Client secret')"
                          :error="form.errors.has(providerClientSecretVariable(provider))"
                          :error-messages="form.errors.get(providerClientSecretVariable(provider))"
                          outlined
                        />

                        <v-text-field
                          :value="providerRedirectUrlValue(provider)"
                          :ref="`provider-${provider}`"
                          :label="$t('Redirect URL')"
                          append-icon="mdi-content-copy"
                          outlined
                          readonly
                          @click:append="copyToClipboard($refs[`provider-${provider}`][0])"
                        />
                      </v-expansion-panel-content>
                    </v-expansion-panel>
                  </v-expansion-panels>
                  <template >
                  </template>
                </v-card-text>
              </v-card>
            </v-tab-item>
            <v-tab-item>
              <v-card flat>
                <v-card-text>
                  <v-select
                    v-model="form.FORMAT_NUMBER_DECIMAL_SEPARATOR"
                    :items="separators"
                    :label="$t('Decimal separator')"
                    :error="form.errors.has('FORMAT_NUMBER_DECIMAL_SEPARATOR')"
                    :error-messages="form.errors.get('FORMAT_NUMBER_DECIMAL_SEPARATOR')"
                    outlined
                  />

                  <v-select
                    v-model="form.FORMAT_NUMBER_THOUSANDS_SEPARATOR"
                    :items="separators"
                    :label="$t('Thousands separator')"
                    :error="form.errors.has('FORMAT_NUMBER_THOUSANDS_SEPARATOR')"
                    :error-messages="form.errors.get('FORMAT_NUMBER_THOUSANDS_SEPARATOR')"
                    outlined
                  />
                </v-card-text>
              </v-card>
            </v-tab-item>
            <v-tab-item>
              <v-card flat>
                <v-card-text>
                  <v-select
                    v-model="form.MAIL_MAILER"
                    :items="mailDrivers"
                    :label="$t('Mail driver')"
                    :error="form.errors.has('MAIL_MAILER')"
                    :error-messages="form.errors.get('MAIL_MAILER')"
                    outlined
                  />
                  <template v-if="form.MAIL_MAILER === 'smtp'">
                    <v-text-field
                      v-model="form.MAIL_HOST"
                      :label="$t('Host')"
                      :error="form.errors.has('MAIL_HOST')"
                      :error-messages="form.errors.get('MAIL_HOST')"
                      outlined
                      @keydown="clearFormErrors($event,'MAIL_HOST')"
                    />

                    <v-text-field
                      v-model="form.MAIL_PORT"
                      :label="$t('Port')"
                      :rules="[validationNumeric]"
                      :error="form.errors.has('MAIL_PORT')"
                      :error-messages="form.errors.get('MAIL_PORT')"
                      outlined
                      @keydown="clearFormErrors($event,'MAIL_PORT')"
                    />

                    <v-text-field
                      v-model="form.MAIL_ENCRYPTION"
                      :label="$t('Encryption')"
                      :error="form.errors.has('MAIL_ENCRYPTION')"
                      :error-messages="form.errors.get('MAIL_ENCRYPTION')"
                      outlined
                      @keydown="clearFormErrors($event,'MAIL_ENCRYPTION')"
                    />

                    <v-text-field
                      v-model="form.MAIL_USERNAME"
                      :label="$t('User name')"
                      :error="form.errors.has('MAIL_USERNAME')"
                      :error-messages="form.errors.get('MAIL_USERNAME')"
                      outlined
                      @keydown="clearFormErrors($event,'MAIL_USERNAME')"
                    />

                    <v-text-field
                      v-model="form.MAIL_PASSWORD"
                      :label="$t('Password')"
                      :error="form.errors.has('MAIL_PASSWORD')"
                      :error-messages="form.errors.get('MAIL_PASSWORD')"
                      outlined
                      type="password"
                      @keydown="clearFormErrors($event,'MAIL_PASSWORD')"
                    />

                    <v-text-field
                      v-model="form.MAIL_FROM_ADDRESS"
                      :label="$t('E-mail from address')"
                      :rules="[validationEmail]"
                      :error="form.errors.has('MAIL_FROM_ADDRESS')"
                      :error-messages="form.errors.get('MAIL_FROM_ADDRESS')"
                      outlined
                      @keydown="clearFormErrors($event,'MAIL_FROM_ADDRESS')"
                    />

                    <v-text-field
                      v-model="form.MAIL_FROM_NAME"
                      :label="$t('E-mail from name')"
                      :error="form.errors.has('MAIL_FROM_NAME')"
                      :error-messages="form.errors.get('MAIL_FROM_NAME')"
                      outlined
                      @keydown="clearFormErrors($event,'MAIL_FROM_NAME')"
                    />
                  </template>
                </v-card-text>
              </v-card>
            </v-tab-item>
            <v-tab-item>
              <v-card flat>
                <v-card-text>
                  <v-switch
                    v-model="form.APP_DEBUG"
                    :label="$t('Debug mode')"
                    color="primary"
                    false-value="false"
                    true-value="true"
                  />

                  <v-select
                    v-model="form.APP_LOG_LEVEL"
                    :items="logLevels"
                    :label="$t('Log level')"
                    :error="form.errors.has('APP_LOG_LEVEL')"
                    :error-messages="form.errors.get('APP_LOG_LEVEL')"
                    outlined
                  />

                  <v-text-field
                    v-model="form.APP_LOG_DAYS"
                    :label="$t('Keep log files for')"
                    :rules="[validationNumeric]"
                    :error="form.errors.has('APP_LOG_DAYS')"
                    :error-messages="form.errors.get('APP_LOG_DAYS')"
                    outlined
                    :suffix="$t('days')"
                    @keydown="clearFormErrors($event,'APP_LOG_DAYS')"
                  />
                </v-card-text>
              </v-card>
            </v-tab-item>
            <template v-for="(pkg, id) in packages">
              <v-tab-item v-if="packageComponents[id]" :key="id">
                <component :is="packageComponents[id]" :form="form" @input="mergePackageConfig" />
              </v-tab-item>
            </template>
          </v-tabs>
          <div class="text-right mt-3">
            <v-btn type="submit" color="primary" :disabled="!formIsValid || form.busy" :loading="form.busy">{{ $t('Save') }}</v-btn>
          </div>
        </v-form>
      </v-card-text>
    </v-card>
  </v-container>
</template>

<script>
import { mapState } from 'vuex'
import Form from 'vform'
import FormMixin from '~/mixins/Form'
import { config } from '~/plugins/config'
import { ucfirst, copyToClipboard } from '~/plugins/utils'
import ColorPicker from '~/components/ColorPicker'
import FileUpload from '~/components/Admin/FileUpload'

export default {
  middleware: ['auth', 'verified', '2fa_passed', 'admin', 'config'],

  components: { FileUpload, ColorPicker },

  mixins: [FormMixin],

  metaInfo () {
    return { title: this.$t('Settings') }
  },

  data () {
    return {
      colorPickers: [false, false, false, false, false, false, false, false],
      form: new Form({
        APP_NAME: config('app.name'),
        LOCALE: config('app.default_locale'),
        THEME_MODE: config('settings.theme.mode'),
        THEME_COLOR_PRIMARY: config('settings.theme.colors.primary'), // $t('Primary color')
        THEME_COLOR_SECONDARY: config('settings.theme.colors.secondary'), // $t('Secondary color')
        THEME_COLOR_ACCENT: config('settings.theme.colors.accent'), // $t('Accent color')
        THEME_COLOR_ERROR: config('settings.theme.colors.error'), // $t('Error color')
        THEME_COLOR_INFO: config('settings.theme.colors.info'), // $t('Info color')
        THEME_COLOR_SUCCESS: config('settings.theme.colors.success'), // $t('Success color')
        THEME_COLOR_WARNING: config('settings.theme.colors.warning'), // $t('Warning color')
        THEME_COLOR_ANCHOR: config('settings.theme.colors.anchor'), // $t('Anchor color')
        THEME_BACKGROUND: config('settings.theme.background'),
        CONTENT_HOME_SLIDER: config('settings.content.home.slider'),
        CONTENT_FOOTER_MENU: config('settings.content.footer.menu'),
        BOTS_GAMES_FREQUENCY: config('settings.bots.games.frequency'),
        BOTS_GAMES_MIN_COUNT: config('settings.bots.games.min_count'),
        BOTS_GAMES_MAX_COUNT: config('settings.bots.games.max_count'),
        BOTS_GAMES_MIN_BET: config('settings.bots.games.min_bet'),
        BOTS_GAMES_MAX_BET: config('settings.bots.games.max_bet'),
        CHAT_ENABLED: '' + config('settings.chat.enabled'),
        CHAT_MESSAGE_MAX_LENGTH: parseInt(config('settings.chat.message_max_length'), 10),
        BONUSES_SIGN_UP: parseInt(config('settings.bonuses.sign_up'), 10),
        BONUSES_DEPOSIT_THRESHOLD: parseInt(config('settings.bonuses.deposit.threshold'), 10),
        BONUSES_DEPOSIT_PCT: parseFloat(config('settings.bonuses.deposit.pct')),
        AFFILIATE_AUTO_APPROVAL_FREQUENCY: config('settings.affiliate.auto_approval_frequency'),
        AFFILIATE_ALLOW_SAME_IP: '' + config('settings.affiliate.allow_same_ip'),
        AFFILIATE_COMMISSIONS_SIGN_UP: config('settings.affiliate.commissions.sign_up.rates'),
        AFFILIATE_COMMISSIONS_GAME_LOSS: config('settings.affiliate.commissions.game_loss.rates'),
        AFFILIATE_COMMISSIONS_GAME_WIN: config('settings.affiliate.commissions.game_win.rates'),
        AFFILIATE_COMMISSIONS_DEPOSIT: config('settings.affiliate.commissions.deposit.rates'),
        SESSION_LIFETIME: parseInt(config('session.lifetime'), 10),
        EMAIL_VERIFICATION: '' + config('settings.email_verification'),
        GTM_CONTAINER_ID: config('services.gtm.container_id'),
        RECAPTCHA_PUBLIC_KEY: config('services.recaptcha.public_key'),
        RECAPTCHA_SECRET_KEY: config('services.recaptcha.secret_key'),
        PUSHER_APP_ID: config('broadcasting.connections.pusher.app_id'),
        PUSHER_APP_KEY: config('broadcasting.connections.pusher.key'),
        PUSHER_APP_SECRET: config('broadcasting.connections.pusher.secret'),
        PUSHER_APP_CLUSTER: config('broadcasting.connections.pusher.options.cluster'),
        FORMAT_NUMBER_DECIMAL_SEPARATOR: parseInt(config('settings.format.number.decimal_separator'), 10),
        FORMAT_NUMBER_THOUSANDS_SEPARATOR: parseInt(config('settings.format.number.thousands_separator'), 10),
        APP_DEBUG: '' + config('app.debug'),
        APP_LOGO: config('app.logo'),
        APP_BANNER: config('app.banner'),
        GAMES_PLAYING_CARDS_FRONT_IMAGE: config('settings.games.playing_cards.front_image'),
        GAMES_PLAYING_CARDS_BACK_IMAGE: config('settings.games.playing_cards.back_image'),
        APP_LOG_LEVEL: config('logging.channels.daily.level'),
        APP_LOG_DAYS: config('logging.channels.daily.days'),
        MAIL_MAILER: config('mail.default'),
        MAIL_HOST: config('mail.mailers.smtp.host'),
        MAIL_PORT: config('mail.mailers.smtp.port'),
        MAIL_FROM_ADDRESS: config('mail.from.address'),
        MAIL_FROM_NAME: config('mail.from.name'),
        MAIL_ENCRYPTION: config('mail.mailers.smtp.encryption'),
        MAIL_USERNAME: config('mail.mailers.smtp.username'),
        MAIL_PASSWORD: config('mail.mailers.smtp.password'),
        ...Object.keys(config('oauth')).reduce((o, provider) => {
          o[provider.toUpperCase() + '_CLIENT_ID'] = config(`oauth.${provider}.client_id`)
          o[provider.toUpperCase() + '_CLIENT_SECRET'] = config(`oauth.${provider}.client_secret`)
          return o
        }, {})
      }),
      packageComponents: {}
    }
  },

  computed: {
    ...mapState('lang', ['locale', 'locales']),
    ...mapState('package-manager', ['packages']),
    backgrounds () {
      return [
        { value: 'Stars', text: this.$t('Stars') },
        { value: 'Squares', text: this.$t('Squares') },
        { value: null, text: this.$t('None') }
      ]
    },
    colors () {
      return Object.keys(this.form).filter(v => v.startsWith('THEME_COLOR_'))
    },
    commissionsApprovalFrequencies () {
      return [
        { value: 'daily', text: this.$t('Every day at 00:00') },
        { value: 'weekly', text: this.$t('Every week on Monday at 00:00') },
        { value: 'monthly', text: this.$t('Every first day of month at 00:00') },
        { value: 'never', text: this.$t('Never') }
      ]
    },
    frequencies () {
      return [
        { value: 'everyMinute', text: this.$t('Every minute') },
        { value: 'everyFiveMinutes', text: this.$t('Every five minutes') },
        { value: 'everyTenMinutes', text: this.$t('Every ten minutes') },
        { value: 'everyFifteenMinutes', text: this.$t('Every fifteen minutes') },
        { value: 'everyThirtyMinutes', text: this.$t('Every thirty minutes') },
        { value: 'hourly', text: this.$t('Every hour') }
      ]
    },
    languages () {
      return Object.keys(this.locales).map(locale => ({ value: locale, text: this.locales[locale].title }))
    },
    logLevels () {
      return ['debug', 'info', 'notice', 'warning', 'error', 'critical', 'alert', 'emergency']
    },
    mailDrivers () {
      return [
        { value: 'smtp', text: this.$t('SMTP') },
        { value: 'sendmail', text: this.$t('SendMail') }
      ]
    },
    modes () {
      return [{ value: 'light', text: this.$t('Light') }, { value: 'dark', text: this.$t('Dark') }]
    },
    providers () {
      return Object.keys(config('oauth'))
    },
    pusherEnabled () {
      return this.form.PUSHER_APP_ID && this.form.PUSHER_APP_KEY && this.form.PUSHER_APP_SECRET && this.form.PUSHER_APP_CLUSTER
    },
    separators () {
      return ['.', ',', ' ', '-', ':', ';'].map(v => ({ value: v.charCodeAt(0), text: v === ' ' ? this.$t('Space') : v }))
    }
  },

  created () {
    Object.keys(this.packages).forEach(async packageId => {
      try {
        const module = await import(/* webpackChunkName: 'js/games/[request]' */ `packages/${packageId}/resources/js/pages/admin/settings`)
        this.$set(this.packageComponents, packageId, module.default)
      } catch (e) {
        // do nothing
      }
    })
  },

  methods: {
    addSlide () {
      this.form.CONTENT_HOME_SLIDER.slides.push({
        title: '',
        subtitle: '',
        image: {
          url: ''
        },
        link: {
          title: '',
          url: ''
        }
      })
    },
    deleteSlide (i) {
      this.form.CONTENT_HOME_SLIDER.slides.splice(i, 1)
    },
    ucfirst (string) {
      return ucfirst(string)
    },
    copyToClipboard (ref) {
      return copyToClipboard(ref.$el.querySelector('input'))
    },
    openLink (url) {
      window.open(url)
    },
    providerClientIdVariable (provider) {
      return provider.toUpperCase() + '_CLIENT_ID'
    },
    providerClientSecretVariable (provider) {
      return provider.toUpperCase() + '_CLIENT_SECRET'
    },
    providerRedirectUrl (provider) {
      return provider.toUpperCase() + '_REDIRECT_URL'
    },
    providerRedirectUrlValue (provider) {
      return config('app.url') + config(`oauth.${provider}.redirect`)
    },
    colorText (v) {
      const s = v.replace('THEME_COLOR_', '').toLowerCase() + ' color'
      return this.$t(ucfirst(s))
    },
    mergePackageConfig (packageVariables) {
      Object.keys(packageVariables).forEach(key => {
        this.form[key] = packageVariables[key]
      })
    },
    async save () {
      const { data } = await this.form.submit('post', '/api/admin/settings')

      this.$store.dispatch('message/' + (data.success ? 'success' : 'error'), { text: data.message })
    }
  }
}
</script>
