<div>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="text-center">
                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                            <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 15.5c-.77.833.192 2.5 1.732 2.5z" />
                            </svg>
                        </div>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Access Denied</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            You don't have permission to access this resource.
                        </p>
                        <div class="mt-6">
                            <a href="{{ route('dashboard') }}"
                               class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                </svg>
                                Go to Dashboard
                            </a>
                        </div>
                        
                        @if(session('error'))
                            <div class="mt-4 p-4 bg-red-50 rounded-md">
                                <p class="text-sm text-red-800">{{ session('error') }}</p>
                            </div>
                        @endif
                        
                        <div class="mt-6 text-left">
                            <h4 class="text-sm font-medium text-gray-900 mb-2">Need Access?</h4>
                            <p class="text-sm text-gray-500">
                                Contact your administrator to request the necessary permissions for this resource.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>