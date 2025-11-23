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
                <x-inputs.text-input id="password" name="password" type="password" class="mt-1 block w-full" placeholder="Votre mot de passe" required value="{{ old('password') }}" />
                @error('password')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-text-gray-700">Confirmation mot de passe *</label>
                <x-inputs.text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" placeholder="Confirmer votre mot de passe" required value="{{ old('password_confirmation') }}" />
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
