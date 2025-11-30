<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Alert - Job Scraper</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
    
    <div class="max-w-md w-full bg-white rounded-lg shadow-lg p-8">
        <h1 class="text-2xl font-bold text-gray-900 mb-2">Test Job Alert</h1>
        <p class="text-sm text-gray-600 mb-6">Quick test - No terminal needed!</p>
        
        <!-- Alert Form -->
        <form id="test-form" class="space-y-4">
            
            <!-- Name -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                <input type="text" 
                       id="user_name" 
                       value="Ahmed Test"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <!-- Email -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" 
                       id="user_email" 
                       value="ahmed@test.com"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <!-- Phone -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">WhatsApp Number</label>
                <input type="tel" 
                       id="user_phone" 
                       value="+212600000000"
                       placeholder="+212600000000"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                <p class="text-xs text-gray-500 mt-1">Replace with your number!</p>
            </div>

            <!-- Keyword -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Job Keyword</label>
                <input type="text" 
                       id="keyword" 
                       value="Developer"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <!-- Location -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Location (optional)</label>
                <input type="text" 
                       id="location" 
                       value="Casablanca"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <!-- Job Types -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Job Types (optional)</label>
                <div class="space-y-2">
                    <label class="flex items-center">
                        <input type="checkbox" name="job_types" value="CDI" checked class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700">CDI</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="job_types" value="CDD" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700">CDD</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="job_types" value="Stage" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700">Stage</span>
                    </label>
                </div>
            </div>

            <!-- Submit Button -->
            <button type="submit" 
                    id="submit-btn"
                    class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 font-medium">
                Create Alert & Test
            </button>

            <!-- Message Area -->
            <div id="message" class="hidden"></div>

        </form>

        <!-- Quick Links -->
        <div class="mt-6 pt-6 border-t border-gray-200">
            <p class="text-xs text-gray-600 mb-2">Quick Actions:</p>
            <div class="space-y-2">
                <a href="/" class="block text-sm text-blue-600 hover:text-blue-800">← Back to Dashboard</a>
                <button onclick="triggerScrape()" class="block text-sm text-green-600 hover:text-green-800">Trigger Manual Scrape</button>
                <button onclick="checkQueue()" class="block text-sm text-purple-600 hover:text-purple-800">Check Queue Status</button>
            </div>
        </div>

        <!-- Response Details -->
        <div id="response-details" class="mt-6 hidden">
            <h3 class="text-sm font-medium text-gray-900 mb-2">Response Details:</h3>
            <pre id="response-json" class="bg-gray-50 p-3 rounded text-xs overflow-auto max-h-48"></pre>
        </div>

    </div>

    <script>
        const API_URL = '/api/v1';

        // Create Alert
        document.getElementById('test-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const submitBtn = document.getElementById('submit-btn');
            const messageDiv = document.getElementById('message');
            const responseDiv = document.getElementById('response-details');
            const responseJson = document.getElementById('response-json');
            
            // Disable button
            submitBtn.disabled = true;
            submitBtn.textContent = 'Creating...';
            
            // Get form data
            const jobTypes = Array.from(document.querySelectorAll('input[name="job_types"]:checked'))
                                 .map(cb => cb.value);
            
            const data = {
                user_name: document.getElementById('user_name').value,
                user_email: document.getElementById('user_email').value,
                user_phone: document.getElementById('user_phone').value,
                keyword: document.getElementById('keyword').value,
                location: document.getElementById('location').value || null,
                job_types: jobTypes,
                sources: ['rekrute', 'emploi', 'mjob']
            };
            
            try {
                const response = await fetch(`${API_URL}/alerts`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                });
                
                const result = await response.json();
                
                // Show response
                responseDiv.classList.remove('hidden');
                responseJson.textContent = JSON.stringify(result, null, 2);
                
                if (response.ok) {
                    messageDiv.className = 'p-4 rounded-md bg-green-50 border border-green-200';
                    messageDiv.innerHTML = `
                        <p class="text-sm font-medium text-green-800">Success!</p>
                        <p class="text-xs text-green-700 mt-1">Alert created with ID: ${result.data.id}</p>
                        <p class="text-xs text-green-700">Queue worker will scrape jobs automatically</p>
                        <p class="text-xs text-green-700 mt-2">Check WhatsApp: ${data.user_phone}</p>
                    `;
                } else {
                    messageDiv.className = 'p-4 rounded-md bg-red-50 border border-red-200';
                    messageDiv.innerHTML = `
                        <p class="text-sm font-medium text-red-800">Error!</p>
                        <p class="text-xs text-red-700 mt-1">${result.message || 'Failed to create alert'}</p>
                    `;
                }
                
                messageDiv.classList.remove('hidden');
                
            } catch (error) {
                console.error('Error:', error);
                messageDiv.className = 'p-4 rounded-md bg-red-50 border border-red-200';
                messageDiv.innerHTML = `
                    <p class="text-sm font-medium text-red-800">Network Error!</p>
                    <p class="text-xs text-red-700 mt-1">${error.message}</p>
                    <p class="text-xs text-red-700 mt-2">Make sure Laravel is running: php artisan serve</p>
                `;
                messageDiv.classList.remove('hidden');
                
                responseDiv.classList.remove('hidden');
                responseJson.textContent = error.stack;
            } finally {
                // Re-enable button
                submitBtn.disabled = false;
                submitBtn.textContent = 'Create Alert & Test';
            }
        });

        // Trigger manual scrape
        async function triggerScrape() {
            const alertId = prompt('Enter Alert ID (or leave empty for last alert):');
            
            try {
                let url = `${API_URL}/alerts`;
                
                // Get alerts to find ID if not provided
                const alertsResponse = await fetch(url);
                const alertsData = await alertsResponse.json();
                
                if (!alertsData.data || alertsData.data.length === 0) {
                    alert('No alerts found. Create an alert first!');
                    return;
                }
                
                const id = alertId || alertsData.data[0].id;
                
                // Trigger scrape
                const response = await fetch(`${API_URL}/alerts/${id}/scrape`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json'
                    }
                });
                
                const result = await response.json();
                
                if (response.ok) {
                    alert(`Scraping triggered for alert #${id}\n\nCheck Terminal 2 (queue worker) for progress!`);
                } else {
                    alert(`Error: ${result.message}`);
                }
                
            } catch (error) {
                alert(`Network error: ${error.message}\n\nMake sure Laravel is running!`);
            }
        }

        // Check queue status
        async function checkQueue() {
            try {
                const response = await fetch(`${API_URL}/alerts`);
                const data = await response.json();
                
                if (response.ok) {
                    const totalAlerts = data.data.length;
                    const activeAlerts = data.data.filter(a => a.is_active).length;
                    
                    alert(`Queue Status:\n\n` +
                          `Total Alerts: ${totalAlerts}\n` +
                          `Active Alerts: ${activeAlerts}\n\n` +
                          `Check Terminal 2 for queue worker logs!`);
                } else {
                    alert(`Error: ${data.message}`);
                }
            } catch (error) {
                alert(`Network error: ${error.message}\n\nMake sure Laravel is running!`);
            }
        }

        // Auto-fill with test data
        console.log('Test page loaded!');
        console.log('Form is pre-filled with test data. Just click "Create Alert & Test"!');
        console.log('\nMake sure you have:');
        console.log('1. Terminal 1: cd whatsapp-service && npm start');
        console.log('2. Terminal 2: php artisan queue:work');
        console.log('3. Terminal 3: php artisan serve');
    </script>

</body>
</html>