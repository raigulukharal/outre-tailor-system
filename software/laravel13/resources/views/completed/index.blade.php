@extends('layouts.app')
@section('content')
<div>
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">✅ Completed Orders</h1>
            <p class="text-gray-500 text-sm mt-1">Orders with past delivery dates are automatically moved here</p>
        </div>
        <div class="bg-emerald-100 text-emerald-800 px-4 py-2 rounded-lg text-sm font-semibold">
            Auto-Managed Status
        </div>
    </div>
    
    <!-- Loading Spinner -->
    <div id="loadingSpinner" class="flex justify-center py-12 hidden">
        <div class="loading-spinner"></div>
    </div>
    
    <!-- Completed Orders Container -->
    <div id="completedContainer" class="bg-white rounded-xl shadow-md overflow-hidden">
        <!-- Table will be loaded here -->
    </div>
</div>

{{-- Update Modal --}}
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
                    💡 If delivery date is changed to future, order will automatically move to Dashboard
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
    let currentOrders = [];
    
    // Simple escape function for display only
    function escapeForDisplay(str) {
        if(!str) return '';
        return String(str).replace(/[&<>]/g, function(m) {
            if(m === '&') return '&amp;';
            if(m === '<') return '&lt;';
            if(m === '>') return '&gt;';
            return m;
        });
    }
    
    // Don't escape for modal data - keep raw
    function unescapeForModal(str) {
        if(!str) return '';
        return String(str);
    }
    
    async function loadCompleted() {
        document.getElementById('loadingSpinner').classList.remove('hidden');
        try {
            const response = await axios.get('{{ route("completed-orders.fetch") }}');
            const orders = response.data.orders;
            currentOrders = orders;
            const container = document.getElementById('completedContainer');
            
            if(orders.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-16">
                        <svg class="w-20 h-20 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <h3 class="text-lg font-semibold text-gray-600 mb-2">No Completed Orders</h3>
                        <p class="text-gray-500">Orders with past delivery dates will automatically appear here.</p>
                    </div>
                `;
                return;
            }
            
            container.innerHTML = `
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Serial No</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dress No</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Delivery Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            ${orders.map(order => `
                                <tr id="order-row-${order.id}" class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4">
                                        <div class="font-medium text-gray-900">${escapeForDisplay(order.name)}</div>
                                        ${order.address ? `<div class="text-xs text-gray-500">${escapeForDisplay(order.address.substring(0, 50))}</div>` : ''}
                                    </td>
                                    <td class="px-6 py-4 text-gray-600">${order.phone}</td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-700">
                                            ${order.serial_no}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-gray-600">${order.dress_no}</td>
                                    <td class="px-6 py-4">
                                        <span class="text-red-600 font-medium">
                                            ${new Date(order.delivery_date).toLocaleDateString()}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-800">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            Completed
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 space-x-2">
                                        <button onclick='openUpdateModal(${order.id}, ${JSON.stringify(order.name)}, ${JSON.stringify(order.phone)}, ${JSON.stringify(order.address || '')}, ${order.dress_no}, ${JSON.stringify(order.delivery_date)})' 
                                                class="text-indigo-600 hover:text-indigo-900 transition">
                                            <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                            Edit
                                        </button>
                                        <button onclick="deleteOrder(${order.id})" 
                                                class="text-red-600 hover:text-red-900 transition">
                                            <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                            Delete
                                        </button>
                                    </td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                </div>
                <div class="bg-gray-50 px-6 py-3 text-sm text-gray-500 border-t">
                    Total Completed Orders: ${orders.length}
                </div>
            `;
        } catch(error) {
            console.error('Error:', error);
            showToast('Failed to load completed orders', 'error');
        } finally {
            document.getElementById('loadingSpinner').classList.add('hidden');
        }
    }
    
    window.openUpdateModal = (id, name, phone, address, dressNo, deliveryDate) => {
        // Set values directly - no escaping needed for modal
        document.getElementById('update_id').value = id;
        document.getElementById('edit_name').value = name;
        document.getElementById('edit_phone').value = phone;
        document.getElementById('edit_address').value = address || '';
        document.getElementById('edit_dress_no').value = dressNo;
        document.getElementById('edit_delivery_date').value = deliveryDate.split('T')[0]; // Handle date format
        
        // Show modal
        document.getElementById('updateModal').classList.remove('hidden');
        document.getElementById('updateModal').classList.add('flex');
    };
    
    window.closeModal = () => {
        document.getElementById('updateModal').classList.add('hidden');
        document.getElementById('updateModal').classList.remove('flex');
    };
    
    document.getElementById('updateForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const id = document.getElementById('update_id').value;
        const newDeliveryDate = document.getElementById('edit_delivery_date').value;
        
        const data = {
            name: document.getElementById('edit_name').value,
            phone: document.getElementById('edit_phone').value,
            address: document.getElementById('edit_address').value,
            dress_no: document.getElementById('edit_dress_no').value,
            delivery_date: newDeliveryDate
        };
        
        const submitBtn = e.target.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = 'Updating...';
        
        try {
            const response = await axios.put(`/orders/${id}`, data);
            
            if(response.data.success) {
                const newStatus = response.data.new_status;
                
                if(newStatus === 'active') {
                    showToast('✅ Order updated! Delivery date changed to future. Order moved to Dashboard.', 'success');
                } else {
                    showToast('Order updated successfully', 'success');
                }
                
                closeModal();
                loadCompleted(); // Refresh the list
            }
        } catch(error) {
            if(error.response?.data?.errors) {
                const errors = error.response.data.errors;
                showToast(Object.values(errors)[0][0], 'error');
            } else {
                showToast('Update failed: ' + (error.response?.data?.message || error.message), 'error');
            }
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    });
    
    window.deleteOrder = async (id) => {
        if(!confirm('⚠️ Are you sure you want to delete this order?\n\nThis action cannot be undone!')) return;
        
        try {
            await axios.delete(`/orders/${id}`);
            showToast('Order deleted successfully', 'success');
            document.getElementById(`order-row-${id}`)?.remove();
            
            // Reload if no orders left
            const remainingRows = document.querySelectorAll('#completedContainer tbody tr').length;
            if(remainingRows === 0) {
                loadCompleted();
            }
        } catch(error) {
            showToast('Delete failed: ' + (error.response?.data?.message || error.message), 'error');
        }
    };
    
    // Auto-refresh every 30 seconds
    setInterval(loadCompleted, 30000);
    
    // Load on page load
    loadCompleted();
</script>
@endpush
@endsection