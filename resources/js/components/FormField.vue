<template>
    <default-field :field="field" :errors="errors">
        <template slot="field">
            <text-auto-complete
                @input="performSearch"
                @selected="selectItem"
                :error="hasError"
                :data='availableItems'
                v-model="value"
                :extra-attributes="extraAttributes"
            ></text-auto-complete>
        </template>
    </default-field>
</template>

<script>
import { FormField, HandlesValidationErrors } from 'laravel-nova'
import TextAutoComplete from "./TextAutoComplete";

export default {
    components: {TextAutoComplete},

    mixins: [FormField, HandlesValidationErrors],

    data: () => ({
        availableItems: [],
    }),

    methods: {
        /**
         * Perform search
         */
        performSearch(search) {
            const trimmedSearch = search.trim()

            if (trimmedSearch == '') {
                return;
            }

            this.availableItems = this.field.items.filter(item => item.toLowerCase().indexOf(trimmedSearch.toLowerCase()) > -1);;
        },

        /**
         * Select item
         */
        selectItem(item) {
            this.value = item;
        }
    },

    computed: {
        defaultAttributes() {
            return {
                type: this.field.type || 'text',
                min: this.field.min,
                max: this.field.max,
                step: this.field.step,
                pattern: this.field.pattern,
                placeholder: this.field.placeholder || this.field.name,
                class: this.errorClasses,
            }
        },

        extraAttributes() {
            const attrs = this.field.extraAttributes

            return {
                // Leave the default attributes even though we can now specify
                // whatever attributes we like because the old number field still
                // uses the old field attributes
                ...this.defaultAttributes,
                ...attrs,
            }
        },
    },
}
</script>
