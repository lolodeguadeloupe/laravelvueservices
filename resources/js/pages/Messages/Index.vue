<script setup>
import { ref, computed, onMounted, nextTick } from 'vue'
import AppLayout from '@/layouts/AppLayout.vue'
import { useForm, router } from '@inertiajs/vue3'
import { route } from '@/utils/routes'

const props = defineProps({
  conversations: {
    type: Array,
    default: () => []
  },
  activeConversation: {
    type: Object,
    default: null
  },
  messages: {
    type: Array,
    default: () => []
  },
  user: {
    type: Object,
    required: true
  }
})

// État de l'interface
const selectedConversationId = ref(props.activeConversation?.id || null)
const searchQuery = ref('')
const isTyping = ref(false)
const showAttachmentMenu = ref(false)
const messagesContainer = ref(null)

// Formulaire de nouveau message
const messageForm = useForm({
  content: '',
  attachment: null,
  attachment_type: null
})

// Conversations filtrées par recherche
const filteredConversations = computed(() => {
  if (!searchQuery.value) return props.conversations
  
  return props.conversations.filter(conversation => {
    const otherParticipant = conversation.participants.find(p => p.id !== props.user.id)
    return otherParticipant?.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
           conversation.last_message?.content.toLowerCase().includes(searchQuery.value.toLowerCase())
  })
})

// Conversation active
const activeConversation = computed(() => {
  return props.conversations.find(c => c.id === selectedConversationId.value)
})

// Autre participant de la conversation active
const otherParticipant = computed(() => {
  if (!activeConversation.value) return null
  return activeConversation.value.participants.find(p => p.id !== props.user.id)
})

// Fonction pour formater l'heure
const formatTime = (date) => {
  return new Intl.DateTimeFormat('fr-FR', {
    hour: '2-digit',
    minute: '2-digit'
  }).format(new Date(date))
}

// Fonction pour formater la date relative
const formatRelativeDate = (date) => {
  const now = new Date()
  const messageDate = new Date(date)
  const diffInHours = (now - messageDate) / (1000 * 60 * 60)
  
  if (diffInHours < 1) {
    return 'À l\'instant'
  } else if (diffInHours < 24) {
    return formatTime(date)
  } else if (diffInHours < 48) {
    return 'Hier'
  } else {
    return new Intl.DateTimeFormat('fr-FR', {
      day: 'numeric',
      month: 'short'
    }).format(messageDate)
  }
}

// Sélectionner une conversation
const selectConversation = (conversationId) => {
  selectedConversationId.value = conversationId
  router.get(route('messages.conversation', conversationId), {}, {
    preserveState: true,
    preserveScroll: true,
    only: ['messages', 'activeConversation']
  })
}

// Envoyer un message
const sendMessage = () => {
  if (!messageForm.content.trim() && !messageForm.attachment) return
  
  messageForm.post(route('messages.store', selectedConversationId.value), {
    preserveScroll: true,
    onSuccess: () => {
      messageForm.reset()
      scrollToBottom()
    }
  })
}

// Gérer l'upload de fichier
const handleFileUpload = (event) => {
  const file = event.target.files[0]
  if (file) {
    messageForm.attachment = file
    messageForm.attachment_type = file.type.startsWith('image/') ? 'image' : 'file'
    showAttachmentMenu.value = false
  }
}

// Supprimer l'attachement
const removeAttachment = () => {
  messageForm.attachment = null
  messageForm.attachment_type = null
}

// Scroll vers le bas des messages
const scrollToBottom = () => {
  nextTick(() => {
    if (messagesContainer.value) {
      messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight
    }
  })
}

// Simuler la frappe (typing indicator)
let typingTimer = null
const handleTyping = () => {
  isTyping.value = true
  clearTimeout(typingTimer)
  typingTimer = setTimeout(() => {
    isTyping.value = false
  }, 1000)
}

// Déterminer si c'est un nouveau jour pour afficher la date
const shouldShowDateSeparator = (currentMessage, previousMessage) => {
  if (!previousMessage) return true
  
  const currentDate = new Date(currentMessage.created_at).toDateString()
  const previousDate = new Date(previousMessage.created_at).toDateString()
  
  return currentDate !== previousDate
}

// Formater la date séparateur
const formatDateSeparator = (date) => {
  const messageDate = new Date(date)
  const today = new Date()
  const yesterday = new Date(today)
  yesterday.setDate(yesterday.getDate() - 1)
  
  if (messageDate.toDateString() === today.toDateString()) {
    return 'Aujourd\'hui'
  } else if (messageDate.toDateString() === yesterday.toDateString()) {
    return 'Hier'
  } else {
    return new Intl.DateTimeFormat('fr-FR', {
      weekday: 'long',
      day: 'numeric',
      month: 'long',
      year: 'numeric'
    }).format(messageDate)
  }
}

// Scroll initial
onMounted(() => {
  scrollToBottom()
})
</script>

<template>
  <AppLayout>
    <div class="h-screen bg-gray-100 flex overflow-hidden">
      <!-- Sidebar - Liste des conversations -->
      <div class="w-80 bg-white border-r border-gray-200 flex flex-col">
        <!-- Header sidebar -->
        <div class="p-4 border-b border-gray-200">
          <h1 class="text-xl font-semibold text-gray-900 mb-4">Messages</h1>
          
          <!-- Recherche -->
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center">
              <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
            </div>
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Rechercher une conversation..."
              class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-green-500 focus:border-green-500"
            >
          </div>
        </div>

        <!-- Liste des conversations -->
        <div class="flex-1 overflow-y-auto">
          <div v-if="filteredConversations.length === 0" class="p-4 text-center text-gray-500">
            <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
            </svg>
            <p class="text-sm">Aucune conversation trouvée</p>
          </div>

          <div
            v-for="conversation in filteredConversations"
            :key="conversation.id"
            @click="selectConversation(conversation.id)"
            :class="[
              'p-4 cursor-pointer hover:bg-gray-50 border-b border-gray-100 transition-colors',
              selectedConversationId === conversation.id ? 'bg-green-50 border-r-2 border-r-green-500' : ''
            ]"
          >
            <div class="flex items-start space-x-3">
              <!-- Avatar -->
              <div class="flex-shrink-0">
                <img
                  :src="conversation.participants.find(p => p.id !== user.id)?.avatar || '/default-avatar.jpg'"
                  :alt="conversation.participants.find(p => p.id !== user.id)?.name"
                  class="h-12 w-12 rounded-full object-cover"
                >
                <div v-if="conversation.participants.find(p => p.id !== user.id)?.is_online" 
                     class="absolute -mt-2 ml-8 h-3 w-3 bg-green-500 border-2 border-white rounded-full"></div>
              </div>

              <!-- Contenu -->
              <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between mb-1">
                  <h3 class="text-sm font-medium text-gray-900 truncate">
                    {{ conversation.participants.find(p => p.id !== user.id)?.name }}
                  </h3>
                  <div class="flex items-center space-x-1">
                    <span class="text-xs text-gray-500">
                      {{ formatRelativeDate(conversation.last_message?.created_at) }}
                    </span>
                    <div v-if="conversation.unread_count > 0" 
                         class="h-5 w-5 bg-green-500 text-white text-xs rounded-full flex items-center justify-center">
                      {{ conversation.unread_count }}
                    </div>
                  </div>
                </div>
                
                <div class="flex items-center justify-between">
                  <p class="text-sm text-gray-600 truncate">
                    <span v-if="conversation.last_message?.user_id === user.id" class="text-gray-500">Vous: </span>
                    {{ conversation.last_message?.content || 'Nouvelle conversation' }}
                  </p>
                  
                  <div class="flex items-center space-x-1">
                    <!-- Indicateur de message lu -->
                    <svg v-if="conversation.last_message?.user_id === user.id && conversation.last_message?.read_at" 
                         class="h-4 w-4 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                    
                    <!-- Indicateur booking lié -->
                    <div v-if="conversation.booking_id" 
                         class="h-2 w-2 bg-green-500 rounded-full" 
                         title="Conversation liée à une réservation"></div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Zone de chat principale -->
      <div class="flex-1 flex flex-col">
        <!-- Header de conversation -->
        <div v-if="activeConversation" class="bg-white border-b border-gray-200 p-4">
          <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
              <div class="relative">
                <img
                  :src="otherParticipant?.avatar || '/default-avatar.jpg'"
                  :alt="otherParticipant?.name"
                  class="h-10 w-10 rounded-full object-cover"
                >
                <div v-if="otherParticipant?.is_online" 
                     class="absolute -bottom-1 -right-1 h-3 w-3 bg-green-500 border-2 border-white rounded-full"></div>
              </div>
              
              <div>
                <h2 class="text-lg font-medium text-gray-900">{{ otherParticipant?.name }}</h2>
                <p class="text-sm text-gray-500">
                  {{ otherParticipant?.is_online ? 'En ligne' : 'Hors ligne' }}
                  <span v-if="isTyping" class="text-green-600"> • en train d'écrire...</span>
                </p>
              </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center space-x-2">
              <button class="p-2 text-gray-400 hover:text-gray-600 rounded-full hover:bg-gray-100">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                </svg>
              </button>
              
              <button class="p-2 text-gray-400 hover:text-gray-600 rounded-full hover:bg-gray-100">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                </svg>
              </button>
              
              <button class="p-2 text-gray-400 hover:text-gray-600 rounded-full hover:bg-gray-100">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                </svg>
              </button>
            </div>
          </div>
        </div>

        <!-- Messages -->
        <div 
          v-if="activeConversation"
          ref="messagesContainer"
          class="flex-1 overflow-y-auto p-4 space-y-4 bg-gray-50"
        >
          <template v-for="(message, index) in messages" :key="message.id">
            <!-- Séparateur de date -->
            <div v-if="shouldShowDateSeparator(message, messages[index - 1])" 
                 class="flex justify-center">
              <span class="bg-white px-3 py-1 rounded-full text-xs text-gray-500 border">
                {{ formatDateSeparator(message.created_at) }}
              </span>
            </div>

            <!-- Message -->
            <div :class="[
              'flex',
              message.user_id === user.id ? 'justify-end' : 'justify-start'
            ]">
              <div :class="[
                'max-w-xs lg:max-w-md px-4 py-2 rounded-2xl relative',
                message.user_id === user.id 
                  ? 'bg-green-600 text-white rounded-br-md' 
                  : 'bg-white text-gray-900 rounded-bl-md shadow-sm'
              ]">
                <!-- Contenu du message -->
                <div v-if="message.content" class="text-sm">
                  {{ message.content }}
                </div>

                <!-- Pièce jointe image -->
                <div v-if="message.attachment_type === 'image'" class="mt-2">
                  <img
                    :src="message.attachment_url"
                    :alt="message.attachment_name"
                    class="max-w-full h-auto rounded-lg cursor-pointer"
                    @click="openImageModal(message.attachment_url)"
                  >
                </div>

                <!-- Pièce jointe fichier -->
                <div v-if="message.attachment_type === 'file'" 
                     class="mt-2 p-2 bg-black bg-opacity-10 rounded-lg flex items-center space-x-2">
                  <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                  </svg>
                  <span class="text-xs truncate">{{ message.attachment_name }}</span>
                </div>

                <!-- Métadonnées -->
                <div :class="[
                  'text-xs mt-1 flex items-center justify-end space-x-1',
                  message.user_id === user.id ? 'text-green-100' : 'text-gray-500'
                ]">
                  <span>{{ formatTime(message.created_at) }}</span>
                  <svg v-if="message.user_id === user.id && message.read_at" 
                       class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                  </svg>
                </div>
              </div>
            </div>
          </template>

          <!-- Indicateur de frappe -->
          <div v-if="isTyping && otherParticipant" class="flex justify-start">
            <div class="bg-white rounded-2xl rounded-bl-md px-4 py-2 shadow-sm">
              <div class="flex space-x-1">
                <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"></div>
                <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
              </div>
            </div>
          </div>
        </div>

        <!-- Zone de saisie -->
        <div v-if="activeConversation" class="bg-white border-t border-gray-200 p-4">
          <!-- Prévisualisation attachement -->
          <div v-if="messageForm.attachment" class="mb-3 p-3 bg-gray-50 rounded-lg flex items-center justify-between">
            <div class="flex items-center space-x-2">
              <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
              </svg>
              <span class="text-sm text-gray-700">{{ messageForm.attachment.name }}</span>
            </div>
            <button @click="removeAttachment" class="text-gray-400 hover:text-red-500">
              <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <!-- Formulaire de message -->
          <form @submit.prevent="sendMessage" class="flex items-end space-x-3">
            <!-- Menu attachements -->
            <div class="relative">
              <button
                type="button"
                @click="showAttachmentMenu = !showAttachmentMenu"
                class="p-2 text-gray-400 hover:text-gray-600 rounded-full hover:bg-gray-100"
              >
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                </svg>
              </button>

              <!-- Menu déroulant attachements -->
              <div v-if="showAttachmentMenu" 
                   class="absolute bottom-full left-0 mb-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-10">
                <label class="flex items-center space-x-3 px-4 py-2 hover:bg-gray-50 cursor-pointer">
                  <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                  </svg>
                  <span class="text-sm">Photo</span>
                  <input type="file" accept="image/*" @change="handleFileUpload" class="hidden">
                </label>
                
                <label class="flex items-center space-x-3 px-4 py-2 hover:bg-gray-50 cursor-pointer">
                  <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                  </svg>
                  <span class="text-sm">Fichier</span>
                  <input type="file" @change="handleFileUpload" class="hidden">
                </label>
              </div>
            </div>

            <!-- Zone de texte -->
            <div class="flex-1">
              <textarea
                v-model="messageForm.content"
                @input="handleTyping"
                @keydown.enter.prevent="sendMessage"
                placeholder="Tapez votre message..."
                rows="1"
                class="w-full resize-none border border-gray-300 rounded-2xl px-4 py-2 focus:ring-green-500 focus:border-green-500 text-sm"
                style="min-height: 40px; max-height: 120px;"
              ></textarea>
            </div>

            <!-- Bouton d'envoi -->
            <button
              type="submit"
              :disabled="messageForm.processing || (!messageForm.content.trim() && !messageForm.attachment)"
              class="p-2 bg-green-600 text-white rounded-full hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <svg v-if="messageForm.processing" class="h-5 w-5 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <svg v-else class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
              </svg>
            </button>
          </form>
        </div>

        <!-- État vide - Aucune conversation sélectionnée -->
        <div v-if="!activeConversation" class="flex-1 flex items-center justify-center bg-gray-50">
          <div class="text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
            </svg>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Sélectionnez une conversation</h3>
            <p class="text-sm text-gray-500">Choisissez une conversation dans la liste pour commencer à chatter</p>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>