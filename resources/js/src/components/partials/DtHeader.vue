<template>
    <div class="row">
        <div class="col-md-3">
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">Per Page</span>
                </div>
                <select class="form-control" v-model="datatable.per_page">
                    <option v-bind:value="r" v-for="r in range()">{{r}}</option>
                </select>
            </div>
        </div>
        <div class="col-md-5">
            <input type="text"
                   class="form-control"
                   @keydown.esc="()=>{$event.target.value='';$emit('input','')}"
                   v-bind:value="value"
                   @change="datatable.current_page=1"
                   @input="$emit('input', $event.target.value)"
                   placeholder="Type and Hit Enter to Search the table, ESC to clear">
        </div>
        <div class="col-md-4 text-right">
            <div class="btn-group">
                <button type="button" class="btn"
                        :class="btn.variant?'btn-'+btn.variant:'btn-dark'"
                        v-if="custom_buttons && custom_buttons.length"
                        v-for="(btn,btn_key) in custom_buttons"
                        :key="btn_key"
                        @click="btn.method">
                    <i :class="btn.icon" v-if="btn.icon"></i> {{btn.text}}
                </button>
            </div>
            <b-dropdown text="Columns" right class="columns-dropdown p-0" variant="primary">
                <ul class="list-group">
                    <li class="list-group-item" v-for="(field,key) in fields" :key="key">
                        <b-form-checkbox v-model="field.visible" :value="true"
                                         :uncheched-value="false"
                                         @change="changeVisibility(field,key)"
                        >
                            {{startCase(field.label || field.key)}}
                        </b-form-checkbox>
                    </li>
                </ul>
            </b-dropdown>
        </div>
    </div>
</template>

<script>
    import {range, changeVisibility, startCase} from "./datatable";

    export default {
        props: {
            fields: {
                type: Array,
                default: () => []
            },
            datatable: Object,
            value: {
                type: String,
                default:""
            },
            custom_buttons: {
                type: Array,
                default: () => []
            }
        },
        methods: {
            range,
            changeVisibility,
            startCase
        }
    }
</script>

<style lang="scss">
    .columns-dropdown {
        .dropdown-menu {
            padding: 0 !important;
            border: 0 !important;
        }

        .list-group-item {
            label {
                cursor: pointer;
            }
        }
    }
</style>
