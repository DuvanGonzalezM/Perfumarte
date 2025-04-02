<script setup>
import { Swiper, SwiperSlide } from 'swiper/vue';
import { Navigation, A11y, EffectFade } from 'swiper/modules';
import { ref, onMounted } from 'vue';

const model = defineModel();
const isMobile = ref(false);

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

onMounted(() => {
  checkScreenSize();
  window.addEventListener('resize', checkScreenSize);
});

function checkScreenSize() {
  isMobile.value = window.innerWidth < 768;
}

const onSwiper = (swiper) => {
  model.value = swiper;
};
import 'swiper/css/navigation';
const modules = [Navigation, A11y, EffectFade];
</script>

<template>
  <div class="slider my-2" :class="{'slider-mobile': isMobile}">
    <swiper 
      navigation 
      :modules="modules" 
      @swiper="onSwiper" 
      @slideChange="props.changeFunction ? props.changeFunction() : ''" 
      effect="cube"
      :space-between="isMobile ? 10 : 20"
      :slides-per-view="isMobile ? 1 : 'auto'"
    >
      <swiper-slide v-for="(n, index) in items" :key="index" class="responsive-slide">
        <slot :name="index" />
      </swiper-slide>
    </swiper>
  </div>
</template>