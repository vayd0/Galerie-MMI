<div class="loading fixed top-0 h-[100%] w-[100%] flex justify-center items-center bg-white z-99 transition-all duration-700">
    <svg class="w-[12rem] h-[12rem]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1400 1200" role="img"
        aria-hidden="true">
        <defs>
            <style>
                @keyframes loading {
                    0% {
                        transform: rotate(0deg)
                    }

                    100% {
                        transform: rotate(360deg)
                    }
                }

                @keyframes loading2 {
                    0% {
                        transform: rotate(360deg);
                    }

                    100% {
                        transform: rotate(-48deg)
                    }
                }

                .cls-1 {
                    fill: #fff;
                }

                .cls-2 {
                    fill: #45c4b0;
                }

                .cls-3 {
                    fill: #dafdba;
                }

                .cls-4 {
                    fill: #1d1d1b;
                    stroke: #1d1d1b;
                    stroke-miterlimit: 10;
                }
            </style>
        </defs>

        <g class="logo">
            <ellipse class="cls-2" cx="640.53" cy="588.57" rx="395.24" ry="170.78"
                transform="rotate(-48.39)"
                style="animation: 1.5s loading2 ; transform-origin: 46% 50%;" />
            <path class="cls-4"
                d="M1118.84,585.49c.13,107.36-273.48,216.72-509.45,208.98-206.45-6.77-453.72-105.46-453.66-208.66.06-103.21,247.52-201.4,453.66-208.24,235.56-7.82,509.32,100.58,509.45,207.92Z"
                style="animation: 0.9s loading ; transform-origin: 46% 50%;" />
            <ellipse class="cls-2" cx="628.16" cy="577.58" rx="170.78" ry="395.24"
                transform="rotate(-48.39)"
                style="animation: 0.5s loading2 ; transform-origin: 46% 50%;" />
            <ellipse class="cls-4" cx="637.98" cy="585.81" rx="170.78" ry="395.24"
                style="animation: 0.4s loading ; transform-origin: 46% 50%;" />
            <g id="yeux"></g>
            <path class="cls-1"
                d="M880.65,612.72c-17.44,17.19-109.79,104.74-255.44,104.41-145.25-.33-237.06-87.83-254.52-105.14,7.76-11.6,92-132.8,246.92-136.74,165.21-4.2,257.3,128.85,263.04,137.47Z" />
        </g>
        <circle class="cls-4" cx="625.67" cy="603.78" r="66.87" />
        <circle class="cls-1" cx="673.66" cy="583.12" r="16.23" />
        <path class="cls-3"
            d="M566.99,436.83c25.7-7.98,98.4-29.28,182.55,1.34,100.14,36.45,144.41,120.24,154.23,140.06-7.67,11.58-15.34,23.16-23,34.73-15.37-19.23-38.49-44.52-70.9-68.89-21.3-16.02-58.33-43.87-110.24-57.97-92.65-25.16-173.16,9.66-199.15,21.34-68.36,30.72-109.61,77.9-129.67,104.79,11.03-23.01,67.91-135.61,196.19-175.41Z" />
        </g>
    </svg>
</div>

<script>
    const loading = document.querySelector(".loading");
    const logo = document.querySelector(".logo");
    const ellipse = logo.querySelectorAll("ellipse");
    const path = logo.querySelector("path");

    setTimeout(() => {
        loading.style.opacity = 0;
        setTimeout(() => {
            loading.style.display = "none"
        }, 700);
    }, 1600);
</script>