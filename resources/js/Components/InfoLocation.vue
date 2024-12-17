<template>
    <div class="col-6 p-2 row cardboxprais cardinfolocation text-center">
        <div class="col-6">
            <strong>{{$page.props.auth.user.location.zone.zone_name}}</strong><br>
            <strong>{{ $page.props.auth.user.location.name }}</strong><br>
        </div>
        <div class="col-6">
            <strong>{{ $page.props.auth.user.location.address }}</strong><br>
            <strong>{{ $page.props.auth.user.location_id }}</strong>
        </div>
    </div>
</template>