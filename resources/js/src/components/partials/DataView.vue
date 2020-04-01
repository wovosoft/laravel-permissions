<template>
    <b-table :items="obj2Table(item,skip,as)" :fields="fields" v-bind="$attrs"
             :head-variant="headVariant"
             :bordered="bordered"
             :small="small"
             :hover="hover"
             :striped="striped">
    </b-table>
</template>

<script>
    import {obj2Table, startCase} from "./datatable";

    export default {
        props: {
            headVariant: {
                type: String,
                default: 'dark'
            },
            bordered: {
                type: Boolean,
                default: true
            },
            small: {
                type: Boolean,
                default: true
            },
            hover: {
                type: Boolean,
                default: true
            },
            striped: {
                type: Boolean,
                default: true
            },
            item: {
                type: Object,
                required: true
            },
            skip: {
                type: Array,
                default: () => []
            },
            as: {
                type: Object,
                default: () => {
                    return {key: 'key', value: 'value'}
                }
            },
            fields: {
                type: Array,
                default: () => {
                    return [
                        {
                            key: 'key', sortable: true,
                            formatter: (v) => startCase(v)
                        },
                        {
                            key: 'value',
                            sortable: true
                        }
                    ];
                }
            }
        },
        methods: {
            obj2Table,
            startCase
        }
    }
</script>

<style scoped>

</style>
