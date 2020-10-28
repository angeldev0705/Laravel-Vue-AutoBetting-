<template>
  <span>{{ timer }}</span>
</template>
<script>
  export default {
    props: {
      endDate: {
        type: Number,
        required: true
      }
    },

    data () {
      return {
        timer: null,
        interval: null
      }
    },

    created () {
      this.tick()
      this.interval = setInterval(this.tick, 1000)
    },

    methods: {
      tick () {
        var diff = Math.max(0, Math.round(this.endDate - Date.now() / 1000))
        var seconds = Math.floor(diff % 60)
        var minutes = Math.floor((diff / 60) % 60)
        var hours = Math.floor((diff / (60 * 60)) % 24)
        var days = Math.floor(diff / (60 * 60 * 24))

        seconds = seconds < 10 ? '0' + seconds : seconds
        minutes = minutes < 10 ? '0' + minutes : minutes
        hours = hours < 10 ? '0' + hours : hours

        this.timer = (days > 0 ? `${days}${this.$t('d')} : ` : '') +
          (hours > 0 || days > 0 ? `${hours}${this.$t('h')} : ` : '') +
          `${minutes}${this.$t('m')} : ${seconds}${this.$t('s')}`

        // clear interval if time elapsed
        if (diff === 0 && this.interval) {
          clearInterval(this.interval)
        }
      }
    }
  }
</script>
