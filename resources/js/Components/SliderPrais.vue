<script setup>
import { Swiper, SwiperSlide } from 'swiper/vue';
import { Navigation, A11y } from 'swiper/modules';
const props = defineProps({
    items: {
      type: Number
    }
});
const onSlideChange = () => {
  // console.log('slide change');
};
  import 'swiper/css/navigation';
const modules = [Navigation, A11y];
</script>
<template>
  <div class="slider my-2">
    <swiper navigation :modules="modules" @slideChange="onSlideChange">
        <swiper-slide v-for="(n, index) in items">
          <slot :name="index" />
        </swiper-slide>
    </swiper>
  </div>
</template>