<template>
    <div
        v-on-clickaway="close"
    >
        <div class="relative">
            <input
                :value="value"
                @input="handleInput"
                ref="search"
                @keydown.enter.prevent="chooseSelected"
                @keydown.down.prevent="move(1)"
                @keydown.up.prevent="move(-1)"
                :class="{ focus: show, 'border-danger': error }"
                class="w-full form-control form-input form-input-bordered"
                :tabindex="show ? -1 : 0"
                type="text"
                spellcheck="false"
                v-bind="extraAttributes" />
        </div>

        <div v-if="show" ref="dropdown" class="form-input px-0 border border-60 absolute pin-t pin-l my-1 overflow-hidden" :style="{ width: inputWidth + 'px', zIndex: 2000 }">

            <div
                ref="container"
                class="search-input-options relative overflow-y-scroll scrolling-touch text-sm"
                tabindex="-1"
                style="max-height: 155px;"
            >

                <div
                    v-for="(item, index) in data"
                    :key="getTrackedByKey(item)"
                    :ref="index === selected ? 'selected' : null"
                    @click="choose(item)"
                    class="px-4 py-2 cursor-pointer"
                    :class="{
                        [`search-input-item-${index}`]: true,
                        'hover:bg-30': index !== selected,
                        'bg-primary text-white': index === selected
                    }"
                >
                    <div class="flex items-center">{{ item }}</div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import _ from 'lodash'
import Vue from 'vue'
import Popper from 'popper.js'
import { mixin as clickaway } from 'vue-clickaway'

export default {
    mixins: [clickaway],
    inheritAttrs: false,
    props: {
        value: '',
        data: [],
        error: {
            type: Boolean,
            default: false,
        },
        extraAttributes: {},
    },

    data: () => ({
        show: false,
        selected: 0,
        popper: null,
        inputWidth: null,
        trackBy: 'value',
    }),

    mounted() {
        document.addEventListener('keydown', e => {
            if (this.show && (e.keyCode == 9 || e.keyCode == 27)) {
                setTimeout(() => this.close(), 50)
            }
        })
    },

    watch: {
        data(data) {
            if(data.length) {
                this.open();
            } else {
                this.close();
            }
        },
        show(show) {
            if (show) {
                let selected = _.findIndex(this.data, [
                    this.trackBy,
                    _.get(this.value, this.trackBy),
                ])
                if (selected !== -1) this.selected = selected
                this.inputWidth = this.$refs.search.offsetWidth

                Vue.nextTick(() => {
                    const vm = this

                    this.popper = new Popper(this.$refs.search, this.$refs.dropdown, {
                        placement: 'bottom-start',
                        onCreate() {
                            vm.$refs.container.scrollTop = vm.$refs.container.scrollHeight
                            vm.updateScrollPosition()
                            vm.$refs.search.focus()
                        },
                        modifiers: {
                            preventOverflow: {
                                boundariesElement: this.boundary ? this.boundary : 'scrollParent',
                            },
                        },
                    })
                })
            } else {
                if (this.popper) this.popper.destroy()
            }
        },
    },

    methods: {
        getTrackedByKey(item) {
            return _.get(item, this.trackBy)
        },

        open() {
            this.show = true
        },

        close() {
            this.show = false
        },

        move(offset) {
            let newIndex = this.selected + offset

            if (newIndex >= 0 && newIndex < this.data.length) {
                this.selected = newIndex
                this.updateScrollPosition()
            }
        },

        updateScrollPosition() {
            Vue.nextTick(() => {
                if (this.$refs.selected && this.$refs.selected.length) {
                    if (
                        this.$refs.selected[0].offsetTop >
                        this.$refs.container.scrollTop +
                            this.$refs.container.clientHeight -
                            this.$refs.selected[0].clientHeight
                    ) {
                        this.$refs.container.scrollTop =
                            this.$refs.selected[0].offsetTop +
                            this.$refs.selected[0].clientHeight -
                            this.$refs.container.clientHeight
                    }

                    if (this.$refs.selected[0].offsetTop < this.$refs.container.scrollTop) {
                        this.$refs.container.scrollTop = this.$refs.selected[0].offsetTop
                    }
                }
            })
        },

        chooseSelected() {
            if (this.data[this.selected] !== undefined) {
                this.$emit('selected', this.data[this.selected])
                this.value = this.data[this.selected].display;
                this.$refs.search.focus()
                Vue.nextTick(() => this.close())
            }
        },

        choose(item) {
            this.selected = _.findIndex(this.data, [this.trackBy, _.get(item, this.trackBy)])
            this.$emit('selected', item)
            this.value = item;
            this.$refs.search.focus()
            Vue.nextTick(() => this.close())
        },

        /**
         * Handle the input event of the search box
         */
        handleInput(e) {
            this.debouncer(() => {
                this.$emit('input', e.target.value)
            })
        },

        /**
         * Debounce function for the input handler
         */
        debouncer: _.debounce(callback => callback(), 500),
    },
}
</script>
