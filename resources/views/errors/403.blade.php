<x-guest-layout>
    <div class="bg-gray-100 flex-grow">
        <div class="min-h-[60vh] flex items-center justify-center p-4">
            <div class="bg-white p-8 rounded-2xl shadow-xl w-full max-w-md">
                <div class="flex flex-col md:flex-row items-center">
                    {{-- Ikon --}}
                    <div class="flex-shrink-0 flex items-center justify-center h-20 w-20 rounded-full bg-red-100">
                        <i class="fas fa-ban text-4xl text-red-600"></i>
                    </div>

                    {{-- Teks --}}
                    <div class="mt-6 md:mt-0 md:ml-6 text-center md:text-left">
                        <h2 class="text-sm font-semibold text-red-600 tracking-wide uppercase">Error 403</h2>
                        <h3 class="mt-2 text-2xl font-bold text-gray-900 tracking-tight">Access Denied</h3>
                        <p class="mt-3 text-base text-gray-500">
                            Sorry, you do not have permission to access this page.
                        </p>
                        <div class="mt-6">
                            <a href="{{ route('home') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700">
                                Go Back Home
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>