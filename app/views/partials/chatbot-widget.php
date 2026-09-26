<div id="chatbot-widget">
    <button id="chatbot-toggle" aria-label="Ouvrir le chat">
        <i class="fas fa-comments"></i>
    </button>
    
    <div id="chatbot-panel" class="hidden">
        <div class="chatbot-header">
            <div>
                <strong>Assistant Immo Colucci</strong>
                <small>En ligne</small>
            </div>
            <button id="chatbot-close">×</button>
        </div>
        
        <div id="chatbot-messages"></div>
        
        <form id="chatbot-form">
            <input type="text" id="chatbot-input" placeholder="Posez votre question..." autocomplete="off">
            <button type="submit"><i class="fas fa-paper-plane"></i></button>
        </form>
        
        <div class="chatbot-suggestions">
            <button data-q="Quels sont vos biens à vendre ?">Biens à vendre</button>
            <button data-q="Quels services proposez-vous ?">Services</button>
            <button data-q="Comment vous contacter ?">Contact</button>
        </div>
    </div>
</div>