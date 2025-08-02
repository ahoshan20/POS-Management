<header class="bg-white shadow-sm border-b border-gray-200 p-4 sticky top-0 z-30">
    <div class="flex justify-between items-center">
        <div class="flex items-center">
            <button id="mobile-menu-btn" class="md:hidden mr-4 text-gray-600 hover:text-gray-800 transition-colors">
                <i class="fas fa-bars text-xl"></i>
            </button>
            <h2 id="page-title" class="text-xl md:text-2xl font-semibold text-gray-800 animate-fade-in">Dashboard</h2>
        </div>
        <div class="flex items-center space-x-2 md:space-x-4">
            <div id="notifications" class="relative">
                <button class="text-gray-600 hover:text-gray-800 relative transition-colors btn-animate">
                    <i class="fas fa-bell text-lg md:text-xl"></i>
                    <span id="notification-count"
                        class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center hidden animate-bounce-in">0</span>
                </button>
            </div>
            <button id="quick-add-btn"
                class="bg-blue-600 text-white px-3 py-2 md:px-4 md:py-2 rounded-lg hover:bg-blue-700 transition-all duration-200 btn-animate text-sm md:text-base">
                <i class="fas fa-plus mr-1 md:mr-2"></i>
                <span class="hidden sm:inline">Quick Add</span>
            </button>
            <div class="relative hidden md:block">
                <input type="text" id="global-search" placeholder="Search..."
                    class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-200 w-48 lg:w-64">
                <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
            </div>
        </div>
    </div>
    <!-- Mobile Search -->
    <div class="mt-4 md:hidden">
        <div class="relative">
            <input type="text" id="mobile-search" placeholder="Search..."
                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-200">
            <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
        </div>
    </div>
</header>
