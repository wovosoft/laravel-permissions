<template>
    <div class="row">
        <div class="col">
            <template v-if="datatable.to">
                Showing {{datatable.from || 0}} to {{datatable.to || 0}} = {{datatable.to - datatable.from +
                (datatable.from)}} items of
                {{datatable.total}} items.
            </template>
            <template v-else>No Items Found</template>
        </div>
        <div class="col">
            <b-pagination
                align="right"
                size="sm"
                v-model="datatable.current_page"
                :total-rows="datatable.total"
                :per-page="datatable.per_page"
                aria-controls="datatable"
            ></b-pagination>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            datatable: Object
        }
    }
</script>

<style scoped>

</style>
