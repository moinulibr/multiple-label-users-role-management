<x-app-layout>
    {{-- Header Section --}}
    @if (session('status') === 'logged-in')
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            <div class="flex flex-col sm:flex-row justify-center items-center text-center">
                <p>Welcome, {{ Auth::user()->name }}!</p>
                <p class="mt-4 sm:mt-0 sm:ml-2">{{ __("You're logged in!") }}</p>
            </div>
        </h2>
    </x-slot>
    @endif

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    {{-- Success, Error, and Sync Results Messages --}}
                    @if (session('success'))
                        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                            <p>{{ session('success') }}</p>
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                            <p>{{ session('error') }}</p>
                        </div>
                    @endif

                    @if(session('sync_results'))
                        <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 mb-4">
                            <p class="font-bold">Sync Results:</p>
                            <ul>
                                @foreach(session('sync_results') as $result)
                                    <li>{{ $result }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>