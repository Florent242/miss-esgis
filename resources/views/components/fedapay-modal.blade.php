<!-- FedaPay Payment Modal -->
<div id="fedapay-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full max-h-[90vh] overflow-y-auto relative">
        <!-- Close Button -->
        <button onclick="closeFedaPay()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition z-10">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>

        <!-- Header -->
        <div class="bg-gradient-to-r from-pink-500 to-purple-600 p-6 rounded-t-2xl">
            <h2 class="text-2xl font-bold text-white text-center">Paiement Sécurisé</h2>
            <p class="text-white text-center text-sm mt-2">Propulsé par FedaPay</p>
        </div>

        <!-- Content -->
        <div class="p-6">
            <!-- Step 1: Phone Number Entry -->
            <div id="step-phone-entry" class="step-content">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Informations de paiement :</h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Numéro de téléphone</label>
                        <div class="flex items-center space-x-2">
                            <span class="px-4 py-3 bg-gray-100 border border-gray-300 rounded-lg text-lg font-semibold text-gray-700">+229</span>
                            <input type="tel" id="fedapay-phone" placeholder="Ex: 0161804972 ou 97123456" 
                                   class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent text-lg"
                                   maxlength="10">
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Numéro à 8 chiffres (97...) ou 10 chiffres (01...)</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email (optionnel)</label>
                        <input type="email" id="fedapay-email" placeholder="voteur@example.com" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                        <p class="text-xs text-gray-500 mt-1">Pour recevoir votre reçu</p>
                    </div>

                    <div class="bg-pink-50 border-l-4 border-pink-500 p-4 rounded">
                        <p class="text-sm text-pink-800 font-medium mb-2">💳 Moyens de paiement :</p>
                        <div class="flex items-center space-x-3 mt-2">
                            <img src="{{ asset('images/operators/mtn.png') }}" alt="MTN" class="h-8 object-contain">
                            <img src="{{ asset('images/operators/moov.png') }}" alt="Moov" class="h-8 object-contain">
                        </div>
                        <p class="text-xs text-pink-700 mt-3">
                            ✓ Montant : <span class="font-bold" id="fedapay-amount">100 FCFA</span><br>
                            ✓ Paiement sécurisé Mobile Money
                        </p>
                    </div>

                    <button onclick="processFedaPay('mobile')" id="btn-fedapay" 
                            class="w-full bg-gradient-to-r from-pink-500 to-purple-600 text-white py-3 rounded-lg font-semibold hover:from-pink-600 hover:to-purple-700 transition shadow-lg">
                        📱 Payer par Mobile Money
                    </button>
                </div>
            </div>

            <!-- Step 2: Processing -->
            <div id="step-processing" class="step-content hidden">
                <div class="text-center py-8">
                    <div class="mb-6">
                        <div class="inline-flex items-center justify-center w-20 h-20 bg-pink-100 rounded-full mb-4">
                            <div class="animate-spin rounded-full h-12 w-12 border-b-4 border-pink-600"></div>
                        </div>
                    </div>

                    <h3 class="text-xl font-bold text-gray-800 mb-2">Initialisation du paiement...</h3>
                    <p class="text-gray-600 mb-6">Veuillez patienter</p>

                    <div class="bg-pink-50 rounded-lg p-4">
                        <p class="text-sm text-gray-700">Vous allez être redirigé vers la page de paiement sécurisée FedaPay</p>
                    </div>
                </div>
            </div>

            <!-- Step 3: Error -->
            <div id="step-fedapay-error" class="step-content hidden">
                <div class="text-center py-8">
                    <div class="mb-6">
                        <div class="inline-flex items-center justify-center w-20 h-20 bg-red-100 rounded-full">
                            <svg class="w-12 h-12 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </div>
                    </div>

                    <h3 class="text-2xl font-bold text-red-600 mb-2">❌ Erreur</h3>
                    <p class="text-gray-600 mb-6" id="fedapay-error-message">Une erreur est survenue</p>

                    <button onclick="retryFedaPay()" class="w-full bg-gray-200 text-gray-700 py-3 rounded-lg font-semibold hover:bg-gray-300 transition mb-3">
                        Réessayer
                    </button>
                    <button onclick="closeFedaPay()" class="w-full text-gray-600 hover:text-gray-800 text-sm">
                        Annuler
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let fedapayMissId = null;
let fedapayVoteCount = 1;
let currentCandidateName = null;
let paymentMethod = 'mobile'; // mobile ou card

function initiateFedaPay(missId, voteCount, candidateName = '') {
    fedapayMissId = missId;
    fedapayVoteCount = voteCount;
    currentCandidateName = candidateName;
    
    // Afficher le montant réel (100 FCFA par vote)
    const displayAmount = voteCount * 100;
    document.getElementById('fedapay-amount').textContent = displayAmount + ' FCFA';
    
    // Pré-remplir avec le numéro déjà saisi
    const existingPhone = document.getElementById('phone')?.value || '';
    if (existingPhone) {
        // Nettoyer le numéro (enlever espaces, tirets, etc.)
        const cleanPhone = existingPhone.replace(/[^\d]/g, '');
        // Si le numéro commence par +229, enlever le préfixe
        const phoneToSet = cleanPhone.startsWith('229') ? cleanPhone.substring(3) : cleanPhone;
        document.getElementById('fedapay-phone').value = phoneToSet;
    }
    
    document.getElementById('fedapay-modal').classList.remove('hidden');
    showFedaPayStep('phone-entry');
}

function closeFedaPay() {
    document.getElementById('fedapay-modal').classList.add('hidden');
    resetFedaPay();
}

function showFedaPayStep(step) {
    document.querySelectorAll('.step-content').forEach(el => el.classList.add('hidden'));
    document.getElementById('step-' + step).classList.remove('hidden');
}

function processFedaPay(method) {
    paymentMethod = method || 'mobile';
    
    const phoneNumber = document.getElementById('fedapay-phone').value.trim();
    const email = document.getElementById('fedapay-email').value.trim();
    
    // Accepter 8 chiffres (97123456) ou 10 chiffres (0161804972)
    if (!phoneNumber || (phoneNumber.length !== 8 && phoneNumber.length !== 10) || !/^\d+$/.test(phoneNumber)) {
        alert('Veuillez entrer un numéro de téléphone valide (8 ou 10 chiffres)');
        return;
    }

    // Formater avec l'indicatif pays Bénin
    const fullPhoneNumber = '+229' + phoneNumber;
    
    // Montant: 100 FCFA par vote
    const totalAmount = fedapayVoteCount * 100;
    const btnFedaPay = document.getElementById('btn-fedapay');
    const btnCard = document.getElementById('btn-card');
    
    // Désactiver les boutons
    if (btnFedaPay) btnFedaPay.disabled = true;
    if (btnCard) btnCard.disabled = true;
    
    showFedaPayStep('processing');

    // Appel API FedaPay
    fetch('/api/fedapay/initiate', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
        },
        body: JSON.stringify({
            miss_id: fedapayMissId,
            phone_number: fullPhoneNumber,
            email: email || 'vote@reine-esgis.com',
            amount: totalAmount,
            vote_count: fedapayVoteCount,
            payment_method: paymentMethod
        })
    })
    .then(response => response.json())
    .then(data => {
        if (btnFedaPay) btnFedaPay.disabled = false;
        if (btnCard) btnCard.disabled = false;
        
        if (data.success) {
            // Sauvegarder les informations pour vérification ultérieure
            localStorage.setItem('fedapay_reference', data.reference);
            localStorage.setItem('fedapay_transaction_id', data.transaction_id);
            localStorage.setItem('fedapay_miss_id', fedapayMissId);
            localStorage.setItem('fedapay_vote_count', fedapayVoteCount);
            localStorage.setItem('fedapay_amount', totalAmount);
            
            // Redirection vers FedaPay
            window.location.href = data.payment_url;
        } else {
            showFedaPayError(data.error || 'Erreur lors de l\'initialisation du paiement');
        }
    })
    .catch(error => {
        if (btnFedaPay) btnFedaPay.disabled = false;
        if (btnCard) btnCard.disabled = false;
        console.error('Error:', error);
        showFedaPayError('Erreur de connexion au serveur');
    });
}

function showFedaPayError(message) {
    document.getElementById('fedapay-error-message').textContent = message;
    showFedaPayStep('fedapay-error');
}

function retryFedaPay() {
    resetFedaPay();
    showFedaPayStep('phone-entry');
}

function resetFedaPay() {
    document.getElementById('fedapay-phone').value = '';
    document.getElementById('fedapay-email').value = '';
}

// Vérifier si on revient d'un paiement FedaPay
window.addEventListener('load', function() {
    const reference = localStorage.getItem('fedapay_reference');
    const transactionId = localStorage.getItem('fedapay_transaction_id');
    const missId = localStorage.getItem('fedapay_miss_id');
    
    if (reference && missId) {
        console.log('Vérification du statut de paiement...', { reference, transactionId });
        
        // Vérifier le statut du paiement
        fetch('/api/fedapay/status', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            },
            body: JSON.stringify({ reference: reference })
        })
        .then(response => response.json())
        .then(data => {
            console.log('Statut du paiement:', data);
            
            if (data.status === 'completed') {
                // Nettoyer le localStorage
                localStorage.removeItem('fedapay_reference');
                localStorage.removeItem('fedapay_transaction_id');
                const voteCount = localStorage.getItem('fedapay_vote_count') || '1';
                const candidateName = currentCandidateName || 'la candidate';
                localStorage.removeItem('fedapay_miss_id');
                localStorage.removeItem('fedapay_vote_count');
                localStorage.removeItem('fedapay_amount');
                
                console.log('✅ Paiement confirmé, redirection vers l\'accueil...');
                
                // Redirection directe vers l'accueil avec message de succès
                const successMessage = `Merci ! Votre vote pour ${candidateName} a été enregistré avec succès.`;
                window.location.href = `/?success=${encodeURIComponent(successMessage)}&voted=true&votes=${voteCount}`;
            } else if (data.status === 'pending') {
                console.log('Paiement en attente...');
            } else {
                console.log('Statut du paiement:', data.status);
            }
        })
        .catch(error => {
            console.error('Error checking payment status:', error);
        });
    }
});
</script>

<style>
@keyframes fadeIn {
    from { opacity: 0; transform: scale(0.95); }
    to { opacity: 1; transform: scale(1); }
}

#fedapay-modal .step-content {
    animation: fadeIn 0.3s ease-in;
}
</style>
