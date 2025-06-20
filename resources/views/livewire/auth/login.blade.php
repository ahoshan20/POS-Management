<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Sign in to your account
            </h2>
        </div>
        
        <form wire:submit.prevent="login" class="mt-8 space-y-6">
            <div class="rounded-md shadow-sm -space-y-px">
                <!-- Email Field -->
                <div>
                    <label for="email" class="sr-only">Email address</label>
                    <input 
                        type="email" 
                        wire:model.blur="email" 
                        autocomplete="email" 
                        class="appearance-none rounded-none relative block w-full px-3 py-2 border 
                               @error('email') border-red-300 @else border-gray-300 @enderror 
                               placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none 
                               focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm" 
                        placeholder="Email address"
                    >
                    @error('email') 
                        <div class="text-red-500 text-sm mt-1">{{ $message }}</div> 
                    @enderror
                </div>
                
                <!-- Password Field -->
                <div>
                    <label for="password" class="sr-only">Password</label>
                    <input 
                        type="password" 
                        wire:model.blur="password" 
                        autocomplete="current-password" 
                        class="appearance-none rounded-none relative block w-full px-3 py-2 border 
                               @error('password') border-red-300 @else border-gray-300 @enderror 
                               placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none 
                               focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm" 
                        placeholder="Password"
                    >
                    @error('password') 
                        <div class="text-red-500 text-sm mt-1">{{ $message }}</div> 
                    @enderror
                </div>
            </div>

            <!-- Remember Me -->
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input 
                        type="checkbox" 
                        wire:model="remember" 
                        class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                    >
                    <label class="ml-2 block text-sm text-gray-900">
                        Remember me
                    </label>
                </div>

                <div class="text-sm">
                    <a href="#" class="font-medium text-indigo-600 hover:text-indigo-500">
                        Forgot your password?
                    </a>
                </div>
            </div>

            <!-- Submit Button -->
            <div>
                <button 
                    type="submit" 
                    class="group relative w-full flex justify-center py-2 px-4 border border-transparent 
                           text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 
                           focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                    Sign in
                </button>
            </div>

            <!-- Register Link -->
            <div class="text-center">
                <span class="text-sm text-gray-600">Don't have an account? </span>
                <a href="{{ route('register') }}" class="font-medium text-indigo-600 hover:text-indigo-500">
                    Sign up
                </a>
            </div>
        </form>
    </div>
</div>