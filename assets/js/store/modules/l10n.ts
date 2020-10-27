import axios from "axios";
const {safeLoad} = require ("js-yaml");

import { VuexModule, Module, Mutation, Action } from 'vuex-module-decorators'
@Module({ namespaced: true, name: 'l10n' })
class L10n extends VuexModule {
    isLoading: boolean = false
    l10n: { [key: string]: string } | null = null

    @Mutation
    setL10n(l10n: { [key: string]: string }) {
        this.l10n = l10n
    }

    get locale(): string {
        return  (window as any).locale
    }

    @Action
    async loadL10n() {
        if (!this.isLoading && !this.l10n) {
            this.isLoading = true
            const yamlL10n= (await axios.get((window as any).l10nUrl)).data
            this.isLoading = false
            this.context.commit('setL10n', safeLoad(yamlL10n));
        }
    }
}