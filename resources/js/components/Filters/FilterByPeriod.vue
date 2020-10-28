<template>
  <v-select
    v-model="period"
    :items="options"
    :label="$t('Period')"
    :disabled="disabled"
    outlined
    @change="change"
  />
</template>

<script>
export default {
  props: {
    disabled: {
      type: Boolean,
      required: false,
      default: false
    }
  },

  data () {
    return {
      period: null,
      options: [
        { text: this.$t('This week'), value: 'week' },
        { text: this.$t('Previous week'), value: 'prev_week' },
        { text: this.$t('This month'), value: 'month' },
        { text: this.$t('Previous month'), value: 'prev_month' },
        { text: this.$t('This year'), value: 'year' },
        { text: this.$t('Previous year'), value: 'prev_year' },
        { text: this.$t('All time'), value: null }
      ]
    }
  },

  methods: {
    change () {
      this.$emit('change', { period: this.period })
    }
  }
}
</script>
