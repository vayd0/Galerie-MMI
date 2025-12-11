@extends("templates.app")
@section('content')
  <div class="h-full w-full flex justify-around items-center">
    <div
      class="p-8 glass-morph h-[60%] w-full max-w-md rounded-2xl shadow-2xl border border-white/20 bg-white/90 backdrop-blur-lg mx-4 flex flex-col justify-center">
      <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Connexion</h1>
        <p class="text-sm text-gray-500 mt-1">Connectez-vous à votre compte</p>
      </div>
      <form action="{{ route('login') }}" method="POST" class="space-y-6" autocomplete="off">
        @csrf
        <div class="relative">
          <div id="email-svg"
            class="pointer-events-none absolute right-[-40%] rotate-[10deg] top-[20%] w-full flex justify-center opacity-0 translate-y-4 transition-all duration-300 z-0">
            <svg class="h-[50px]" id="Calque_1" data-name="Calque 1" xmlns="http://www.w3.org/2000/svg"
              viewBox="0 0 1200 1200">
              <defs>
                <style>
                  .color-primary {
                    fill: #13678a;
                  }

                  .color-white {
                    fill: #fff;
                  }

                  .color-secondary {
                    fill: #45c4b0;
                  }

                  .color-accent {
                    fill: #dafdba;
                  }

                  .color-stroke {
                    fill: none;
                    stroke: #0d0d0d;
                    stroke-width: 17px;
                  }

                  .color-stroke,
                  .color-black {
                    stroke-miterlimit: 10;
                  }

                  .color-black {
                    fill: #1d1d1b;
                    stroke: #1d1d1b;
                  }
                </style>
              </defs>
              <path class="color-accent"
                d="M999.36,403.67c109.35,162.61,129.38,351.64,44.32,470.69-85.41,119.55-245.65,124.96-389.31,129.81-39.11,1.32-273.82,6-474.55-146.39-47.85-36.32-88.02-68.7-109.34-125.81-58.65-157.18,93.35-328.45,109.29-345.92-25.69,239.15,18.05,322.51,68.5,350.75,81.47,45.61,161.51-62.86,342.21-57.25,198.72,6.17,286.06,144.37,365.02,102.74,45.83-24.16,85.55-107.17,43.86-378.61Z" />
              <path class="color-secondary"
                d="M853.98,1156.64c-8.25-12.23-22.11-31-42.39-50.63-107.98-104.5-247.41-76.4-332.71-115.94-86.93-40.29-189.38-145.22-241.41-438.15,70.55-6.27,304.46-18.17,489.34,136.99,47.45,39.82,130.05,109.14,156.04,230.54,23.38,109.23-11.99,200.27-28.87,237.19Z" />
              <path class="color-primary"
                d="M969.61,529.51c-73.23,362.37-178.27,462.64-264.58,482.92-77.62,18.24-172.23-20.59-278.85,41.29-53.47,31.03-86.5,73.89-105.17,102.92-21.11-32.04-55.52-95.11-51.27-174.41,7.12-132.94,117.78-216.58,171.63-257.28,55.78-42.16,93.06-49.31,317.68-124.15,92.68-30.88,166.83-56.24,210.56-71.28Z" />
              <path class="color-white"
                d="M830.97,874.37c-17.44,17.19-109.79,104.74-255.44,104.41-145.25-.33-237.06-87.83-254.52-105.14,7.76-11.6,92-132.8,246.92-136.74,165.21-4.2,257.3,128.85,263.04,137.47Z" />
              <circle class="color-black" cx="587.49" cy="820.81" r="66.87" />
              <path class="color-accent"
                d="M517.2,698.23c25.7-7.98,98.4-29.28,182.55,1.34,100.14,36.45,144.41,120.24,154.23,140.06-7.67,11.58-15.34,23.16-23,34.73-15.37-19.23-38.49-44.52-70.9-68.89-21.3-16.02-58.33-43.87-110.24-57.97-92.65-25.16-173.16,9.66-199.15,21.34-68.36,30.72-109.61,77.9-129.67,104.79,11.03-23.01,67.91-135.61,196.19-175.41Z" />
              <polygon class="color-primary" points="77.37 605.35 147.58 453.68 95.78 596.67 77.37 605.35" />
              <circle class="color-white" cx="589.42" cy="785.67" r="16.23" />
              <path class="color-primary"
                d="M1033.54,555.97c-1.61-.08,1.5-27.1-5.84-61.82-5.28-24.95-12.53-37.68-11.84-37.96,1.13-.46,21.82,32.91,21.43,74.2-.13,14.08-2.69,25.63-3.75,25.58Z" />
              <path class="color-primary"
                d="M1059.8,553.95c-1.6.18-2.8-27-15.53-60.12-9.15-23.8-18.33-35.23-17.69-35.61,1.04-.63,26.75,29.05,32.88,69.88,2.09,13.93,1.39,25.73.34,25.85Z" />
              <path class="color-primary"
                d="M121.7,598.55c11.87-49.59,23.75-99.19,35.62-148.78-9.45,46.27-18.89,92.53-28.34,138.8-2.43,3.33-4.86,6.66-7.28,9.98Z" />
              <g>
                <path class="color-stroke"
                  d="M853.02,436.46h-467.06c-6.6,0-12-5.4-12-12,14.94-28.03,41.98-87.78,41.98-168.02,0-31.77-4.24-97.21-41.98-168.02,0-.81.1-5.05,3.53-8.48,2.17-2.17,5.17-3.52,8.47-3.52h467.06c6.6,0,12,5.4,12,12-16.73,36.39-41.16,102.14-41.36,187.25-.17,70.01,16.12,125.88,29.36,160.79Z" />
                <path class="color-stroke"
                  d="M394.34,134.12c75.05,57.47,150.1,114.93,225.15,172.4,77.95-63.44,155.91-126.88,233.86-190.33" />
                <line class="color-stroke" x1="580.76" y1="276.87" x2="408.86" y2="436.46" />
                <line class="color-stroke" x1="655.94" y1="276.87" x2="804.58" y2="436.46" />
              </g>
            </svg>
          </div>
          <label for="email" class="block text-sm font-semibold text-gray-700 mb-2 relative z-10">
            <i class="fa-solid fa-envelope mr-2 text-blue"></i>
            Email
          </label>
          <input type="email" id="email" name="email" required placeholder="Entrez votre email" value="{{ old('email') }}"
            class="w-full px-4 py-3 border border-gray-200 rounded-xl bg-gray-50 backdrop-blur-sm focus:outline-none focus:ring-2 focus:ring-darkblue/50 focus:border-darkblue focus:bg-white transition-all duration-200 relative z-10"
            autofocus>
        </div>
        <div class="relative">
          <div id="password-svg"
            class="pointer-events-none absolute right-[-40%] rotate-[-10deg] top-[20%] w-full flex justify-center opacity-0 translate-y-4 transition-all duration-300 z-0">
            <svg class="h-[50px]" id="Calque_1" data-name="Calque 1" xmlns="http://www.w3.org/2000/svg"
              viewBox="0 0 1200 1200">
              <defs>
                <style>
                  .color-stroke {
                    stroke: #000;
                  }

                  .color-black,
                  .color-black2,
                  .color-black3 {
                    fill: #1d1d1b;
                  }

                  .color-stroke,
                  .color-black3 {
                    stroke-miterlimit: 10;
                  }

                  .color-primary {
                    fill: #13678a;
                  }

                  .color-white {
                    fill: #fff;
                  }

                  .color-secondary {
                    fill: #45c4b0;
                  }

                  .color-accent {
                    fill: #dafdba;
                  }

                  .color-black3 {
                    stroke: #1d1d1b;
                  }
                </style>
              </defs>
              <path class="color-accent"
                d="M999.36,403.67c109.35,162.61,129.38,351.64,44.32,470.69-85.41,119.55-245.65,124.96-389.31,129.81-39.11,1.32-273.82,6-474.55-146.39-47.99-36.43-89.09-68.49-109.34-125.81-53.25-150.78,95.79-312,111.26-328.3-24.51,230.36,20.34,306.55,66.52,333.13,81.01,46.63,161.62-62.86,342.21-57.25,198.72,6.17,286.06,144.37,365.02,102.74,45.83-24.16,85.55-107.17,43.86-378.61Z" />
              <path class="color-secondary"
                d="M853.98,1156.64c-8.25-12.23-22.11-31-42.39-50.63-107.98-104.5-247.41-76.4-332.71-115.94-86.93-40.29-189.38-145.22-241.41-438.15,70.55-6.27,304.46-18.17,489.34,136.99,47.45,39.82,130.05,109.14,156.04,230.54,23.38,109.23-11.99,200.27-28.87,237.19Z" />
              <path class="color-primary"
                d="M969.61,529.51c-73.23,362.37-178.27,462.64-264.58,482.92-77.62,18.24-172.23-20.59-278.85,41.29-53.47,31.03-86.5,73.89-105.17,102.92-21.11-32.04-55.52-95.11-51.27-174.41,7.12-132.94,117.78-216.58,171.63-257.28,55.78-42.16,93.06-49.31,317.68-124.15,92.68-30.88,166.83-56.24,210.56-71.28Z" />
              <path class="color-white"
                d="M830.97,874.37c-17.44,17.19-109.79,104.74-255.44,104.41-145.25-.33-237.06-87.83-254.52-105.14,7.76-11.6,92-132.8,246.92-136.74,165.21-4.2,257.3,128.85,263.04,137.47Z" />
              <circle class="color-black3" cx="587.49" cy="820.81" r="66.87" />
              <path class="color-accent"
                d="M517.2,698.23c25.7-7.98,98.4-29.28,182.55,1.34,100.14,36.45,144.41,120.24,154.23,140.06-7.67,11.58-15.34,23.16-23,34.73-15.37-19.23-38.49-44.52-70.9-68.89-21.3-16.02-58.33-43.87-110.24-57.97-92.65-25.16-173.16,9.66-199.15,21.34-68.36,30.72-109.61,77.9-129.67,104.79,11.03-23.01,67.91-135.61,196.19-175.41Z" />
              <polygon class="color-primary" points="77.37 605.35 147.58 453.68 95.78 596.67 77.37 605.35" />
              <circle class="color-white" cx="589.42" cy="785.67" r="16.23" />
              <path class="color-primary"
                d="M1033.54,555.97c-1.61-.08,1.5-27.1-5.84-61.82-5.28-24.95-12.53-37.68-11.84-37.96,1.13-.46,21.82,32.91,21.43,74.2-.13,14.08-2.69,25.63-3.75,25.58Z" />
              <path class="color-primary"
                d="M1059.8,553.95c-1.6.18-2.8-27-15.53-60.12-9.15-23.8-18.33-35.23-17.69-35.61,1.04-.63,26.75,29.05,32.88,69.88,2.09,13.93,1.39,25.73.34,25.85Z" />
              <path class="color-primary"
                d="M121.7,598.55c11.87-49.59,23.75-99.19,35.62-148.78-9.45,46.27-18.89,92.53-28.34,138.8-2.43,3.33-4.86,6.66-7.28,9.98Z" />
              <path class="color-stroke"
                d="M756.8,90.27c-72.88-19.3-147.6,24.14-166.9,97.02-4.82,18.22-5.73,36.55-3.23,54.11,7.49,52.67,45.59,98.31,100.25,112.79,72.88,19.3,147.6-24.14,166.9-97.02,19.3-72.88-24.14-147.6-97.02-166.9ZM765.85,254.73c-27.15-7.19-43.33-35.02-36.14-62.17,7.19-27.15,35.02-43.33,62.17-36.14,27.15,7.19,43.33,35.02,36.14,62.17-7.19,27.15-35.02,43.33-62.17,36.14Z" />
              <path class="color-black"
                d="M628.96,237.81c-21.74,1.84-32.27,48.17-59.21,47.38-13.17-.39-23.76-11.86-27.44-8.14-3.47,3.51,6.78,12.85,5.38,25.79-1.81,16.73-22.35,32.69-37.81,30.26-11.07-1.74-17.71-12.63-20.06-10.84-2.34,1.78,6.31,11.03,5.59,22.42-1.1,17.24-23.43,35.48-41.5,33.21-12.54-1.58-20.51-12.71-23.27-10.36-2.89,2.45,7.27,13.43,5.36,24.7-2.06,12.19-17.4,19.84-30.6,24.49l-24.81,74.73,82.13-3.11,187.35-150.94c-.31-.56-7.05-13.2-.47-24.34,5.86-9.92,19.72-14.35,32.36-9.02-23.63-54.46-40.83-67.26-53-66.23ZM580.02,365.23l-96.93,72.48c-7.39,5.98-18.43,4.6-24.09-2.97-5.66-7.57-3.87-18.55,3.96-23.95l96.93-72.48c8.27-8.26,21.4-6.86,27.07.69,5.69,7.57,3.35,20.62-6.94,26.23Z" />
            </svg>
          </div>
          <label for="password" class="block text-sm font-semibold text-gray-700 mb-2 relative z-10">
            <i class="fa-solid fa-lock mr-2 text-blue"></i>
            Mot de passe
          </label>
          <input type="password" id="password" name="password" required placeholder="Entrez votre mot de passe"
            class="w-full px-4 py-3 border border-gray-200 rounded-xl bg-gray-50 backdrop-blur-sm focus:outline-none focus:ring-2 focus:ring-darkblue/50 focus:border-darkblue focus:bg-white transition-all duration-200 relative z-10">
        </div>
        <div class="flex items-center gap-2">
          <input type="checkbox" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}
            class="rounded border-gray-300 text-blue shadow-sm focus:ring-darkblue/50">
          <label for="remember" class="text-sm text-gray-600">Se souvenir de moi</label>
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
        <div class="flex justify-end gap-3 pt-2">
          <button type="submit"
            class="px-6 py-3 bg-blue text-white rounded-xl hover:bg-darkblue transition-all duration-200 font-medium shadow-lg hover:shadow-xl transform hover:scale-105">
            Connexion
          </button>
        </div>
      </form>
    </div>
  </div>
  <script>
    function setupInputSvgAnimation(inputId, svgId) {
      const input = document.getElementById(inputId);
      const svg = document.getElementById(svgId);
      if (!input || !svg) return;
      input.addEventListener('focus', () => {
        svg.classList.remove('opacity-0', 'translate-y-4');
        svg.classList.add('opacity-100', '-translate-y-8');
      });
      input.addEventListener('blur', () => {
        svg.classList.add('opacity-0', 'translate-y-4');
        svg.classList.remove('opacity-100', '-translate-y-8');
      });
    }

    setupInputSvgAnimation('email', 'email-svg');
    setupInputSvgAnimation('password', 'password-svg');
  </script>
@endsection