<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-h3 font-bold text-neutral-900">Assistant IA</h1>
                <p class="text-sm text-neutral-500 mt-1">
                    Discussion sur <strong>{{ $candidat->name }}</strong> &mdash; {{ $offre->title }}
                </p>
            </div>
            <a href="{{ route('offres.show', $offre) }}" class="text-sm text-primary-600 hover:text-primary-700">
                &larr; Retour &agrave; l'analyse
            </a>
        </div>
    </x-slot>

    @php
        $initialMessages = $messages->toJson();
    @endphp

    <div class="max-w-4xl mx-auto">
        <x-card class="flex flex-col h-[600px]">
            <div
                x-data="chatState()"
                x-init="init(@js($messages))"
                class="flex flex-col h-full"
            >
                <div
                    x-ref="messages"
                    x-on:scroll="handleScroll()"
                    class="flex-1 overflow-y-auto p-4 space-y-4"
                >
                    <template x-if="messages.length === 0 && !loading">
                        <div class="flex flex-col items-center justify-center h-full text-center px-8">
                            <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center mb-4">
                                <svg class="w-8 h-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-neutral-700 mb-2">Assistant RH</h3>
                            <p class="text-sm text-neutral-500 mb-6">
                                Posez vos questions sur <strong>{{ $candidat->name }}</strong> et son analyse pour l'offre <strong>{{ $offre->title }}</strong>.
                            </p>
                            <div class="space-y-2 w-full max-w-sm">
                                <button
                                    type="button"
                                    x-on:click="sendSuggested('Quelles sont les compétences de {{ $candidat->name }} ?')"
                                    class="w-full text-left px-4 py-2.5 text-sm text-neutral-600 bg-neutral-50 rounded-lg hover:bg-primary-50 hover:text-primary-700 hover:border-primary-200 border border-neutral-200 transition-colors"
                                >
                                    Quelles sont les compétences de {{ $candidat->name }} ?
                                </button>
                                <button
                                    type="button"
                                    x-on:click="sendSuggested('Quel est le score de correspondance de {{ $candidat->name }} ?')"
                                    class="w-full text-left px-4 py-2.5 text-sm text-neutral-600 bg-neutral-50 rounded-lg hover:bg-primary-50 hover:text-primary-700 hover:border-primary-200 border border-neutral-200 transition-colors"
                                >
                                    Quel est le score de correspondance de {{ $candidat->name }} ?
                                </button>
                                <button
                                    type="button"
                                    x-on:click="sendSuggested('Recommanderais-tu {{ $candidat->name }} pour ce poste ?')"
                                    class="w-full text-left px-4 py-2.5 text-sm text-neutral-600 bg-neutral-50 rounded-lg hover:bg-primary-50 hover:text-primary-700 hover:border-primary-200 border border-neutral-200 transition-colors"
                                >
                                    Recommanderais-tu {{ $candidat->name }} pour ce poste ?
                                </button>
                            </div>
                        </div>
                    </template>

                    <template x-for="(msg, i) in messages" :key="msg.id || i">
                        <div>
                            <div :class="msg.role === 'user' ? 'flex justify-end' : 'flex justify-start'">
                                <div :class="msg.role === 'user' ? 'order-1' : 'order-1'">
                                    <div class="flex items-end gap-2" :class="msg.role === 'user' ? 'flex-row-reverse' : 'flex-row'">
                                        <div class="flex-shrink-0">
                                            <template x-if="msg.role === 'assistant'">
                                                <div class="w-8 h-8 bg-neutral-200 rounded-full flex items-center justify-center">
                                                    <svg class="w-4 h-4 text-neutral-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                                    </svg>
                                                </div>
                                            </template>
                                            <template x-if="msg.role === 'user'">
                                                <div class="w-8 h-8 bg-primary-100 rounded-full flex items-center justify-center">
                                                    <svg class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                                    </svg>
                                                </div>
                                            </template>
                                        </div>
                                         <div>
                                            <template x-if="msg.role === 'assistant' && msg.toolCalls && msg.toolCalls.length > 0">
                                                <div class="mb-2 space-y-1">
                                                    <template x-for="(tool, ti) in msg.toolCalls" :key="ti">
                                                        <div class="flex items-center gap-2 bg-neutral-50 border border-neutral-200 rounded-lg px-3 py-1.5 text-xs max-w-[80%]">
                                                            <template x-if="tool.status === 'running'">
                                                                <svg class="w-3.5 h-3.5 text-primary-500 animate-spin" fill="none" viewBox="0 0 24 24">
                                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                                                </svg>
                                                            </template>
                                                            <template x-if="tool.status === 'done'">
                                                                <svg class="w-3.5 h-3.5 text-success-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                                </svg>
                                                            </template>
                                                            <template x-if="tool.status === 'error'">
                                                                <svg class="w-3.5 h-3.5 text-danger-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                                </svg>
                                                            </template>
                                                            <span x-text="tool.name"></span>
                                                            <template x-if="tool.status === 'running'">
                                                                <span class="text-primary-600">En cours...</span>
                                                            </template>
                                                            <template x-if="tool.status === 'done'">
                                                                <span class="text-success-600">Terminé</span>
                                                            </template>
                                                            <template x-if="tool.status === 'error'">
                                                                <span class="text-danger-600" x-text="'Erreur: ' + (tool.error || 'inconnue')"></span>
                                                            </template>
                                                        </div>
                                                    </template>
                                                </div>
                                            </template>
                                            <div
                                                :class="msg.role === 'user'
                                                    ? 'bg-primary-600 text-white rounded-2xl rounded-br-sm'
                                                    : 'bg-neutral-100 text-neutral-900 rounded-2xl rounded-bl-sm'"
                                                class="max-w-[80%] px-4 py-3 text-sm leading-relaxed whitespace-pre-wrap"
                                                x-html="msg.role === 'assistant' ? renderMarkdown(msg.content) : escapeHtml(msg.content)"
                                            ></div>
                                            <div class="flex items-center gap-1 mt-1" :class="msg.role === 'user' ? 'justify-end' : 'justify-start'">
                                                <span class="text-xs text-neutral-400" x-text="formatTime(msg.created_at)"></span>
                                                <template x-if="msg.role === 'user' && msg.state">
                                                    <span class="inline-flex items-center">
                                                        <template x-if="msg.state === 'sending'">
                                                            <svg class="w-3.5 h-3.5 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                            </svg>
                                                        </template>
                                                        <template x-if="msg.state === 'sent'">
                                                            <svg class="w-3.5 h-3.5 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                            </svg>
                                                        </template>
                                                        <template x-if="msg.state === 'delivered'">
                                                            <svg class="w-3.5 h-3.5 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                            </svg>
                                                        </template>
                                                        <template x-if="msg.state === 'error'">
                                                            <button x-on:click="retry(msg)" class="inline-flex items-center gap-1 text-xs text-danger-600 hover:text-danger-700">
                                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                                </svg>
                                                                Échec d'envoi
                                                            </button>
                                                        </template>
                                                    </span>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>

                    <div x-show="loading && !streamActive" class="flex justify-start">
                        <div class="flex items-end gap-2">
                            <div class="w-8 h-8 bg-neutral-200 rounded-full flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-neutral-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div class="bg-neutral-100 rounded-2xl rounded-bl-sm px-4 py-3">
                                <div class="flex items-center gap-1.5">
                                    <span class="w-2 h-2 bg-neutral-400 rounded-full animate-bounce" style="animation-delay: 0ms"></span>
                                    <span class="w-2 h-2 bg-neutral-400 rounded-full animate-bounce" style="animation-delay: 150ms"></span>
                                    <span class="w-2 h-2 bg-neutral-400 rounded-full animate-bounce" style="animation-delay: 300ms"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div x-show="showScrollButton" class="relative">
                    <button
                        x-on:click="scrollToBottom()"
                        class="absolute bottom-2 right-8 w-8 h-8 bg-white border border-neutral-300 rounded-full shadow-md flex items-center justify-center hover:bg-neutral-50 transition-colors"
                    >
                        <svg class="w-4 h-4 text-neutral-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                        </svg>
                    </button>
                </div>

                <div class="border-t border-neutral-200 p-4">
                    <div class="flex gap-3 items-end">
                        <div class="flex-1 relative">
                            <textarea
                                x-ref="input"
                                x-model="draft"
                                x-on:keydown="handleKeydown($event)"
                                placeholder="Posez une question sur ce candidat..."
                                maxlength="2000"
                                x-bind:disabled="loading"
                                class="w-full border border-neutral-300 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 disabled:opacity-50 resize-none"
                                style="min-height: 40px; max-height: 160px;"
                                x-init="$el.style.height = 'auto'; $el.style.height = $el.scrollHeight + 'px'"
                                x-on:input="$el.style.height = 'auto'; $el.style.height = Math.min($el.scrollHeight, 160) + 'px'"
                            ></textarea>
                            <div class="absolute bottom-1.5 right-3">
                                <span x-show="draft.length > 1900" class="text-xs" :class="draft.length >= 2000 ? 'text-danger-500' : 'text-neutral-400'" x-text="draft.length + '/2000'"></span>
                            </div>
                        </div>
                        <button
                            x-on:click="sendMessage()"
                            x-bind:disabled="loading || !draft.trim()"
                            class="px-4 py-2.5 bg-primary-600 text-white rounded-lg text-sm font-medium hover:bg-primary-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors flex items-center gap-2"
                        >
                            <template x-if="!loading">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                </svg>
                            </template>
                            <template x-if="loading">
                                <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                </svg>
                            </template>
                            Envoyer
                        </button>
                    </div>
                    <p x-show="loading" class="text-xs text-neutral-400 mt-2 text-center">Assistant répond...</p>
                </div>
            </div>
        </x-card>
    </div>

    @push('scripts')
    <script>
        function chatState() {
            return {
                messages: [],
                draft: '',
                loading: false,
                streamActive: false,
                showScrollButton: false,
                eventSource: null,

                init(initialMessages) {
                    this.messages = initialMessages.map(m => ({ ...m, state: 'delivered' }));
                    this.$nextTick(() => this.scrollToBottom());
                },

                scrollToBottom() {
                    const container = this.$refs.messages;
                    if (container) {
                        container.scrollTo({ top: container.scrollHeight, behavior: 'smooth' });
                    }
                },

                handleScroll() {
                    const container = this.$refs.messages;
                    if (!container) return;
                    const threshold = 100;
                    this.showScrollButton = container.scrollHeight - container.scrollTop - container.clientHeight > threshold;
                },

                sendMessage() {
                    if (this.loading || !this.draft.trim()) return;

                    const text = this.draft.trim();
                    this.draft = '';
                    this.loading = true;
                    this.streamActive = false;

                    const tempId = 'msg_' + Date.now();
                    const userMsg = {
                        id: tempId,
                        role: 'user',
                        content: text,
                        created_at: new Date().toISOString(),
                        state: 'sending',
                    };
                    this.messages.push(userMsg);
                    this.$nextTick(() => this.scrollToBottom());

                    this.startStream(tempId, text);
                },

                sendSuggested(text) {
                    this.draft = text;
                    this.sendMessage();
                },

                startStream(tempId, text) {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                    const url = '{{ route("conversations.stream", [$offre, $candidat]) }}?message=' + encodeURIComponent(text);

                    this.eventSource = new EventSource(url);

                    const assistantId = 'asst_' + Date.now();
                    const assistantMsg = {
                        id: assistantId,
                        role: 'assistant',
                        content: '',
                        toolCalls: [],
                        created_at: new Date().toISOString(),
                    };
                    this.messages.push(assistantMsg);

                    this.streamActive = true;

                    this.eventSource.onmessage = (e) => {
                        try {
                            const data = JSON.parse(e.data);
                            if (data.done) {
                                this.streamActive = false;
                                this.loading = false;
                                this.eventSource.close();
                                this.eventSource = null;
                                this.updateMessageState(tempId, 'delivered');
                                this.$nextTick(() => this.scrollToBottom());
                                return;
                            }
                            if (data.token) {
                                const lastMsg = this.messages[this.messages.length - 1];
                                if (lastMsg && lastMsg.id === assistantId) {
                                    lastMsg.content += data.token;
                                    this.$nextTick(() => this.scrollToBottom());
                                }
                            }
                            if (data.type === 'tool_call') {
                                const lastMsg = this.messages[this.messages.length - 1];
                                if (lastMsg && lastMsg.id === assistantId) {
                                    lastMsg.toolCalls.push({
                                        name: data.tool_name,
                                        status: 'running',
                                    });
                                    this.$nextTick(() => this.scrollToBottom());
                                }
                            }
                            if (data.type === 'tool_result') {
                                const lastMsg = this.messages[this.messages.length - 1];
                                if (lastMsg && lastMsg.id === assistantId) {
                                    const tool = lastMsg.toolCalls.find(t => t.name === data.tool_name);
                                    if (tool) {
                                        tool.status = data.successful ? 'done' : 'error';
                                        tool.error = data.error;
                                    }
                                    this.$nextTick(() => this.scrollToBottom());
                                }
                            }
                        } catch(err) {
                            // ignore parse errors
                        }
                    };

                    this.eventSource.onerror = () => {
                        this.streamActive = false;
                        this.loading = false;
                        this.eventSource.close();
                        this.eventSource = null;
                        this.updateMessageState(tempId, 'error');
                    };
                },

                updateMessageState(id, state) {
                    const msg = this.messages.find(m => m.id === id);
                    if (msg) {
                        msg.state = state;
                    }
                },

                retry(msg) {
                    const text = msg.content;
                    this.messages = this.messages.filter(m => m.id !== msg.id);
                    this.draft = text;
                    this.$nextTick(() => this.sendMessage());
                },

                handleKeydown(e) {
                    if (e.key === 'Enter' && !e.shiftKey && !e.ctrlKey) {
                        e.preventDefault();
                        this.sendMessage();
                    }
                },

                escapeHtml(text) {
                    const div = document.createElement('div');
                    div.textContent = text;
                    return div.innerHTML;
                },

                renderMarkdown(text) {
                    let html = this.escapeHtml(text);
                    html = html.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
                    html = html.replace(/`([^`]+)`/g, '<code class="px-1 py-0.5 bg-neutral-200 rounded text-xs font-mono">$1</code>');
                    html = html.replace(/^- (.*?)(?:\n|$)/gm, '<li class="ml-4 list-disc text-sm">$1</li>');
                    html = html.replace(/(<li.*<\/li>)/s, '<ul class="space-y-1">$1</ul>');
                    html = html.replace(/\n/g, '<br>');
                    return html;
                },

                formatTime(iso) {
                    if (!iso) return '';
                    const date = new Date(iso);
                    const now = new Date();
                    const isToday = date.toDateString() === now.toDateString();
                    const hours = String(date.getHours()).padStart(2, '0');
                    const minutes = String(date.getMinutes()).padStart(2, '0');
                    if (isToday) return hours + ':' + minutes;
                    const day = String(date.getDate()).padStart(2, '0');
                    const month = String(date.getMonth() + 1).padStart(2, '0');
                    return day + '/' + month + ' ' + hours + ':' + minutes;
                },
            };
        }
    </script>
    @endpush
</x-app-layout>
