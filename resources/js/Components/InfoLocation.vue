<template>
    <div class="p-2 row cardboxprais cardinfolocation text-center" :class="{'info-location-mobile': isMobile}">
        <div class="col-md-6 col-sm-12">
            <strong>{{$page.props.auth.user.location_user[0].zone.zone_name}}</strong><br>
        </div>
        <div class="col-md-6 col-sm-12">
            <strong>{{ $page.props.auth.user.location_user[0].name }}</strong><br>
            <!-- <strong>{{ $page.props.auth.user.location_id }}</strong> -->
        </div>
        <div class="col-12">
            <strong>{{ $page.props.auth.user.location_user[0].address }}</strong><br>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const isMobile = ref(false);

onMounted(() => {
    checkScreenSize();
    window.addEventListener('resize', checkScreenSize);
});

function checkScreenSize() {
    isMobile.value = window.innerWidth < 768;
}
</script>