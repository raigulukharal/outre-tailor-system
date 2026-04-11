<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>OUTRE Tailor System - Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <style>
        .toast { position: fixed; top: 20px; right: 20px; padding: 12px 24px; border-radius: 8px; color: white; z-index: 1000; animation: slideIn 0.3s ease; }
        .toast.success { background: #10b981; }
        .toast.error { background: #ef4444; }
        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
    </style>
</head>
<body class="bg-gray-100">
    <div id="toast"></div>
    
    <div class="min-h-screen flex items-center justify-center">
        <div class="max-w-md w-full bg-white rounded-xl shadow-lg p-8">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-indigo-900">OUTRE Tailor</h1>
                <p class="text-gray-600 mt-2">Sign in to your account</p>
            </div>
            
            <form id="loginForm">
                @csrf
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Email Address</label>
                    <input type="email" name="email" id="email" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" 
                           placeholder=""
                           autocomplete="off"
                           required>
                </div>
                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Password</label>
                    <input type="password" name="password" id="password" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" 
                           placeholder="••••••••"
                           autocomplete="off"
                           required>
                </div>
                <div class="mb-4 flex items-center justify-between">
                    <label class="flex items-center">
                        <input type="checkbox" name="remember" class="mr-2">
                        <span class="text-sm text-gray-600">Remember me</span>
                    </label>
                </div>
                <button type="submit" id="loginBtn" class="w-full bg-indigo-900 text-white font-bold py-2 px-4 rounded-lg hover:bg-indigo-800 transition duration-200">
                    Login
                </button>
            </form>
            
            
        </div>
    </div>

    <script>
        const form = document.getElementById('loginForm');
        const btn = document.getElementById('loginBtn');
        const toast = document.getElementById('toast');
        
        function showToast(message, type) {
            toast.innerHTML = `<div class="toast ${type}">${message}</div>`;
            setTimeout(() => {
                toast.innerHTML = '';
            }, 3000);
        }
        
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            btn.disabled = true;
            btn.innerHTML = 'Loading...';
            
            const formData = new FormData(form);
            const data = {
                email: formData.get('email'),
                password: formData.get('password'),
                remember: formData.get('remember') ? true : false
            };
            
            try {
                const response = await axios.post('{{ route("login") }}', data);
                if (response.data.success) {
                    showToast('Login successful! Redirecting...', 'success');
                    setTimeout(() => {
                        window.location.href = response.data.redirect;
                    }, 1000);
                }
            } catch (error) {
                let message = 'Login failed';
                if (error.response?.data?.message) {
                    message = error.response.data.message;
                } else if (error.response?.status === 401) {
                    message = 'Invalid email or password';
                }
                showToast(message, 'error');
                btn.disabled = false;
                btn.innerHTML = 'Login';
            }
        });
        
        // Clear any autofilled values on page load
        window.addEventListener('load', function() {
            document.getElementById('email').value = '';
            document.getElementById('password').value = '';
        });
    </script>
</body>
</html>