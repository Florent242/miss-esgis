@extends('layouts.base')

@section('title', 'Connexion - Reine ESGIS')

@section('content')
<div class="max-w-md mx-auto px-4 py-6">
    <div class="text-center mb-8">
        <h1 class="text-3xl font-bold gradient-text mb-4">Connexion</h1>
        <p class="text-gray-600">Accédez à votre espace candidate</p>
    </div>

    @if(session('error'))
                <div id="showtoast" class="alert alert-danger">{{ session('error') }}</div>
                  <script>

                setTimeout(()=>
            {
               if(document.getElementById('showtoast'))
                {
                    document.getElementById('showtoast').style.opacity='0'
                    setTimeout(()=>
                {
                    document.getElementById('showtoast')?.remove()
                },500)
                }
            },3000)
            </script>
        @endif


    <div class="bg-white rounded-xl shadow-sm p-6">
        <form action="" method="POST">
            @csrf

            @include('components.inputs.text-input', [
                'label' => 'Adresse email',
                'required' => true,
                'name' => 'email',
                'type' => 'email',
                'placeholder' => 'exemple@mail.com',
                'value' => old('email'),
                'error' => $errors->first('email')
            ])

            @include('components.inputs.text', [
                'label' => 'Mot de passe',
                'required' => true,
                'name' => 'password',
                'type' => 'password',
                'placeholder' => 'Votre mot de passe',
                'error' => $errors->first('password')
            ])

            <x-buttons.primary-button type="submit" class="w-full mt-4">
                Se connecter
            </x-buttons.primary-button>
        </form>

        <div class="mt-6 text-center">
            <p class="text-gray-600">
                Pas encore inscrite ?
                <a href="{{ route('register') }}" class="text-miss-pink font-medium hover:underline">
                    Devenir candidate
                </a>
            </p>
        </div>
    </div>
</div>
@endsection
