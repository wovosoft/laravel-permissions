<template>
    <b-card :title="title">
        <template v-slot:header>
            <dt-header
                :custom_buttons="custom_buttons"
                :datatable="datatable"
                :fields="fields"
                v-bind:value="value"
                @input="$emit('input',$event)"
            ></dt-header>
        </template>
        <slot name="table"></slot>
        <template v-slot:footer>
            <dt-footer :datatable="datatable"></dt-footer>
        </template>
    </b-card>
</template>

<script>
    import DtHeader from "./DtHeader";
    import DtFooter from "./DtFooter";


    export default {
        components: {
            DtHeader,
            DtFooter
        },
        props: {
            title: {
                type: String,
                default: ""
            },
            fields: {
                type: Array,
                default: () => []
            },
            datatable: {
                type: Object,
                default: () => {
                    return {
                        per_page: 10,
                        current_page: 1,
                        total: 0,
                        from: 0,
                        to: 0,
                    }
                }
            },
            value: {
                type: String,
                default: ''
            },
            custom_buttons: {
                type: Array,
                default: () => []
            }
        },
        data() {
            return {
                ff: [],
                cbuttons: [1, 2, 3]
            }
        },
        // created() {
        //     setTimeout(() => {
        //         let context = this.$slots.table[0].context;
        //         this.ff = context.fields;
        //         this.cbuttons = context.custom_buttons;
        //
        //     }, 10);
        //
        // }
    }
</script>

<style scoped>

</style>
