<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-h3 font-bold text-neutral-900">Assistant IA</h1>
                <p class="text-sm text-neutral-500 mt-1">
                    Discussion sur <strong>{{ $candidat->name }}</strong> — {{ $offre->title }}
                </p>
            </div>
            <a href="{{ route('offres.show', $offre) }}" class="text-sm text-primary-600 hover:text-primary-700">
                ← Retour à l'analyse
            </a>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto">
        <x-card class="flex flex-col h-[600px]">
            <div
                x-data="{
                    messages: [],
                    loading: false,
                    init() {
                        this.$nextTick(() => this.scrollToBottom());
                    },
                    scrollToBottom() {
                        const container = this.$refs.messages;
                        if (container) container.scrollTop = container.scrollHeight;
                    }
                }"
                class="flex flex-col h-full"
            >
                <div
                    x-ref="messages"
                    class="flex-1 overflow-y-auto p-4 space-y-4"
                >
                    <template x-for="(msg, i) in messages" :key="i">
                        <div :class="msg.role === 'user' ? 'flex justify-end' : 'flex justify-start'">
                            <div
                                :class="msg.role === 'user'
                                    ? 'bg-primary-600 text-white rounded-2xl rounded-br-sm'
                                    : 'bg-neutral-100 text-neutral-900 rounded-2xl rounded-bl-sm'"
                                class="max-w-[80%] px-4 py-3 text-sm leading-relaxed"
                                x-text="msg.content"
                            ></div>
                        </div>
                    </template>

                    <div x-show="loading" class="flex justify-start">
                        <div class="bg-neutral-100 text-neutral-500 rounded-2xl rounded-bl-sm px-4 py-3 text-sm flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            Analyse en cours...
                        </div>
                    </div>
                </div>

                <div class="border-t border-neutral-200 p-4">
                    <form
                        action="{{ route('conversations.store', [$offre, $candidat]) }}"
                        method="POST"
                        x-on:submit="loading = true; $nextTick(() => scrollToBottom())"
                        class="flex gap-3"
                    >
                        @csrf
                        <input
                            type="text"
                            name="message"
                            required
                            maxlength="2000"
                            placeholder="Posez une question sur ce candidat..."
                            x-bind:disabled="loading"
                            class="flex-1 border border-neutral-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 disabled:opacity-50"
                        >
                        <x-button type="submit" variant="primary" :disabled="false" x-bind:disabled="loading">
                            Envoyer
                        </x-button>
                    </form>
                </div>
            </div>
        </x-card>
    </div>
</x-app-layout>
