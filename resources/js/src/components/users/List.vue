<template>
    <div>
        <dt-table :title="title" v-model="search" :fields="fields" :datatable="datatable"
                  :custom_buttons="custom_buttons">
            <template v-slot:table>
                <b-table ref="datatable" variant="primary" responsive="md" hover bordered small striped
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
                                        currentItem.roles= currentItem.roles.map(item=>item.name);
                                   }"
                                      v-b-modal.viewModal>
                                <i class="fa fa-eye"></i>
                            </b-button>
                            <b-button variant="warning"
                                      @click="()=>{
                                        form=JSON.parse(JSON.stringify(row.item));
                                        form.roles = form.roles.map(item=>item.name);
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
        <b-modal id="addModal" :title="add_modal_title " size="xl"
                 header-bg-variant="dark"
                 header-text-variant="light">
            <add-item @created="(v)=>{$refs.datatable.refresh();$bvModal.hide('addModal');msgBox(v);}"
                      @error="v=>msgBox(v.data)"
                      :hide_submit="true"
                      :submit_url="submit_url"
                      v-model="form"
                      :password_enabled="password_enabled"
                      ref="ItemAdd"></add-item>
            <template v-slot:modal-footer="{close}">
                <b-button-group class="float-right">
                    <b-button variant="primary" @click="$refs.ItemAdd.hitSubmit()">SUBMIT</b-button>
                    <b-button @click="close()">Close</b-button>
                </b-button-group>
            </template>
        </b-modal>
        <b-modal id="viewModal" :title="view_modal_title"
                 size="lg"
                 header-bg-variant="dark"
                 header-text-variant="light"
                 no-body>
            <data-view :item="currentItem"
                       :fields="[
                            {
                                key: 'key', sortable: true,
                                formatter: (v) => startCase(v)
                            },
                            {
                                key: 'value',
                                sortable: true,
                                formatter:(data,key,row)=>{
                                    if ((row.key==='created_at' || key==='updated_at') && data){
                                        return data;    //apply date formats here using moment, dayjs, fetcha etc
                                    }else if (row.key==='roles' && data && Array.isArray(data)){
                                        return data.join(', ');
                                    }else if (row.key==='permissions' && data && Array.isArray(data)){
                                        return data.map(item=>startCase(item.name)).join(', ');
                                    }
                                    return data;
                                }
                            }
                       ]">
            </data-view>
        </b-modal>
    </div>
</template>

<script>
    import DtHeader from '../partials/DtHeader'
    import DtFooter from '../partials/DtFooter'
    import AddItem from "./Add";
    import Datatable from "../partials/datatable";
    import DtTable from "../partials/DtTable";
    import DataView from "../partials/DataView";

    export default {
        name: "UsersList",
        components: {
            DtHeader,
            DtFooter,
            AddItem,
            DtTable,
            DataView
        },
        mixins: [Datatable],
        props: {
            password_enabled: {
                type: Boolean,
                default: true
            },
            title: {
                type: String,
                default: ''
            },
            api_url: {
                type: String,
                default: "/backend/users/list"
            },
            trash_url: {
                type: String,
                default: "/backend/users/delete"
            },
            submit_url: {
                type: String,
                default: "/backend/users/store"
            },
            add_modal_title: {
                type: String,
                default: 'Add / Edit User'
            },
            view_modal_title: {
                type: String,
                default: 'View User'
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

        data() {
            return {
                form: {},
                fields: [
                    {key: 'id', sortable: true},
                    {key: 'name', sortable: true},
                    {key: 'email', sortable: true},
                    {key: 'created_at', sortable: true},
                    {key: 'updated_at', sortable: true},
                    {key: 'action', sortable: false, searchable: false, thClass: 'text-right', tdClass: 'text-right'},
                ]
            }
        }
    }
</script>
