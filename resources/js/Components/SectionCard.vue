<template>
    <section class="section-prais position-relative container py-0">
        <div class="over-section position-absolute d-flex align-items-center m-auto p-3">
            <slot name="headerSection" />
        </div>
        <div class="m-auto section-content-prais">
            <slot />
        </div>
    </section>
</template>