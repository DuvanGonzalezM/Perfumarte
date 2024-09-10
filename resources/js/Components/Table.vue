<!-- <script setup>
    props: [
        "items",
        'perPage'
    ],
    data() {
        return {
            currentPage: 1,
            search: ''
        }
    },
    computed: {
        rows() {
            return this.items.length;
        }
    }
}
</script> -->

<template>
    <div>
        <!-- <b-row>
            <b-col class="" md="3">
                <b-form-input v-model="search" type="search" placeholder="Buscar..."></b-form-input>
            </b-col>
        </b-row>
        <b-row>
            <b-col>
                <b-table striped hover :items="items" :filter="search" :per-page="perPage"
                    :current-page="currentPage"></b-table>
                <b-pagination v-if="items.length > perPage" v-model="currentPage" :total-rows="rows"
                    :per-page="perPage"></b-pagination>
            </b-col>
        </b-row> -->
    </div>
</template>