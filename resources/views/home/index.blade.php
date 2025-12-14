@extends("templates.home")
@section('content')
    @include("partials.navbar")

    <div class="w-full h-[90vh] flex justify-center m-4 md:m-0">
        <div class="flex flex-col w-full md:w-full ml-auto">
            <div class="w-full mt-[1vh] flex flex-col justify-end text-right">
                <h1 class="text-4xl md:text-5xl font-bold text-basic entrance-text" aria-label="Squish">Squish</h1>
                <p class="text-basic font-light entrance-desc">Votre galerie photo en ligne pour partager et découvrir de superbes images.</p>
                <div class="mt-12 flex flex-wrap gap-4 justify-end">
                    @auth
                        <a class="backdrop-blur-md bg-white/10 border border-white/20 text-basic py-3 px-6 rounded-full transition-transform duration-700 hover:scale-105" href="/albums">Créer un album</a>
                        <a class="py-3 px-6 bg-basic text-zinc-900 rounded-full" href="/albums">Voir les albums</a>
                    @else
                        <a class="backdrop-blur-md bg-white/10 border border-white/20 text-basic py-3 px-6 rounded-full transition-transform duration-700 hover:scale-105" href="/register">Inscription</a>
                        <a class="py-3 px-6 bg-basic text-zinc-900 rounded-full" href="/login">Se connecter</a>
                    @endauth
                </div>
            </div>

            <div class="fixed bottom-[5vh] w-full mt-10 overflow-visible">
                <div id="carousel-track"
                    class="flex gap-5 w-full py-5 pr-0 overflow-x-scroll overflow-y-visible scroll-smooth scroll-snap-x snap-mandatory hide-scrollbar scroll-px-10 md:scroll-px-[40vw]">
                    @foreach([
                        [
                            'img' => 'carousel-1.png',
                            'title' => 'Avengers: Infinity War',
                            'desc' => 'Iron Man et les Avengers affrontent Thanos dans une bataille cosmique.'
                        ],
                        [
                            'img' => 'carousel-2.png',
                            'title' => 'Parasite',
                            'desc' => 'Deux familles que tout oppose : une famille riche vivant dans le luxe...'
                        ],
                        [
                            'img' => 'carousel-3.png',
                            'title' => 'Film Épique 3',
                            'desc' => "Description courte du film pour l'exemple."
                        ]
                    ] as $i => $card)
                        <div class="flex-shrink-0 w-[350px] md:w-[60vw] h-[150px] md:h-[250px] rounded-2xl relative overflow-hidden shadow-lg snap-center transition-transform duration-300 glass-morph {{ $loop->last ? 'mr-[60vw]' : '' }}"
                            data-card-index="{{ $i }}">
                            <img src="{{ asset('assets/carousel/' . $card['img']) }}"
                                class="absolute inset-0 w-full h-full object-cover z-0" alt="{{ $card['title'] }}" style="perspective:1000px;">
                            <div class="absolute inset-0 bg-gradient-to-t from-darkblue/50 via-transparent to-transparent z-10"></div>
                            <div class="relative z-20 h-full flex flex-col justify-end p-6">
                                <div class="flex justify-end items-end w-full">
                                    <a href="/albums" class="backdrop-blur-md bg-white/10 border border-white/20 text-white py-2 px-3 rounded-full flex items-center gap-2 text-sm hover:bg-white/30 transition">&rarr;</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const carouselTrack = document.getElementById('carousel-track');
            const cards = carouselTrack.querySelectorAll('[data-card-index]');
            const indicatorsContainer = document.getElementById('indicators');

            cards.forEach((_, index) => {
                const dot = document.createElement('div');
                dot.classList.add('h-2', 'w-2', 'rounded-full', 'bg-white/30', 'cursor-pointer', 'transition-all', 'duration-300');
                if (index === 0) dot.classList.add('bg-white', 'w-10');

                dot.addEventListener('click', () => {
                    scrollToCard(index);
                });

                indicatorsContainer.appendChild(dot);
            });

            const dots = indicatorsContainer.querySelectorAll('div');

            function scrollToCard(index) {
                cards[index].scrollIntoView({ behavior: 'smooth', inline: 'center' });
            }

            const observerOptions = {
                root: carouselTrack,
                threshold: 0.7
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    const cardIndex = parseInt(entry.target.dataset.cardIndex);

                    if (entry.isIntersecting) {
                        dots.forEach(d => {
                            d.classList.remove('bg-white', 'w-10');
                            d.classList.add('bg-white/30', 'w-2');
                        });
                        if (dots[cardIndex]) {
                            dots[cardIndex].classList.remove('bg-white/30', 'w-2');
                            dots[cardIndex].classList.add('bg-white', 'w-10');
                        }
                    }
                });
            }, observerOptions);

            cards.forEach(card => observer.observe(card));

            if (cards.length > 0) {
                cards[0].scrollIntoView({ behavior: 'auto', inline: 'center' });
            }
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => {
                const el = document.getElementById('SQUISH1');
                if (el && typeof animateTo === 'function') {
                    animateTo(el, 0, -200, {
                        rotate: 5
                    });
                }
            }, 1500);

            setTimeout(() => {
                const el = document.getElementById('SQUISH2');
                if (el && typeof animateTo === 'function') {
                    animateTo(el, 90, -10, {
                        rotate: -10
                    });
                }
            }, 2000);
        });
    </script>
@endsection