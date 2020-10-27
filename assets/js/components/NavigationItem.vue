<template>
  <li :class="{'non-empty': true, 'no-icon': !icon, active }">
    <a :href="getLink(path)">
      <i :class="{[icon]: true}" />
      <slot />
    </a>
  </li>
</template>

<script lang="ts">
import { Component, Prop, Vue } from 'vue-property-decorator'

@Component
export default class NavigationItem extends Vue {
  @Prop({ required: true})
  path!: string

  @Prop({ required: true})
  icon!: string


  get active() : boolean {
    return !this.path.split('/')
    .find(pathPart =>
      !window.location.pathname.split('/').includes(pathPart)
    )
  }

  getLink = (path: string) => path.indexOf('/') === 0 ? path : `/?action=${path}`;
}
</script>

<style scoped>

</style>