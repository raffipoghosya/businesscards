<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Digital Cards
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">

                <div class="p-6 text-gray-900">
                    @if (session('success'))
                        <div class="mb-4 p-4 bg-green-100 text-green-700 border border-green-200 rounded-md">
                            {{ session('success') }}
                        </div>
                    @endif
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-2xl font-semibold text-black dark:text-black">Բոլոր քարտերը</h3>

                        <a href="{{ route('cards.create') }}"
                            class="inline-flex items-center justify-center px-10 py-4 bg-gray-800 border-2 border-gray-800 rounded-md font-bold text-lg text-white uppercase tracking-wide hover:bg-gray-700 hover:border-gray-700 active:bg-gray-900 active:border-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900 transition ease-in-out duration-150 disabled:opacity-25">
                            Ստեղծել նորը
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        ID</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Title</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Slug (Link)</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($cards as $card)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $card->id }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $card->title }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">/{{ $card->slug }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="#" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                            <a href="#" class="text-red-600 hover:text-red-900 ml-4">Delete</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">
                                            Դեռ ստեղծված քարտեր չկան։
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>