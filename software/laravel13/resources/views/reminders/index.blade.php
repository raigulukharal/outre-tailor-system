@extends('layouts.app')
@section('content')
<div>
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">📅 Tomorrow's Delivery Reminders</h1>
        <p class="text-gray-500 text-sm" id="reminderDate"></p>
    </div>
    
    <!-- Stats Card for Reminders -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div class="bg-gradient-to-r from-indigo-900 to-indigo-800 rounded-xl shadow-lg p-6 text-white">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-indigo-200 text-sm">Total Orders for Tomorrow</p>
                    <p class="text-4xl font-bold mt-2" id="totalReminders">0</p>
                </div>
                <div class="bg-indigo-700 p-3 rounded-full">
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
                    <p class="text-4xl font-bold mt-2" id="totalDressesReminder">0</p>
                </div>
                <div class="bg-emerald-500 p-3 rounded-full">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Action Buttons -->
    <div class="print-buttons mb-4 flex flex-wrap gap-3 no-print">
        <button onclick="window.print()" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition">
            🖨️ Print
        </button>
        <button id="downloadPDF" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition">
            📄 Download PDF
        </button>
        <button id="downloadImage" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
            🖼️ Download Image
        </button>
        <button id="whatsappShare" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
            📱 WhatsApp Share
        </button>
    </div>
    
    <!-- Loading Spinner -->
    <div id="loadingSpinner" class="flex justify-center py-12">
        <div class="loading-spinner"></div>
    </div>
    
    <!-- Reminders Container -->
    <div id="remindersContainer" class="print-area">
        <!-- Reminders will be loaded here -->
    </div>
</div>

@push('scripts')
<script>
    let remindersData = [];
    
    // Set reminder date header
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    document.getElementById('reminderDate').innerHTML = `Reminders for: <strong>${tomorrow.toLocaleDateString('en-PK', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })}</strong>`;
    
    async function loadReminders() {
        const spinner = document.getElementById('loadingSpinner');
        const container = document.getElementById('remindersContainer');
        
        spinner.classList.remove('hidden');
        
        try {
            const response = await axios.get('/reminders/fetch', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });
            
            console.log('API Response:', response.data);
            
            const orders = response.data.orders || [];
            remindersData = orders;
            
            // Update stats
            updateReminderStats(orders);
            
            if(orders.length === 0) {
                container.innerHTML = `
                    <div class="bg-white rounded-xl shadow-md p-12 text-center">
                        <svg class="w-20 h-20 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <h3 class="text-xl font-semibold text-gray-600 mb-2">No Deliveries Tomorrow</h3>
                        <p class="text-gray-500">No orders scheduled for delivery on ${tomorrow.toLocaleDateString()}</p>
                    </div>
                `;
                return;
            }
            
            // Display orders - Only Name and Serial Number
            container.innerHTML = `
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                    ${orders.map(order => `
                        <div class="bg-white rounded-xl shadow-md hover:shadow-lg transition-all duration-300 overflow-hidden">
                            <div class="bg-gradient-to-r from-indigo-900 to-indigo-800 px-5 py-4">
                                <div class="text-center">
                                    <h3 class="font-bold text-xl text-white mb-1">${escapeHtml(order.name)}</h3>
                                    <div class="inline-block bg-emerald-500 text-white text-sm font-semibold px-3 py-1 rounded-full mt-2">
                                        Suit #${order.serial_no}
                                    </div>
                                </div>
                            </div>
                            <div class="px-5 py-3 bg-gray-50 border-t border-gray-100">
                                <div class="flex justify-center items-center text-sm text-gray-600">
                                    <svg class="w-4 h-4 mr-1 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <span>Delivery: ${new Date(order.delivery_date).toLocaleDateString()}</span>
                                </div>
                            </div>
                        </div>
                    `).join('')}
                </div>
            `;
            
        } catch(error) {
            console.error('API Error:', error);
            container.innerHTML = `
                <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg">
                    <p class="text-red-700">Failed to load reminders: ${error.response?.data?.message || error.message}</p>
                </div>
            `;
            showToast('Failed to load reminders', 'error');
        } finally {
            spinner.classList.add('hidden');
        }
    }
    
    function updateReminderStats(orders) {
        // Total orders for tomorrow
        document.getElementById('totalReminders').innerText = orders.length;
        
        // Total dresses
        const totalDresses = orders.reduce((sum, order) => sum + parseInt(order.dress_no || 0), 0);
        document.getElementById('totalDressesReminder').innerText = totalDresses;
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
    
    // PDF Download
    document.getElementById('downloadPDF')?.addEventListener('click', async () => {
        const element = document.querySelector('.print-area');
        if(!element) return;
        
        const canvas = await html2canvas(element, { scale: 2, backgroundColor: '#ffffff' });
        const imgData = canvas.toDataURL('image/png');
        const { jsPDF } = window.jspdf;
        const pdf = new jsPDF('p', 'mm', 'a4');
        const imgProps = pdf.getImageProperties(imgData);
        const pdfWidth = pdf.internal.pageSize.getWidth();
        const pdfHeight = (imgProps.height * pdfWidth) / imgProps.width;
        pdf.addImage(imgData, 'PNG', 0, 0, pdfWidth, pdfHeight);
        pdf.save('outre-reminders-' + new Date().toISOString().slice(0,10) + '.pdf');
    });
    
    // Image Download
    document.getElementById('downloadImage')?.addEventListener('click', async () => {
        const element = document.querySelector('.print-area');
        if(!element) return;
        
        const canvas = await html2canvas(element, { scale: 2 });
        const link = document.createElement('a');
        link.download = 'outre-reminders-' + new Date().toISOString().slice(0,10) + '.png';
        link.href = canvas.toDataURL();
        link.click();
    });
    
    // WhatsApp Share
    document.getElementById('whatsappShare')?.addEventListener('click', () => {
        if(remindersData.length === 0) {
            showToast('No reminders to share', 'error');
            return;
        }
        
        let message = "🔔 *OUTRE Tailor - Tomorrow's Delivery Reminders* 🔔\n\n";
        message += `📅 Date: ${tomorrow.toLocaleDateString()}\n`;
        message += `📊 Total Orders: ${remindersData.length}\n`;
        const totalDresses = remindersData.reduce((sum, order) => sum + parseInt(order.dress_no || 0), 0);
        message += `👗 Total Dresses: ${totalDresses}\n\n`;
        message += "━━━━━━━━━━━━━━━━━━━━\n\n";
        
        remindersData.forEach((order, index) => {
            message += `👤 *${index + 1}. ${order.name}*\n`;
            message += `🔢 Suit #${order.serial_no}\n`;
            message += `━━━━━━━━━━━━━━━━━━━━\n\n`;
        });
        
        message += `\n📌 Please ensure all suits are ready for delivery.\n`;
        message += `📍 OUTRE Tailor Management System`;
        
        const url = `https://wa.me/?text=${encodeURIComponent(message)}`;
        window.open(url, '_blank');
    });
    
    // Load reminders on page load
    loadReminders();
    
    // Auto refresh every 5 minutes
    setInterval(loadReminders, 300000);
</script>
@endpush
@endsection