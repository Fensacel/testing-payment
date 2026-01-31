@extends('admin.layouts.app')

@section('title', 'WhatsApp Settings')

@section('content')
<div class="container mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">WhatsApp Settings</h1>
        <div id="status-badge">
            <!-- Will be populated by JS -->
            <span class="px-3 py-1 rounded-full text-sm font-semibold bg-gray-200 text-gray-700">Checking...</span>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
        <span class="block sm:inline">{{ session('success') }}</span>
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
        <span class="block sm:inline">{{ session('error') }}</span>
    </div>
    @endif

    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            <!-- Connection Status -->
            <div>
                <h2 class="text-lg font-semibold mb-4 border-b pb-2">Connection Status</h2>
                
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">Session Name:</span>
                        <span class="font-mono bg-gray-100 px-2 py-1 rounded text-sm">{{ env('WAHA_SESSION', 'default') }}</span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">Current Status:</span>
                        <span id="status-text" class="font-bold text-gray-800">Unknown</span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">WhatsApp Number:</span>
                        <span id="wa-number" class="text-gray-800">-</span>
                    </div>
                     
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">Push Name:</span>
                        <span id="wa-name" class="text-gray-800">-</span>
                    </div>

                    <div class="mt-6 space-y-2">
                        <!-- Actions -->
                        <div id="action-buttons" class="flex flex-wrap gap-2">
                            <!-- Buttons will be injected by JS based on status -->
                            <form id="form-start" action="{{ route('admin.whatsapp.start') }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="bg-green-500 hover:bg-green-600 text-black px-4 py-2 rounded shadow transition">
                                    <i class="fas fa-play mr-2"></i> Start Session
                                </button>
                            </form>

                            <form id="form-stop" action="{{ route('admin.whatsapp.stop') }}" method="POST" class="inline hidden">
                                @csrf
                                <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-black px-4 py-2 rounded shadow transition">
                                    <i class="fas fa-stop mr-2"></i> Stop Session
                                </button>
                            </form>

                            <form id="form-logout" action="{{ route('admin.whatsapp.logout') }}" method="POST" class="inline hidden">
                                @csrf
                                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded shadow transition">
                                    <i class="fas fa-sign-out-alt mr-2"></i> Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- QR Code Section -->
            <div class="flex flex-col items-center justify-center bg-gray-50 rounded-lg p-6 border border-gray-200">
                <h3 class="text-md font-semibold text-gray-700 mb-4">Scan QR Code</h3>
                
                <div id="qr-container" class="bg-white p-2 rounded shadow-sm border border-gray-200 w-64 h-64 flex items-center justify-center relative">
                    <div id="qr-loading" class="absolute inset-0 flex items-center justify-center bg-white bg-opacity-80 z-10 hidden">
                        <i class="fas fa-spinner fa-spin text-3xl text-blue-500"></i>
                    </div>
                    
                    <img id="qr-image" src="" alt="QR Code" class="w-full h-full object-contain hidden">
                    
                    <div id="qr-placeholder" class="text-center text-gray-400">
                        <i class="fas fa-qrcode text-4xl mb-2"></i>
                        <p id="qr-message" class="text-sm">QR Code will appear here<br>when session needs authentication</p>
                    </div>
                </div>

                <p id="qr-status" class="text-sm text-gray-500 mt-4 text-center">
                    Status: <span class="font-medium">Waiting...</span>
                </p>
            </div>
        </div>
    </div>
    
    <!-- Config Info -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-lg font-semibold mb-4 border-b pb-2">Configuration</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            <div>
                <span class="text-gray-500 block">WAHA URL</span>
                <code class="bg-gray-100 px-2 py-1 rounded">{{ env('WAHA_URL') }}</code>
            </div>
            <div>
                <span class="text-gray-500 block">WAHA Session</span>
                <code class="bg-gray-100 px-2 py-1 rounded">{{ env('WAHA_SESSION') }}</code>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const statusBadge = document.getElementById('status-badge');
        const statusText = document.getElementById('status-text');
        const waNumber = document.getElementById('wa-number');
        const waName = document.getElementById('wa-name');
        const qrImage = document.getElementById('qr-image');
        const qrPlaceholder = document.getElementById('qr-placeholder');
        const qrMessage = document.getElementById('qr-message');
        const qrStatus = document.getElementById('qr-status');
        
        const formStart = document.getElementById('form-start');
        const formStop = document.getElementById('form-stop');
        const formLogout = document.getElementById('form-logout');

        function updateUI(status, data) {
            // Update Status Text
            statusText.textContent = status || 'Unknown';
            
            // Update Badge
            let badgeClass = 'bg-gray-200 text-gray-700';
            if (status === 'WORKING') badgeClass = 'bg-green-100 text-green-800';
            else if (status === 'SCAN_QR_CODE') badgeClass = 'bg-yellow-100 text-yellow-800';
            else if (status === 'STARTING') badgeClass = 'bg-blue-100 text-blue-800';
            else if (status === 'STOPPED') badgeClass = 'bg-red-100 text-red-800';
            else if (status === 'FAILED') badgeClass = 'bg-red-200 text-red-900';

            statusBadge.innerHTML = `<span class="px-3 py-1 rounded-full text-sm font-semibold ${badgeClass}">${status}</span>`;

            // Update Info
            if (data && data.me) {
                waNumber.textContent = data.me.id ? data.me.id.split('@')[0] : '-';
                waName.textContent = data.me.pushName || '-';
            } else {
                waNumber.textContent = '-';
                waName.textContent = '-';
            }

            // Update QR Code
            if (status === 'SCAN_QR_CODE') {
                qrImage.src = "{{ route('admin.whatsapp.qr') }}?t=" + new Date().getTime();
                qrImage.classList.remove('hidden');
                qrPlaceholder.classList.add('hidden');
                qrStatus.innerHTML = '<span class="text-yellow-600"><i class="fas fa-exclamation-triangle"></i> Please scan the QR code</span>';
            } else if (status === 'WORKING') {
                qrImage.classList.add('hidden');
                qrPlaceholder.classList.remove('hidden');
                qrPlaceholder.innerHTML = '<i class="fas fa-check-circle text-4xl mb-2 text-green-500"></i><p class="text-sm text-green-600">WhatsApp Connected</p>';
                qrStatus.innerHTML = '<span class="text-green-600">Connected</span>';
            } else if (status === 'STOPPED') {
                qrImage.classList.add('hidden');
                qrPlaceholder.classList.remove('hidden');
                qrPlaceholder.innerHTML = '<i class="fas fa-power-off text-4xl mb-2 text-red-400"></i><p class="text-sm text-gray-500">Session Stopped<br>Click Start Session to connect</p>';
                qrStatus.innerHTML = '<span class="text-red-600">Stopped</span>';
            } else {
                qrImage.classList.add('hidden');
                qrPlaceholder.classList.remove('hidden');
                qrPlaceholder.innerHTML = '<i class="fas fa-qrcode text-4xl mb-2"></i><p class="text-sm">QR Code will appear here<br>when session needs authentication</p>';
                qrStatus.innerHTML = `Status: <span class="font-medium">${status}</span>`;
            }

            // Update Buttons Visibility
            if (status === 'STOPPED' || status === 'FAILED' || status === 'unknown') {
                formStart.style.display = 'block';
                formStop.style.display = 'none';
                formLogout.style.display = 'none';
            } else {
                formStart.style.display = 'none';
                formStop.style.display = 'block';
                formLogout.style.display = 'block';
            }
        }

        function checkStatus() {
            fetch("{{ route('admin.whatsapp.status') }}")
                .then(response => response.json())
                .then(res => {
                    if (res.success) {
                        updateUI(res.status, res.data);
                    } else {
                        statusText.textContent = 'Error';
                        statusBadge.innerHTML = `<span class="px-3 py-1 rounded-full text-sm font-semibold bg-red-100 text-red-800">Error</span>`;
                        console.error(res.error);
                    }
                })
                .catch(err => console.error('Failed to fetch status', err));
        }

        // Check immediately
        checkStatus();

        // Poll every 5 seconds
        setInterval(checkStatus, 5000);
    });
</script>
@endsection
