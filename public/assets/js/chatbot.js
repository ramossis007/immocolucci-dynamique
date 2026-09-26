const Chatbot = {
    sessionId: 'sess_' + Math.random().toString(36).substr(2, 9),
    isOpen: false,

    init() {
        this.toggle = document.getElementById('chatbot-toggle');
        this.panel = document.getElementById('chatbot-panel');
        this.close = document.getElementById('chatbot-close');
        this.form = document.getElementById('chatbot-form');
        this.input = document.getElementById('chatbot-input');
        this.messages = document.getElementById('chatbot-messages');

        if (!this.toggle || !this.panel) return;

        this.toggle.addEventListener('click', () => this.open());
        this.close.addEventListener('click', () => this.closePanel());
        this.form.addEventListener('submit', (e) => {
            e.preventDefault();
            this.envoyer(this.input.value.trim());
        });
        document.querySelectorAll('.chatbot-suggestions button').forEach(btn => {
            btn.addEventListener('click', () => this.envoyer(btn.dataset.q));
        });
        this.ajouterMessage('bot', 'Bonjour ! Je suis l\'assistant Immo Colucci. Comment puis-je vous aider ?');
    },
    open() { this.panel.classList.remove('hidden'); this.isOpen = true; this.input.focus(); },
    closePanel() { this.panel.classList.add('hidden'); this.isOpen = false; },
    ajouterMessage(type, texte) {
        const div = document.createElement('div');
        div.className = 'chatbot-msg ' + type;
        div.textContent = texte;
        this.messages.appendChild(div);
        this.messages.scrollTop = this.messages.scrollHeight;
    },
    async envoyer(question) {
        if (!question) return;
        this.ajouterMessage('user', question);
        this.input.value = '';
        const typing = document.createElement('div');
        typing.className = 'chatbot-msg bot typing';
        typing.textContent = '...';
        this.messages.appendChild(typing);
        try {
            const res = await fetch((window.BASE_PATH || '') + '/public/chatbot', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({ question: question, session_id: this.sessionId })
            });
            const data = await res.json();
            typing.remove();
            this.ajouterMessage('bot', data.reponse || 'Erreur');
        } catch (err) {
            typing.remove();
            this.ajouterMessage('bot', 'Desole, une erreur est survenue.');
        }
    }
};
document.addEventListener('DOMContentLoaded', () => Chatbot.init());