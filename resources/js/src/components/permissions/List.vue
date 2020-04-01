<template>
    <div>
        <dt-table :title="title" v-model="search" :fields="fields" :datatable="datatable" :custom_buttons="custom_buttons">
            <template v-slot:table>
                <b-table ref="datatable" variant="primary" responsive="md" hover bordered small striped
                         head-variant="dark"
                         :items="getItems"
                         :fields="fields" id="datatable" :filter="search"
                         :per-page="datatable.per_page" :current-page="datatable.current_page"
                >
                    <template v-slot:cell(action)="row">
                        <b-button-group size="sm">
                            <b-button variant="primary" @click="currentItem=row.item" v-b-modal.viewModal>
                                <i class="fa fa-eye"></i>
                            </b-button>
                            <b-button variant="warning" @click="form=JSON.parse(JSON.stringify(row.item))"
                                      v-b-modal.addModal>
                                <i class="fa fa-edit"></i>
                            </b-button>
                            <b-button variant="danger" @click="trash(row.item.id)">
                                <i class="fa fa-trash"></i>
                            </b-button>
                        </b-button-group>
                    </template>
                </b-table>
            </template>
        </dt-table>
        <b-modal id="addModal" :title="add_modal_title " size="xl" header-bg-variant="dark"
                 header-text-variant="light" lazy>
            <add-item @created="(v)=>{$refs.datatable.refresh();$bvModal.hide('addModal');msgBox(v);}"
                      :hide_submit="true"
                      :submit_url="submit_url"
                      v-model="form"
                      ref="ItemAdd"></add-item>
            <template v-slot:modal-footer="{close}">
                <b-button-group class="float-right">
                    <b-button variant="primary" @click="$refs.ItemAdd.hitSubmit()">SUBMIT</b-button>
                    <b-button @click="close()">Close</b-button>
                </b-button-group>
            </template>
        </b-modal>
        <b-modal id="viewModal" :title="view_modal_title" size="lg" header-bg-variant="dark" header-text-variant="light"
                 no-body lazy>
            <data-view :item="currentItem"></data-view>
            <roles title="Assigned Roles" :permission_id="currentItem.id"></roles>
            <br>
            <users title="Assigned Users" :permission_id="currentItem.id"></users>
        </b-modal>
    </div>
</template>

<script>
    import AddItem from "./Add";
    import Datatable from "../partials/datatable";
    import Users from "./Users";
    import Roles from "./Roles";
    import DtTable from "../partials/DtTable";
    import DataView from "../partials/DataView";

    export default {
        name: "PermissionsList",
        props: {
            title: {
                type: String,
                default: ''
            },
            debounce: {
                type: Number,
                default: 300
            },
            api_url: {
                type: String,
                default: "/backend/permissions/list"
            },
            trash_url: {
                type: String,
                default: "/backend/permissions/delete"
            },
            submit_url: {
                type: String,
                default: "/backend/permissions/store"
            },
            add_modal_title: {
                type: String,
                default: 'Add / Edit Permission'
            },
            view_modal_title: {
                type: String,
                default: 'View Permission'
            },
            custom_buttons: {
                type: Array,
                default() {
                    return [
                        {
                            text: 'Add',
                            method: () => {
                                this.$bvModal.show("addModal");
                            }
                        }
                    ]
                }
            },
        },
        components: {
            DataView,
            DtTable,
            AddItem,
            Users,
            Roles
        },
        mixins: [Datatable],
        mounted() {
            this.$root.$on('bv::modal::hidden', (bvEvent, modalId) => {
                if (modalId === 'addModal') {
                    this.form = {};
                } else if (modalId === 'viewModal') {
                    this.currentItem = {};
                }
            })
        },
        data: function () {
            return {
                _: null,
                form: {},
                fields: [
                    {key: 'id', sortable: true, visible: true},
                    {key: 'name', sortable: true, visible: true},
                    {key: 'guard_name', sortable: true, visible: true, label: "Guard"},
                    {key: 'description', sortable: true, visible: true},
                    {key: 'created_at', sortable: true, visible: true},
                    {key: 'updated_at', sortable: true, visible: true},
                    {key: 'action', sortable: false, searchable: false, thClass: 'text-right', tdClass: 'text-right'},
                ]
            }
        },
        created() {
            this._ = window._;
        },
        methods: {}
    }
</script>
