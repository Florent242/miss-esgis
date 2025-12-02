@extends('layouts.base')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <a href="{{ route('candidates.show', $miss->id) }}"
            class="inline-flex items-center text-text-gray-600 hover:text-primary-pink mb-6 transition-colors duration-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Retour
        </a>

        <div class="text-center mb-8">
            <h1 class="text-3xl md:text-4xl font-extrabold text-text-gray-900 mb-4">Finaliser votre vote</h1>
            <p class="text-lg text-text-gray-600">Confirmez votre choix et procédez au paiement</p>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6 md:p-8 max-w-2xl mx-auto">
            <!-- Candidate Info Card -->
            <div class="flex items-center space-x-4 p-4 bg-pink-50 rounded-lg mb-6">
                <img src="{{ asset('storage/media/' . $miss->photo_principale) }}" alt="{{ $miss->prenom }} {{ $miss->nom }}"
                    class="w-16 h-16 rounded-full object-cover shadow-sm" />
                <div class="flex-grow">
                    <h3 class="text-xl font-semibold text-text-gray-800">{{ $miss->prenom }} {{ $miss->nom }}</h3>
                    <p class="text-text-gray-600 text-sm">{{ $miss->ville ?? '' }} {{ $miss->pays }}</p>
                </div>
                <span
                    class="inline-block bg-bg-pink-200 text-text-pink-800 text-xs font-bold px-3 py-1 rounded-full">Vote</span>
            </div>

            <!-- Informations de vote -->
            <div class="mb-6">
                <h2 class="text-xl font-bold text-text-gray-800 mb-4">Informations de vote</h2>
                
                <!-- Numéro de téléphone -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Numéro de téléphone</label>
                    <div class="flex items-center space-x-2">
                        <span class="px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-sm font-semibold text-gray-700">+229</span>
                        <input type="tel" id="phone" placeholder="Ex: 97123456 ou 0161804972" 
                               class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent"
                               maxlength="10" required>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Numéro à 8 chiffres (97...) ou 10 chiffres (01...)</p>
                </div>
                
                <!-- Récapitulatif -->
                <h3 class="text-lg font-semibold text-gray-800 mb-3">Récapitulatif</h3>
                <div class="space-y-2 text-text-gray-700">
                    <div class="flex justify-between">
                        <span>Prix du vote</span>
                        <span>100 FCFA</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span>Nombre de votes</span>
                        <div class="flex items-center gap-2">
                            <button type="button" id="decrement-btn" onclick="decrementVotes()" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 px-3 rounded-lg transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                </svg>
                            </button>
                            <input type="number" id="vote-amount" name="amount" min="1" value="1" class="w-20 border border-gray-300 rounded-lg px-3 py-2 text-center font-semibold focus:ring-2 focus:ring-pink-500 focus:border-transparent">
                            <button type="button" id="increment-btn" onclick="incrementVotes()" class="bg-pink-500 hover:bg-pink-600 text-white font-bold py-2 px-3 rounded-lg transition-colors shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="flex justify-between font-bold text-lg border-t pt-2 mt-2">
                        <span>Total à payer</span>
                        <span id="total-price">100 FCFA</span>
                    </div>
                </div>
            </div>

            <!-- Moyen de paiement -->
            @csrf
            <div class="mt-8">
                <x-buttons.primary-button id="pay-button" type="button" class="w-full">
                    Procéder au paiement
                </x-buttons.primary-button>
            </div>

            <p class="text-center text-xs text-text-gray-500 mt-4">
                En votant, vous acceptez nos conditions d'utilisation.
            </p>
        </div>
    </div>

    <!-- Include FedaPay Payment Modal -->
    @include('components.fedapay-modal')
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const voteAmountInput = document.getElementById('vote-amount');
        const totalPriceSpan = document.getElementById('total-price');
        const payButton = document.getElementById('pay-button');
        const pricePerVote = 100;
        let numberOfVotes = 1;

        // Fonctions pour incrémenter/décrémenter
        window.incrementVotes = function() {
            const input = document.getElementById('vote-amount');
            const currentValue = parseInt(input.value) || 1;
            const newValue = Math.min(currentValue + 1, 100); // Maximum 100 votes
            input.value = newValue;
            numberOfVotes = newValue;
            updateTotal();
        }

        window.decrementVotes = function() {
            const input = document.getElementById('vote-amount');
            const currentValue = parseInt(input.value) || 1;
            const newValue = Math.max(currentValue - 1, 1); // Minimum 1 vote
            input.value = newValue;
            numberOfVotes = newValue;
            updateTotal();
        }

        function updateTotal() {
            const total = numberOfVotes * pricePerVote;
            totalPriceSpan.textContent = total + ' FCFA';
            payButton.innerHTML = 'Procéder au paiement (' + total + ' FCFA)';
            
            // Mettre à jour l'état des boutons
            const decrementBtn = document.getElementById('decrement-btn');
            const incrementBtn = document.getElementById('increment-btn');
            
            // Le bouton - devient grisé si on est à 1
            if (numberOfVotes <= 1) {
                decrementBtn.classList.add('opacity-50', 'cursor-not-allowed');
                decrementBtn.classList.remove('hover:bg-gray-300');
            } else {
                decrementBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                decrementBtn.classList.add('hover:bg-gray-300');
            }
            
            // Le bouton + devient grisé si on est à 100
            if (numberOfVotes >= 100) {
                incrementBtn.classList.add('opacity-50', 'cursor-not-allowed');
                incrementBtn.classList.remove('hover:bg-pink-600');
            } else {
                incrementBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                incrementBtn.classList.add('hover:bg-pink-600');
            }
        }

        voteAmountInput.addEventListener('input', function () {
            let amount = parseInt(this.value);
            if (isNaN(amount) || amount < 1) {
                amount = 1;
                this.value = 1;
            } else if (amount > 100) {
                amount = 100;
                this.value = 100;
            }
            numberOfVotes = amount;
            updateTotal();
        });

        payButton.addEventListener('click', function () {
            const phoneInput = document.getElementById('phone');
            const phoneValue = phoneInput.value.trim();
            
            // Vérifier le numéro de téléphone
            if (!phoneValue || (phoneValue.length !== 8 && phoneValue.length !== 10) || !/^\d+$/.test(phoneValue)) {
                alert('Veuillez entrer un numéro de téléphone valide (8 ou 10 chiffres)');
                phoneInput.focus();
                return;
            }
            
            // Passer le nom de la candidate pour le message de succès
            const candidateName = '{{ $miss->prenom }} {{ $miss->nom }}';
            initiateFedaPay({{ $miss->id }}, numberOfVotes, candidateName);
        });
    });
</script>
@endpush

<!-- Success Modal -->
<div id="success-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full transform transition-all">
        <div class="p-8 text-center">
            <!-- Success Icon -->
            <div class="mb-6">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-green-100 rounded-full">
                    <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
            </div>

            <!-- Success Message -->
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-3">🎉 Vote enregistré !</h2>
            <p class="text-gray-600 mb-2">Votre paiement a été confirmé avec succès.</p>
            <p class="text-lg font-semibold text-pink-600 mb-6" id="success-vote-count">X vote(s) pour {{ $miss->prenom }} {{ $miss->nom }}</p>

            <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                <p class="text-sm text-green-800">
                    <strong>✓ Transaction complétée</strong><br>
                    Merci pour votre soutien !
                </p>
            </div>

            <!-- Redirect Message -->
            <p class="text-sm text-gray-500 mb-4">Redirection vers l'accueil dans <span id="countdown">3</span> secondes...</p>

            <!-- Buttons -->
            <div class="flex flex-col sm:flex-row gap-3">
                <button onclick="window.location.href='/'" class="flex-1 bg-gradient-to-r from-pink-500 to-purple-600 text-white py-3 rounded-lg font-semibold hover:from-pink-600 hover:to-purple-700 transition">
                    Retour à l'accueil
                </button>
                <button onclick="window.location.href='{{ route('candidates.show', $miss->id) }}'" class="flex-1 bg-gray-200 text-gray-700 py-3 rounded-lg font-semibold hover:bg-gray-300 transition">
                    Voir le profil
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function showSuccessModal(voteCount) {
    const modal = document.getElementById('success-modal');
    const voteCountSpan = document.getElementById('success-vote-count');
    const countdownSpan = document.getElementById('countdown');
    
    if (voteCountSpan) {
        voteCountSpan.textContent = `${voteCount} vote(s) pour {{ $miss->prenom }} {{ $miss->nom }}`;
    }
    
    modal.classList.remove('hidden');
    
    // Countdown
    let seconds = 3;
    const interval = setInterval(() => {
        seconds--;
        if (countdownSpan) {
            countdownSpan.textContent = seconds;
        }
        if (seconds <= 0) {
            clearInterval(interval);
            window.location.href = '/';
        }
    }, 1000);
}

// Vérifier si on vient d'un paiement réussi
@if(session('success'))
    setTimeout(() => {
        const message = "{{ session('success') }}";
        const voteMatch = message.match(/(\d+) vote\(s\)/);
        const voteCount = voteMatch ? voteMatch[1] : '1';
        showSuccessModal(voteCount);
    }, 500);
@endif
</script>
