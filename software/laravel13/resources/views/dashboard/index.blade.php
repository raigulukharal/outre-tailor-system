@extends('layouts.app')
@section('content')
<div>
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-gradient-to-r from-indigo-900 to-indigo-800 rounded-xl shadow-lg p-6 text-white">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-indigo-200 text-sm">Total Active Orders</p>
                    <p class="text-4xl font-bold mt-2" id="totalActiveOrders">0</p>
                </div>
                <div class="bg-indigo-700 p-3 rounded-full">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="bg-gradient-to-r from-orange-500 to-red-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-orange-100 text-sm">Urgent Deliveries</p>
                    <p class="text-4xl font-bold mt-2" id="urgentOrders">0</p>
                    <p class="text-orange-100 text-xs mt-1">Next 2 days</p>
                </div>
                <div class="bg-orange-600 p-3 rounded-full">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="bg-gradient-to-r from-emerald-600 to-teal-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-emerald-100 text-sm">Total Dresses</p>
                    <p class="text-4xl font-bold mt-2" id="totalDresses">0</p>
                </div>
                <div class="bg-emerald-500 p-3 rounded-full">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>
    
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Active Orders Dashboard</h1>
    
    <!-- Search Bar -->
    <div class="mb-6">
        <div class="relative">
            <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            <input type="text" id="searchInput" placeholder="Search by name, phone, serial no, reference name or reference phone..." 
                   class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
        </div>
    </div>

    <!-- Loading Spinner -->
    <div id="loadingSpinner" class="flex justify-center py-12 hidden">
        <div class="loading-spinner"></div>
    </div>

    <!-- Orders Container -->
    <div id="ordersContainer" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Orders will be loaded here -->
    </div>
</div>

{{-- Update Modal for Dashboard --}}
<div id="updateModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md p-6 transform transition-all">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-gray-800">✏️ Edit Order</h3>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <form id="updateForm">
            <input type="hidden" id="update_id">
            
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-medium mb-2">Customer Name *</label>
                <input type="text" id="edit_name" required 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-medium mb-2">Phone *</label>
                <input type="text" id="edit_phone" required 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-medium mb-2">Address</label>
                <textarea id="edit_address" rows="2" 
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-medium mb-2">Dress No *</label>
                <input type="number" id="edit_dress_no" required 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-medium mb-2">Delivery Date *</label>
                <input type="date" id="edit_delivery_date" required 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                <p class="text-xs text-gray-500 mt-1">
                    💡 If delivery date is changed to past, order will move to Completed Orders
                </p>
            </div>
            
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" onclick="closeModal()" 
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition">
                    Cancel
                </button>
                <button type="submit" 
                        class="px-4 py-2 bg-indigo-900 text-white rounded-lg hover:bg-indigo-800 transition">
                    Update Order
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    let currentSearch = '';
    let allOrders = [];

    async function loadOrders(search = '') {
        document.getElementById('loadingSpinner').classList.remove('hidden');
        try {
            const response = await axios.get('{{ route("orders.search") }}', { params: { q: search } });
            const orders = response.data.orders;
            allOrders = orders;
            const container = document.getElementById('ordersContainer');
            
            updateStats(orders);
            
            if(orders.length === 0) {
                container.innerHTML = '<div class="col-span-full text-center py-12 text-gray-500">No active orders found</div>';
                return;
            }
            
            container.innerHTML = orders.map(order => `
                <div class="bg-white rounded-xl shadow-md hover:shadow-lg transition-all duration-300 overflow-hidden" id="order-card-${order.id}">
                    <div class="bg-gradient-to-r from-indigo-900 to-indigo-800 px-5 py-3">
                        <div class="flex justify-between items-center">
                            <div>
                                <h3 class="font-bold text-lg text-white">${escapeHtml(order.name)}</h3>
                                <p class="text-indigo-200 text-sm">📞 ${order.phone}</p>
                            </div>
                            <span class="bg-emerald-500 text-white text-xs font-semibold px-2 py-1 rounded-full">#${order.serial_no}</span>
                        </div>
                    </div>
                    
                    <div class="p-5 space-y-3">
                        <div class="flex justify-between items-center pb-2 border-b border-gray-100">
                            <span class="text-gray-500 text-sm">👗 Dress No:</span>
                            <span class="font-semibold text-gray-800">${order.dress_no}</span>
                        </div>
                        
                        ${order.address ? `
                        <div class="flex justify-between items-start pb-2 border-b border-gray-100">
                            <span class="text-gray-500 text-sm">📍 Address:</span>
                            <span class="text-gray-700 text-sm text-right max-w-[60%]">${escapeHtml(order.address)}</span>
                        </div>
                        ` : ''}
                        
                        ${order.reference_name ? `
                        <div class="flex justify-between items-center pb-2 border-b border-gray-100">
                            <span class="text-gray-500 text-sm">👤 Reference:</span>
                            <span class="text-gray-700 text-sm">${escapeHtml(order.reference_name)}</span>
                        </div>
                        ` : ''}
                        
                        ${order.reference_phone ? `
                        <div class="flex justify-between items-center pb-2 border-b border-gray-100">
                            <span class="text-gray-500 text-sm">📞 Ref Phone:</span>
                            <span class="text-gray-700 text-sm">${order.reference_phone}</span>
                        </div>
                        ` : ''}
                        
                        <div class="flex justify-between items-center pb-2 border-b border-gray-100">
                            <span class="text-gray-500 text-sm">📅 Booking:</span>
                            <span class="text-gray-700 text-sm">${new Date(order.booking_date).toLocaleDateString()}</span>
                        </div>
                        
                        <div class="flex justify-between items-center pt-1">
                            <span class="text-gray-500 text-sm">🚚 Delivery:</span>
                            <span class="font-bold ${isDeliveryUrgent(order.delivery_date) ? 'text-red-600' : 'text-emerald-600'}">
                                ${new Date(order.delivery_date).toLocaleDateString()}
                                ${isDeliveryUrgent(order.delivery_date) ? ' ⚠️' : ''}
                            </span>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="flex justify-end gap-2 pt-3 mt-2 border-t border-gray-100">
                            <button onclick='openEditModal(${order.id}, ${JSON.stringify(order.name)}, ${JSON.stringify(order.phone)}, ${JSON.stringify(order.address || '')}, ${order.dress_no}, ${JSON.stringify(order.delivery_date)})' 
                                    class="text-indigo-600 hover:text-indigo-800 text-sm flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                Edit
                            </button>
                            <button onclick="deleteOrder(${order.id})" 
                                    class="text-red-600 hover:text-red-800 text-sm flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                                Delete
                            </button>
                        </div>
                    </div>
                </div>
            `).join('');
        } catch(error) {
            showToast('Failed to load orders', 'error');
        } finally {
            document.getElementById('loadingSpinner').classList.add('hidden');
        }
    }
    
    function updateStats(orders) {
        document.getElementById('totalActiveOrders').innerText = orders.length;
        
        const urgent = orders.filter(order => {
            const delivery = new Date(order.delivery_date);
            const today = new Date();
            const diffTime = delivery - today;
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            return diffDays <= 2 && diffDays >= 0;
        });
        document.getElementById('urgentOrders').innerText = urgent.length;
        
        const totalDresses = orders.reduce((sum, order) => sum + parseInt(order.dress_no || 0), 0);
        document.getElementById('totalDresses').innerText = totalDresses;
    }
    
    function isDeliveryUrgent(deliveryDate) {
        const today = new Date();
        const delivery = new Date(deliveryDate);
        const diffTime = delivery - today;
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        return diffDays <= 2 && diffDays >= 0;
    }
    
    function escapeHtml(str) {
        if(!str) return '';
        return String(str).replace(/[&<>]/g, function(m) {
            if(m === '&') return '&amp;';
            if(m === '<') return '&lt;';
            if(m === '>') return '&gt;';
            return m;
        });
    }
    
    // Open Edit Modal
    window.openEditModal = (id, name, phone, address, dressNo, deliveryDate) => {
        document.getElementById('update_id').value = id;
        document.getElementById('edit_name').value = name;
        document.getElementById('edit_phone').value = phone;
        document.getElementById('edit_address').value = address || '';
        document.getElementById('edit_dress_no').value = dressNo;
        document.getElementById('edit_delivery_date').value = deliveryDate.split('T')[0];
        
        document.getElementById('updateModal').classList.remove('hidden');
        document.getElementById('updateModal').classList.add('flex');
    };
    
    // Close Modal
    window.closeModal = () => {
        document.getElementById('updateModal').classList.add('hidden');
        document.getElementById('updateModal').classList.remove('flex');
    };
    
    // Update Order
    document.getElementById('updateForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const id = document.getElementById('update_id').value;
        const data = {
            name: document.getElementById('edit_name').value,
            phone: document.getElementById('edit_phone').value,
            address: document.getElementById('edit_address').value,
            dress_no: document.getElementById('edit_dress_no').value,
            delivery_date: document.getElementById('edit_delivery_date').value
        };
        
        const submitBtn = e.target.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = 'Updating...';
        
        try {
            const response = await axios.put(`/orders/${id}`, data);
            
            if(response.data.success) {
                showToast('Order updated successfully', 'success');
                closeModal();
                loadOrders(currentSearch);
            }
        } catch(error) {
            if(error.response?.data?.errors) {
                const errors = error.response.data.errors;
                showToast(Object.values(errors)[0][0], 'error');
            } else {
                showToast('Update failed', 'error');
            }
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    });
    
    // Delete Order
    window.deleteOrder = async (id) => {
        if(!confirm('⚠️ Are you sure you want to delete this order?\n\nThis action cannot be undone!')) return;
        
        try {
            await axios.delete(`/orders/${id}`);
            showToast('Order deleted successfully', 'success');
            document.getElementById(`order-card-${id}`)?.remove();
            
            // Reload stats
            const remainingCards = document.querySelectorAll('#ordersContainer > div').length;
            if(remainingCards === 0) {
                loadOrders(currentSearch);
            } else {
                // Update stats without reload
                const remainingOrders = allOrders.filter(order => order.id !== id);
                updateStats(remainingOrders);
            }
        } catch(error) {
            showToast('Delete failed', 'error');
        }
    };
    
    // Live search with debounce
    let searchTimeout;
    document.getElementById('searchInput').addEventListener('input', (e) => {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            currentSearch = e.target.value;
            loadOrders(currentSearch);
        }, 300);
    });
    
    loadOrders();
</script>
@endpush
@endsection