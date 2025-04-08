<script setup>
import { ref, onMounted } from 'vue';

const model = defineModel();

const props = defineProps({
    labelValue: {
        type: String,
    },
    id: {
        type: String,
        required: true,
    },
    name: {
        type: String,
        required: true,
    },
    type: {
        type: String,
        required: true,
    },
    required: {
        type: Boolean,
    },
    focus: {
        type: Boolean,
    },
    messageError: {
        type: String,
    },
    minimo: {
        type: Number,
    },
});

const hasFocus = ref(false);
const input = ref(null);
const label = ref(null);
const isMobile = ref(false);
const isTablet = ref(false);

onMounted(() => {
    checkScreenSize();
    window.addEventListener('resize', checkScreenSize);
});

function checkScreenSize() {
    isMobile.value = window.innerWidth < 576;
    isTablet.value = window.innerWidth >= 576 && window.innerWidth < 992;
}

const showPassword = ref(false);

const togglePasswordVisibility = () => {
    showPassword.value = !showPassword.value;
};
</script>

<template>
    <div class="inputContainer position-relative" 
         :class="{
             'errorField': messageError && !(hasFocus ||  focus),
             'input-mobile': isMobile,
             'input-tablet': isTablet
         }">
        <input 
            :id="id"
            :name="name"
            :class="{ 
                'focus': hasFocus || model || focus,
                'input-field-mobile': isMobile,
                'input-field-tablet': isTablet
            }"
            :type="showPassword ? 'text' : type"
            @focus="hasFocus = true"
            @blur="hasFocus = false"
            v-model="model"
            :required="required"
            :min="minimo"
            ref="input"
        />
        <label class="position-absolute pe-none" 
               ref="label" 
               :for="id" 
               v-if="labelValue"
               :class="{
                   'label-mobile': isMobile,
                   'label-tablet': isTablet
               }">
            {{ labelValue }}
        </label>
        <a type="button" v-if="type === 'password'" 
            class="password-toggle" 
            @click="togglePasswordVisibility"
            tabindex="-1"
        >
            <i :class="showPassword ? 'fa-solid fa-eye-slash' : 'fa-solid fa-eye'"></i>
        </a>
        <div class="mt-1" 
             v-if="messageError && !(hasFocus ||  focus)"
             :class="{
                 'error-message-mobile': isMobile,
                 'error-message-tablet': isTablet
             }">
            <p>
                {{ messageError }}
            </p>
        </div>
    </div>
</template>