{{-- resources/views/orders/create.blade.php --}}
@extends('layouts.app')
@section('content')
<div class="max-w-4xl mx-auto">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Create New Order</h1>
    
    <div class="bg-white rounded-xl shadow-md p-6">
        <form id="orderForm">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-gray-700 font-medium mb-2">Customer Name *</label>
                    <input type="text" name="name" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500" required>
                </div>
                <div>
                    <label class="block text-gray-700 font-medium mb-2">Phone *</label>
                    <input type="text" name="phone" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500" required>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-gray-700 font-medium mb-2">Address</label>
                    <textarea name="address" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"></textarea>
                </div>
                <div>
                    <label class="block text-gray-700 font-medium mb-2">Serial No * (Unique)</label>
                    <input type="text" name="serial_no" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500" required>
                </div>
                <div>
                    <label class="block text-gray-700 font-medium mb-2">Dress No *</label>
                    <input type="number" name="dress_no" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500" required>
                </div>
                <div>
                    <label class="block text-gray-700 font-medium mb-2">Reference Name</label>
                    <input type="text" name="reference_name" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-gray-700 font-medium mb-2">Reference Phone</label>
                    <input type="text" name="reference_phone" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-gray-700 font-medium mb-2">Booking Date *</label>
                    <input type="date" name="booking_date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500" required>
                </div>
                <div>
                    <label class="block text-gray-700 font-medium mb-2">Delivery Date *</label>
                    <input type="date" name="delivery_date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500" required>
                </div>
            </div>
            <div class="mt-6">
                <button type="submit" id="submitBtn" class="bg-indigo-900 text-white px-6 py-2 rounded-lg hover:bg-indigo-800 transition">Save Order</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('orderForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const btn = document.getElementById('submitBtn');
        btn.disabled = true;
        btn.innerHTML = '<div class="loading-spinner w-5 h-5 border-2 inline-block"></div> Saving...';
        
        const formData = new FormData(e.target);
        const data = Object.fromEntries(formData.entries());
        
        try {
            await axios.post('{{ route("orders.store") }}', data);
            showToast('Order created successfully!');
            e.target.reset();
        } catch(error) {
            if(error.response?.data?.errors) {
                const errors = error.response.data.errors;
                const firstError = Object.values(errors)[0][0];
                showToast(firstError, 'error');
            } else {
                showToast('Failed to create order', 'error');
            }
        } finally {
            btn.disabled = false;
            btn.innerHTML = 'Save Order';
        }
    });
</script>
@endpush
@endsection