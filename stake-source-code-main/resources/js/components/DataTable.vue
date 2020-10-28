<template>
  <div>
    <v-row v-if="searchEnabled" justify="end">
      <v-col cols="12" md="6" lg="3">
        <v-form @submit.prevent="search">
          <v-text-field
            v-model="options.search"
            :label="searchLabel || $t('Search')"
            append-outer-icon="mdi-magnify"
            single-line
            :hide-details="true"
            clearable
            @click:clear="clear"
            @click:append-outer="search"
          />
        </v-form>
      </v-col>
    </v-row>
    <v-data-table
      :headers="headers"
      :items="items"
      :page="options.page"
      :sort-by="options.sortBy"
      :sort-desc="options.sortDesc"
      :items-per-page="options.itemsPerPage"
      :footer-props="footerProps"
      :server-items-length="itemsTotal"
      :loading="loading"
      :must-sort="true"
      :hide-default-footer="hideFooter"
      :no-data-text="$t('No data found')"
      :no-results-text="$t('No data found')"
      class="elevation-1"
      @update:options="updateDataTableOptions"
    >
      <!--Allow to pass scoped slots to v-table from the parent component-->
      <!--
        v-table expects something like this:
        <template v-slot:item.name="{ item }">
          <v-chip>{{ item.name }}</v-chip>
        </template>
      -->
      <template v-slot:loading>
        <div v-for="(v,i) in Array(options.itemsPerPage).fill(0)" :key="i" class="align-center my-7">
          <v-skeleton-loader type="text" />
        </div>
      </template>
      <template v-for="(header, slot) of slotHeaderMap" v-slot:[slot]="{ item }">
        <slot v-if="$scopedSlots[slot]" :name="slot" :item="item" />
        <template v-else>
          {{ header.format ? format(header.format, get(item, header.value)) : get(item, header.value) }}
        </template>
      </template>
    </v-data-table>
  </div>
</template>

<script>
import axios from 'axios'
import { mapGetters, mapActions } from 'vuex'
import { get } from '~/plugins/utils'
import * as format from '~/plugins/format'

export default {
  name: 'DataTable',

  props: {
    api: {
      type: String,
      required: true
    },
    filters: {
      type: Object,
      required: false,
      default: () => ({})
    },
    headers: {
      type: Array,
      required: true
    },
    sortBy: {
      type: String,
      required: false,
      default: 'id'
    },
    sortDesc: {
      type: Boolean,
      required: false,
      default: true
    },
    searchEnabled: {
      type: Boolean,
      required: false,
      default: false
    },
    searchLabel: {
      type: String,
      required: false,
      default: null
    },
    hideFooter: {
      type: Boolean,
      required: false,
      default: false
    }
  },

  data () {
    return {
      loading: true,
      options: {},
      items: [],
      itemsTotal: 0
    }
  },

  computed: {
    ...mapGetters({ cacheGet: 'cache/get' }),
    cacheKey () {
      return 'datatable.' + this.$route.name
    },
    footerProps () {
      return {
        'items-per-page-options': [5, 10, 25, 50],
        'items-per-page-text': this.$t('Rows per page')
      }
    },
    sortDirection () {
      return this.options.sortDesc ? 'desc' : 'asc'
    },
    // Get slot => header map
    slotHeaderMap () {
      return this.headers.reduce((obj, header) => {
        obj['item.' + header.value] = header
        return obj
      }, {})
    }
  },

  watch: {
    filters (filters) {
      this.fetchData()
    }
  },

  created () {
    // get current search & sorting settings from cache or initialize with defaults
    // we don't save filters in cache, because it's difficult to initialize their default values after user left the page
    this.options = this.cacheGet(this.cacheKey) || {
      page: 1,
      itemsPerPage: 10,
      sortBy: this.sortBy,
      sortDesc: this.sortDesc,
      search: null
    }
  },

  methods: {
    ...mapActions({ cachePut: 'cache/put' }),
    get (...args) {
      return get(...args)
    },
    format (type, value) {
      return typeof format[type] === 'function' ? format[type](value) : value
    },
    updateDataTableOptions ({ page, itemsPerPage, sortBy, sortDesc }) {
      this.options = { ...this.options, page, itemsPerPage, sortBy: sortBy[0], sortDesc: sortDesc[0] }
      this.cacheOptions()
      this.fetchData()
    },
    cacheOptions () {
      this.cachePut({ key: this.cacheKey, value: this.options }) // save current search & sorting settings into cache
    },
    clear () {
      // workaround: manually reset the search field value, otherwise it's reset later after this function is called
      this.options.search = null
      this.search()
    },
    search () {
      this.fetchData()
    },
    async fetchData () {
      this.loading = true

      const { data } = await axios.get(this.api, {
        params: {
          page: this.options.page,
          items_per_page: this.options.itemsPerPage,
          sort_by: this.options.sortBy,
          sort_direction: this.sortDirection,
          search: this.options.search,
          ...this.filters
        }
      })

      this.items = data.items
      this.itemsTotal = data.count

      this.loading = false
    }
  }
}
</script>
