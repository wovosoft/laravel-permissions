<template>
    <div>
        <div class="input-group">
            <input type="text" class="form-control"
                   v-bind="$attrs"
                   v-model="search_value"
                   @focusin="show_dropdown=true,$emit('dropdownShown',true)"
                   @focusout="hideDropdown"
                   @input="getList"
                   @keypress="()=>{if(!show_dropdown) show_dropdown=true}"
                   @keydown.enter.prevent="enterPressed"
                   :placeholder="placeholder"
                   @keydown.down="current_position = current_position < items.length - 1 ? current_position + 1 : (items.length > 0 ? items.length - 1 : 0),fixPosition(current_position)"
                   @keydown.up="current_position = current_position > 0 ? current_position-1 : 0,fixPosition(current_position)"
            >
            <div class="input-group-append">
                <button type="button" class="btn btn-secondary" @click="search_value='',$emit('reset')">X</button>
            </div>
        </div>
        <b-dropdown v-if="show_dropdown && items.length"
                    :menu-class="{'show':show_dropdown,'w-100':true,'scrollable-dropdown':true}"
                    style="top:-15px;" ref="ddown_typehead" toggle-class="d-none" class="w-100">
            <b-dropdown-item style="cursor: pointer"
                             :class="{'bg-light':current_position===item_key,'ddown-item':true}"
                             v-for="(item,item_key) in items"
                             @click="itemClicked(item)"
                             :key="item_key">
                <slot name="option" v-bind="item">
                    {{item}}}
                </slot>
            </b-dropdown-item>
        </b-dropdown>

    </div>
</template>

<script>
    /**
     * Events
     *      1. @items  Returns the list of items fetched by Promise "searchItems"
     *      2. @selected Returns the selected item by enter or click event
     *      3. @dropdownHidden Triggers when the dropdown below the input box is hidden
     *      4. @dropdownShown Triggers when the dropdown is shown
     */
    export default {
        inheritAttrs: false,
        props: {
            closeOnSelect: {
                type: Boolean,
                default: true
            },
            clearOnSelect: {
                type: Boolean,
                default: false
            },
            resetOnSelect: {
                type: Boolean,
                default: false
            },
            search: {
                type: String,
                default: ''
            },
            /**
             * It should return a promise, that fetches data from remote server.
             */
            searchItems: {
                type: Function
            },
            api_url: {
                type: String,
                default: ""
            },
            placeholder: {
                type: String,
                default: "Type and Search"
            },
            formatter: {
                type: Function,
                default(option) {
                    return `${option.text || option.name || JSON.stringify(option)}`;
                }
            },
        },
        data() {
            return {
                show_dropdown: false,
                current_position: 0,
                items: [],
                search_value: this.search
            }
        },
        methods: {
            enterPressed() {
                if (this.show_dropdown) {
                    this.$emit('selected', this.items[this.current_position]);
                    this.search_value = this.clearOnSelect ? "" : this.formatter(this.items[this.current_position]);
                    if (this.closeOnSelect) {
                        this.show_dropdown = false;
                    }
                    if (this.resetOnSelect) {
                        this.items = [];
                    }
                }
            },
            itemClicked(item) {
                this.search_value = this.clearOnSelect ? "" : this.formatter(item);
                if (this.resetOnSelect) {
                    this.items = [];
                }
                this.$emit('selected', item);
            },
            getList(e) {
                let q = null;
                if (this.api_url !== "") {
                    q = axios.post(this.api_url, {
                        search: e.target.value
                    });
                } else {
                    q = this.searchItems(e.target.value);
                }
                if (q) {
                    q.then(res => {
                        this.items = res.data || [];
                        this.$emit("items", (res.data || []));
                    }).catch(err => {
                        this.items = [];
                        this.$emit("items", []);
                        console.error(err.response)
                    })
                }
            },
            hideDropdown() {
                setTimeout(() => this.show_dropdown = false, 300);
                this.current_position = 0;
                this.$emit("dropdownHidden", true);
            },

            changePosition(ev, type) {
                if (type === "down" && this.current_position > this.items.length - 1) {
                    this.current_position = this.current_position + 1;
                    console.log("down")
                } else if (type === "up" && this.current_position > 0) {
                    this.current_position = this.current_position - 1;
                    console.log("up")
                }

            },
            fixPosition(index) {
                if (this.$refs.ddown_typehead) {
                    let items = this.$refs.ddown_typehead.$el.querySelectorAll(".ddown-item");
                    if (items.length) {
                        items[index || this.current_position].scrollIntoView({
                            behavior: 'smooth',
                            block: 'nearest',
                            inline: 'start'
                        });
                    }
                }
            }
        }
    }
</script>

<style>
    .scrollable-dropdown {
        max-height: 300px;
        overflow-y: auto;
    }
</style>
