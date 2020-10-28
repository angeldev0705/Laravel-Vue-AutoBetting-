<template>
  <v-hover v-slot:default="{ hover }">
    <v-card :elevation="hover ? 12 : 2">
      <router-link :to="{ name: 'game', params: slug ? { game: id, slug } : { game: id } }">
        <v-img
          :src="banner"
          height="150px"
        >
          <template v-slot:placeholder>
            <v-skeleton-loader type="image" />
          </template>
        </v-img>
      </router-link>

      <v-card-title>
        {{ name }}
      </v-card-title>

      <v-card-text>
        <v-btn color="primary" :to="{ name: 'game', params: slug ? { game: id, slug } : { game: id } }">
          {{ $t('Play') }}
        </v-btn>
      </v-card-text>
    </v-card>
  </v-hover>
</template>
<script>
export default {
  props: {
    id: {
      type: String,
      required: true
    },
    name: {
      type: String,
      required: true
    },
    banner: {
      type: String,
      required: true
    },
    slug: {
      type: String,
      required: false,
      default: ''
    }
  }
}
</script>
