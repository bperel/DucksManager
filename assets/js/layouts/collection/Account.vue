<template>
  <form
    v-if="user"
    method="post"
  >
    <b-alert
      v-if="isSuccess"
      variant="success"
    >
      {{ $t("OK") }} !
    </b-alert>

    <h5>{{ $t("Adresse e-mail") }}</h5>
    <Errorable id="email">
      <b-form-input
        id="email"
        v-model="user.email"
        name="email"
        required
        autofocus
      />
    </Errorable>

    <h5>{{ $t("Phrase de présentation") }} <sup>{{ $t('Nouveau !') }}</sup></h5>
    <b-alert
      variant="info"
      show
      class="mb-0"
    >
      <template v-if="hasRequestedPresentationSentence">
        {{ $t('Votre phrase de présentation est en cours de modération, un e-mail vous sera envoyé lorsqu\'elle sera validée.') }}
      </template>
      <template v-else>
        {{ $t('Votre phrase de présentation sera soumise à modération. Les messages à caractère politique ou contraires à la loi ne sont pas acceptés.') }}
      </template>
    </b-alert>
    <Errorable id="presentationSentenceRequest">
      <b-form-input
        id="presentationSentenceRequest"
        class="mt-0"
        :value="user.presentationSentence"
        name="presentationSentenceRequest"
        maxlength="100"
        :placeholder="$t('Présentez-vous en quelques mots (100 caractères maximum)')"
        @input="user.presentationSentenceRequest = $event"
      />
    </Errorable>
    <h5>{{ $t("Changement de mot de passe") }}</h5>
    <Errorable id="password">
      <b-form-input
        id="password"
        name="password"
        type="password"
        :placeholder="$t('Mot de passe actuel')"
      />
    </Errorable>
    <Errorable id="passwordNew">
      <b-form-input
        id="passwordNew"
        name="passwordNew"
        type="password"
        :placeholder="$t('Nouveau mot de passe')"
      />
    </Errorable>
    <Errorable id="passwordNewConfirmation">
      <b-form-input
        id="passwordNewConfirmation"
        name="passwordNewConfirmation"
        type="password"
        :placeholder="$t('Nouveau mot de passe (confirmation)')"
      />
    </Errorable>

    <h5>{{ $t("Options") }}</h5>
    <b-form-checkbox
      id="share-enabled"
      v-model="user.isShareEnabled"
      name="isShareEnabled"
    >
      {{ $t("Activer le partage de ma collection") }}
    </b-form-checkbox>

    <b-form-checkbox
      id="show-video"
      v-model="user.isVideoShown"
      name="isVideoShown"
    >
      {{ $t("Afficher la vidéo d'explication pour la sélection des numéros") }}
    </b-form-checkbox>

    <b-btn
      variant="success"
      size="xl"
      type="submit"
    >
      {{ $t("Valider") }}
    </b-btn>

    <h5 class="mt-5">
      {{ $t("Zone danger") }}
    </h5>
    <div>
      <b-btn
        variant="danger"
        @click="emptyCollection"
      >
        {{ $t("Vider ma liste de numéros") }}
      </b-btn>
    </div>
    <div>
      <b-btn
        variant="danger"
        @click="deleteAccount"
      >
        {{ $t("Supprimer mon compte DucksManager") }}
      </b-btn>
    </div>
  </form>
</template>

<script>
import l10nMixin from "../../mixins/l10nMixin";
import Errorable from "../../components/Errorable";
import { mapActions, mapMutations, mapState } from "vuex";
import axios from "axios";

export default {
  name: "Account",
  components: { Errorable },
  mixins: [l10nMixin],
  props: {
    errors: { type: String, default: "" },
    success: { type: String, default: null },
    hasrequestedpresentationsentence: { type: String, default: null },
  },

  computed: {
    ...mapState("collection", ["user"]),
    isSuccess() {
      return this.success === null ? null : parseInt(this.success) === 1;
    },
    hasRequestedPresentationSentence() {
      return this.hasrequestedpresentationsentence === null ? null : parseInt(this.hasrequestedpresentationsentence) === 1;
    }
  },

  async mounted() {
    await this.loadUser();
    this.addErrors(JSON.parse(this.errors));
  },

  methods: {
    ...mapMutations("form", ["addErrors"]),
    ...mapActions("collection", ["loadUser"]),

    async emptyCollection() {
      if (confirm(this.$t("Votre collection va être vidée. Continuer ?"))) {
        await axios.delete(`/collection`);
        window.location.replace(this.$r("/collection/show"));
      }
    },

    async deleteAccount() {
      if (confirm(this.$t("Votre compte DucksManager va être supprimé incluant toutes les informations de votre collection. Continuer ?"))) {
        await axios.post(`/collection/empty`);
        window.location.replace(this.$r("/logout"));
      }
    }
  }
};
</script>

<style scoped lang="scss">
h5, .btn {
  margin-top: 20px;
}
</style>
