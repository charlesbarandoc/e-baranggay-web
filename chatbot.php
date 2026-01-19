<div id="chatbot" class="fixed bottom-6 right-6 w-80 max-w-full bg-white shadow-xl rounded-xl overflow-hidden flex flex-col z-40">
    
  
    <div id="chatbot-header" class="bg-red-600 text-white px-4 py-3 cursor-pointer flex justify-between items-center">
        <span class="font-bold">BM Assistant</span>
        <span id="chatbot-toggle" class="text-xl">✕</span>
    </div>

 
    <div id="chatbot-body" class="p-4 flex-1 overflow-y-auto hidden flex-col space-y-2 bg-slate-50" style="max-height: 400px;">
    </div>


    <div id="chatbot-input-area" class="p-2 bg-slate-100 hidden flex flex-col space-y-2">
        <input type="text" id="chatbot-username" placeholder="Enter your name..." 
               class="px-3 py-2 rounded-lg border border-slate-300 focus:outline-none focus:border-red-500" />

  
        <div id="chatbot-quick-replies" class="hidden flex flex-wrap gap-2">
            <button type="button" class="quick-reply" data-message="Latest news">Latest News</button>
            <button type="button" class="quick-reply" data-message="Urgent news">Urgent</button>
            <button type="button" class="quick-reply" data-action="openDocument" data-doc="barangay_clearance">Clearance</button>
            <button type="button" class="quick-reply" data-action="openDocument" data-doc="certificate_of_residency">Residency</button>
            <button type="button" class="quick-reply" data-action="openDocument" data-doc="certificate_of_indigency">Indigency</button>
            <button type="button" class="quick-reply" data-action="openEmergency">Hotlines</button>
        </div>

        <div class="flex">
            <input type="text" id="chatbot-input" placeholder="Type your question..." 
                   class="flex-1 px-3 py-2 rounded-l-lg border border-slate-300 focus:outline-none focus:border-red-500" />
            <button id="chatbot-send" class="bg-red-600 text-white px-4 py-2 rounded-r-lg hover:bg-red-700 font-bold">
                Send
            </button>
        </div>
    </div>
</div>


<div id="documentModal" class="modal">
    <div class="modal-content">
        <div class="bg-red-600 text-white p-6 rounded-t-xl">
            <div class="flex justify-between items-center">
                <h2 class="text-2xl font-bold">Request Document</h2>
                <button onclick="closeModal()" class="text-white text-2xl hover:text-red-200">&times;</button>
            </div>
        </div>
        
        <form id="documentForm" class="p-6">
            <input type="hidden" name="csrf_token" value="<?php echo isset($csrf_token) ? $csrf_token : ''; ?>">
            <input type="hidden" name="document_type" id="documentType">
            
            <div class="mb-6 bg-blue-50 p-4 rounded-lg">
                <h3 class="font-bold text-lg text-blue-900 mb-2" id="modalTitle">Document Type</h3>
                <p class="text-sm text-blue-700" id="modalDescription">Description</p>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-slate-700 font-medium mb-2">Full Name <span class="text-red-500">*</span></label>
                    <input type="text" name="full_name" required 
                           class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-red-500">
                </div>

                <div>
                    <label class="block text-slate-700 font-medium mb-2">Contact Number <span class="text-red-500">*</span></label>
                    <input type="tel" name="contact_number" required pattern="^(\+63|0)?9\d{9}$"
                           placeholder="09XXXXXXXXX"
                           class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-red-500">
                    <p class="text-xs text-slate-500 mt-1">Format: 09XXXXXXXXX</p>
                </div>

                <div>
                    <label class="block text-slate-700 font-medium mb-2">Email Address (Optional)</label>
                    <input type="email" name="email"
                           class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-red-500">
                </div>

                <div>
                    <label class="block text-slate-700 font-medium mb-2">Complete Address <span class="text-red-500">*</span></label>
                    <textarea name="address" rows="2" required
                              class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-red-500"></textarea>
                </div>

                <div>
                    <label class="block text-slate-700 font-medium mb-2">Purpose <span class="text-red-500">*</span></label>
                    <textarea name="purpose" required rows="3"
                              placeholder="Please specify the purpose of this document request"
                              class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:outline-none focus:border-red-500"></textarea>
                </div>
            </div>

            <div id="formMessage" class="mt-4 hidden"></div>

            <div class="mt-6 flex space-x-3">
                <button type="submit" id="submitBtn"
                        class="flex-1 bg-red-600 hover:bg-red-700 text-white font-bold py-3 rounded-lg transition-colors">
                    Submit Request
                </button>
                <button type="button" onclick="closeModal()"
                        class="px-6 bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold py-3 rounded-lg transition-colors">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>


<div id="emergencyModal" class="modal">
    <div class="modal-content max-w-md">
        <div class="bg-red-600 text-white p-6 rounded-t-xl">
            <div class="flex justify-between items-center">
                <h2 class="text-2xl font-bold flex items-center gap-2">
                    <i data-lucide="alert-circle" class="w-6 h-6"></i>
                    Emergency Hotlines
                </h2>
                <button onclick="closeEmergencyModal()" class="text-white text-2xl hover:text-red-200">&times;</button>
            </div>
        </div>
        
        <div class="p-6">
            <div class="space-y-4">
                <div class="border-l-4 border-red-500 pl-4 py-2">
                    <h3 class="font-bold text-lg">Barangay Emergency Hotline</h3>
                    <p class="text-2xl font-bold text-red-600">+63 917 123 4567</p>
                    <p class="text-sm text-slate-500">Available 24/7</p>
                </div>

                <div class="border-l-4 border-blue-500 pl-4 py-2">
                    <h3 class="font-bold">Police Station</h3>
                    <p class="text-xl font-bold text-blue-600">117</p>
                </div>

                <div class="border-l-4 border-orange-500 pl-4 py-2">
                    <h3 class="font-bold">Fire Department</h3>
                    <p class="text-xl font-bold text-orange-600">160</p>
                </div>

                <div class="border-l-4 border-green-500 pl-4 py-2">
                    <h3 class="font-bold">Medical Emergency</h3>
                    <p class="text-xl font-bold text-green-600">911</p>
                </div>
            </div>

            <div class="mt-6 bg-yellow-50 p-4 rounded-lg">
                <p class="text-sm text-yellow-800">
                    <strong>Note:</strong> In case of emergency, call the Barangay Hotline first or visit the Barangay Hall immediately.
                </p>
            </div>

            <button onclick="closeEmergencyModal()" 
                    class="w-full mt-4 bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold py-3 rounded-lg transition-colors">
                Close
            </button>
        </div>
    </div>
</div>
