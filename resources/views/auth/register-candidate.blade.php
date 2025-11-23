@extends('layouts.base')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="text-center mb-8">
        <h1 class="text-4xl md:text-5xl font-extrabold text-text-gray-900 mb-4">Devenir candidate</h1>
        <p class="text-lg text-text-gray-600">Rejoignez le concours Reine ESGIS {{ date('Y') }} </p>
    </div>

    <div class="bg-white rounded-xl shadow-lg p-6 md:p-8 max-w-2xl mx-auto">
        <h2 class="text-2xl font-bold text-text-gray-800 mb-6">Formulaire d'inscription</h2>

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        <form action="{{ route('candidates.store') }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @csrf

            <div>
                <label for="prenom" class="block text-sm font-medium text-text-gray-700">Prénom *</label>
                <x-inputs.text-input id="prenom" name="prenom" type="text" class="mt-1 block w-full" placeholder="Votre prénom" required autofocus value="{{ old('prenom') }}" />
                @error('prenom')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="nom" class="block text-sm font-medium text-text-gray-700">Nom *</label>
                <x-inputs.text-input id="nom" name="nom" type="text" class="mt-1 block w-full" placeholder="Votre nom" required value="{{ old('nom') }}" />
                @error('nom')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="age" class="block text-sm font-medium text-text-gray-700">Âge *</label>
                <x-inputs.text-input id="age" name="age" type="number" class="mt-1 block w-full" placeholder="Votre âge" required value="{{ old('age') }}" />
                @error('age')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="ville" class="block text-sm font-medium text-text-gray-700">Ville *</label>
                <x-inputs.text-input id="ville" name="ville" type="text" class="mt-1 block w-full" placeholder="Votre ville" required value="{{ old('ville') }}" />
                @error('ville')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="pays" class="block text-sm font-medium text-text-gray-700">Pays *</label>
                <x-inputs.text-input id="pays" name="pays" type="text" class="mt-1 block w-full" placeholder="Votre pays" required value="{{ old('pays') }}" />
                @error('pays')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="telephone" class="block text-sm font-medium text-text-gray-700">Téléphone</label>
                <x-inputs.text-input id="telephone" name="telephone" type="tel" class="mt-1 block w-full" placeholder="+229 61 XX XX XX XX" value="{{ old('telephone') }}" />
                @error('telephone')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="md:col-span-2">
                <label for="email" class="block text-sm font-medium text-text-gray-700">Email *</label>
                <x-inputs.text-input id="email" name="email" type="email" class="mt-1 block w-full" placeholder="votre@email.com" required value="{{ old('email') }}" />
                @error('email')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-text-gray-700">Mot de passe *</label>
                <div class="relative mt-1">
                    <x-inputs.text-input id="password" name="password" type="password" class="block w-full pr-10" placeholder="Votre mot de passe" required value="{{ old('password') }}" />
                    <button type="button" onclick="togglePassword('password')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-600 hover:text-gray-800">
                        <svg id="password-eye-open" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <svg id="password-eye-closed" class="h-5 w-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                        </svg>
                    </button>
                </div>
                @error('password')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-text-gray-700">Confirmation mot de passe *</label>
                <div class="relative mt-1">
                    <x-inputs.text-input id="password_confirmation" name="password_confirmation" type="password" class="block w-full pr-10" placeholder="Confirmer votre mot de passe" required value="{{ old('password_confirmation') }}" />
                    <button type="button" onclick="togglePassword('password_confirmation')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-600 hover:text-gray-800">
                        <svg id="password_confirmation-eye-open" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <svg id="password_confirmation-eye-closed" class="h-5 w-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                        </svg>
                    </button>
                </div>
                @error('password_confirmation')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>


            <div class="md:col-span-2">
                <label for="photo_principale" class="block text-sm font-medium text-text-gray-700">Photo principale *</label>
                <div class="mt-1 flex flex-col sm:flex-row sm:items-center gap-2">
                    <label class="cursor-pointer bg-primary-pink hover:bg-pink-600 text-white font-bold py-2 px-4 rounded-md shadow-sm transition duration-150 ease-in-out shrink-0">
                        Choisir un fichier
                        <input id="photo_principale" name="photo_principale" type="file" class="sr-only" accept="image/jpeg,image/png,image/jpg" onchange="document.getElementById('file-name').innerText = this.files[0] ? this.files[0].name : 'Aucun fichier choisi'" required />
                    </label>
                    <span id="file-name" class="text-text-gray-500 text-sm truncate overflow-hidden max-w-full">Aucun fichier choisi</span>
                </div>
                <p class="text-xs text-text-gray-500 mt-1">Format accepté : JPG, PNG (max 5MB)</p>
                @error('photo_principale')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="md:col-span-2">
                <label for="bio" class="block text-sm font-medium text-text-gray-700">Présentation courte</label>
                <textarea id="bio" name="bio" rows="4" class="mt-1 block w-full border-gray-300 focus:border-primary-pink focus:ring-primary-pink rounded-md shadow-sm" placeholder="Parlez-nous de vous, vos passions, vos projets..." maxlength="500">{{ old('bio') }}</textarea>
                <p class="text-xs text-text-gray-500 mt-1 text-right"><span id="char-count">0</span>/500 caractères</p>
                @error('bio')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="md:col-span-2 mt-4">
                <x-buttons.primary-button class="w-full">
                    Envoyer ma candidature
                </x-buttons.primary-button>
            </div>

            <div class="md:col-span-2 text-center text-xs text-text-gray-500 mt-4">
                En soumettant ce formulaire, vous acceptez nos conditions d'utilisation et notre politique de confidentialité.
            </div>
        </form>
    </div>
</div>

<script>
    // Toggle password visibility
    function togglePassword(fieldId) {
        const input = document.getElementById(fieldId);
        const eyeOpen = document.getElementById(fieldId + '-eye-open');
        const eyeClosed = document.getElementById(fieldId + '-eye-closed');
        
        if (input.type === 'password') {
            input.type = 'text';
            eyeOpen.classList.add('hidden');
            eyeClosed.classList.remove('hidden');
        } else {
            input.type = 'password';
            eyeOpen.classList.remove('hidden');
            eyeClosed.classList.add('hidden');
        }
    }

    // Image preview
    document.getElementById('photo_principale').addEventListener('change', function(e) {
    if (this.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            // Supprimer l'ancien aperçu s'il existe
            const oldPreview = document.getElementById('image-preview');
            if (oldPreview) {
                oldPreview.remove();
            }

            const preview = document.createElement('img');
            preview.id = 'image-preview';
            preview.src = e.target.result;
            preview.classList.add('mt-2', 'rounded-lg', 'shadow', 'w-full', 'sm:w-auto', 'object-cover');
            preview.style.maxWidth = '200px';
            preview.style.maxHeight = '200px';
            document.getElementById('file-name').parentElement.after(preview);
        }
        reader.readAsDataURL(this.files[0]);
    }
});

</script>
@endsection
