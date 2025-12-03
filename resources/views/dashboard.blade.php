<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Moroccan Job Scraper</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 py-6 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold text-gray-900">Moroccan Job Scraper</h1>
            <p class="mt-1 text-sm text-gray-600">Free WhatsApp job alerts from Rekrute, Emploi.ma & M-job</p>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 py-8 sm:px-6 lg:px-8">
        
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-8">
            <!-- Total Alerts -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Active Alerts</dt>
                                <dd class="text-lg font-semibold text-gray-900" id="total-alerts">0</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Jobs -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Jobs</dt>
                                <dd class="text-lg font-semibold text-gray-900" id="total-jobs">0</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- New Jobs Today -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">New Today</dt>
                                <dd class="text-lg font-semibold text-gray-900" id="new-today">0</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notifications Sent -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">WhatsApp Sent</dt>
                                <dd class="text-lg font-semibold text-gray-900" id="notifications-sent">0</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            
            <!-- Create Alert Form -->
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Create Job Alert</h2>
                
                <form id="create-alert-form" class="space-y-4">
                    <!-- Name -->
                    <div>
                        <label for="user_name" class="block text-sm font-medium text-gray-700">Name</label>
                        <input type="text" id="user_name" name="user_name" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2 border">
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="user_email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" id="user_email" name="user_email" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2 border">
                    </div>

                    <!-- Phone -->
                    <div>
                        <label for="user_phone" class="block text-sm font-medium text-gray-700">WhatsApp Number</label>
                        <input type="tel" id="user_phone" name="user_phone" placeholder="+212600000000" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2 border">
                        <p class="mt-1 text-xs text-gray-500">Format: +212600000000</p>
                    </div>

                    <!-- Keyword -->
                    <div>
                        <label for="keyword" class="block text-sm font-medium text-gray-700">Job Keyword</label>
                        <input type="text" id="keyword" name="keyword" placeholder="e.g., Laravel Developer" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2 border">
                    </div>

                    <!-- Location -->
                    <div>
                        <label for="location" class="block text-sm font-medium text-gray-700">Location (Optional)</label>
                        <input type="text" id="location" name="location" placeholder="e.g., Casablanca"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2 border">
                    </div>

                    <!-- Job Types -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Job Types (Optional)</label>
                        <div class="space-y-2">
                            <label class="inline-flex items-center mr-4">
                                <input type="checkbox" name="job_types[]" value="CDI" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">CDI</span>
                            </label>
                            <label class="inline-flex items-center mr-4">
                                <input type="checkbox" name="job_types[]" value="CDD" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">CDD</span>
                            </label>
                            <label class="inline-flex items-center mr-4">
                                <input type="checkbox" name="job_types[]" value="Stage" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">Stage</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="job_types[]" value="Freelance" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">Freelance</span>
                            </label>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div>
                        <button type="submit" 
                                class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Create Alert
                        </button>
                    </div>

                    <!-- Success/Error Messages -->
                    <div id="form-message" class="hidden"></div>
                </form>
            </div>

            <!-- Recent Alerts -->
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Your Alerts</h2>
                
                <div id="alerts-list" class="space-y-3">
                    <!-- Alerts will be loaded here -->
                    <div class="text-center py-8 text-gray-500">
                        <p>No alerts yet. Create your first alert!</p>
                    </div>
                </div>
            </div>

        </div>

        <!-- Recent Jobs -->
        <div class="mt-8 bg-white shadow rounded-lg p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Recent Jobs</h2>
            
            <div id="jobs-list" class="space-y-4">
                <!-- Jobs will be loaded here -->
                <div class="text-center py-8 text-gray-500">
                    <p>No jobs scraped yet. Create an alert to start!</p>
                </div>
            </div>

            <!-- Pagination -->
            <nav class="flex items-center justify-between border-t border-gray-200 pt-4 mt-6" id="pagination" style="display: none;">
                <div class="flex-1 flex justify-between sm:hidden">
                    <button id="prev-mobile" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Previous
                    </button>
                    <button id="next-mobile" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Next
                    </button>
                </div>
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700">
                            Showing <span id="from">0</span> to <span id="to">0</span> of <span id="total">0</span> results
                        </p>
                    </div>
                    <div>
                        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" id="page-numbers"></nav>
                    </div>
                </div>
            </nav>
        </div>

    </main>

    <!-- Footer -->
    <footer class="bg-white border-t mt-12">
        <div class="max-w-7xl mx-auto px-4 py-6 sm:px-6 lg:px-8">
            <p class="text-center text-gray-500 text-sm">
                Moroccan Job Scraper - FREE WhatsApp Notifications
            </p>
        </div>
    </footer>

    <!-- JavaScript -->
    <script>
        // API Base URL
        const API_URL = '/api/v1';
        let currentPage = 1;

        // Load Statistics
        async function loadStatistics() {
            try {
                const response = await fetch(`${API_URL}/jobs/statistics`);
                const data = await response.json();
                
                document.getElementById('total-alerts').textContent = data.total_alerts || 0;
                document.getElementById('total-jobs').textContent = data.total_jobs || 0;
                document.getElementById('new-today').textContent = data.new_today || 0;
                document.getElementById('notifications-sent').textContent = data.notifications_sent || 0;
            } catch (error) {
                console.error('Error loading statistics:', error);
            }
        }

        // Load Alerts
        async function loadAlerts() {
            try {
                const response = await fetch(`${API_URL}/alerts`);
                const data = await response.json();
                
                const alertsList = document.getElementById('alerts-list');
                
                if (data.data && data.data.length > 0) {
                    alertsList.innerHTML = data.data.map(alert => `
                        <div class="border rounded-lg p-4 ${alert.is_active ? 'border-green-200 bg-green-50' : 'border-gray-200 bg-gray-50'}">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <h3 class="font-medium text-gray-900">${alert.keyword}</h3>
                                    <p class="text-sm text-gray-600 mt-1">
                                        ${alert.location || 'All Morocco'} • ${alert.user_name}
                                    </p>
                                    <p class="text-xs text-gray-500 mt-1">
                                        Created: ${new Date(alert.created_at).toLocaleDateString()}
                                    </p>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${alert.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'}">
                                        ${alert.is_active ? 'Active' : 'Inactive'}
                                    </span>
                                    <button onclick="deleteAlert(${alert.id})" class="text-red-600 hover:text-red-800">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    `).join('');
                } else {
                    alertsList.innerHTML = '<div class="text-center py-8 text-gray-500"><p>No alerts yet. Create your first alert!</p></div>';
                }
            } catch (error) {
                console.error('Error loading alerts:', error);
            }
        }

        // Load Recent Jobs with Pagination
        async function loadRecentJobs(page = 1) {
            try {
                const response = await fetch(`${API_URL}/jobs?page=${page}`);
                const data = await response.json();
                
                const jobsList = document.getElementById('jobs-list');
                const pagination = document.getElementById('pagination');
                
                if (data.data && data.data.length > 0) {
                    jobsList.innerHTML = data.data.map(job => `
                        <div class="border rounded-lg p-4 hover:border-blue-300 transition">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <h3 class="font-medium text-gray-900">${job.title}</h3>
                                    <p class="text-sm text-gray-600 mt-1">${job.company} • ${job.location}</p>
                                    <div class="flex items-center space-x-2 mt-2">
                                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                            ${job.job_type || 'Not specified'}
                                        </span>
                                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                            ${job.source}
                                        </span>
                                        ${job.salary ? `<span class="text-xs text-gray-600">${job.salary}</span>` : ''}
                                    </div>
                                    <p class="text-xs text-gray-500 mt-2">
                                        Posted: ${new Date(job.created_at).toLocaleDateString()}
                                    </p>
                                </div>
                                <a href="${job.url}" target="_blank" 
                                   class="ml-4 inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                    View Job
                                </a>
                            </div>
                        </div>
                    `).join('');

                    // Show pagination if more than 1 page
                    if (data.last_page > 1) {
                        pagination.style.display = 'flex';
                        updatePagination(data);
                    } else {
                        pagination.style.display = 'none';
                    }
                } else {
                    jobsList.innerHTML = '<div class="text-center py-8 text-gray-500"><p>No jobs scraped yet. Create an alert to start!</p></div>';
                    pagination.style.display = 'none';
                }
            } catch (error) {
                console.error('Error loading jobs:', error);
            }
        }

        function updatePagination(data) {
            currentPage = data.current_page;
            
            document.getElementById('from').textContent = data.from || 0;
            document.getElementById('to').textContent = data.to || 0;
            document.getElementById('total').textContent = data.total || 0;
            
            const prevMobile = document.getElementById('prev-mobile');
            const nextMobile = document.getElementById('next-mobile');
            
            prevMobile.disabled = data.current_page === 1;
            prevMobile.onclick = () => loadRecentJobs(data.current_page - 1);
            
            nextMobile.disabled = data.current_page === data.last_page;
            nextMobile.onclick = () => loadRecentJobs(data.current_page + 1);
            
            const pageNumbers = document.getElementById('page-numbers');
            let html = '';
            
            html += `
                <button onclick="loadRecentJobs(${data.current_page - 1})" 
                        ${data.current_page === 1 ? 'disabled' : ''}
                        class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50">
                    Previous
                </button>
            `;
            
            const start = Math.max(1, data.current_page - 2);
            const end = Math.min(data.last_page, data.current_page + 2);
            
            for (let i = start; i <= end; i++) {
                html += `
                    <button onclick="loadRecentJobs(${i})"
                            class="relative inline-flex items-center px-4 py-2 border text-sm font-medium ${i === data.current_page ? 'z-10 bg-blue-50 border-blue-500 text-blue-600' : 'border-gray-300 bg-white text-gray-700 hover:bg-gray-50'}">
                        ${i}
                    </button>
                `;
            }
            
            html += `
                <button onclick="loadRecentJobs(${data.current_page + 1})"
                        ${data.current_page === data.last_page ? 'disabled' : ''}
                        class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50">
                    Next
                </button>
            `;
            
            pageNumbers.innerHTML = html;
        }

        // Create Alert
        document.getElementById('create-alert-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const formData = new FormData(e.target);
            const data = {
                user_name: formData.get('user_name'),
                user_email: formData.get('user_email'),
                user_phone: formData.get('user_phone'),
                keyword: formData.get('keyword'),
                location: formData.get('location') || null,
                job_types: formData.getAll('job_types[]'),
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
                
                const messageDiv = document.getElementById('form-message');
                
                if (response.ok) {
                    messageDiv.className = 'p-4 rounded-md bg-green-50 border border-green-200';
                    messageDiv.innerHTML = '<p class="text-sm text-green-800">Alert created successfully! You will receive WhatsApp notifications.</p>';
                    messageDiv.classList.remove('hidden');
                    
                    // Reset form
                    e.target.reset();
                    
                    // Reload data
                    loadStatistics();
                    loadAlerts();
                    loadRecentJobs(1);
                    
                    // Hide message after 5 seconds
                    setTimeout(() => {
                        messageDiv.classList.add('hidden');
                    }, 5000);
                } else {
                    messageDiv.className = 'p-4 rounded-md bg-red-50 border border-red-200';
                    messageDiv.innerHTML = `<p class="text-sm text-red-800">Error: ${result.message || 'Failed to create alert'}</p>`;
                    messageDiv.classList.remove('hidden');
                }
            } catch (error) {
                console.error('Error creating alert:', error);
                const messageDiv = document.getElementById('form-message');
                messageDiv.className = 'p-4 rounded-md bg-red-50 border border-red-200';
                messageDiv.innerHTML = '<p class="text-sm text-red-800">Network error. Please try again.</p>';
                messageDiv.classList.remove('hidden');
            }
        });

        // Delete Alert
        async function deleteAlert(id) {
            if (!confirm('Are you sure you want to delete this alert?')) {
                return;
            }
            
            try {
                const response = await fetch(`${API_URL}/alerts/${id}`, {
                    method: 'DELETE'
                });
                
                if (response.ok) {
                    loadStatistics();
                    loadAlerts();
                } else {
                    alert('Failed to delete alert');
                }
            } catch (error) {
                console.error('Error deleting alert:', error);
                alert('Network error. Please try again.');
            }
        }

        // Load data on page load
        document.addEventListener('DOMContentLoaded', () => {
            loadStatistics();
            loadAlerts();
            loadRecentJobs(1);
            
            // Refresh data every 30 seconds
            setInterval(() => {
                loadStatistics();
                loadAlerts();
            }, 30000);
        });
    </script>
</body>
</html>