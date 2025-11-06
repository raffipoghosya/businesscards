<x-app-layout>
    <!-- Վերնագիր -->
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Խմբագրել քարտը. {{ $card->title }}
        </h2>
    </x-slot>

    <div class="w-full bg-gray-100 dark:bg-gray-100 py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gradient-to-br from-white to-gray-50 overflow-hidden shadow-xl sm:rounded-lg">
                
                <div class="p-6 md:p-10 text-gray-900 dark:text-gray-900">
                    
                    <!-- Ֆորման ուղղված է դեպի 'update' route-ը -->
                    <form method="POST" action="{{ route('cards.update', $card) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT') <!-- Ասում ենք Laravel-ին, որ սա UPDATE հարցում է -->
                        
                        <!-- Բաժին 1. Հիմնական կարգավորումներ -->
                        <div class="flex items-center border-b border-gray-200 pb-4 mb-6">
                            <svg class="h-6 w-6 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.21 1.434l-1.003.827c-.293.24-.438.613-.43.992a6.759 6.759 0 010 1.25c.008.378.138.75.43.99l1.005.828c.424.35.532.955.21 1.434l-1.298 2.247a1.125 1.125 0 01-1.369.49l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 01-.22.128c-.332.183-.582.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.063-.374-.313-.686-.645-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.075-.124l-1.217.456a1.125 1.125 0 01-1.37-.49l-1.296-2.247a1.125 1.125 0 01.21-1.434l1.004-.827c.292-.24.437-.613.43-.992a6.759 6.759 0 010-1.25c-.008-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.21-1.434l1.298-2.247c.303-.52.934-.686 1.37-.49l1.217.456c.355.133.75.072 1.076.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.213-1.28z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                            <h3 class="ml-2 text-2xl font-semibold text-black dark:text-black">Հիմնական կարգավորումներ</h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Title -->
                            <div>
                                <label for="title" class="block font-semibold text-xl text-black dark:text-black">Վերնագիր (Օրինակ՝ Flexitree)</label>
                                <input id="title" class="block mt-1 w-full bg-white dark:bg-white border-gray-300 text-base text-gray-900 dark:text-gray-900 focus:border-gray-500 focus:ring-gray-500 rounded-md shadow-sm" type="text" name="title" value="{{ old('title', $card->title) }}" required autofocus>
                                <x-input-error :messages="$errors->get('title')" class="mt-2" />
                            </div>

                            <!-- Slug -->
                            <div>
                                <label for="slug" class="block font-semibold text-xl text-black dark:text-black">Հղում (Օրինակ՝ flexitree)</label>
                                <input id="slug" class="block mt-1 w-full bg-white dark:bg-white border-gray-300 text-base text-gray-900 dark:text-gray-900 focus:border-gray-500 focus:ring-gray-500 rounded-md shadow-sm" type="text" name="slug" value="{{ old('slug', $card->slug) }}" required>
                                <x-input-error :messages="$errors->get('slug')" class="mt-2" />
                            </div>

                            <!-- Logo -->
                            <div>
                                <label for="logo" class="block font-semibold text-xl text-black dark:text-black">Փոխել Լոգոն</label>
                                <input id="logo" type="file" name="logo" class="block w-full mt-1 text-sm text-gray-700 dark:text-gray-700 border border-gray-300 rounded-md cursor-pointer bg-white dark:bg-white focus:outline-none file:mr-4 file:py-2 file:px-4 file:rounded-l-md file:border-0 file:font-medium file:bg-gray-200 file:text-gray-700 hover:file:bg-gray-300">
                                <x-input-error :messages="$errors->get('logo')" class="mt-2" />
                                @if ($card->logo_path)
                                    <div class="mt-2">
                                        <span class="text-sm text-gray-500">Ընթացիկ լոգո:</span>
                                        <img src="{{ Storage::url($card->logo_path) }}" alt="Current Logo" class="mt-1 h-16 w-16 rounded-full object-cover border border-gray-200">
                                    </div>
                                @endif
                            </div>

                            <!-- Background Image -->
                            <div>
                                <label for="background_image" class="block font-semibold text-xl text-black dark:text-black">Փոխել ֆոնի նկարը</label>
                                <input id="background_image" type="file" name="background_image" class="block w-full mt-1 text-sm text-gray-700 dark:text-gray-700 border border-gray-300 rounded-md cursor-pointer bg-white dark:bg-white focus:outline-none file:mr-4 file:py-2 file:px-4 file:rounded-l-md file:border-0 file:font-medium file:bg-gray-200 file:text-gray-700 hover:file:bg-gray-300">
                                <x-input-error :messages="$errors->get('background_image')" class="mt-2" />
                                @if ($card->background_image_path)
                                    <div class="mt-2">
                                        <span class="text-sm text-gray-500">Ընթացիկ ֆոն:</span>
                                        <img src="{{ Storage::url($card->background_image_path) }}" alt="Current Background" class="mt-1 h-16 w-32 rounded-md object-cover border border-gray-200">
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Բաժին 2. Դիզայնի կարգավորումներ -->
                        <hr class="my-8 border-gray-200">
                        <div class="flex items-center border-b border-gray-200 pb-4 mb-6">
                            <svg class="h-6 w-6 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.53 16.122a3 3 0 00-5.78 1.128 2.25 2.25 0 01-2.43 2.43a4.5 4.5 0 008.636-3.558 3.001 3.001 0 00-1.426-5.78zM12 6.344a4.5 4.5 0 01.37-1.831c.126-.31.29-.603.49-.886a4.5 4.5 0 018.11 3.558 3.001 3.001 0 01-1.426 5.78z" /></svg>
                            <h3 class="ml-2 text-2xl font-semibold text-black dark:text-black">Դիզայնի կարգավորումներ</h3>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Brand Color -->
                            <div>
                                <label for="brand_color" class="block font-semibold text-xl text-black dark:text-black">Հիմնական Գույն (Իկոններ, Տեքստեր, Լոգոյի Ֆոն)</label>
                                <input id="brand_color" type="color" name="brand_color" value="{{ old('brand_color', $card->brand_color) }}" class="block mt-1 w-full h-12 border-gray-300 dark:bg-white focus:border-gray-500 focus:ring-gray-500 rounded-md shadow-sm cursor-pointer">
                                <x-input-error :messages="$errors->get('brand_color')" class="mt-2" />
                            </div>

                            <!-- Logo BG Opacity -->
                            <div>
                                <label for="logo_bg_opacity" id="logo_bg_opacity_label" class="block font-semibold text-xl text-black dark:text-black">Լոգոյի ֆոնի թափանցիկություն: {{ old('logo_bg_opacity', $card->logo_bg_opacity) }}</label>
                                <input 
                                    id="logo_bg_opacity" 
                                    type="range" 
                                    name="logo_bg_opacity" 
                                    value="{{ old('logo_bg_opacity', $card->logo_bg_opacity) }}" 
                                    min="0" max="1" step="0.01" 
                                    class="block mt-4 w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-gray-600"
                                    oninput="updateOpacityLabel(this.value)">
                                <x-input-error :messages="$errors->get('logo_bg_opacity')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Բաժին 3. Կոնտակտային հղումներ -->
                        <hr class="my-8 border-gray-200">
                        <div class="flex items-center border-b border-gray-200 pb-4 mb-6">
                            <svg class="h-6 w-6 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244" /></svg>
                            <h3 class="ml-2 text-2xl font-semibold text-black dark:text-black">Կոնտակտային հղումներ</h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            
                            @foreach ($availableLinks as $linkKey => $linkLabel)
                                @php
                                    // *** ՍԽԱԼԻ ՈՒՂՂՈՒՄԸ ԱՅՍՏԵՂ Է ***
                                    // 1. Ստուգում ենք, որ $card->links-ը դատարկ չէ
                                    $existingLinksMap = [];
                                    if (!empty($card->links)) {
                                        // 2. Օգտագործում ենք array_column-ը՝ PHP զանգվածը վերածելու հարմար map-ի
                                        //    Սա [ 'phone' => ['key' => 'phone', 'value' => '123'], ... ] տեսքի է բերում
                                        $existingLinksMap = array_column($card->links, null, 'key');
                                    }

                                    // 3. Ստուգում ենք՝ արդյոք այս $linkKey-ը (օրինակ՝ 'phone') գոյություն ունի մեր map-ի մեջ
                                    $isActive = isset($existingLinksMap[$linkKey]);
                                    
                                    // 4. Վերցնում ենք արժեքը, եթե այն գոյություն ունի
                                    $value = $isActive ? $existingLinksMap[$linkKey]['value'] : '';
                                @endphp
                                <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm">
                                    <div class="flex items-center">
                                        <input type="checkbox" 
                                               name="links[{{ $linkKey }}][active]" 
                                               id="links-{{ $linkKey }}-active"
                                               class="h-4 w-4 rounded border-gray-400 dark:border-gray-500 text-gray-600 shadow-sm focus:ring-gray-500"
                                               {{ old('links.'.$linkKey.'.active', $isActive) ? 'checked' : '' }}>
                                        
                                        <label for="links-{{ $linkKey }}-active" class="ml-2 block text-lg font-semibold text-gray-900 dark:text-gray-900">
                                            Ակտիվացնել {{ $linkLabel }}
                                        </label>
                                    </div>
                                    <div class="mt-3">
                                        <label for="links-{{ $linkKey }}-value" class="sr-only">{{ $linkLabel }} (Արժեք)</label>
                                        <input 
                                            id="links-{{ $linkKey }}-value" 
                                            class="block mt-1 w-full bg-white dark:bg-white border-gray-300 text-base text-gray-900 dark:text-gray-900 focus:border-gray-500 focus:ring-gray-500 rounded-md shadow-sm" 
                                            type="text" 
                                            name="links[{{ $linkKey }}][value]" 
                                            value="{{ old('links.'.$linkKey.'.value', $value) }}" 
                                            placeholder="Լրացրեք արժեքը այստեղ...">
                                    </div>
                                </div>
                            @endforeach

                        </div>
                        
                        <!-- Պահպանելու կոճակ -->
                        <div class="flex items-center justify-end mt-8 pt-8 border-t border-gray-200">
                            <button type="submit" class="inline-flex items-center justify-center px-10 py-4 bg-gray-800 border-2 border-gray-800 rounded-md font-bold text-lg text-white uppercase tracking-wide hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900 transition ease-in-out duration-150">
                                Թարմացնել
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
    
    <!-- JavaScript՝ թափանցիկության slider-ի լեյբլը թարմացնելու համար -->
    <script>
        function updateOpacityLabel(value) {
            const formattedValue = Number(value).toFixed(2);
            const label = document.getElementById('logo_bg_opacity_label');
            if (label) {
                label.innerText = `Լոգոյի ֆոնի թափանցիկություն: ${formattedValue}`;
            }
        }
        document.addEventListener('DOMContentLoaded', function() {
            const slider = document.getElementById('logo_bg_opacity');
            if (slider) {
                updateOpacityLabel(slider.value);
            }
        });
    </script>
</x-app-layout>