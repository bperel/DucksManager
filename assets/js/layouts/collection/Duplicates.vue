<template>
  <div v-if="duplicateIssues && hasPublicationNames">
    <IssueList
      v-for="(issueNumbers, publicationcode) in issueNumbersByPublicationCode"
      :key="publicationcode"
      :publicationcode="publicationcode"
      duplicates-only
    />
  </div>
  <div v-else>
    {{ $t("Chargement...") }}
  </div>
</template>
<script>
import { mapActions, mapGetters, mapState } from "vuex";
import l10nMixin from "../../mixins/l10nMixin";
import collectionMixin from "../../mixins/collectionMixin";
import IssueList from "../../components/IssueList";

export default {
  name: "Duplicates",
  components: { IssueList },
  mixins: [l10nMixin, collectionMixin],

  data: () => ({
    hasPublicationNames: false,
    issueNumbersByPublicationCode: null
  }),

  computed: {
    ...mapGetters("collection", ["total", "duplicateIssues"]),
    ...mapState("coa", ["publicationNames"])
  },

  watch: {
    duplicateIssues: {
      immediate: true,
      async handler(duplicateIssues) {
        if (duplicateIssues) {
          const vm = this
          this.issueNumbersByPublicationCode = {}
          Object.keys(duplicateIssues).forEach(issuecode => {
            const [publicationcode, issuenumber] = issuecode.split(' ')
            if (!vm.issueNumbersByPublicationCode[publicationcode]) {
              vm.issueNumbersByPublicationCode[publicationcode] = []
            }
            vm.issueNumbersByPublicationCode[publicationcode].push(issuenumber)
          })

          await this.fetchPublicationNames(Object.keys(this.issueNumbersByPublicationCode))
          this.hasPublicationNames = true
        }
      }
    }
  },

  methods: {
    ...mapActions("coa", ["fetchPublicationNames"])
  }
};
</script>
<style scoped>
</style>
