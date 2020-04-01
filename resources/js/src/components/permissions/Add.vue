<template>
    <div class="row">
        <div class="col">
            <form @submit.prevent="onSubmit">
                <b-form-group label="Name *">
                    <b-form-input v-model="form.name" placeholder="Name" :required="true"/>
                </b-form-group>
                <b-form-group label="Guard Name">
                    <b-form-input v-model="form.guard_name" placeholder="Guard Name"/>
                </b-form-group>

                <b-form-group label="Description">
                    <b-form-textarea v-model="form.description" :rows="7" placeholder="Description"></b-form-textarea>
                </b-form-group>
                <b-button ref="submitBtn" variant="primary" :hidden="hide_submit" block type="submit">SUBMIT</b-button>
            </form>
        </div>
        <div class="col-md-8" v-if="form.id">
            <roles title="Assigned Roles" :permission_id="form.id"></roles>
            <br>
            <users title="Assigned Users" :permission_id="form.id"></users>
        </div>

        <!--        <pre v-html="form"></pre>-->
    </div>
</template>

<script>
    import Roles from "./Roles";
    import Users from "./Users";

    export default {
        components: {Users, Roles},
        props: {
            submit_url: {
                type: String,
                default: null
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
                url: null,
                form: {},
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
