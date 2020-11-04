import Vue from 'vue'
import axios from "axios";

export default {
  namespaced: true,
  state: () => ({
    loadedSprites: {},

    bookcaseUsername: null,
    bookcase: null,
    bookcaseTextures: null,
    bookcaseOrder: null,
  }),

  mutations: {
    addLoadedSprite(state, {spritePath, css}) {
      Vue.set(state.loadedSprites, spritePath, css)
    },
    setBookcase(state, bookcase) {
      state.bookcase = bookcase
    },
    setBookcaseUsername(state, bookcaseUsername) {
      state.bookcaseUsername = bookcaseUsername
    },
    setBookcaseTextures(state, bookcaseTextures) {
      state.bookcaseTextures = bookcaseTextures
    },
    setBookcaseOrder(state, bookcaseOrder) {
      state.bookcaseOrder = bookcaseOrder
    },
  },

  getters: {
    isSharedBookcase: state => window.username !== state.bookcaseUsername,

    bookcaseWithPopularities: (state, getters, rootState) => rootState.collection.popularIssuesInCollection && state.bookcase &&
      state.bookcase
        .map((issue) => {
          const publicationCode = `${issue.countryCode}/${issue.magazineCode}`;
          const issueCode = `${issue.publicationCode} ${issue.issueNumber}`;
          return {
            ...issue,
            publicationCode,
            issueCode,
            popularity: rootState.collection.popularIssuesInCollection[issueCode] || 0
          };
        }),
  },

  actions: {
    loadBookcase: async ({state, commit}) => {
      if (!state.bookcase) {
        commit("setBookcase", (await axios.get(`/api/bookcase/${state.bookcaseUsername}`)).data)
      }
    },
    loadBookcaseTextures: async ({state, commit}) => {
      if (!state.bookcaseTextures) {
        commit("setBookcaseTextures", (await axios.get(`/api/bookcase/${state.bookcaseUsername}/textures`)).data)
      }
    },
    updateBookcaseTextures: async ({state}) => {
        await axios.post(`/api/bookcase/textures`, state.bookcaseTextures)
    },

    loadBookcaseOrder: async ({state, commit}) => {
      if (!state.bookcaseOrder) {
        commit("setBookcaseOrder", (await axios.get(`/api/bookcase/${state.bookcaseUsername}/sort`)).data)
      }
    },
    updateBookcaseOrder: async ({state}) => {
        await axios.post(`/api/bookcase/sort`, {sorts: state.bookcaseOrder})
    },
  }
}