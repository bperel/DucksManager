<template>
  <div v-if="l10nRoutes">
    <table>
      <tr>
        <td>
          <a :href="$r('/collection/show')"><img
            id="logo"
            alt="logo"
            :src="`${imagePath}/logo_small.png`"
          ></a>
        </td>
        <td>{{ $t('Collection DucksManager de') }} {{ username }}</td>
      </tr>
    </table>
    <Collectable v-if="currentType === 'collectable'" />
    <Classic v-else-if="currentType === 'classic'" />
  </div>
</template>

<script>
import Collectable from "../layouts/print/Collectable";
import Classic from "../layouts/print/Classic";
import l10nMixin from "../mixins/l10nMixin";

export default {
  name: "Print",
  components: {
    Collectable,
    Classic
  },
  mixins: [l10nMixin],
  props: {
    currentType: {type: String, required: true}
  }
}
</script>

<style lang="scss" scoped>
#logo {
  height: 100px;
}

div table {
  width: 90%;
  font-size: 15px;
}

table tr td {
  &:nth-child(1) {
    text-align: left;
    width: 1%;
  }

  &:nth-child(2) {
    text-align: center;
  }
}
</style>
