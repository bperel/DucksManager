import { Component, Vue } from 'vue-property-decorator'

import { namespace } from 'vuex-class'
const l10n = namespace('l10n')

@Component
class L10nMixin extends Vue {
    @l10n.State
    l10n!: { [key: string]: string; }

    async mounted() {
        await this.loadL10n()
    }

    @l10n.Action
    loadL10n!: () => void

    $t(key: string, parameters: any[] = []) {
        let match
        let parameterIndex = 0
        let translation = this.l10n[key]
        while (true) {
            if ((match = /%\w/.exec(translation)) != null) {
                let replacement = parameters[parameterIndex++];
                if (replacement === undefined) {
                    replacement = ''
                }
                translation = translation.substring(0, match.index) + replacement + translation.substring(match.index + match[0].length);
            } else break;
        }
        return translation
    }

    ucFirst = (string: string) => string[0].toUpperCase() + string.substr(1)
}

export default L10nMixin