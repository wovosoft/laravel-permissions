<template>
    <div>
        <dt-table :title="title" v-model="search" :fields="fields" :datatable="datatable" :custom_buttons="custom_buttons">
            <template v-slot:table>
                <b-table ref="datatable" variant="primary"
                         responsive="md" hover bordered small striped
                         head-variant="dark"
                         :items="getItems"
                         :fields="fields" id="datatable" :filter="search"
                         :per-page="datatable.per_page" :current-page="datatable.current_page"
                >
                    <template v-slot:cell(action)="row">
                        <b-button-group size="sm">
                            <b-button variant="primary"
                                      @click="()=>{
                                            currentItem=JSON.parse(JSON.stringify(row.item));
                                      }"
                                      v-b-modal.viewModal>
                                <i class="fa fa-eye"></i>
                            </b-button>
                            <b-button variant="warning"
                                      @click="()=>{
                                            form=JSON.parse(JSON.stringify(row.item));
                                      }"
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
        <b-modal id="addModal" :title="add_modal_title " size="xl" header-bg-variant="dark" header-text-variant="light" lazy>
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
        <b-modal id="viewModal" :title="view_modal_title" size="xl" header-bg-variant="dark"
                 header-text-variant="light" no-body lazy>
            <data-view :item="currentItem" :skip="['permissions']"></data-view>

            <b-card header="Assigned Permissions" v-if="currentItem.permissions">
                <div class="row">
                    <div class="col">
                        <ul v-html="currentItem.permissions.slice(0,currentItem.permissions.length/2).map(item=> '<li>'+item.name.toUpperCase() +'</li>').join('')"></ul>
                    </div>
                    <div class="col">
                        <ul v-html="currentItem.permissions.slice(currentItem.permissions.length/2,currentItem.permissions.length).map(item=> '<li>'+item.name.toUpperCase() +'</li>').join('')"></ul>
                    </div>
                </div>
            </b-card>
            <br>
            <users title="Assigned Users" :role_id="currentItem.id" :disable_action="true"></users>
        </b-modal>
    </div>
</template>

<script>
    import AddItem from "./Add";
    import Datatable from "../partials/datatable";
    import Users from "./Users";
    import DtTable from "../partials/DtTable";
    import DataView from "../partials/DataView";

    export default {
        name: "RolesList",
        components: {
            DataView,
            DtTable,
            AddItem,
            Users
        },
        mixins: [Datatable],
        props: {
            title: {
                type: String,
                default: ''
            },
            api_url: {
                type: String,
                default: "/backend/roles/list"
            },
            trash_url: {
                type: String,
                default: "/backend/roles/delete"
            },
            submit_url: {
                type: String,
                default: "/backend/roles/store"
            },
            add_modal_title: {
                type: String,
                default: 'Add / Edit Role'
            },
            view_modal_title: {
                type: String,
                default: 'View Role'
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
        }
    }
</script>
