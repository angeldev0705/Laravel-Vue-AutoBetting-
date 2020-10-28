import store from '~/store'
import { config } from '~/plugins/config'

export default async (to, from, next) => {
  if (!config('settings.email_verification') || (store.state.auth.user && store.state.auth.user.email_verified_at !== null)) {
    next({ name: 'home' })
  } else {
    next()
  }
}
