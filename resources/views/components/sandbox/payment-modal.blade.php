<!-- Sandbox Payment Modal -->
<div id="sandbox-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full max-h-[90vh] overflow-y-auto relative">
        <!-- Close Button -->
        <button onclick="closeSandbox()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>

        <!-- Header -->
        <div class="bg-gradient-to-r from-pink-500 to-purple-600 p-6 rounded-t-2xl">
            <h2 class="text-2xl font-bold text-white text-center">Paiement Mobile Money</h2>
            <p class="text-white text-center text-sm mt-2">Choisissez votre opérateur</p>
        </div>

        <!-- Content -->
        <div class="p-6">
            <!-- Step 1: Operator Selection -->
            <div id="step-operator" class="step-content">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Sélectionnez votre opérateur :</h3>
                
                <div class="space-y-3">
                    <!-- MTN -->
                    <button onclick="selectOperator('mtn')" class="operator-btn w-full flex items-center justify-between p-4 border-2 border-gray-200 rounded-xl hover:border-yellow-400 hover:bg-yellow-50 transition">
                        <div class="flex items-center space-x-4">
                            <img src="{{ asset('images/operators/mtn.png') }}" alt="MTN" class="w-14 h-14 object-contain">
                            <div class="text-left">
                                <p class="font-bold text-gray-800">MTN Mobile Money</p>
                            </div>
                        </div>
                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>

                    <!-- Moov -->
                    <button onclick="selectOperator('moov')" class="operator-btn w-full flex items-center justify-between p-4 border-2 border-gray-200 rounded-xl hover:border-blue-400 hover:bg-blue-50 transition">
                        <div class="flex items-center space-x-4">
                            <img src="{{ asset('images/operators/moov.png') }}" alt="Moov" class="w-14 h-14 object-contain">
                            <div class="text-left">
                                <p class="font-bold text-gray-800">Moov Money</p>
                            </div>
                        </div>
                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>

                    <!-- Celtiis -->
                    <button onclick="selectOperator('celtiis')" class="operator-btn w-full flex items-center justify-between p-4 border-2 border-gray-200 rounded-xl hover:border-orange-400 hover:bg-orange-50 transition">
                        <div class="flex items-center space-x-4">
                            <img src="{{ asset('images/operators/celtiis.png') }}" alt="Celtiis" class="w-14 h-14 object-contain">
                            <div class="text-left">
                                <p class="font-bold text-gray-800">Celtiis Cash</p>
                            </div>
                        </div>
                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Step 2: Phone Number Entry -->
            <div id="step-phone" class="step-content hidden">
                <button onclick="backToOperators()" class="text-gray-600 hover:text-gray-800 mb-4 flex items-center text-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Retour
                </button>

                <div id="selected-operator-info" class="bg-gray-50 rounded-xl p-4 mb-6"></div>

                <h3 class="text-lg font-semibold text-gray-800 mb-4">Entrez votre numéro :</h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Numéro de téléphone</label>
                        <input type="tel" id="phone-number" placeholder="Ex: 91234567" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent text-lg">
                        <p class="text-xs text-gray-500 mt-1">Format: 8 ou 10 chiffres</p>
                    </div>

                    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
                        <p class="text-sm text-blue-800 font-medium mb-2">📱 Vous allez recevoir :</p>
                        <ul class="text-xs text-blue-700 space-y-1">
                            <li>✓ Un pop-up de confirmation sur votre téléphone</li>
                            <li>✓ Montant : <span class="font-bold" id="payment-amount">100 FCFA</span></li>
                            <li>✓ Validez avec votre code PIN pour confirmer</li>
                        </ul>
                    </div>

                    <button onclick="initiatePayment()" id="btn-initiate" class="w-full bg-gradient-to-r from-pink-500 to-purple-600 text-white py-3 rounded-lg font-semibold hover:from-pink-600 hover:to-purple-700 transition">
                        Lancer le paiement
                    </button>
                </div>
            </div>

            <!-- Step 3: Waiting for Payment -->
            <div id="step-waiting" class="step-content hidden">
                <div class="text-center py-8">
                    <div class="mb-6">
                        <div class="inline-flex items-center justify-center w-20 h-20 bg-yellow-100 rounded-full mb-4">
                            <div class="animate-spin rounded-full h-12 w-12 border-b-4 border-yellow-600"></div>
                        </div>
                    </div>

                    <h3 class="text-xl font-bold text-gray-800 mb-2">En attente de confirmation...</h3>
                    <p class="text-gray-600 mb-6">Vérifiez votre téléphone et validez le paiement</p>

                    <div class="bg-gradient-to-r from-yellow-50 to-orange-50 border border-yellow-200 rounded-xl p-6 mb-6">
                        <p class="text-sm text-gray-700 mb-3 font-medium">📱 Un pop-up apparaît sur votre téléphone</p>
                        <div class="bg-white rounded-lg p-4 mb-3">
                            <p class="text-xs text-gray-500 mb-1">Montant à confirmer</p>
                            <p class="text-2xl font-bold text-pink-600" id="display-amount">100 FCFA</p>
                        </div>
                        <div class="bg-yellow-100 rounded-lg p-3">
                            <p class="text-xs text-yellow-800 font-medium">⏱️ Validez avec votre code PIN dans les 5 minutes</p>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <div class="flex items-center justify-center space-x-2 text-sm text-gray-600">
                            <svg class="w-5 h-5 text-yellow-500 animate-pulse" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                            </svg>
                            <span>Vérification automatique en cours...</span>
                        </div>
                        <p class="text-xs text-gray-500">Référence : <span id="payment-reference" class="font-mono">---</span></p>
                        <p class="text-xs text-gray-500">Expire dans : <span id="time-remaining" class="font-semibold">5:00</span></p>
                    </div>

                    <button onclick="cancelPayment()" class="mt-6 text-red-500 hover:text-red-700 text-sm font-medium">
                        Annuler le paiement
                    </button>
                </div>
            </div>

            <!-- Step 4: Success -->
            <div id="step-success" class="step-content hidden">
                <div class="text-center py-8">
                    <div class="mb-6">
                        <div class="inline-flex items-center justify-center w-20 h-20 bg-green-100 rounded-full">
                            <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                    </div>

                    <h3 class="text-2xl font-bold text-green-600 mb-2">✅ Paiement confirmé !</h3>
                    <p class="text-gray-600 mb-4">Votre vote a été enregistré avec succès</p>

                    <div class="bg-green-50 rounded-lg p-4 mb-6">
                        <p class="text-sm text-gray-700">
                            <span class="font-bold" id="success-vote-count">1</span> vote(s) pour 
                            <span class="font-bold" id="success-candidate">{{ $miss->prenom ?? '' }}</span>
                        </p>
                    </div>

                    <button onclick="redirectToSuccess()" class="w-full bg-gradient-to-r from-green-500 to-teal-500 text-white py-3 rounded-lg font-semibold hover:from-green-600 hover:to-teal-600 transition">
                        Terminer
                    </button>
                </div>
            </div>

            <!-- Step 5: Error -->
            <div id="step-error" class="step-content hidden">
                <div class="text-center py-8">
                    <div class="mb-6">
                        <div class="inline-flex items-center justify-center w-20 h-20 bg-red-100 rounded-full">
                            <svg class="w-12 h-12 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </div>
                    </div>

                    <h3 class="text-2xl font-bold text-red-600 mb-2">❌ Paiement échoué</h3>
                    <p class="text-gray-600 mb-6" id="error-message">Une erreur est survenue</p>

                    <button onclick="retryPayment()" class="w-full bg-gray-200 text-gray-700 py-3 rounded-lg font-semibold hover:bg-gray-300 transition mb-3">
                        Réessayer
                    </button>
                    <button onclick="closeSandbox()" class="w-full text-gray-600 hover:text-gray-800 text-sm">
                        Annuler
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let selectedOperator = null;
let selectedMissId = null;
let voteAmount = 1;
let paymentReference = null;
let statusCheckInterval = null;
let countdownInterval = null;
let expiresAt = null;

const operatorConfig = {
    mtn: {
        name: 'MTN Mobile Money',
        color: 'yellow',
        ussd: '*155#'
    },
    moov: {
        name: 'Moov Money (Flooz)',
        color: 'blue',
        ussd: '*155#'
    },
    celtiis: {
        name: 'Celtiis Cash',
        color: 'orange',
        ussd: '*124#'
    }
};

function openSandbox(missId, amount) {
    selectedMissId = missId;
    voteAmount = amount;
    document.getElementById('sandbox-modal').classList.remove('hidden');
    showStep('operator');
}

function closeSandbox() {
    document.getElementById('sandbox-modal').classList.add('hidden');
    clearIntervals();
    resetSandbox();
}

function showStep(step) {
    document.querySelectorAll('.step-content').forEach(el => el.classList.add('hidden'));
    document.getElementById('step-' + step).classList.remove('hidden');
}

function selectOperator(operator) {
    selectedOperator = operator;
    const config = operatorConfig[operator];
    
    document.getElementById('selected-operator-info').innerHTML = `
        <div class="flex items-center space-x-3">
            <img src="/images/operators/${operator}.png" alt="${config.name}" class="w-12 h-12 object-contain">
            <div>
                <p class="font-bold text-gray-800">${config.name}</p>
                <p class="text-xs text-gray-500">Code USSD : ${config.ussd}</p>
            </div>
        </div>
    `;
    
    const amountDisplay = document.getElementById('payment-amount');
    if (amountDisplay) {
        amountDisplay.textContent = (voteAmount * 100) + ' FCFA';
    }
    
    showStep('phone');
}

function backToOperators() {
    selectedOperator = null;
    showStep('operator');
}

function initiatePayment() {
    const phoneNumber = document.getElementById('phone-number').value.trim();
    
    if (!phoneNumber || phoneNumber.length < 8) {
        alert('Veuillez entrer un numéro de téléphone valide');
        return;
    }

    const totalAmount = voteAmount * 100;
    const btnInitiate = document.getElementById('btn-initiate');
    
    // Désactiver le bouton pendant le traitement
    btnInitiate.disabled = true;
    btnInitiate.textContent = 'Initialisation...';

    // Appel API pour déclencher le débit direct
    fetch('/api/sandbox/initiate', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            miss_id: selectedMissId,
            operator: selectedOperator,
            phone_number: phoneNumber,
            amount: totalAmount,
            vote_count: voteAmount
        })
    })
    .then(response => response.json())
    .then(data => {
        btnInitiate.disabled = false;
        btnInitiate.textContent = 'Lancer le paiement';
        
        if (data.success) {
            paymentReference = data.reference;
            expiresAt = new Date(Date.now() + 5 * 60 * 1000); // 5 minutes
            
            document.getElementById('payment-reference').textContent = data.reference;
            document.getElementById('display-amount').textContent = data.amount + ' FCFA';
            
            // Afficher l'écran d'attente
            showStep('waiting');
            
            // Message pour l'utilisateur
            showNotification('📱 Vérifiez votre téléphone ! Un pop-up de confirmation devrait apparaître.', 'info');
            
            // Démarrer la vérification automatique
            startStatusCheck();
            startCountdown();
        } else {
            showError(data.error || 'Erreur lors de l\'initialisation du paiement');
        }
    })
    .catch(error => {
        btnInitiate.disabled = false;
        btnInitiate.textContent = 'Lancer le paiement';
        console.error('Error:', error);
        showError('Erreur de connexion au serveur');
    });
}

function showNotification(message, type = 'info') {
    // Simple notification en haut de la modal
    const notification = document.createElement('div');
    notification.className = `fixed top-4 left-1/2 transform -translate-x-1/2 z-50 px-6 py-3 rounded-lg shadow-lg ${
        type === 'info' ? 'bg-blue-500' : 'bg-red-500'
    } text-white font-medium`;
    notification.textContent = message;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 5000);
}

function startStatusCheck() {
    statusCheckInterval = setInterval(() => {
        fetch('/api/sandbox/status', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ reference: paymentReference })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'confirmed') {
                clearIntervals();
                document.getElementById('success-vote-count').textContent = voteAmount;
                showStep('success');
            } else if (data.status === 'expired' || data.status === 'failed') {
                clearIntervals();
                showError('Le paiement a expiré. Veuillez réessayer.');
            }
        });
    }, 3000); // Vérifier toutes les 3 secondes
}

function startCountdown() {
    updateCountdown();
    countdownInterval = setInterval(updateCountdown, 1000);
}

function updateCountdown() {
    if (!expiresAt) return;
    
    const now = new Date();
    const diff = expiresAt - now;
    
    if (diff <= 0) {
        clearIntervals();
        showError('Le délai de paiement est expiré');
        return;
    }
    
    const minutes = Math.floor(diff / 60000);
    const seconds = Math.floor((diff % 60000) / 1000);
    
    document.getElementById('time-remaining').textContent = 
        `${minutes}:${seconds.toString().padStart(2, '0')}`;
}

function clearIntervals() {
    if (statusCheckInterval) {
        clearInterval(statusCheckInterval);
        statusCheckInterval = null;
    }
    if (countdownInterval) {
        clearInterval(countdownInterval);
        countdownInterval = null;
    }
}

function cancelPayment() {
    clearIntervals();
    closeSandbox();
}

function retryPayment() {
    resetSandbox();
    showStep('operator');
}

function resetSandbox() {
    selectedOperator = null;
    paymentReference = null;
    expiresAt = null;
    document.getElementById('phone-number').value = '';
}

function showError(message) {
    document.getElementById('error-message').textContent = message;
    showStep('error');
}

function redirectToSuccess() {
    window.location.href = '/vote/' + selectedMissId + '/success';
}
</script>

<style>
.operator-btn:hover {
    transform: translateX(4px);
}

@keyframes pulse-border {
    0%, 100% {
        border-color: rgba(236, 72, 153, 0.5);
    }
    50% {
        border-color: rgba(236, 72, 153, 1);
    }
}

#step-waiting {
    animation: fadeIn 0.3s ease-in;
}

@keyframes fadeIn {
    from { opacity: 0; transform: scale(0.95); }
    to { opacity: 1; transform: scale(1); }
}
</style>
