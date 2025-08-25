<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Restaurant</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
        body {
            font-family: 'Inter', sans-serif;
        }
        
        .step-indicator {
            background: linear-gradient(90deg, #3b82f6 0%, #8b5cf6 100%);
        }
        
        .step-completed {
            background: #10b981;
        }
        
        .step-active {
            background: linear-gradient(90deg, #3b82f6 0%, #8b5cf6 100%);
        }
        
        .step-inactive {
            background: #e5e7eb;
        }
        
        .form-transition {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .form-field {
            display: flex;
            flex-direction: column;
        }
        
        .form-label {
            font-size: 0.875rem;
            font-weight: 500;
            color: #374151;
            margin-bottom: 0.5rem;
        }
        
        .form-input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            font-size: 1rem;
            background: white;
            transition: all 0.2s ease-out;
        }
        
        .form-input:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        
        .form-textarea {
            resize: none;
            min-height: 5rem;
        }
        
        .preview-drop-zone {
            border: 2px dashed #d1d5db;
            transition: all 0.3s ease;
        }
        
        .preview-drop-zone:hover,
        .preview-drop-zone.dragover {
            border-color: #3b82f6;
            background-color: #eff6ff;
        }
        
        .time-input-grid {
            display: grid;
            grid-template-columns: 80px 1fr 40px 1fr 120px;
            gap: 1rem;
            align-items: center;
        }
        
        @media (max-width: 768px) {
            .time-input-grid {
                grid-template-columns: 1fr;
                gap: 0.5rem;
            }
        }
        
        .fade-in {
            animation: fadeIn 0.5s ease-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .progress-bar {
            background: linear-gradient(90deg, #10b981 0%, #3b82f6 50%, #8b5cf6 100%);
            height: 4px;
            transition: width 0.3s ease;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Header with Progress -->
    <div class="bg-white shadow-sm border-b sticky top-0 z-40">
        <div class="max-w-4xl mx-auto px-6 py-4">
            <div class="flex justify-between items-center mb-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Create New Restaurant</h1>
                    <p class="text-sm text-gray-600">Step <span id="current-step">1</span> of 4</p>
                </div>
                <button onclick="goBack()" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back
                </button>
            </div>
            
            <!-- Progress Bar -->
            <div class="w-full bg-gray-200 rounded-full h-1">
                <div class="progress-bar rounded-full" id="progress-bar" style="width: 25%"></div>
            </div>
            
            <!-- Step Indicators -->
            <div class="flex justify-between mt-4">
                <div class="flex items-center">
                    <div class="w-8 h-8 rounded-full step-active flex items-center justify-center text-white text-sm font-semibold" id="step-1">1</div>
                    <span class="ml-2 text-sm font-medium text-gray-900">Basic Info</span>
                </div>
                <div class="flex items-center">
                    <div class="w-8 h-8 rounded-full step-inactive flex items-center justify-center text-gray-500 text-sm font-semibold" id="step-2">2</div>
                    <span class="ml-2 text-sm text-gray-500">Contact</span>
                </div>
                <div class="flex items-center">
                    <div class="w-8 h-8 rounded-full step-inactive flex items-center justify-center text-gray-500 text-sm font-semibold" id="step-3">3</div>
                    <span class="ml-2 text-sm text-gray-500">Images</span>
                </div>
                <div class="flex items-center">
                    <div class="w-8 h-8 rounded-full step-inactive flex items-center justify-center text-gray-500 text-sm font-semibold" id="step-4">4</div>
                    <span class="ml-2 text-sm text-gray-500">Hours</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-4xl mx-auto px-6 py-8">
        <form id="restaurant-form" action="/owner/restaurants" method="POST" enctype="multipart/form-data" class="space-y-8">
            <!-- Add CSRF token for Laravel -->
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <!-- Step 1: Basic Information -->
            <div id="step-basic" class="step-content fade-in">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-100">
                        <div class="flex items-center space-x-3">
                            <div class="p-3 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl shadow-lg">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Basic Information</h3>
                                <p class="text-sm text-gray-600">Let's start with the essentials</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6 space-y-8">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <div class="form-field">
                                <input type="text" id="name" name="name" class="form-input" required>
                                <label for="name" class="form-label">Restaurant Name *</label>
                            </div>
                            
                            <div class="form-field">
                                <input type="text" id="subdomain" name="subdomain" class="form-input" required>
                                <label for="subdomain" class="form-label">Subdomain *</label>
                                <div class="text-sm text-gray-500 mt-2">Will create: <span id="subdomain-preview" class="font-mono text-blue-600">yourrestaurant.qrmenu.com</span></div>
                            </div>
                        </div>
                        
                        <div class="form-field">
                            <textarea id="description" name="description" rows="3" class="form-input resize-none" style="padding-top: 1.5rem;"></textarea>
                            <label for="description" class="form-label">Description (optional)</label>
                            <div class="text-sm text-gray-500 mt-2">Brief description of your restaurant</div>
                        </div>
                        
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <div class="form-field">
                                <input type="text" id="cuisine_type" name="cuisine_type" class="form-input">
                                <label for="cuisine_type" class="form-label">Cuisine Type</label>
                                <div class="text-sm text-gray-500 mt-2">e.g., Italian, Mexican, Asian</div>
                            </div>
                            
                            <div>
                                <label for="price_range" class="block text-sm font-medium text-gray-700 mb-2">Price Range</label>
                                <select id="price_range" name="price_range" class="w-full px-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all bg-white">
                                    <option value="">Select price range</option>
                                    <option value="$">$ - Budget Friendly ($1-15)</option>
                                    <option value="$">$ - Moderate ($15-30)</option>
                                    <option value="$$">$$ - Expensive ($30-60)</option>
                                    <option value="$$">$$ - Very Expensive ($60+)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 2: Contact Information -->
            <div id="step-contact" class="step-content hidden">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-100">
                        <div class="flex items-center space-x-3">
                            <div class="p-3 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl shadow-lg">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Contact & Location</h3>
                                <p class="text-sm text-gray-600">Help customers find and reach you</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6 space-y-6">
                        <div class="form-field">
                            <label for="address" class="form-label">Full Address *</label>
                            <textarea id="address" name="address" rows="3" class="form-input form-textarea" required></textarea>
                        </div>
                        
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <div class="form-field">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="tel" id="phone" name="phone" class="form-input" placeholder="+1 (555) 123-4567">
                            </div>
                            
                            <div class="form-field">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" id="email" name="email" class="form-input" placeholder="info@restaurant.com">
                            </div>
                        </div>
                        
                        <div class="form-field">
                            <label for="website" class="form-label">Website</label>
                            <input type="url" id="website" name="website" class="form-input" placeholder="https://www.restaurant.com">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 3: Images -->
            <div id="step-images" class="step-content hidden">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-100">
                        <div class="flex items-center space-x-3">
                            <div class="p-3 bg-gradient-to-br from-purple-500 to-pink-600 rounded-xl shadow-lg">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Restaurant Images</h3>
                                <p class="text-sm text-gray-600">Upload your branding and showcase images</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6 space-y-6">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-3">Logo</label>
                                <div class="preview-drop-zone rounded-xl p-8 text-center cursor-pointer" onclick="document.getElementById('logo').click()">
                                    <div id="logo-preview" class="hidden">
                                        <img class="w-32 h-32 mx-auto object-cover rounded-lg mb-3" alt="Logo preview">
                                        <p class="text-sm text-gray-600">Click to change</p>
                                    </div>
                                    <div id="logo-placeholder">
                                        <svg class="w-12 h-12 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                        </svg>
                                        <p class="text-sm text-gray-600">Drop your logo here or click to browse</p>
                                        <p class="text-xs text-gray-400 mt-1">PNG, JPG up to 2MB</p>
                                    </div>
                                </div>
                                <input type="file" id="logo" name="logo" class="hidden" accept="image/*" onchange="previewImage(this, 'logo-preview', 'logo-placeholder')">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-3">Cover Image</label>
                                <div class="preview-drop-zone rounded-xl p-8 text-center cursor-pointer" onclick="document.getElementById('cover_image').click()">
                                    <div id="cover-preview" class="hidden">
                                        <img class="w-full h-32 mx-auto object-cover rounded-lg mb-3" alt="Cover preview">
                                        <p class="text-sm text-gray-600">Click to change</p>
                                    </div>
                                    <div id="cover-placeholder">
                                        <svg class="w-12 h-12 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        <p class="text-sm text-gray-600">Drop your cover image here or click to browse</p>
                                        <p class="text-xs text-gray-400 mt-1">PNG, JPG up to 4MB</p>
                                    </div>
                                </div>
                                <input type="file" id="cover_image" name="cover_image" class="hidden" accept="image/*" onchange="previewImage(this, 'cover-preview', 'cover-placeholder')">
                            </div>
                        </div>
                        
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-blue-800">
                                        <strong>Pro tip:</strong> Use high-quality images that represent your restaurant's atmosphere. Your logo will appear on QR menus and your cover image will be the first thing customers see.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 4: Operating Hours -->
            <div id="step-hours" class="step-content hidden">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-100">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="p-3 bg-gradient-to-br from-orange-500 to-red-600 rounded-xl shadow-lg">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">Operating Hours</h3>
                                    <p class="text-sm text-gray-600">Set your restaurant's schedule</p>
                                </div>
                            </div>
                            <button type="button" onclick="copyHoursToAll()" class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                                Copy Mon to all days
                            </button>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4" id="hours-container">
                            <!-- Hours will be populated by JavaScript -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- Navigation Buttons -->
            <div class="flex justify-between pt-6 border-t">
                <button type="button" id="prev-btn" onclick="prevStep()" class="px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg transition-colors hidden">
                    Previous
                </button>
                <div class="ml-auto flex space-x-4">
                    <button type="button" onclick="saveDraft()" class="px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg transition-colors">
                        Save Draft
                    </button>
                    <button type="button" id="next-btn" onclick="nextStep()" class="px-8 py-3 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-semibold rounded-lg transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                        Next Step
                    </button>
                    <button type="submit" id="submit-btn" class="px-8 py-3 bg-gradient-to-r from-green-600 to-blue-600 hover:from-green-700 hover:to-blue-700 text-white font-semibold rounded-lg transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 hidden">
                        Create Restaurant
                    </button>
                </div>
            </div>
        </form>
    </div>

    <script>
        let currentStep = 1;
        const totalSteps = 4;
        
        // Initialize the form
        document.addEventListener('DOMContentLoaded', function() {
            initializeHoursSection();
            setupFormValidation();
            setupImagePreview();
            updateSubdomainPreview();
        });
        
        function nextStep() {
            if (validateCurrentStep()) {
                if (currentStep < totalSteps) {
                    currentStep++;
                    showStep(currentStep);
                }
            }
        }
        
        function prevStep() {
            if (currentStep > 1) {
                currentStep--;
                showStep(currentStep);
            }
        }
        
        function showStep(step) {
            // Hide all steps
            document.querySelectorAll('.step-content').forEach(content => {
                content.classList.add('hidden');
                content.classList.remove('fade-in');
            });
            
            // Show current step
            const stepContent = document.getElementById(getStepId(step));
            stepContent.classList.remove('hidden');
            stepContent.classList.add('fade-in');
            
            // Update step indicators
            updateStepIndicators(step);
            
            // Update navigation buttons
            updateNavigationButtons(step);
            
            // Update progress bar
            updateProgressBar(step);
            
            // Update step counter
            document.getElementById('current-step').textContent = step;
        }
        
        function getStepId(step) {
            const stepIds = ['step-basic', 'step-contact', 'step-images', 'step-hours'];
            return stepIds[step - 1];
        }
        
        function updateStepIndicators(step) {
            for (let i = 1; i <= totalSteps; i++) {
                const stepEl = document.getElementById(`step-${i}`);
                const stepText = stepEl.nextElementSibling;
                
                if (i < step) {
                    stepEl.className = 'w-8 h-8 rounded-full step-completed flex items-center justify-center text-white text-sm font-semibold';
                    stepEl.innerHTML = '✓';
                    stepText.className = 'ml-2 text-sm font-medium text-green-600';
                } else if (i === step) {
                    stepEl.className = 'w-8 h-8 rounded-full step-active flex items-center justify-center text-white text-sm font-semibold';
                    stepEl.innerHTML = i;
                    stepText.className = 'ml-2 text-sm font-medium text-gray-900';
                } else {
                    stepEl.className = 'w-8 h-8 rounded-full step-inactive flex items-center justify-center text-gray-500 text-sm font-semibold';
                    stepEl.innerHTML = i;
                    stepText.className = 'ml-2 text-sm text-gray-500';
                }
            }
        }
        
        function updateNavigationButtons(step) {
            const prevBtn = document.getElementById('prev-btn');
            const nextBtn = document.getElementById('next-btn');
            const submitBtn = document.getElementById('submit-btn');
            
            if (step === 1) {
                prevBtn.classList.add('hidden');
            } else {
                prevBtn.classList.remove('hidden');
            }
            
            if (step === totalSteps) {
                nextBtn.classList.add('hidden');
                submitBtn.classList.remove('hidden');
            } else {
                nextBtn.classList.remove('hidden');
                submitBtn.classList.add('hidden');
            }
        }
        
        function updateProgressBar(step) {
            const progressBar = document.getElementById('progress-bar');
            const percentage = (step / totalSteps) * 100;
            progressBar.style.width = percentage + '%';
        }
        
        function validateCurrentStep() {
            const stepId = getStepId(currentStep);
            const stepElement = document.getElementById(stepId);
            const requiredInputs = stepElement.querySelectorAll('[required]');
            let isValid = true;
            
            requiredInputs.forEach(input => {
                if (!input.value.trim()) {
                    showFieldError(input, 'This field is required');
                    isValid = false;
                } else {
                    clearFieldError(input);
                }
            });
            
            // Additional validation for specific fields
            if (currentStep === 1) {
                const subdomain = document.getElementById('subdomain');
                if (subdomain.value && !isValidSubdomain(subdomain.value)) {
                    showFieldError(subdomain, 'Subdomain can only contain letters, numbers, and hyphens');
                    isValid = false;
                }
            }
            
            return isValid;
        }
        
        function showFieldError(input, message) {
            const errorEl = input.parentElement.querySelector('.error-message');
            if (errorEl) {
                errorEl.textContent = message;
                errorEl.classList.remove('hidden');
            }
            input.classList.add('border-red-500');
        }
        
        function clearFieldError(input) {
            const errorEl = input.parentElement.querySelector('.error-message');
            if (errorEl) {
                errorEl.classList.add('hidden');
            }
            input.classList.remove('border-red-500');
        }
        
        function isValidSubdomain(subdomain) {
            return /^[a-z0-9-]+$/.test(subdomain) && !subdomain.startsWith('-') && !subdomain.endsWith('-');
        }
        
        function setupFormValidation() {
            // Auto-generate subdomain from restaurant name
            document.getElementById('name').addEventListener('input', function() {
                updateSubdomainFromName();
                updateSubdomainPreview();
            });
            
            document.getElementById('subdomain').addEventListener('input', function() {
                updateSubdomainPreview();
            });
        }
        
        function updateSubdomainFromName() {
            const name = document.getElementById('name').value;
            const subdomain = name
                .toLowerCase()
                .replace(/[^a-z0-9]+/g, '-')
                .replace(/^-+|-+$/g, '')
                .substring(0, 50);
            document.getElementById('subdomain').value = subdomain;
        }
        
        function updateSubdomainPreview() {
            const subdomain = document.getElementById('subdomain').value || 'yourrestaurant';
            document.getElementById('subdomain-preview').textContent = subdomain + '.qrmenu.com';
        }
        
        function previewImage(input, previewId, placeholderId) {
            const preview = document.getElementById(previewId);
            const placeholder = document.getElementById(placeholderId);
            const img = preview.querySelector('img');
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    img.src = e.target.result;
                    preview.classList.remove('hidden');
                    placeholder.classList.add('hidden');
                }
                
                reader.readAsDataURL(input.files[0]);
            } else {
                preview.classList.add('hidden');
                placeholder.classList.remove('hidden');
            }
        }
        
        function initializeHoursSection() {
            const days = [
                { key: 'monday', label: 'Monday' },
                { key: 'tuesday', label: 'Tuesday' },
                { key: 'wednesday', label: 'Wednesday' },
                { key: 'thursday', label: 'Thursday' },
                { key: 'friday', label: 'Friday' },
                { key: 'saturday', label: 'Saturday' },
                { key: 'sunday', label: 'Sunday' }
            ];
            
            const container = document.getElementById('hours-container');
            
            days.forEach(day => {
                const dayRow = document.createElement('div');
                dayRow.className = 'time-input-grid p-4 bg-gray-50 rounded-lg';
                dayRow.innerHTML = `
                    <div class="text-sm font-medium text-gray-700">${day.label}</div>
                    <div class="flex items-center space-x-2">
                        <input type="time" name="hours[${day.key}][open]" class="form-control text-sm px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent" id="${day.key}-open">
                    </div>
                    <div class="text-center text-sm text-gray-500">to</div>
                    <div class="flex items-center space-x-2">
                        <input type="time" name="hours[${day.key}][close]" class="form-control text-sm px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent" id="${day.key}-close">
                    </div>
                    <div class="flex items-center justify-end">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" name="hours[${day.key}][closed]" class="sr-only peer" id="${day.key}-closed">
                            <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-red-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-red-600"></div>
                            <span class="ml-3 text-sm text-gray-600">Closed</span>
                        </label>
                    </div>
                `;
                container.appendChild(dayRow);
                
                // Handle closed day toggles
                const closedCheckbox = dayRow.querySelector(`#${day.key}-closed`);
                const timeInputs = dayRow.querySelectorAll('input[type="time"]');
                
                closedCheckbox.addEventListener('change', function() {
                    timeInputs.forEach(input => {
                        input.disabled = this.checked;
                        if (this.checked) {
                            input.value = '';
                        }
                    });
                });
            });
        }
        
        function copyHoursToAll() {
            const mondayOpen = document.getElementById('monday-open').value;
            const mondayClose = document.getElementById('monday-close').value;
            const mondayClosed = document.getElementById('monday-closed').checked;
            
            const days = ['tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
            
            if (confirm('This will copy Monday\'s hours to all other days. Continue?')) {
                days.forEach(day => {
                    document.getElementById(`${day}-open`).value = mondayOpen;
                    document.getElementById(`${day}-close`).value = mondayClose;
                    document.getElementById(`${day}-closed`).checked = mondayClosed;
                    
                    // Trigger change event
                    const event = new Event('change', { bubbles: true });
                    document.getElementById(`${day}-closed`).dispatchEvent(event);
                });
            }
        }
        
        function saveDraft() {
            // Show saving indicator
            const originalText = event.target.textContent;
            event.target.textContent = 'Saving...';
            event.target.disabled = true;
            
            // Simulate save (replace with actual AJAX call)
            setTimeout(() => {
                event.target.textContent = 'Saved!';
                setTimeout(() => {
                    event.target.textContent = originalText;
                    event.target.disabled = false;
                }, 1000);
            }, 1000);
        }
        
        function goBack() {
            if (confirm('Are you sure you want to leave? Any unsaved changes will be lost.')) {
                // Navigate back to restaurants index
                window.location.href = '/owner/restaurants';
            }
        }
        
        function setupImagePreview() {
            // Add drag and drop support
            ['logo', 'cover_image'].forEach(id => {
                const dropZone = document.querySelector(`[onclick="document.getElementById('${id}').click()"]`);
                
                dropZone.addEventListener('dragover', function(e) {
                    e.preventDefault();
                    this.classList.add('dragover');
                });
                
                dropZone.addEventListener('dragleave', function(e) {
                    e.preventDefault();
                    this.classList.remove('dragover');
                });
                
                dropZone.addEventListener('drop', function(e) {
                    e.preventDefault();
                    this.classList.remove('dragover');
                    
                    const files = e.dataTransfer.files;
                    if (files.length > 0) {
                        const input = document.getElementById(id);
                        input.files = files;
                        
                        const previewId = id === 'logo' ? 'logo-preview' : 'cover-preview';
                        const placeholderId = id === 'logo' ? 'logo-placeholder' : 'cover-placeholder';
                        previewImage(input, previewId, placeholderId);
                    }
                });
            });
        }
        
        // Form submission
        document.getElementById('restaurant-form').addEventListener('submit', function(e) {
            // For demo purposes, prevent default. In real Laravel app, remove this line
            // and let the form submit naturally to the controller
            e.preventDefault();
            
            if (validateCurrentStep()) {
                // Show submission state
                const submitBtn = document.getElementById('submit-btn');
                const originalHTML = submitBtn.innerHTML;
                submitBtn.innerHTML = `
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Creating Restaurant...
                `;
                submitBtn.disabled = true;
                
                // In a real Laravel app, the form would submit naturally and redirect via:
                // return redirect()->route('owner.restaurants.show', $restaurant)->with('success', 'Restaurant created successfully');
                
                // For demo purposes, simulate the success flow
                setTimeout(() => {
                    showSuccessMessage();
                    
                    // Simulate redirect to restaurant show page or dashboard
                    setTimeout(() => {
                        // This would be handled by Laravel controller redirect in real app
                        window.location.href = '/owner/restaurants';
                    }, 1500);
                }, 2000);
            }
        });
        
        function showSuccessMessage() {
            // Create success overlay
            const successOverlay = document.createElement('div');
            successOverlay.className = 'fixed inset-0 bg-black/50 flex items-center justify-center z-50';
            successOverlay.innerHTML = `
                <div class="bg-white rounded-2xl p-8 max-w-md mx-4 text-center">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Restaurant Created Successfully!</h3>
                    <p class="text-gray-600 mb-4">Your restaurant has been set up. You'll be redirected to your dashboard shortly.</p>
                    <div class="flex justify-center">
                        <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600"></div>
                    </div>
                </div>
            `;
            document.body.appendChild(successOverlay);
        }
    </script>
</body>
</html>