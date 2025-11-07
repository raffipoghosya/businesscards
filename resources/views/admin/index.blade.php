<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Digital Cards
            </h2>
            <a href="{{ route('cards.create') }}"
                class="inline-flex items-center justify-center px-6 py-2 bg-gray-800 dark:bg-gray-200 border-2 border-transparent rounded-md font-bold text-base text-white dark:text-gray-800 uppercase tracking-wide hover:bg-gray-700 dark:hover:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition">
                Ստեղծել նորը
            </a>
        </div>
    </x-slot>

    <div class="w-full bg-gray-100 dark:bg-gray-900 py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-6 bg-green-100 dark:bg-green-900 border-l-4 border-green-500 text-green-700 dark:text-green-200 p-4 rounded-md shadow-md"
                    role="alert">
                    <p class="font-bold">Հաջողություն</p>
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            <div class="mb-6 flex justify-between items-center">
                <h3 class="text-3xl font-bold text-black dark:text-white">Բոլոր քարտերը</h3>
            </div>

            @if ($cards->isEmpty())
                <div class="text-center p-12 bg-white dark:bg-gray-800 rounded-lg shadow-xl">
                    <p class="text-xl font-medium text-gray-500 dark:text-gray-400">Դեռ ստեղծված քարտեր չկան։</p>
                    <p class="mt-2 text-gray-400">Սեղմեք «Ստեղծել նորը»՝ Ձեր առաջին քարտն ավելացնելու համար։</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach ($cards as $card)
                        <div
                            class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden transition-all duration-300 hover:shadow-2xl">
                            <div class="p-6">
                                <div class="flex items-center space-x-4 mb-4">
                                    <div class="flex-shrink-0">
                                        @if ($card->logo_path)
                                            <img class="h-16 w-16 rounded-full object-cover border-2 border-gray-200"
                                                src="{{ Storage::url($card->logo_path) }}"
                                                alt="{{ $card->title }} Logo">
                                        @else
                                            <div
                                                class="h-16 w-16 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                                                <span
                                                    class="text-2xl font-bold text-gray-500 dark:text-gray-300">{{ strtoupper(substr($card->title, 0, 2)) }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p
                                            class="text-xl font-bold text-gray-900 dark:text-white truncate">
                                            {{ $card->title }}</p>
                                        <p
                                            class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                            {{ route('card.public.show', $card) }}
                                        </p>
                                    </div>
                                </div>

                                <div
                                    class="flex justify-center items-center p-4 bg-white rounded-lg mt-4 border border-gray-100 dark:border-gray-700">
                                    {!! QrCode::size(150)->format('svg')->backgroundColor(255, 255, 255, 0)->generate(route('card.public.show', $card)) !!}

                                </div>

                                <div class="mt-6 grid grid-cols-2 gap-3">
                                    <a href="{{ route('cards.edit', $card) }}"
                                        class="col-span-2 inline-flex items-center justify-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition">
                                        <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none"
                                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                        </svg>
                                        Խմբագրել (Edit)
                                    </a>
                                    <a href="{{ route('card.public.show', $card) }}" target="_blank"
                                        class="inline-flex items-center justify-center px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-200 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition">
                                        <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none"
                                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        Տեսնել
                                    </a>

                                    <a href="{{ route('cards.qr.download', $card) }}"
                                        class="inline-flex items-center justify-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 active:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition">
                                        <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none"
                                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                                        </svg>
                                        QR
                                    </a>
                                </div>
                                
                                <div class="mt-3 text-center">
                                    <form method="POST" action="{{ route('cards.destroy', $card) }}" 
                                        class="inline"
                                        onsubmit="return confirm('Վստա՞հ եք, որ ցանկանում եք ջնջել «{{ $card->title }}» քարտը։ Այս գործողությունը անդառնալի է։')">
                                        @csrf
                                        @method('DELETE')
                                        
                                        <button type="submit" class="text-sm text-red-500 hover:text-red-700 dark:hover:text-red-400 font-medium">
                                            Ջնջել քարտը
                                        </button>
                                    </form>
                                </div>
                                </div>
                        </div>
                    @endforeach
                </div>
            @endif

        </div>
    </div>
</x-app-layout>