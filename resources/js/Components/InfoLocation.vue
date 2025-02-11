<template>
    <div class="p-2 row cardboxprais cardinfolocation text-center">
        {{ console.log($page.props.auth.user) }}
        <div class="col-6">
            <strong>{{$page.props.auth.user.location_user[0].zone.zone_name}}</strong><br>
        </div>
        <div class="col-6">
            <strong>{{ $page.props.auth.user.location_user[0].name }}</strong><br>
            <!-- <strong>{{ $page.props.auth.user.location_id }}</strong> -->
        </div>
        <div class="col-12">
            <strong>{{ $page.props.auth.user.location_user[0].address }}</strong><br>
        </div>
    </div>
</template>