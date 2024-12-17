<script setup>
// You can add props or other logic here if needed
</script>
<template>
    
    <div class="col-6 p-2 cardboxprais cardinfolocation">
        <strong>{{$page.props.auth.user.location}} </strong>
        {{ console.log($page.props) }}
        <!-- <span>{{ props.requestPrais.user?.location?.name || 'No disponible' }}</span><br> -->
        <strong>Usuario: </strong>
        <!-- <span>{{ props.requestPrais.user?.username || 'No disponible' }}</span><br> -->
        <strong>Fecha: </strong>
        <!-- <span>{{ moment(props.requestPrais.created_at).format('DD/MM/Y') }}</span> -->
    </div>
</template>