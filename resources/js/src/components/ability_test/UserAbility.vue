<template>
    <div>
        <form
            @reset="form= {user:null,permissions: []}"
            @submit.prevent="onSubmit"
        >
            <b-card>

                <b-form-group label="Search Users">
                    <type-head api_url="/backend/users/search"
                               placeholder="Type and Search User"
                               @selected="item=>form.user=item.id"
                               @reset="form.user=null"
                               :formatter="(item)=>`${item.id} # ${item.name} | ${item.email}`">
                        <template v-slot:option="item">
                            <div v-html="`${item.id} # ${item.name} | ${item.email}`"></div>
                        </template>
                    </type-head>
                </b-form-group>

                <b-form-group label="Search Permissions">
                    <type-head api_url="/backend/permissions/search"
                               :clear-on-select="true"
                               placeholder="Type and Search Permission"
                               @selected="item=>{
                           if (!form.permissions.filter(permission => permission === item.name).length){
                                    form.permissions.push(item.name)
                               }
                           }"
                               :formatter="(item)=>item.name">
                        <template v-slot:option="item">
                            {{item.name}}
                        </template>
                    </type-head>
                </b-form-group>
                <div>
                    <button class="btn btn-sm btn-secondary m-1"
                            title="Click to Remove"
                            v-for="(permission,ukey) in form.permissions"
                            @click="form.permissions.splice(ukey,1)"
                            :key="ukey">
                        {{permission}}
                        <b-icon icon="x"></b-icon>
                    </button>
                </div>
                <template v-slot:footer>
                    <div class="text-right">
                        <button type="reset" class="btn btn-dark">Reset</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </template>
            </b-card>
        </form>

        <b-table-lite v-if="output.length" small bordered striped hover head-variant="dark" :items="output" class="m-0"
                 :fields="[{key:'permission',sortable:true},{key:'ability',sortable: true}]"/>
    </div>
</template>

<script>
    import TypeHead from "../partials/TypeHead";

    export default {
        components: {TypeHead},
        props: {
            api_url: {
                type: String,
                default: "/backend/users/search"
            },
            option_bind_to_html: {
                type: Boolean,
                default: true
            }
        },
        data() {
            return {
                form: {
                    user: null,
                    permissions: []
                },
                output: []
            }
        },
        methods: {
            onSubmit() {
                if (!this.form.user || !this.form.permissions.length) {
                    alert("Please Fill the form currently");
                    return false;
                }
                axios.post("/backend/users/abilities/check", this.form)
                    .then(res => {
                        this.output = res.data || [];
                    })
                    .catch(err => {
                        this.output = [];
                        console.log(err.response)
                    });
            }
        }
    }
</script>

<style scoped>

</style>
