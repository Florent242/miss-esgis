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
                    <p class="text-text-gray-600 text-sm">{{ $miss->ville ?? '' }}, {{ $miss->pays }}</p>
                </div>
                <span
                    class="inline-block bg-bg-pink-200 text-text-pink-800 text-xs font-bold px-3 py-1 rounded-full">Vote</span>
            </div>

            <!-- Récapitulatif -->
            <div class="mb-6">
                <h2 class="text-xl font-bold text-text-gray-800 mb-4">Récapitulatif</h2>
                <div class="space-y-2 text-text-gray-700">
                    <div class="flex justify-between">
                        <span>Prix du vote</span>
                        <span>100 FCFA</span>
                    </div>
                    <div>
                        <span>Nombre de votes</span>
                        <input type="number" id="vote-amount" name="amount" min="1" value="1" class="w-24 border rounded px-2 py-1 text-center">
                    </div>  

                    <div class="flex justify-between font-bold text-lg border-t pt-2 mt-2">
                        <span>Total</span>
                        <span id="total-price">100 FCFA</span>
                    </div>
                </div>
            </div>

            <!-- Moyen de paiement -->
            @csrf
            <div class="mt-8">
                <x-buttons.primary-button id="pay-button" type="button" class="w-full">
                    Confirmer le vote
                </x-buttons.primary-button>

            </div>

            <p class="text-center text-xs text-text-gray-500 mt-4">
                En votant, vous acceptez nos conditions d'utilisation.
            </p>
        </div>
    </div>
@endsection
@push('scripts')
<script src="https://cdn.kkiapay.me/k.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const voteAmountInput = document.getElementById('vote-amount');
        const totalPriceSpan = document.getElementById('total-price');
        const pricePerVote = 100;
        let numberOfVotes = 1;

        voteAmountInput.addEventListener('input', function () {
            let amount = parseInt(this.value);
            if (isNaN(amount) || amount < 1) {
                amount = 1;
                this.value = 1;
            }
            numberOfVotes = amount;
            const total = amount * pricePerVote;
            totalPriceSpan.textContent = total + ' FCFA';
        });

        function successHandler(response) {
            const total = numberOfVotes * pricePerVote;
            fetch('{{ route('vote.process', $miss->id) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    montant: total,
                    moyen_paiement: 'kkiapay',
                    email: '',
                    numero_telephone: '',
                    transaction_id: response.transactionId,
                    nombre_de_votes: numberOfVotes
                })
            }).then(res => {
                if (res.ok) {
                    window.location.href = '{{ route("vote.success", $miss->id) }}';
                } else {
                    res.json().then(data => {
                        console.error('Erreur du serveur:', data);
                        alert('Erreur lors de l’enregistrement du vote: ' + (data.error || 'Erreur inconnue'));
                    });
                }
            });
        }

        addKkiapayListener('success', successHandler);

        const payButton = document.getElementById('pay-button');
        payButton.addEventListener('click', function () {
            const totalBrut = numberOfVotes * pricePerVote;
            const totalNet = totalBrut * (1 - 0.02);

            openKkiapayWidget({
                amount: totalNet,
                key: "{{ config('services.kkiapay.public_key') }}",
                sandbox: true,
                phone: "",
                name: "Miss {{ $miss->prenom }}",
                email: "",
            });
        });
    });
</script>

@endpush