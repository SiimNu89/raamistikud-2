<!DOCTYPE html>
<html lang="et">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ilmateade - {{ $city }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">

    <div class="w-full max-w-md mx-auto p-4">
        <div class="bg-gradient-to-br from-blue-500 to-indigo-700 rounded-3xl shadow-2xl p-8 text-white relative overflow-hidden">
            
            <form action="{{ route('weather.index') }}" method="GET" class="relative z-10 mb-6 flex gap-2">
                <input type="text" name="city" value="{{ $city }}" 
                       class="flex-grow rounded-xl border-none px-4 py-2 text-gray-900 focus:ring-2 focus:ring-blue-300 outline-none" 
                       placeholder="Sisesta linn...">
                <button type="submit" class="bg-white text-blue-600 font-bold px-4 py-2 rounded-xl hover:bg-gray-100 transition shadow-lg">
                    Otsi
                </button>
            </form>

            @if($weather)
                <div class="relative z-10 text-center">
                    <h2 class="text-2xl font-light tracking-wide italic">{{ $weather['name'] }}, {{ $weather['sys']['country'] }}</h2>
                    
                    <div class="flex flex-col items-center my-4">
                        <img src="https://openweathermap.org/img/wn/{{ $weather['weather'][0]['icon'] }}@4x.png" 
                             alt="ilm" class="w-32 h-32 -my-4">
                        <div class="text-7xl font-black tracking-tighter">
                            {{ round($weather['main']['temp']) }}°C
                        </div>
                    </div>

                    <p class="text-xl capitalize font-medium mb-6 opacity-90">
                        {{ $weather['weather'][0]['description'] }}
                    </p>

                    <div class="grid grid-cols-2 gap-4 border-t border-white/20 pt-6">
                        <div class="text-left">
                            <span class="block text-xs uppercase opacity-60">Tuule kiirus</span>
                            <span class="text-lg font-semibold">{{ $weather['wind']['speed'] }} m/s</span>
                        </div>
                        <div class="text-right">
                            <span class="block text-xs uppercase opacity-60">Õhuniiskus</span>
                            <span class="text-lg font-semibold">{{ $weather['main']['humidity'] }}%</span>
                        </div>
                    </div>
                </div>
            @else
                <div class="relative z-10 bg-red-500/20 p-4 rounded-xl text-center">
                    <p class="font-bold">Hups! Linna ei leitud.</p>
                    <p class="text-sm opacity-80">Kontrolli õigekirja ja proovi uuesti.</p>
                </div>
            @endif

            <div class="absolute -bottom-10 -right-10 w-40 h-40 bg-white/10 rounded-full blur-2xl"></div>
        </div>

        <div class="text-center mt-6 text-gray-400 text-sm">
            Andmed pärinevad OpenWeather API-st
        </div>
    </div>

</body>
</html>