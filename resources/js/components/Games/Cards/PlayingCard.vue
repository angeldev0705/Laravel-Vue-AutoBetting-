<template>
  <div class="playing-card-container">
    <slot name="top"></slot>
    <div class="playing-card ml-n10 mx-lg-1" :class="{ 'face-down': value === null, clickable, inactive }">
      <div class="front elevation-2" :style="{ backgroundImage: frontImageUrl }">
        <div class="d-flex flex-column pa-2">
          <card-value :value="value" :suit="suit" />
          <card-suit :suit="suit" />
        </div>
      </div>
      <div class="back elevation-2" :style="{ backgroundImage: backImageUrl }"></div>
    </div>
  </div>
</template>

<script>
import { config } from '~/plugins/config'
import CardValue from './PlayingCardValue'
import CardSuit from './PlayingCardSuit'

export default {
  components: {
    CardValue,
    CardSuit
  },

  props: {
    card: {
      required: true,
      validator: value => (typeof value === 'string' && value.length === 2) || value === null
    },
    clickable: {
      type: Boolean,
      required: false,
      default: false
    },
    inactive: {
      type: Boolean,
      required: false,
      default: false
    }
  },

  computed: {
    suit () {
      return this.card ? this.card[0] : null
    },
    value () {
      return this.card ? this.card[1] : null
    },
    frontImageUrl () {
      return `url("${config('settings.games.playing_cards.front_image')}")`
    },
    backImageUrl () {
      return `url("${config('settings.games.playing_cards.back_image')}")`
    }
  }
}
</script>

<style lang="scss" scoped>
.playing-card-container {
  perspective: 600px;

  .playing-card {
    position: relative;
    width: 5em;
    height: 7em;
    transform-style: preserve-3d;
    transition: all 0.5s ease-out;

    &.inactive {
      opacity: 0.4;
    }

    .front, .back {
      border-radius: 0.75em;
      position: absolute;
      width: 100%;
      height: 100%;
      background-size: 100%;
      background-position: center;
      background-repeat: no-repeat;
      backface-visibility: hidden;
    }

    .front {
      background-color: var(--v-secondary-lighten1);
    }

    .back {
      transform: rotateY(180deg);
      background-color: var(--v-primary-darken4);
    }

    &.face-down {
      transform: rotateY(180deg);
    }

    &.clickable {
      cursor: pointer;
    }
  }
}
</style>
