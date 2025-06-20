<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enhanced Inventory Management CRM</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    animation: {
                        'fade-in': 'fadeIn 0.5s ease-in-out',
                        'slide-in': 'slideIn 0.3s ease-out',
                        'bounce-in': 'bounceIn 0.6s ease-out',
                        'pulse-slow': 'pulse 3s infinite',
                        'spin-slow': 'spin 3s linear infinite',
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': { opacity: '0', transform: 'translateY(10px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' }
                        },
                        slideIn: {
                            '0%': { transform: 'translateX(-100%)' },
                            '100%': { transform: 'translateX(0)' }
                        },
                        bounceIn: {
                            '0%': { transform: 'scale(0.3)', opacity: '0' },
                            '50%': { transform: 'scale(1.05)' },
                            '70%': { transform: 'scale(0.9)' },
                            '100%': { transform: 'scale(1)', opacity: '1' }
                        }
                    }
                }
            }
        }
        
        // Suppress Tailwind CDN warning for development
        if (typeof console !== 'undefined') {
            const originalWarn = console.warn;
            console.warn = function(...args) {
                if (args[0] && args[0].includes('cdn.tailwindcss.com should not be used in production')) {
                    return;
                }
                originalWarn.apply(console, args);
            };
        }
    </script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .loading-spinner {
            border: 3px solid #f3f4f6;
            border-top: 3px solid #3b82f6;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            animation: spin 1s linear infinite;
        }
        
        .card-hover {
            transition: all 0.3s ease;
        }
        
        .card-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        
        .btn-animate {
            transition: all 0.2s ease;
        }
        
        .btn-animate:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }
        
        .btn-animate:active {
            transform: translateY(0);
        }
        
        .sidebar-transition {
            transition: transform 0.3s ease-in-out;
        }
        
        @media (max-width: 768px) {
            .sidebar-hidden {
                transform: translateX(-100%);
            }
        }
        
        .modal-backdrop {
            backdrop-filter: blur(4px);
        }
        
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        
        .table-responsive::-webkit-scrollbar {
            height: 8px;
        }
        
        .table-responsive::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 4px;
        }
        
        .table-responsive::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        
        .table-responsive::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
</head>
<body class="bg-gray-50 font-sans">
    <!-- Mobile Menu Overlay -->
    <div id="mobile-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden md:hidden"></div>
    
    <!-- Navigation Sidebar -->
    <div class="flex h-screen">
        <x-layouts.partials.sitebar></x-layouts.partials.sitebar>

        <!-- Main Content -->
        <div class="flex-1 overflow-hidden md:ml-0">
            <x-layouts.partials.header></x-layouts.partials.header>

            <main class="p-3 md:p-6 overflow-y-auto h-full">
                <!-- Dashboard Section -->
                <div id="dashboard-section" class="section animate-fade-in">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-6 md:mb-8">
                        <div class="bg-white p-4 md:p-6 rounded-xl shadow-sm border border-gray-200 card-hover">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-600">Total Products</p>
                                    <p id="total-products" class="text-2xl md:text-3xl font-bold text-gray-900 animate-bounce-in">0</p>
                                </div>
                                <div class="bg-blue-100 p-3 rounded-full">
                                    <i class="fas fa-box text-blue-600 text-lg md:text-xl"></i>
                                </div>
                            </div>
                        </div>
                        <div class="bg-white p-4 md:p-6 rounded-xl shadow-sm border border-gray-200 card-hover">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-600">Low Stock Items</p>
                                    <p id="low-stock" class="text-2xl md:text-3xl font-bold text-red-600 animate-bounce-in">0</p>
                                </div>
                                <div class="bg-red-100 p-3 rounded-full">
                                    <i class="fas fa-exclamation-triangle text-red-600 text-lg md:text-xl animate-pulse-slow"></i>
                                </div>
                            </div>
                        </div>
                        <div class="bg-white p-4 md:p-6 rounded-xl shadow-sm border border-gray-200 card-hover">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-600">Pending Invoices</p>
                                    <p id="pending-invoices" class="text-2xl md:text-3xl font-bold text-orange-600 animate-bounce-in">0</p>
                                </div>
                                <div class="bg-orange-100 p-3 rounded-full">
                                    <i class="fas fa-file-invoice text-orange-600 text-lg md:text-xl"></i>
                                </div>
                            </div>
                        </div>
                        <div class="bg-white p-4 md:p-6 rounded-xl shadow-sm border border-gray-200 card-hover">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-600">Monthly Revenue</p>
                                    <p id="monthly-revenue" class="text-2xl md:text-3xl font-bold text-green-600 animate-bounce-in">$0</p>
                                </div>
                                <div class="bg-green-100 p-3 rounded-full">
                                    <i class="fas fa-dollar-sign text-green-600 text-lg md:text-xl"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 md:gap-6">
                        <div class="bg-white p-4 md:p-6 rounded-xl shadow-sm border border-gray-200 card-hover">
                            <h3 class="text-lg font-semibold mb-4">Recent Orders</h3>
                            <div id="recent-orders" class="space-y-3">
                                <p class="text-gray-500">No recent orders</p>
                            </div>
                        </div>
                        <div class="bg-white p-4 md:p-6 rounded-xl shadow-sm border border-gray-200 card-hover">
                            <h3 class="text-lg font-semibold mb-4">Low Stock Alerts</h3>
                            <div id="low-stock-alerts" class="space-y-3">
                                <p class="text-gray-500">No low stock alerts</p>
                            </div>
                        </div>
                        <div class="bg-white p-4 md:p-6 rounded-xl shadow-sm border border-gray-200 card-hover">
                            <h3 class="text-lg font-semibold mb-4">Recent Activities</h3>
                            <div id="recent-activities" class="space-y-3">
                                <p class="text-gray-500">No recent activities</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Products Section -->
                <div id="products-section" class="section hidden">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 animate-fade-in">
                        <div class="p-4 md:p-6 border-b border-gray-200">
                            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                                <h3 class="text-lg font-semibold">Product Inventory</h3>
                                <div class="flex flex-wrap gap-2">
                                    <button id="import-products-btn" class="bg-green-600 text-white px-3 py-2 md:px-4 md:py-2 rounded-lg hover:bg-green-700 transition-all duration-200 btn-animate text-sm">
                                        <i class="fas fa-upload mr-1 md:mr-2"></i>
                                        Import
                                    </button>
                                    <button id="export-products-btn" class="bg-gray-600 text-white px-3 py-2 md:px-4 md:py-2 rounded-lg hover:bg-gray-700 transition-all duration-200 btn-animate text-sm">
                                        <i class="fas fa-download mr-1 md:mr-2"></i>
                                        Export
                                    </button>
                                    <button id="add-product-btn" class="bg-blue-600 text-white px-3 py-2 md:px-4 md:py-2 rounded-lg hover:bg-blue-700 transition-all duration-200 btn-animate text-sm">
                                        <i class="fas fa-plus mr-1 md:mr-2"></i>
                                        Add Product
                                    </button>
                                </div>
                            </div>
                            <div class="mt-4 flex flex-col sm:flex-row gap-3">
                                <input type="text" id="product-search" placeholder="Search products..." class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-200">
                                <select id="category-filter" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-200">
                                    <option value="">All Categories</option>
                                </select>
                                <select id="stock-filter" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-200">
                                    <option value="">All Stock Levels</option>
                                    <option value="in-stock">In Stock</option>
                                    <option value="low-stock">Low Stock</option>
                                    <option value="out-of-stock">Out of Stock</option>
                                </select>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="w-full min-w-full">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                                        <th class="px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SKU</th>
                                        <th class="px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">Category</th>
                                        <th class="px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                                        <th class="px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                                        <th class="px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Status</th>
                                        <th class="px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="products-table" class="bg-white divide-y divide-gray-200">
                                    <tr>
                                        <td colspan="7" class="px-4 md:px-6 py-4 text-center text-gray-500">
                                            <div class="loading-spinner mx-auto mb-2"></div>
                                            Loading products...
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Categories Section -->
                <div id="categories-section" class="section hidden">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 animate-fade-in">
                        <div class="p-4 md:p-6 border-b border-gray-200">
                            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                                <h3 class="text-lg font-semibold">Product Categories</h3>
                                <button id="add-category-btn" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-all duration-200 btn-animate">
                                    <i class="fas fa-plus mr-2"></i>
                                    Add Category
                                </button>
                            </div>
                        </div>
                        <div class="p-4 md:p-6">
                            <div id="categories-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                                <div class="text-center text-gray-500 col-span-full">
                                    <div class="loading-spinner mx-auto mb-2"></div>
                                    Loading categories...
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Suppliers Section -->
                <div id="suppliers-section" class="section hidden">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 animate-fade-in">
                        <div class="p-4 md:p-6 border-b border-gray-200">
                            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                                <h3 class="text-lg font-semibold">Supplier Management</h3>
                                <button id="add-supplier-btn" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-all duration-200 btn-animate">
                                    <i class="fas fa-plus mr-2"></i>
                                    Add Supplier
                                </button>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="w-full min-w-full">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Company</th>
                                        <th class="px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">Contact Person</th>
                                        <th class="px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                        <th class="px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Phone</th>
                                        <th class="px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden lg:table-cell">Products</th>
                                        <th class="px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="suppliers-table" class="bg-white divide-y divide-gray-200">
                                    <tr>
                                        <td colspan="6" class="px-4 md:px-6 py-4 text-center text-gray-500">
                                            <div class="loading-spinner mx-auto mb-2"></div>
                                            Loading suppliers...
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Customers Section -->
                <div id="customers-section" class="section hidden">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 animate-fade-in">
                        <div class="p-4 md:p-6 border-b border-gray-200">
                            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                                <h3 class="text-lg font-semibold">Customer Management</h3>
                                <button id="add-customer-btn" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-all duration-200 btn-animate">
                                    <i class="fas fa-plus mr-2"></i>
                                    Add Customer
                                </button>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="w-full min-w-full">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                        <th class="px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                        <th class="px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">Phone</th>
                                        <th class="px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Orders</th>
                                        <th class="px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden lg:table-cell">Total Spent</th>
                                        <th class="px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="customers-table" class="bg-white divide-y divide-gray-200">
                                    <tr>
                                        <td colspan="6" class="px-4 md:px-6 py-4 text-center text-gray-500">No customers found</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Orders Section -->
                <div id="orders-section" class="section hidden">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 animate-fade-in">
                        <div class="p-4 md:p-6 border-b border-gray-200">
                            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                                <h3 class="text-lg font-semibold">Order Management</h3>
                                <button id="add-order-btn" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-all duration-200 btn-animate">
                                    <i class="fas fa-plus mr-2"></i>
                                    New Order
                                </button>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="w-full min-w-full">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order ID</th>
                                        <th class="px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                        <th class="px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">Date</th>
                                        <th class="px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Items</th>
                                        <th class="px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                        <th class="px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden lg:table-cell">Status</th>
                                        <th class="px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="orders-table" class="bg-white divide-y divide-gray-200">
                                    <tr>
                                        <td colspan="7" class="px-4 md:px-6 py-4 text-center text-gray-500">No orders found</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Invoices Section -->
                <div id="invoices-section" class="section hidden">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 animate-fade-in">
                        <div class="p-4 md:p-6 border-b border-gray-200">
                            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                                <h3 class="text-lg font-semibold">Invoice Management</h3>
                                <div class="flex flex-wrap gap-2">
                                    <button id="create-invoice-btn" class="bg-blue-600 text-white px-3 py-2 md:px-4 md:py-2 rounded-lg hover:bg-blue-700 transition-all duration-200 btn-animate text-sm">
                                        <i class="fas fa-plus mr-1 md:mr-2"></i>
                                        Create Invoice
                                    </button>
                                    <button id="invoice-templates-btn" class="bg-gray-600 text-white px-3 py-2 md:px-4 md:py-2 rounded-lg hover:bg-gray-700 transition-all duration-200 btn-animate text-sm">
                                        <i class="fas fa-file-alt mr-1 md:mr-2"></i>
                                        Templates
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="w-full min-w-full">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Invoice #</th>
                                        <th class="px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                        <th class="px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">Date</th>
                                        <th class="px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Due Date</th>
                                        <th class="px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                        <th class="px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden lg:table-cell">Status</th>
                                        <th class="px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="invoices-table" class="bg-white divide-y divide-gray-200">
                                    <tr>
                                        <td colspan="7" class="px-4 md:px-6 py-4 text-center text-gray-500">No invoices found</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Purchase Orders Section -->
                <div id="purchases-section" class="section hidden">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 animate-fade-in">
                        <div class="p-4 md:p-6 border-b border-gray-200">
                            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                                <h3 class="text-lg font-semibold">Purchase Orders</h3>
                                <button id="add-purchase-btn" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-all duration-200 btn-animate">
                                    <i class="fas fa-plus mr-2"></i>
                                    New Purchase Order
                                </button>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="w-full min-w-full">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PO #</th>
                                        <th class="px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supplier</th>
                                        <th class="px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">Date</th>
                                        <th class="px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Items</th>
                                        <th class="px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                        <th class="px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden lg:table-cell">Status</th>
                                        <th class="px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="purchases-table" class="bg-white divide-y divide-gray-200">
                                    <tr>
                                        <td colspan="7" class="px-4 md:px-6 py-4 text-center text-gray-500">No purchase orders found</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Stock Adjustments Section -->
                <div id="stock-adjustments-section" class="section hidden">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 animate-fade-in">
                        <div class="p-4 md:p-6 border-b border-gray-200">
                            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                                <h3 class="text-lg font-semibold">Stock Adjustments</h3>
                                <button id="add-adjustment-btn" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-all duration-200 btn-animate">
                                    <i class="fas fa-plus mr-2"></i>
                                    New Adjustment
                                </button>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="w-full min-w-full">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                        <th class="px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                                        <th class="px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">Type</th>
                                        <th class="px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                                        <th class="px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Reason</th>
                                        <th class="px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="adjustments-table" class="bg-white divide-y divide-gray-200">
                                    <tr>
                                        <td colspan="6" class="px-4 md:px-6 py-4 text-center text-gray-500">No stock adjustments found</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Reports Section -->
                <div id="reports-section" class="section hidden">
                    <div class="animate-fade-in space-y-6">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 md:gap-6">
                            <div class="bg-white p-4 md:p-6 rounded-xl shadow-sm border border-gray-200 card-hover">
                                <h3 class="text-lg font-semibold mb-4">Inventory Summary</h3>
                                <div class="space-y-4">
                                    <div class="flex justify-between items-center">
                                        <span>Total Products:</span>
                                        <span id="report-total-products" class="font-semibold">0</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span>Total Stock Value:</span>
                                        <span id="report-stock-value" class="font-semibold">$0.00</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span>Low Stock Items:</span>
                                        <span id="report-low-stock" class="font-semibold text-red-600">0</span>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-white p-4 md:p-6 rounded-xl shadow-sm border border-gray-200 card-hover">
                                <h3 class="text-lg font-semibold mb-4">Sales Summary</h3>
                                <div class="space-y-4">
                                    <div class="flex justify-between items-center">
                                        <span>Total Orders:</span>
                                        <span id="report-total-orders" class="font-semibold">0</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span>Total Revenue:</span>
                                        <span id="report-total-revenue" class="font-semibold">$0.00</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span>Pending Invoices:</span>
                                        <span id="report-pending-invoices" class="font-semibold">0</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-white p-4 md:p-6 rounded-xl shadow-sm border border-gray-200 card-hover">
                            <h3 class="text-lg font-semibold mb-4">Generate Reports</h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                <button class="p-4 border border-gray-300 rounded-lg hover:bg-gray-50 text-left transition-all duration-200 btn-animate">
                                    <i class="fas fa-chart-line text-blue-600 mb-2 text-xl"></i>
                                    <div class="font-medium">Sales Report</div>
                                    <div class="text-sm text-gray-500">Monthly sales analysis</div>
                                </button>
                                <button class="p-4 border border-gray-300 rounded-lg hover:bg-gray-50 text-left transition-all duration-200 btn-animate">
                                    <i class="fas fa-boxes text-green-600 mb-2 text-xl"></i>
                                    <div class="font-medium">Inventory Report</div>
                                    <div class="text-sm text-gray-500">Stock levels and valuation</div>
                                </button>
                                <button class="p-4 border border-gray-300 rounded-lg hover:bg-gray-50 text-left transition-all duration-200 btn-animate">
                                    <i class="fas fa-users text-purple-600 mb-2 text-xl"></i>
                                    <div class="font-medium">Customer Report</div>
                                    <div class="text-sm text-gray-500">Customer analysis</div>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Settings Section -->
                <div id="settings-section" class="section hidden">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 md:gap-6 animate-fade-in">
                        <div class="bg-white p-4 md:p-6 rounded-xl shadow-sm border border-gray-200 card-hover">
                            <h3 class="text-lg font-semibold mb-4">Company Information</h3>
                            <form id="company-settings-form">
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Company Name</label>
                                        <input type="text" id="company-name" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-200">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                                        <textarea id="company-address" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-200"></textarea>
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                                            <input type="tel" id="company-phone" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-200">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                            <input type="email" id="company-email" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-200">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Tax ID</label>
                                        <input type="text" id="company-tax-id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-200">
                                    </div>
                                </div>
                                <button type="submit" class="mt-4 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-all duration-200 btn-animate">
                                    Save Settings
                                </button>
                            </form>
                        </div>
                        <div class="bg-white p-4 md:p-6 rounded-xl shadow-sm border border-gray-200 card-hover">
                            <h3 class="text-lg font-semibold mb-4">System Settings</h3>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Currency</label>
                                    <select id="currency-setting" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-200">
                                        <option value="USD">USD ($)</option>
                                        <option value="EUR">EUR (€)</option>
                                        <option value="GBP">GBP (£)</option>
                                        <option value="JPY">JPY (¥)</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Tax Rate (%)</label>
                                    <input type="number" id="tax-rate" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-200" value="10">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Low Stock Threshold</label>
                                    <input type="number" id="low-stock-threshold" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-200" value="10">
                                </div>
                                <div class="flex items-center">
                                    <input type="checkbox" id="auto-backup" class="mr-2 focus:ring-2 focus:ring-blue-500">
                                    <label for="auto-backup" class="text-sm text-gray-700">Enable automatic backup</label>
                                </div>
                            </div>
                            <div class="mt-6 pt-6 border-t border-gray-200">
                                <h4 class="font-medium mb-4">Data Management</h4>
                                <div class="space-y-2">
                                    <button id="export-data-btn" class="w-full bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-all duration-200 btn-animate">
                                        <i class="fas fa-download mr-2"></i>
                                        Export All Data
                                    </button>
                                    <button id="import-data-btn" class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-all duration-200 btn-animate">
                                        <i class="fas fa-upload mr-2"></i>
                                        Import Data
                                    </button>
                                    <button id="reset-data-btn" class="w-full bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-all duration-200 btn-animate">
                                        <i class="fas fa-trash mr-2"></i>
                                        Reset All Data
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Product Modal -->
    <div id="product-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 modal-backdrop">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-xl max-w-md w-full p-6 animate-bounce-in">
                <div class="flex justify-between items-center mb-4">
                    <h3 id="product-modal-title" class="text-lg font-semibold">Add Product</h3>
                    <button id="close-product-modal" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <form id="product-form">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Product Name</label>
                            <input type="text" id="product-name" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-200" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">SKU</label>
                            <input type="text" id="product-sku" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-200" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                            <select id="product-category" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-200" required>
                                <option value="">Select Category</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Supplier</label>
                            <select id="product-supplier" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-200">
                                <option value="">Select Supplier</option>
                            </select>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Stock Quantity</label>
                                <input type="number" id="product-stock" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-200" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Price</label>
                                <input type="number" step="0.01" id="product-price" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-200" required>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Cost Price</label>
                                <input type="number" step="0.01" id="product-cost" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-200">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Minimum Stock</label>
                                <input type="number" id="product-min-stock" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-200" value="10">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                            <textarea id="product-description" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-200"></textarea>
                        </div>
                    </div>
                    <div class="flex flex-col sm:flex-row justify-end gap-3 mt-6">
                        <button type="button" id="cancel-product" class="px-4 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 transition-all duration-200 btn-animate">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all duration-200 btn-animate">Save Product</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Category Modal -->
    <div id="category-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 modal-backdrop">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-xl max-w-md w-full p-6 animate-bounce-in">
                <div class="flex justify-between items-center mb-4">
                    <h3 id="category-modal-title" class="text-lg font-semibold">Add Category</h3>
                    <button id="close-category-modal" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <form id="category-form">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Category Name</label>
                            <input type="text" id="category-name" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-200" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                            <textarea id="category-description" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-200" placeholder="Brief description of the category"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Color</label>
                            <select id="category-color" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-200">
                                <option value="blue">Blue</option>
                                <option value="green">Green</option>
                                <option value="purple">Purple</option>
                                <option value="red">Red</option>
                                <option value="yellow">Yellow</option>
                                <option value="indigo">Indigo</option>
                                <option value="pink">Pink</option>
                                <option value="gray">Gray</option>
                            </select>
                        </div>
                    </div>
                    <div class="flex flex-col sm:flex-row justify-end gap-3 mt-6">
                        <button type="button" id="cancel-category" class="px-4 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 transition-all duration-200 btn-animate">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all duration-200 btn-animate">Save Category</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Supplier Modal -->
    <div id="supplier-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 modal-backdrop">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-xl max-w-md w-full p-6 animate-bounce-in">
                <div class="flex justify-between items-center mb-4">
                    <h3 id="supplier-modal-title" class="text-lg font-semibold">Add Supplier</h3>
                    <button id="close-supplier-modal" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <form id="supplier-form">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Company Name</label>
                            <input type="text" id="supplier-company" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-200" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Contact Person</label>
                            <input type="text" id="supplier-contact" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-200" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" id="supplier-email" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-200" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                            <input type="tel" id="supplier-phone" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-200" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                            <textarea id="supplier-address" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-200"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Website</label>
                            <input type="url" id="supplier-website" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-200" placeholder="https://example.com">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Payment Terms</label>
                            <select id="supplier-payment-terms" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-200">
                                <option value="net-30">Net 30</option>
                                <option value="net-15">Net 15</option>
                                <option value="net-60">Net 60</option>
                                <option value="cod">Cash on Delivery</option>
                                <option value="prepaid">Prepaid</option>
                            </select>
                        </div>
                    </div>
                    <div class="flex flex-col sm:flex-row justify-end gap-3 mt-6">
                        <button type="button" id="cancel-supplier" class="px-4 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 transition-all duration-200 btn-animate">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all duration-200 btn-animate">Save Supplier</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Customer Modal -->
    <div id="customer-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 modal-backdrop">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-xl max-w-md w-full p-6 animate-bounce-in">
                <div class="flex justify-between items-center mb-4">
                    <h3 id="customer-modal-title" class="text-lg font-semibold">Add Customer</h3>
                    <button id="close-customer-modal" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <form id="customer-form">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                            <input type="text" id="customer-name" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-200" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" id="customer-email" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-200" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                            <input type="tel" id="customer-phone" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-200">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                            <textarea id="customer-address" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all duration-200"></textarea>
                        </div>
                    </div>
                    <div class="flex flex-col sm:flex-row justify-end gap-3 mt-6">
                        <button type="button" id="cancel-customer" class="px-4 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 transition-all duration-200 btn-animate">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all duration-200 btn-animate">Save Customer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Quick Add Modal -->
    <div id="quick-add-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 modal-backdrop">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-xl max-w-md w-full p-6 animate-bounce-in">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold">Quick Add</h3>
                    <button id="close-quick-add-modal" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <button class="quick-add-option p-4 border border-gray-300 rounded-lg hover:bg-gray-50 text-center transition-all duration-200 btn-animate" data-type="product">
                        <i class="fas fa-box text-blue-600 text-2xl mb-2"></i>
                        <div class="font-medium">Product</div>
                    </button>
                    <button class="quick-add-option p-4 border border-gray-300 rounded-lg hover:bg-gray-50 text-center transition-all duration-200 btn-animate" data-type="category">
                        <i class="fas fa-tags text-purple-600 text-2xl mb-2"></i>
                        <div class="font-medium">Category</div>
                    </button>
                    <button class="quick-add-option p-4 border border-gray-300 rounded-lg hover:bg-gray-50 text-center transition-all duration-200 btn-animate" data-type="supplier">
                        <i class="fas fa-truck text-orange-600 text-2xl mb-2"></i>
                        <div class="font-medium">Supplier</div>
                    </button>
                    <button class="quick-add-option p-4 border border-gray-300 rounded-lg hover:bg-gray-50 text-center transition-all duration-200 btn-animate" data-type="customer">
                        <i class="fas fa-user text-green-600 text-2xl mb-2"></i>
                        <div class="font-medium">Customer</div>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Initialize data storage
            let products = JSON.parse(localStorage.getItem('products')) || [];
            let customers = JSON.parse(localStorage.getItem('customers')) || [];
            let orders = JSON.parse(localStorage.getItem('orders')) || [];
            let invoices = JSON.parse(localStorage.getItem('invoices')) || [];
            let suppliers = JSON.parse(localStorage.getItem('suppliers')) || [];
            let categories = JSON.parse(localStorage.getItem('categories')) || [];
            let purchases = JSON.parse(localStorage.getItem('purchases')) || [];
            let adjustments = JSON.parse(localStorage.getItem('adjustments')) || [];
            let settings = JSON.parse(localStorage.getItem('settings')) || {
                companyName: 'Your Company',
                companyAddress: '',
                companyPhone: '',
                companyEmail: '',
                companyTaxId: '',
                currency: 'USD',
                taxRate: 10,
                lowStockThreshold: 10
            };
            
            let currentEditId = null;
            let currentEditType = null;
            let invoiceCounter = parseInt(localStorage.getItem('invoiceCounter')) || 1000;

            // Initialize default categories if empty
            if (categories.length === 0) {
                categories = [
                    { id: 1, name: 'Electronics', description: 'Electronic devices and accessories', color: 'blue' },
                    { id: 2, name: 'Clothing', description: 'Apparel and fashion items', color: 'green' },
                    { id: 3, name: 'Books', description: 'Books and publications', color: 'purple' },
                    { id: 4, name: 'Home & Garden', description: 'Home improvement and garden supplies', color: 'yellow' }
                ];
                localStorage.setItem('categories', JSON.stringify(categories));
            }

            // Initialize default suppliers if empty
            if (suppliers.length === 0) {
                suppliers = [
                    { 
                        id: 1, 
                        company: 'Tech Solutions Inc', 
                        contactPerson: 'John Smith', 
                        email: 'john@techsolutions.com', 
                        phone: '+1-555-0123',
                        address: '123 Tech Street, Silicon Valley, CA 94000',
                        website: 'https://techsolutions.com',
                        paymentTerms: 'net-30'
                    },
                    { 
                        id: 2, 
                        company: 'Global Supplies Ltd', 
                        contactPerson: 'Sarah Johnson', 
                        email: 'sarah@globalsupplies.com', 
                        phone: '+1-555-0456',
                        address: '456 Supply Avenue, New York, NY 10001',
                        website: 'https://globalsupplies.com',
                        paymentTerms: 'net-15'
                    }
                ];
                localStorage.setItem('suppliers', JSON.stringify(suppliers));
            }

            // Mobile menu functionality
            $('#mobile-menu-btn').click(function() {
                $('#sidebar').removeClass('sidebar-hidden');
                $('#mobile-overlay').removeClass('hidden');
            });

            $('#close-sidebar, #mobile-overlay').click(function() {
                $('#sidebar').addClass('sidebar-hidden');
                $('#mobile-overlay').addClass('hidden');
            });

            // Navigation with animations
            $('.nav-item').click(function(e) {
                e.preventDefault();
                const section = $(this).data('section');
                
                // Update active nav item with animation
                $('.nav-item').removeClass('bg-blue-700 text-white').addClass('text-gray-300');
                $(this).removeClass('text-gray-300').addClass('bg-blue-700 text-white');
                
                // Hide current section with fade out
                $('.section:not(.hidden)').addClass('hidden');
                
                // Show new section with fade in animation
                setTimeout(() => {
                    $(`#${section}-section`).removeClass('hidden').addClass('animate-fade-in');
                }, 100);
                
                // Update page title with animation
                const titles = {
                    dashboard: 'Dashboard',
                    products: 'Product Management',
                    categories: 'Category Management',
                    suppliers: 'Supplier Management',
                    customers: 'Customer Management',
                    orders: 'Order Management',
                    invoices: 'Invoice Management',
                    purchases: 'Purchase Orders',
                    'stock-adjustments': 'Stock Adjustments',
                    reports: 'Reports & Analytics',
                    settings: 'Settings'
                };
                $('#page-title').text(titles[section]).addClass('animate-fade-in');
                
                // Close mobile menu
                $('#sidebar').addClass('sidebar-hidden');
                $('#mobile-overlay').addClass('hidden');
                
                // Load section data
                loadSectionData(section);
            });

            // Quick Add functionality with animations
            $('#quick-add-btn').click(function() {
                $('#quick-add-modal').removeClass('hidden');
            });

            $('#close-quick-add-modal').click(function() {
                $('#quick-add-modal').addClass('hidden');
            });

            $('.quick-add-option').click(function() {
                const type = $(this).data('type');
                $('#quick-add-modal').addClass('hidden');
                
                // Add loading animation
                $(this).addClass('animate-pulse');
                
                setTimeout(() => {
                    switch(type) {
                        case 'product':
                            openProductModal();
                            break;
                        case 'category':
                            openCategoryModal();
                            break;
                        case 'supplier':
                            openSupplierModal();
                            break;
                        case 'customer':
                            openCustomerModal();
                            break;
                    }
                    $(this).removeClass('animate-pulse');
                }, 200);
            });

            // Product Management with animations
            $('#add-product-btn').click(() => openProductModal());
            $('#close-product-modal, #cancel-product').click(() => $('#product-modal').addClass('hidden'));

            function openProductModal(productId = null) {
                currentEditId = productId;
                currentEditType = 'product';
                $('#product-modal-title').text(productId ? 'Edit Product' : 'Add Product');

                // Load categories and suppliers into the form
                loadCategoryFilter('#product-category');
                loadProductSuppliers('#product-supplier');

                if (productId) {
                    const product = products.find(p => p.id === productId);
                    if (product) {
                        $('#product-name').val(product.name);
                        $('#product-sku').val(product.sku);
                        $('#product-category').val(product.categoryId);
                        $('#product-supplier').val(product.supplierId || '');
                        $('#product-stock').val(product.stock);
                        $('#product-price').val(product.price);
                        $('#product-cost').val(product.cost);
                        $('#product-min-stock').val(product.minStock);
                        $('#product-description').val(product.description);
                    }
                } else {
                    $('#product-form')[0].reset();
                }

                $('#product-modal').removeClass('hidden');
            }

            $('#product-form').submit(function(e) {
                e.preventDefault();

                // Add loading animation to submit button
                const submitBtn = $(this).find('button[type="submit"]');
                const originalText = submitBtn.html();
                submitBtn.html('<div class="loading-spinner inline-block mr-2"></div>Saving...');

                setTimeout(() => {
                    const product = {
                        id: currentEditId || Date.now(),
                        name: $('#product-name').val(),
                        sku: $('#product-sku').val(),
                        categoryId: parseInt($('#product-category').val()),
                        supplierId: $('#product-supplier').val() ? parseInt($('#product-supplier').val()) : null,
                        stock: parseInt($('#product-stock').val()),
                        price: parseFloat($('#product-price').val()),
                        cost: parseFloat($('#product-cost').val() || 0),
                        minStock: parseInt($('#product-min-stock').val() || 10),
                        description: $('#product-description').val()
                    };

                    if (currentEditId) {
                        const index = products.findIndex(p => p.id === currentEditId);
                        products[index] = product;
                        addActivity(`Updated product: ${product.name}`);
                    } else {
                        products.push(product);
                        addActivity(`Added new product: ${product.name}`);
                    }

                    localStorage.setItem('products', JSON.stringify(products));
                    $('#product-modal').addClass('hidden');
                    loadProducts();
                    updateDashboard();
                    
                    // Reset button
                    submitBtn.html(originalText);
                    
                    // Show success notification
                    showNotification('Product saved successfully!', 'success');
                }, 1000);
            });

            function loadProducts() {
                const table = $('#products-table');
                table.html('<tr><td colspan="7" class="px-4 md:px-6 py-4 text-center text-gray-500"><div class="loading-spinner mx-auto mb-2"></div>Loading products...</td></tr>');

                setTimeout(() => {
                    table.empty();

                    if (products.length === 0) {
                        table.append('<tr><td colspan="7" class="px-4 md:px-6 py-4 text-center text-gray-500">No products found</td></tr>');
                        return;
                    }

                    products.forEach((product, index) => {
                        const category = categories.find(c => c.id === product.categoryId);
                        const supplier = suppliers.find(s => s.id === product.supplierId);
                        const stockStatus = product.stock > 0 ? (product.stock <= product.minStock ? 'Low Stock' : 'In Stock') : 'Out of Stock';
                        const stockColor = product.stock > 0 ? (product.stock <= product.minStock ? 'text-orange-600' : 'text-green-600') : 'text-red-600';

                        const row = $(`
                            <tr class="animate-fade-in hover:bg-gray-50 transition-colors" style="animation-delay: ${index * 0.1}s">
                                <td class="px-4 md:px-6 py-4 whitespace-nowrap">
                                    <div class="font-medium text-gray-900">${product.name}</div>
                                    <div class="text-sm text-gray-500 md:hidden">${product.sku}</div>
                                </td>
                                <td class="px-4 md:px-6 py-4 whitespace-nowrap hidden md:table-cell">${product.sku}</td>
                                <td class="px-4 md:px-6 py-4 whitespace-nowrap hidden sm:table-cell">${category ? category.name : 'N/A'}</td>
                                <td class="px-4 md:px-6 py-4 whitespace-nowrap">
                                    <span class="${stockColor} font-medium">${product.stock}</span>
                                    <div class="text-xs text-gray-500 md:hidden">${stockStatus}</div>
                                </td>
                                <td class="px-4 md:px-6 py-4 whitespace-nowrap font-medium">$${product.price.toFixed(2)}</td>
                                <td class="px-4 md:px-6 py-4 whitespace-nowrap hidden md:table-cell">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full ${stockColor.replace('text-', 'bg-').replace('-600', '-100')} ${stockColor}">${stockStatus}</span>
                                </td>
                                <td class="px-4 md:px-6 py-4 whitespace-nowrap">
                                    <div class="flex space-x-2">
                                        <button class="text-blue-600 hover:text-blue-900 edit-product btn-animate" data-id="${product.id}">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="text-red-600 hover:text-red-900 delete-product btn-animate" data-id="${product.id}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        `);
                        table.append(row);
                    });
                }, 500);
            }

            $(document).on('click', '.edit-product', function() {
                const id = parseInt($(this).data('id'));
                $(this).addClass('animate-pulse');
                setTimeout(() => {
                    openProductModal(id);
                    $(this).removeClass('animate-pulse');
                }, 200);
            });

            $(document).on('click', '.delete-product', function() {
                const id = parseInt($(this).data('id'));
                const product = products.find(p => p.id === id);

                if (confirm(`Are you sure you want to delete the product "${product.name}"?`)) {
                    $(this).closest('tr').addClass('animate-fade-out');
                    setTimeout(() => {
                        products = products.filter(p => p.id !== id);
                        localStorage.setItem('products', JSON.stringify(products));
                        loadProducts();
                        updateDashboard();
                        addActivity(`Deleted product: ${product.name}`);
                        showNotification('Product deleted successfully!', 'success');
                    }, 300);
                }
            });

            function loadCategoryFilter(selector = '#category-filter') {
                const select = $(selector);
                select.empty().append('<option value="">All Categories</option>');
                categories.forEach(category => {
                    select.append(`<option value="${category.id}">${category.name}</option>`);
                });
            }

            function loadProductSuppliers(selector = '#product-supplier') {
                const select = $(selector);
                select.empty().append('<option value="">Select Supplier</option>');
                suppliers.forEach(supplier => {
                    select.append(`<option value="${supplier.id}">${supplier.company}</option>`);
                });
            }

            // Category Management with animations
            $('#add-category-btn').click(() => openCategoryModal());
            $('#close-category-modal, #cancel-category').click(() => $('#category-modal').addClass('hidden'));

            function openCategoryModal(categoryId = null) {
                currentEditId = categoryId;
                currentEditType = 'category';
                $('#category-modal-title').text(categoryId ? 'Edit Category' : 'Add Category');
                
                if (categoryId) {
                    const category = categories.find(c => c.id === categoryId);
                    if (category) {
                        $('#category-name').val(category.name);
                        $('#category-description').val(category.description);
                        $('#category-color').val(category.color || 'blue');
                    }
                } else {
                    $('#category-form')[0].reset();
                    $('#category-color').val('blue');
                }
                
                $('#category-modal').removeClass('hidden');
            }

            $('#category-form').submit(function(e) {
                e.preventDefault();
                
                const submitBtn = $(this).find('button[type="submit"]');
                const originalText = submitBtn.html();
                submitBtn.html('<div class="loading-spinner inline-block mr-2"></div>Saving...');

                setTimeout(() => {
                    const category = {
                        id: currentEditId || Date.now(),
                        name: $('#category-name').val(),
                        description: $('#category-description').val(),
                        color: $('#category-color').val()
                    };

                    if (currentEditId) {
                        const index = categories.findIndex(c => c.id === currentEditId);
                        categories[index] = category;
                        addActivity(`Updated category: ${category.name}`);
                    } else {
                        categories.push(category);
                        addActivity(`Added new category: ${category.name}`);
                    }

                    localStorage.setItem('categories', JSON.stringify(categories));
                    $('#category-modal').addClass('hidden');
                    loadCategories();
                    loadCategoryFilter();
                    updateDashboard();
                    
                    submitBtn.html(originalText);
                    showNotification('Category saved successfully!', 'success');
                }, 1000);
            });

            // Supplier Management with animations
            $('#add-supplier-btn').click(() => openSupplierModal());
            $('#close-supplier-modal, #cancel-supplier').click(() => $('#supplier-modal').addClass('hidden'));

            function openSupplierModal(supplierId = null) {
                currentEditId = supplierId;
                currentEditType = 'supplier';
                $('#supplier-modal-title').text(supplierId ? 'Edit Supplier' : 'Add Supplier');
                
                if (supplierId) {
                    const supplier = suppliers.find(s => s.id === supplierId);
                    if (supplier) {
                        $('#supplier-company').val(supplier.company);
                        $('#supplier-contact').val(supplier.contactPerson);
                        $('#supplier-email').val(supplier.email);
                        $('#supplier-phone').val(supplier.phone);
                        $('#supplier-address').val(supplier.address);
                        $('#supplier-website').val(supplier.website);
                        $('#supplier-payment-terms').val(supplier.paymentTerms);
                    }
                } else {
                    $('#supplier-form')[0].reset();
                    $('#supplier-payment-terms').val('net-30');
                }
                
                $('#supplier-modal').removeClass('hidden');
            }

            $('#supplier-form').submit(function(e) {
                e.preventDefault();
                
                const submitBtn = $(this).find('button[type="submit"]');
                const originalText = submitBtn.html();
                submitBtn.html('<div class="loading-spinner inline-block mr-2"></div>Saving...');

                setTimeout(() => {
                    const supplier = {
                        id: currentEditId || Date.now(),
                        company: $('#supplier-company').val(),
                        contactPerson: $('#supplier-contact').val(),
                        email: $('#supplier-email').val(),
                        phone: $('#supplier-phone').val(),
                        address: $('#supplier-address').val(),
                        website: $('#supplier-website').val(),
                        paymentTerms: $('#supplier-payment-terms').val()
                    };

                    if (currentEditId) {
                        const index = suppliers.findIndex(s => s.id === currentEditId);
                        suppliers[index] = supplier;
                        addActivity(`Updated supplier: ${supplier.company}`);
                    } else {
                        suppliers.push(supplier);
                        addActivity(`Added new supplier: ${supplier.company}`);
                    }

                    localStorage.setItem('suppliers', JSON.stringify(suppliers));
                    $('#supplier-modal').addClass('hidden');
                    loadSuppliers();
                    loadProductSuppliers();
                    updateDashboard();
                    
                    submitBtn.html(originalText);
                    showNotification('Supplier saved successfully!', 'success');
                }, 1000);
            });

            function loadCategories() {
                const grid = $('#categories-grid');
                grid.html('<div class="text-center text-gray-500 col-span-full"><div class="loading-spinner mx-auto mb-2"></div>Loading categories...</div>');

                setTimeout(() => {
                    grid.empty();

                    if (categories.length === 0) {
                        grid.append('<div class="text-center text-gray-500 col-span-full">No categories found</div>');
                        return;
                    }

                    categories.forEach((category, index) => {
                        const productCount = products.filter(p => p.categoryId === category.id).length;
                        const colorClasses = {
                            blue: 'border-l-blue-500 bg-blue-50',
                            green: 'border-l-green-500 bg-green-50',
                            purple: 'border-l-purple-500 bg-purple-50',
                            red: 'border-l-red-500 bg-red-50',
                            yellow: 'border-l-yellow-500 bg-yellow-50',
                            indigo: 'border-l-indigo-500 bg-indigo-50',
                            pink: 'border-l-pink-500 bg-pink-50',
                            gray: 'border-l-gray-500 bg-gray-50'
                        };
                        
                        const colorClass = colorClasses[category.color] || colorClasses.blue;
                        
                        const card = $(`
                            <div class="bg-white p-4 rounded-xl border-l-4 ${colorClass} shadow-sm border border-gray-200 card-hover animate-fade-in" style="animation-delay: ${index * 0.1}s">
                                <div class="flex justify-between items-start mb-3">
                                    <h4 class="font-semibold text-gray-900">${category.name}</h4>
                                    <div class="flex space-x-2">
                                        <button class="text-blue-600 hover:text-blue-900 edit-category btn-animate" data-id="${category.id}">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="text-red-600 hover:text-red-900 delete-category btn-animate" data-id="${category.id}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                                <p class="text-sm text-gray-600 mb-3">${category.description}</p>
                                <div class="flex justify-between items-center">
                                    <span class="text-xs text-gray-500">${productCount} products</span>
                                    <div class="w-3 h-3 rounded-full bg-${category.color}-500"></div>
                                </div>
                            </div>
                        `);
                        grid.append(card);
                    });
                }, 500);
            }

            // Add event handlers for category edit/delete buttons
            $(document).on('click', '.edit-category', function() {
                const id = parseInt($(this).data('id'));
                $(this).addClass('animate-pulse');
                setTimeout(() => {
                    openCategoryModal(id);
                    $(this).removeClass('animate-pulse');
                }, 200);
            });

            $(document).on('click', '.delete-category', function() {
                const id = parseInt($(this).data('id'));
                const category = categories.find(c => c.id === id);
                const productCount = products.filter(p => p.categoryId === id).length;
                
                if (productCount > 0) {
                    showNotification(`Cannot delete category "${category.name}" because it has ${productCount} products assigned to it.`, 'error');
                    return;
                }
                
                if (confirm(`Are you sure you want to delete the category "${category.name}"?`)) {
                    $(this).closest('.card-hover').addClass('animate-fade-out');
                    setTimeout(() => {
                        categories = categories.filter(c => c.id !== id);
                        localStorage.setItem('categories', JSON.stringify(categories));
                        loadCategories();
                        loadCategoryFilter();
                        addActivity(`Deleted category: ${category.name}`);
                        showNotification('Category deleted successfully!', 'success');
                    }, 300);
                }
            });

            function loadSuppliers() {
                const table = $('#suppliers-table');
                table.html('<tr><td colspan="6" class="px-4 md:px-6 py-4 text-center text-gray-500"><div class="loading-spinner mx-auto mb-2"></div>Loading suppliers...</td></tr>');

                setTimeout(() => {
                    table.empty();

                    if (suppliers.length === 0) {
                        table.append('<tr><td colspan="6" class="px-4 md:px-6 py-4 text-center text-gray-500">No suppliers found</td></tr>');
                        return;
                    }

                    suppliers.forEach((supplier, index) => {
                        const productCount = products.filter(p => p.supplierId === supplier.id).length;

                        const row = $(`
                            <tr class="animate-fade-in hover:bg-gray-50 transition-colors" style="animation-delay: ${index * 0.1}s">
                                <td class="px-4 md:px-6 py-4 whitespace-nowrap">
                                    <div class="font-medium text-gray-900">${supplier.company}</div>
                                    <div class="text-sm text-gray-500 sm:hidden">${supplier.contactPerson}</div>
                                </td>
                                <td class="px-4 md:px-6 py-4 whitespace-nowrap hidden sm:table-cell">${supplier.contactPerson}</td>
                                <td class="px-4 md:px-6 py-4 whitespace-nowrap">
                                    <a href="mailto:${supplier.email}" class="text-blue-600 hover:text-blue-900">${supplier.email}</a>
                                </td>
                                <td class="px-4 md:px-6 py-4 whitespace-nowrap hidden md:table-cell">
                                    <a href="tel:${supplier.phone}" class="text-blue-600 hover:text-blue-900">${supplier.phone}</a>
                                </td>
                                <td class="px-4 md:px-6 py-4 whitespace-nowrap hidden lg:table-cell">
                                    <span class="px-2 py-1 text-xs font-semibold bg-blue-100 text-blue-800 rounded-full">${productCount}</span>
                                </td>
                                <td class="px-4 md:px-6 py-4 whitespace-nowrap">
                                    <div class="flex space-x-2">
                                        <button class="text-blue-600 hover:text-blue-900 edit-supplier btn-animate" data-id="${supplier.id}">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="text-red-600 hover:text-red-900 delete-supplier btn-animate" data-id="${supplier.id}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        `);
                        table.append(row);
                    });
                }, 500);
            }

            // Add event handlers for supplier edit/delete buttons
            $(document).on('click', '.edit-supplier', function() {
                const id = parseInt($(this).data('id'));
                $(this).addClass('animate-pulse');
                setTimeout(() => {
                    openSupplierModal(id);
                    $(this).removeClass('animate-pulse');
                }, 200);
            });

            $(document).on('click', '.delete-supplier', function() {
                const id = parseInt($(this).data('id'));
                const supplier = suppliers.find(s => s.id === id);
                const productCount = products.filter(p => p.supplierId === id).length;
                
                if (productCount > 0) {
                    showNotification(`Cannot delete supplier "${supplier.company}" because it has ${productCount} products assigned to it.`, 'error');
                    return;
                }
                
                if (confirm(`Are you sure you want to delete the supplier "${supplier.company}"?`)) {
                    $(this).closest('tr').addClass('animate-fade-out');
                    setTimeout(() => {
                        suppliers = suppliers.filter(s => s.id !== id);
                        localStorage.setItem('suppliers', JSON.stringify(suppliers));
                        loadSuppliers();
                        loadProductSuppliers();
                        addActivity(`Deleted supplier: ${supplier.company}`);
                        showNotification('Supplier deleted successfully!', 'success');
                    }, 300);
                }
            });
        
            function loadSectionData(section) {
                switch (section) {
                    case 'dashboard':
                        updateDashboard();
                        break;
                    case 'products':
                        loadProducts();
                        loadCategoryFilter();
                        loadProductSuppliers();
                        break;
                    case 'categories':
                        loadCategories();
                        break;
                    case 'suppliers':
                        loadSuppliers();
                        break;
                    case 'customers':
                        loadCustomers();
                        break;
                    case 'orders':
                        loadOrders();
                        break;
                    case 'invoices':
                        loadInvoices();
                        break;
                    case 'purchases':
                        loadPurchases();
                        break;
                    case 'stock-adjustments':
                        loadAdjustments();
                        break;
                    case 'reports':
                        updateReports();
                        break;
                    case 'settings':
                        loadSettings();
                        break;
                }
            }

            function updateDashboard() {
                // Animate dashboard metrics
                animateCounter('#total-products', products.length);
                animateCounter('#low-stock', products.filter(p => p.stock <= p.minStock).length);
                animateCounter('#pending-invoices', invoices.filter(i => i.status === 'Pending').length);
                
                const monthlyRevenue = calculateMonthlyRevenue();
                $('#monthly-revenue').text('$' + monthlyRevenue.toFixed(2));

                loadRecentActivities();
                loadLowStockAlerts();
                loadRecentOrders();
            }

            function animateCounter(selector, targetValue) {
                const element = $(selector);
                const currentValue = parseInt(element.text()) || 0;
                const increment = targetValue > currentValue ? 1 : -1;
                const duration = 1000;
                const steps = Math.abs(targetValue - currentValue);
                const stepDuration = duration / steps;

                let current = currentValue;
                const timer = setInterval(() => {
                    current += increment;
                    element.text(current);
                    
                    if (current === targetValue) {
                        clearInterval(timer);
                    }
                }, stepDuration);
            }

            function calculateMonthlyRevenue() {
                const currentMonth = new Date().getMonth();
                const currentYear = new Date().getFullYear();
                return invoices
                    .filter(i => {
                        const invoiceDate = new Date(i.date);
                        return invoiceDate.getMonth() === currentMonth && 
                               invoiceDate.getFullYear() === currentYear &&
                               i.status === 'Paid';
                    })
                    .reduce((sum, i) => sum + i.total, 0);
            }

            function loadRecentActivities() {
                const activities = JSON.parse(localStorage.getItem('activities')) || [];
                const container = $('#recent-activities');
                container.empty();

                if (activities.length === 0) {
                    container.append('<p class="text-gray-500">No recent activities</p>');
                    return;
                }

                activities.slice(-5).reverse().forEach((activity, index) => {
                    const activityElement = $(`
                        <div class="flex items-center p-3 bg-gray-50 rounded-lg animate-fade-in" style="animation-delay: ${index * 0.1}s">
                            <div class="flex-1">
                                <div class="text-sm">${activity.message}</div>
                                <div class="text-xs text-gray-500">${new Date(activity.timestamp).toLocaleString()}</div>
                            </div>
                        </div>
                    `);
                    container.append(activityElement);
                });
            }

            function loadLowStockAlerts() {
                const lowStockProducts = products.filter(p => p.stock <= p.minStock);
                const container = $('#low-stock-alerts');
                container.empty();

                if (lowStockProducts.length === 0) {
                    container.append('<p class="text-gray-500">No low stock alerts</p>');
                    return;
                }

                lowStockProducts.forEach((product, index) => {
                    const alertElement = $(`
                        <div class="flex justify-between items-center p-3 bg-red-50 rounded-lg animate-fade-in" style="animation-delay: ${index * 0.1}s">
                            <div>
                                <div class="font-medium text-red-900">${product.name}</div>
                                <div class="text-sm text-red-700">SKU: ${product.sku}</div>
                            </div>
                            <div class="text-right">
                                <div class="font-medium text-red-600">${product.stock} left</div>
                                <div class="text-sm text-red-500">Min: ${product.minStock}</div>
                            </div>
                        </div>
                    `);
                    container.append(alertElement);
                });

                // Update notification count
                if (lowStockProducts.length > 0) {
                    $('#notification-count').text(lowStockProducts.length).removeClass('hidden');
                } else {
                    $('#notification-count').addClass('hidden');
                }
            }

            function loadRecentOrders() {
                const recentOrders = orders.slice(-5).reverse();
                const container = $('#recent-orders');
                container.empty();

                if (recentOrders.length === 0) {
                    container.append('<p class="text-gray-500">No recent orders</p>');
                    return;
                }

                recentOrders.forEach((order, index) => {
                    const orderElement = $(`
                        <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg animate-fade-in" style="animation-delay: ${index * 0.1}s">
                            <div>
                                <div class="font-medium">#${order.id}</div>
                                <div class="text-sm text-gray-500">${order.customerName}</div>
                            </div>
                            <div class="text-right">
                                <div class="font-medium">$${order.total.toFixed(2)}</div>
                                <div class="text-sm text-gray-500">${order.date}</div>
                            </div>
                        </div>
                    `);
                    container.append(orderElement);
                });
            }

            function loadCustomers() {
                // Placeholder for customer loading
            }

            function loadOrders() {
                // Placeholder for order loading
            }

            function loadInvoices() {
                // Placeholder for invoice loading
            }

            function loadPurchases() {
                // Placeholder for purchase order loading
            }

            function loadAdjustments() {
                // Placeholder for stock adjustment loading
            }

            function updateReports() {
                // Placeholder for reports update
            }

            function loadSettings() {
                // Placeholder for settings loading
            }

            function addActivity(activity) {
                const activities = JSON.parse(localStorage.getItem('activities')) || [];
                activities.push({
                    id: Date.now(),
                    message: activity,
                    timestamp: new Date().toISOString()
                });
                
                // Keep only last 100 activities
                if (activities.length > 100) {
                    activities.splice(0, activities.length - 100);
                }
                
                localStorage.setItem('activities', JSON.stringify(activities));
            }

            function showNotification(message, type = 'info') {
                const colors = {
                    success: 'bg-green-500',
                    error: 'bg-red-500',
                    info: 'bg-blue-500',
                    warning: 'bg-yellow-500'
                };

                const notification = $(`
                    <div class="fixed top-4 right-4 ${colors[type]} text-white px-6 py-3 rounded-lg shadow-lg z-50 animate-slide-in">
                        <div class="flex items-center">
                            <i class="fas fa-${type === 'success' ? 'check' : type === 'error' ? 'times' : 'info'}-circle mr-2"></i>
                            ${message}
                        </div>
                    </div>
                `);

                $('body').append(notification);

                setTimeout(() => {
                    notification.addClass('animate-fade-out');
                    setTimeout(() => notification.remove(), 300);
                }, 3000);
            }

            // Search functionality
            $('#global-search, #mobile-search').on('input', function() {
                const searchTerm = $(this).val().toLowerCase();
                // Implement global search logic here
                if (searchTerm.length > 2) {
                    // Show search results with animation
                }
            });

            // Initialize with dashboard
            $('.nav-item[data-section="dashboard"]').click();
        });
    </script>
</body>
</html>