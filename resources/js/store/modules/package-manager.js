// state
export const state = {
  packages: window.packages || {}
}

// getters
export const getters = {
  get: state => id => state.packages[id] || null,
  games: state => Object.keys(state.packages).filter(id => state.packages[id].type === 'game').reduce((a, id) => { return { ...a, [id]: state.packages[id] } }, {})
}

// mutations
export const mutations = {
  //
}

// actions
export const actions = {
  //
}
