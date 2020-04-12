<template>
    <div>
        <breadcrumb></breadcrumb>
        <b-card no-body>
            <b-tabs small>
                <b-tab title="Users" lazy>
                    <users title="User Management" :password_enabled="false"></users>
                </b-tab>
                <b-tab title="Roles" lazy>
                    <roles title="Role Management"></roles>
                </b-tab>
                <b-tab title="Permissions" lazy>
                    <permissions title="Permission Management"></permissions>
                </b-tab>
                <b-tab title="Test User Abilities" lazy>
                    <user-ability></user-ability>
                </b-tab>
                <b-tab title="Test Role Abilities" lazy>
                    <role-ability></role-ability>
                </b-tab>
            </b-tabs>
        </b-card>
    </div>
</template>

<script>
    // import Roles from "./components/roles/List";
    // import Permissions from "./components/permissions/List";
    // import Users from "./components/users/List";
    // import UserAbility from "./components/ability_test/UserAbility"
    // import RoleAbility from "./components/ability_test/RoleAbility";
    import Breadcrumb from "../../../../../../resources/js/components/Breadcrumb";

    export default {
        components: {
            Roles: () => import("./components/roles/List"),
            Permissions: () => import("./components/permissions/List"),
            Users: () => import("./components/users/List"),
            UserAbility: () => import("./components/ability_test/UserAbility"),
            RoleAbility: () => import("./components/ability_test/RoleAbility"),
            Breadcrumb
        }
    }
</script>
<style lang="scss">
    #tabs_list {
        .tab-pane {
            .card {
                border: 0 !important;
            }
        }
    }
</style>
