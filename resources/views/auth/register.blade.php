@extends("templates.app")
@section('content')
    <div class="h-full w-full flex justify-around items-center">
        <div
            class="p-10 glass-morph h-auto w-full max-w-md rounded-2xl shadow-2xl border border-white/20 bg-white/90 backdrop-blur-lg mx-4 flex flex-col justify-center">
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-800">Inscription</h1>
                <p class="text-sm text-gray-500 mt-1">Créez votre compte</p>
            </div>
            <form action="{{ route('register') }}" method="POST" class="space-y-6" autocomplete="off">
                @csrf
                <div>
                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fa-solid fa-user mr-2 text-blue"></i>
                        Nom
                    </label>
                    <input type="text" id="name" name="name" required placeholder="Votre nom" value="{{ old('name') }}"
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl bg-gray-50 backdrop-blur-sm focus:outline-none focus:ring-2 focus:ring-darkblue/50 focus:border-darkblue focus:bg-white transition-all duration-200">
                </div>
                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fa-solid fa-envelope mr-2 text-blue"></i>
                        Email
                    </label>
                    <input type="email" id="email" name="email" required placeholder="Votre email"
                        value="{{ old('email') }}"
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl bg-gray-50 backdrop-blur-sm focus:outline-none focus:ring-2 focus:ring-darkblue/50 focus:border-darkblue focus:bg-white transition-all duration-200">
                </div>
                <div>
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fa-solid fa-lock mr-2 text-blue"></i>
                        Mot de passe
                    </label>
                    <input type="password" id="password" name="password" required placeholder="Mot de passe"
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl bg-gray-50 backdrop-blur-sm focus:outline-none focus:ring-2 focus:ring-darkblue/50 focus:border-darkblue focus:bg-white transition-all duration-200">
                </div>
                <div>
                    <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fa-solid fa-lock mr-2 text-blue"></i>
                        Confirmation
                    </label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required
                        placeholder="Confirmez le mot de passe"
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl bg-gray-50 backdrop-blur-sm focus:outline-none focus:ring-2 focus:ring-darkblue/50 focus:border-darkblue focus:bg-white transition-all duration-200">
                </div>
                @if ($errors->any())
                    <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 text-sm">
                        <ul class="list-disc pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div class="flex items-center justify-between gap-3">
                    <div class="text-center">
                        <p class="text-sm text-gray-500">
                            Déjà un compte ? <a href="{{ route('login') }}"
                                class="text-blue hover:underline">Connectez-vous</a>
                        </p>
                    </div>
                    <button type="submit"
                        class="px-6 py-3 bg-blue text-white rounded-xl hover:bg-darkblue transition-all duration-200">
                        S'inscrire
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection