<template>
  <v-app>
    <v-main class="content">
      <animated-background />
      <message />
      <router-view />
    </v-main>

    <footer-menu />
  </v-app>
</template>

<script>
import { config } from '~/plugins/config'
import Message from '~/components/Message'
import AnimatedBackground from '~/components/AnimatedBackground'
import FooterMenu from '~/components/FooterMenu'

export default {
  name: 'AuthLayout',

  components: { Message, AnimatedBackground, FooterMenu },

  computed: {
    appName () {
      return config('app.name')
    }
  }
}
</script>
