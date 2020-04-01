<template>
    <div class="row">
        <div class="col-md-4">
            <form @submit.prevent="onSubmit">
                <b-form-group label="Name *">
                    <b-form-input v-model="form.name" placeholder="Name" :required="true"/>
                </b-form-group>
                <b-form-group label="Email">
                    <b-form-input type="email" v-model="form.email" placeholder="Email Address" :required="true"/>
                </b-form-group>
                <b-form-group :label="'Password '+ (!form.id ?'*':'')" v-if="!form.id || (form.id && password_enabled)">
                    <b-form-input type="password" autocomplete="off" name="password" v-model="form.password"
                                  :required="!form.id"></b-form-input>
                </b-form-group>
                <b-form-group label="Roles *">
                    <b-form-select
                        :required="true"
                        :options="roles"
                        v-model="temp_role"
                        @input="(role)=>{
                           if( role && form.roles.indexOf(role)<0){
                                form.roles.push(role);
                           }
                       }"
                    >
                        <b-form-select-option :value="null">Please select an option</b-form-select-option>
                    </b-form-select>

                    <b-button size="sm" class="m-1"
                              @click="()=>{
                                  if(temp_role==form.roles[key]){
                                      temp_role=null;
                                  }
                                  form.roles.splice(key,1);
                              }"
                              v-for="(role,key) in form.roles"
                              :key="key" title="Click to Remove">
                        <b-icon-x/>
                        {{role}}
                    </b-button>
                </b-form-group>


                <b-button ref="submitBtn" variant="primary" :hidden="hide_submit" block type="submit">SUBMIT</b-button>
            </form>
        </div>
        <div class="col-md-8">
            <b-card header="Direct Permissions">
                <b-form-checkbox-group
                    class="custom-control-w-25"
                    v-model="form.direct_permissions"
                    :options="permissions"
                ></b-form-checkbox-group>
                <!--                <pre v-html="form.direct_permissions"></pre>-->
            </b-card>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            password_enabled: {
                type: Boolean,
                default: true
            },
            submit_url: {
                type: String,
                default: null
            },
            search_url: {
                type: String,
                default: "/backend/roles/search"
            },
            data_url: {
                type: String,
                default: "/backend/users/data"
            },
            value: {
                type: Object,
                default: function () {
                    return {
                        id: null
                    }
                }
            },
            hide_submit: {
                type: Boolean,
                default: false
            }
        },
        mounted() {
            this.form = JSON.parse(JSON.stringify(this.value));
            if (!this.form.roles) {
                this.$set(this.form, "roles", []);
            }

            axios.post(this.data_url, {
                roles: this.roles.length <= 0 ? ["id", "name"] : false,
                permissions: this.permissions.length <= 0 ? ["id", "name"] : false,
                direct_permissions: this.form.id || false
            }).then(res => {
                // console.log(res)
                if (res.data.roles) {
                    this.roles = (res.data.roles || []).map(item => item.name);
                }
                if (res.data.permissions) {
                    this.permissions = (res.data.permissions || []).map(item => item.name);
                }
                if (res.data.direct_permissions) {
                    this.$set(this.form, "direct_permissions", res.data.direct_permissions);
                } else {
                    this.form.direct_permissions = [];
                }
            }).catch(err => {
                console.log(err.response);
                this.$emit("error", err.response);
            });

        },
        watch: {
            form: {
                handler(value) {
                    this.$emit('input', value);
                },
                deep: true
            }
        },
        data() {
            return {
                temp_role: null,
                form: {},
                roles: [],
                permissions: [],
                direct_permissions: []
            }
        },
        methods: {
            onSubmit() {
                axios.post(this.submit_url, this.form)
                    .then(res => {
                        this.$emit('created', res.data);
                    })
                    .catch(err => {
                        this.$emit('error', err.response);
                        console.log(err.response.data);
                        // this.$emit('error',res)
                    });
            },
            hitSubmit() {
                this.$refs.submitBtn.click();
            }
        }
    }
</script>
<style lang="scss">
    .custom-control-w-25 {
        .custom-control {
            min-width: 20%;
        }
    }
</style>
