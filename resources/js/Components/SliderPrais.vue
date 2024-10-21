<script setup>
import { Swiper, SwiperSlide } from 'swiper/vue';
import { Navigation, A11y, EffectFade } from 'swiper/modules';

const model = defineModel();

const props = defineProps({
  items: {
    type: Number
  },
  changeFunction: {
    type: Function,
    required: false,
  },
  swiperFunction: {
    type: Function,
    required: false,
  },
});
const onSwiper = (swiper) => {
  model.value = swiper;
};
import 'swiper/css/navigation';
const modules = [Navigation, A11y, EffectFade];
</script>
<template>
  <div class="slider my-2">
    <swiper navigation :modules="modules" @swiper="onSwiper" @slideChange="props.changeFunction ? props.changeFunction() : ''" effect="cube">
      <swiper-slide v-for="(n, index) in items" >
        <slot :name="index" />
      </swiper-slide>
    </swiper>
  </div>
</template>