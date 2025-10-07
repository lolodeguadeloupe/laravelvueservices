<template>
  <Teleport to="body">
    <Transition
      enter-active-class="duration-300 ease-out"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="duration-200 ease-in"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div
        v-if="show"
        class="fixed inset-0 z-50 overflow-y-auto"
        aria-labelledby="modal-title"
        role="dialog"
        aria-modal="true"
      >
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
          <!-- Background overlay -->
          <div
            class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
            aria-hidden="true"
            @click="closeModal"
          ></div>

          <!-- This element is to trick the browser into centering the modal contents. -->
          <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

          <!-- Modal panel -->
          <Transition
            enter-active-class="duration-300 ease-out"
            enter-from-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            enter-to-class="opacity-100 translate-y-0 sm:scale-100"
            leave-active-class="duration-200 ease-in"
            leave-from-class="opacity-100 translate-y-0 sm:scale-100"
            leave-to-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
          >
            <div
              v-if="show"
              :class="[
                'inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:p-6',
                maxWidthClasses[maxWidth]
              ]"
            >
              <!-- Header -->
              <div v-if="title || $slots.header" class="flex items-center justify-between mb-4">
                <div v-if="title" class="flex items-center">
                  <h3 class="text-lg font-medium text-gray-900 dark:text-white" id="modal-title">
                    {{ title }}
                  </h3>
                </div>
                <div v-if="$slots.header">
                  <slot name="header" />
                </div>
                <button
                  v-if="closable"
                  @click="closeModal"
                  type="button"
                  class="ml-4 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors"
                >
                  <span class="sr-only">Fermer</span>
                  <Icon name="x" class="h-6 w-6" />
                </button>
              </div>

              <!-- Content -->
              <div class="text-gray-900 dark:text-white">
                <slot />
              </div>

              <!-- Footer -->
              <div v-if="$slots.footer" class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                <slot name="footer" />
              </div>
            </div>
          </Transition>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup lang="ts">
import { watch, onMounted, onUnmounted } from 'vue'
import Icon from './Icon.vue'

interface Props {
  show: boolean
  title?: string
  maxWidth?: 'sm' | 'md' | 'lg' | 'xl' | '2xl' | 'full'
  closable?: boolean
  closeOnEscape?: boolean
  closeOnBackdropClick?: boolean
}

interface Emits {
  (e: 'update:show', value: boolean): void
  (e: 'close'): void
}

const props = withDefaults(defineProps<Props>(), {
  maxWidth: 'md',
  closable: true,
  closeOnEscape: true,
  closeOnBackdropClick: true
})

const emit = defineEmits<Emits>()

const maxWidthClasses = {
  sm: 'sm:max-w-sm sm:w-full',
  md: 'sm:max-w-md sm:w-full',
  lg: 'sm:max-w-lg sm:w-full',
  xl: 'sm:max-w-xl sm:w-full',
  '2xl': 'sm:max-w-2xl sm:w-full',
  full: 'sm:max-w-full sm:w-full sm:mx-4'
}

const closeModal = () => {
  if (props.closeOnBackdropClick) {
    emit('update:show', false)
    emit('close')
  }
}

const handleEscapeKey = (event: KeyboardEvent) => {
  if (event.key === 'Escape' && props.show && props.closeOnEscape) {
    emit('update:show', false)
    emit('close')
  }
}

watch(() => props.show, (newValue) => {
  if (newValue) {
    document.body.style.overflow = 'hidden'
  } else {
    document.body.style.overflow = ''
  }
})

onMounted(() => {
  document.addEventListener('keydown', handleEscapeKey)
})

onUnmounted(() => {
  document.removeEventListener('keydown', handleEscapeKey)
  document.body.style.overflow = ''
})
</script>