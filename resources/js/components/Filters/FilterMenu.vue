<template>
  <v-menu
    v-model="menu"
    :close-on-content-click="false"
    left
    offset-x
    :nudge-width="200"
  >
    <template v-slot:activator="{ on }">
      <v-btn icon :disabled="disabled" v-on="on">
        <v-icon>
          mdi-filter-outline
        </v-icon>
      </v-btn>
    </template>
    <v-card>
      <v-card-title>
        {{ $t('Filter') }}
      </v-card-title>
      <v-divider />
      <v-card-text class="pt-5">
        <component
          :is="'filter-by-' + component"
          v-for="component in components"
          :key="component"
          @change="change($event)"
        />
      </v-card-text>
      <v-card-actions>
        <v-spacer />
        <v-btn
          color="primary"
          text
          :disabled="disabled"
          @click="apply"
        >
          {{ $t('Apply') }}
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-menu>
</template>

<script>
import FilterByPeriod from './FilterByPeriod'
import FilterByComparisonPeriod from './FilterByComparisonPeriod'
import FilterByRole from './FilterByRole'

export default {
  components: { FilterByPeriod, FilterByComparisonPeriod, FilterByRole },

  props: {
    components: {
      type: Array,
      required: false,
      default: () => ['period']
    },
    disabled: {
      type: Boolean,
      required: false,
      default: false
    }
  },

  data () {
    return {
      menu: false,
      filters: null
    }
  },

  methods: {
    change (filter) {
      this.filters = { ...this.filters, ...filter }
    },
    apply (filter) {
      this.menu = false // close menu
      this.$emit('apply', this.filters)
    }
  }
}
</script>
