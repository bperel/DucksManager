import {createApp} from 'vue'
import App from "./layouts/App"
import {BootstrapVue, BootstrapVueIcons} from 'bootstrap-vue'
import store from "./store"

import 'bootstrap/dist/css/bootstrap.css'
import 'bootstrap-vue/dist/bootstrap-vue.css'
import '../css/app.scss';

const app = createApp({
  store,
  render(createElement) {
    const vm = this
    let props = {}, component = null
    Object.keys(this.$el.attributes).forEach(key => {
      const {name, value} = vm.$el.attributes[key]
      if (name === 'component') {
        component = value
      } else {
        props[name] = value
      }
    })
    return createElement(App, {
      attrs: {
        props,
        component
      }
    })
  }
}).$mount('#app')

app.use(BootstrapVue)
app.use(BootstrapVueIcons)
