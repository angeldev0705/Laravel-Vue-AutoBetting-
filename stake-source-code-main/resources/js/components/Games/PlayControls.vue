<template>
  <v-form ref="form" v-model="formIsValid" @submit.prevent="play">
    <div class="d-flex justify-center flex-wrap mt-3">
      <slot name="before-bet-input" />
      <v-text-field
        v-model.number="bet"
        dense
        outlined
        :full-width="false"
        class="bet-input text-center"
        :label="betLabel || $t('Bet')"
        :disabled="playing"
        :rules="[validationInteger, v => validationMin(v, minBet), v => validationMax(v, Math.min(Math.floor(account.balance), maxBet))]"
        prepend-inner-icon="mdi-minus"
        append-icon="mdi-plus"
        @click:prepend-inner="decreaseBet"
        @click:append="increaseBet"
      >
        <template v-slot:prepend-inner>
          <v-btn small text icon color="primary" @click="bet = minBet">
            <v-icon small>mdi-arrow-down</v-icon>
          </v-btn>
          <v-btn small text icon color="primary" @click="decreaseBet">
            <v-icon small>mdi-minus</v-icon>
          </v-btn>
        </template>
        <template v-slot:append>
          <v-btn small text icon color="primary" @click="increaseBet">
            <v-icon small>mdi-plus</v-icon>
          </v-btn>
          <v-btn small text icon color="primary" @click="bet = maxBet">
            <v-icon small>mdi-arrow-up</v-icon>
          </v-btn>
        </template>
      </v-text-field>
      <slot name="after-bet-input" />
      <v-btn
        type="submit"
        color="primary"
        :loading="loading"
        :disabled="disabled || !formIsValid || isPlayDisabled"
        class="ml-2"
      >
        {{ $t('Play') }}
      </v-btn>
    </div>
  </v-form>
</template>

<script>
import { mapState } from 'vuex'
import { isInteger } from '~/plugins/utils'
import FormMixin from '~/mixins/Form'
import GameMixin from '~/mixins/Game'
import SoundMixin from '~/mixins/Sound'
import clickSound from '~/../audio/common/click.wav'

export default {
  mixins: [FormMixin, GameMixin, SoundMixin],

  props: {
    betLabel: {
      type: String,
      required: false,
      default: ''
    },
    loading: {
      type: Boolean,
      required: true
    },
    playing: {
      type: Boolean,
      required: true
    },
    disabled: {
      type: Boolean,
      required: false,
      default: false
    }
  },

  data () {
    return {
      bet: null
    }
  },

  computed: {
    ...mapState('auth', ['account']),
    defaultBet () {
      return parseInt(this.config.default_bet_amount, 10)
    },
    minBet () {
      return parseInt(this.config.min_bet, 10)
    },
    maxBet () {
      return parseInt(this.config.max_bet, 10)
    },
    betStep () {
      return parseInt(this.config.bet_change_amount, 10)
    },
    isPlayDisabled () {
      return !this.provablyFairGame.hash || this.playing || this.bet > this.account.balance
    }
  },

  watch: {
    bet (bet, oldBet) {
      this.$emit('bet-change', isInteger(bet) ? bet : 0)
    }
  },

  created () {
    // it's important to wait until next tick to ensure config computed property is updated
    // after switching from one game page to another.
    this.$nextTick(() => {
      this.bet = this.defaultBet
    })
  },

  methods: {
    play () {
      this.sound(clickSound)
      this.$emit('play', this.bet)
    },
    decreaseBet () {
      this.sound(clickSound)
      const bet = this.bet - this.betStep
      this.bet = Math.max(this.minBet, bet)
    },
    increaseBet () {
      this.sound(clickSound)
      const bet = this.bet + this.betStep
      this.bet = Math.min(this.maxBet, bet)
    }
  }
}
</script>
<style lang="scss" scoped>
.bet-input {
  max-width: 250px;
}
</style>
