<template>
    <div>
        <b-card :header="title" no-body>
            <b-table ref="datatable" variant="primary"
                     class="m-0 border-0"
                     hover bordered small striped
                     head-variant="dark"
                     :items="getItems"
                     :fields="fields"
                     :per-page="datatable.per_page" :current-page="datatable.current_page"
            >
                <template v-slot:cell(action)="row">
                    <b-button-group size="sm">
                        <b-button variant="danger"
                                  title="Revoke User"
                                  @click="detach(row.item.id)">
                            <i class="fa fa-trash"></i> Detach
                        </b-button>
                    </b-button-group>
                </template>
            </b-table>
            <template v-slot:footer>
                <dt-footer :datatable="datatable"></dt-footer>
            </template>
        </b-card>
    </div>
</template>

<script>
    import DtFooter from "../partials/DtFooter"
    import {msgBox} from "../partials/datatable"

    export default {
        components: {
            DtFooter
        },
        props: {
            title: {
                type: String,
                default: ''
            },
            api_url: {
                type: String,
                default: "/backend/users/listByRole"
            },
            trash_url: {
                type: String,
                default: "/backend/roles/detuch"
            },
            role_id: {
                type: Number,
                default: 0,
                required: true
            },
            disable_action: {
                type: Boolean,
                default: false
            }
        },
        // mixins: [Datatable],

        data: function () {
            return {
                _: null,
                form: {},
                fields: [
                    {key: 'id', sortable: true, visible: true},
                    {key: 'name', sortable: true, visible: true},
                    {key: 'email', sortable: true, visible: true},
                    {
                        key: 'action', sortable: false, searchable: false,
                        thClass: {
                            'text-right': true,
                            "d-none": this.disable_action
                        },
                        tdClass: {
                            'text-right': true,
                            "d-none": this.disable_action
                        }
                    },
                ],
                datatable: {
                    per_page: 10,
                    current_page: 1,
                    total: 0,
                    from: 0,
                    to: 0,
                },
            }
        },
        created() {
            this._ = window._;
        },
        methods: {
            getItems(ctx) {
                // console.log(ctx)
                let columns = [];
                if (ctx.filter) {
                    for (let x in this.fields) {
                        if (_.isBoolean(this.fields[x].visible) && this.fields[x].visible) {
                            if (!_.isBoolean(this.fields[x].searchable) || this.fields[x].searchable) {
                                columns.push(this.fields[x].key)
                            }
                        }
                    }
                }
                if (!this.role_id) {
                    return [];
                }

                return axios.post(this.api_url + "?page=" + (ctx.currentPage ? ctx.currentPage : 1), {
                    per_page: this.datatable.per_page,
                    orderBy: ctx.sortBy,
                    order: ctx.sortDesc ? 'desc' : 'asc',
                    filter: ctx.filter,
                    columns: columns,
                    role_id: this.role_id
                }).then(res => {
                    // console.log(res.data);
                    this.datatable.total = res.data.total;
                    this.datatable.from = res.data.from;
                    this.datatable.to = res.data.to;
                    this.datatable.current_page = res.data.current_page;
                    return res.data.data;
                }).catch(err => {
                    console.log(err.response);
                    return [];
                });
            },
            detach(id) {
                this.$bvModal.msgBoxConfirm('Are you sure?', {
                    title: 'Please Confirm',
                    size: 'sm',
                    buttonSize: 'sm',
                    okVariant: 'danger',
                    okTitle: 'YES',
                    cancelTitle: 'NO',
                    footerClass: 'p-2',
                    hideHeaderClose: false,
                    centered: false,
                    headerBgVariant: 'dark',
                    headerTextVariant: 'light'
                }).then(value => {
                    if (value) {
                        axios.post(this.trash_url, {
                            role_id: this.role_id,
                            user_id: id
                        }).then(res => {
                            console.log(res)
                            this.msgBox(res.data);
                            this.$refs.datatable.refresh();
                        }).catch(err => {
                            this.msgBox(err.response);
                            console.log(err.response)
                        });
                    }
                }).catch(err => {
                    console.log(err)
                });
            },
            msgBox: msgBox
        }
    }
</script>
