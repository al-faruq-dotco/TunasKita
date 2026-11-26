<div class="relative hidden min-h-[680px] overflow-hidden rounded-[2rem] bg-[#193d2c] p-10 text-white lg:flex lg:flex-col lg:justify-between">
    <div class="absolute inset-0 bg-[radial-gradient(circle_at_75%_25%,#477957_0,transparent_32%),linear-gradient(145deg,#193d2c,#315b3c_70%,#6d8c62)]"></div>
    <div class="absolute -right-12 bottom-0 opacity-20" aria-hidden="true">
        <svg viewBox="0 0 360 420" class="h-[430px] w-[360px]" fill="none">
            <path d="M180 390C210 305 145 260 190 175 222 112 183 75 204 24" stroke="#d8e8c7" stroke-width="5" stroke-linecap="round"/>
            <path d="M186 292C120 253 82 271 54 230c51-21 91-5 132 39ZM197 203c49-51 87-51 126-81-1 56-46 94-122 105ZM196 127c-59-42-70-74-67-112 50 23 77 58 78 109Z" fill="#d8e8c7"/>
            <g fill="#65477d" stroke="#e7d8ef" stroke-width="3">
                <circle cx="225" cy="194" r="20"/><circle cx="255" cy="205" r="20"/><circle cx="240" cy="236" r="20"/>
                <circle cx="210" cy="231" r="20"/><circle cx="225" cy="267" r="20"/><circle cx="195" cy="263" r="20"/>
                <circle cx="211" cy="299" r="20"/><circle cx="181" cy="295" r="20"/>
            </g>
        </svg>
    </div>
    <a href="{{ url('/') }}" class="relative inline-flex items-center gap-3 text-xl font-extrabold tracking-tight">
        <span class="grid h-11 w-11 place-items-center rounded-2xl bg-white/10 text-2xl">🌱</span>
        TunasKita
    </a>
    <div class="relative max-w-lg pb-8">
        <p class="mb-4 text-xs font-bold uppercase tracking-[.24em] text-[#c4d9b9]">Tumbuh bersama, mulai dari satu bibit</p>
        <h2 class="text-4xl font-extrabold leading-tight">{{ $heading }}</h2>
        <p class="mt-5 max-w-md text-base leading-7 text-green-50/80">{{ $description }}</p>
        <div class="mt-8 flex items-center gap-2 text-sm text-green-50/80"><span class="h-2 w-2 rounded-full bg-[#c4d9b9]"></span> Bibit anggur pilihan untuk kebun rumah Anda</div>
    </div>
</div>
