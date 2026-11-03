<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Procurement Document Tracking Portal - LGU Can-avid</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <!-- HTML5 QR Code Scanner -->
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <!-- QRCode.js Generator -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

    <style>
        @media print {
            .no-print { display: none !important; }
            .print-only { display: block !important; }
            body { background: white !important; color: black !important; }
        }
        .print-only { display: none; }
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-800 font-sans antialiased min-h-screen flex flex-col">

    <!-- GLOBAL TOP UTILITY BAR -->
    <header class="bg-slate-900 text-slate-300 px-4 py-2 text-xs flex justify-between items-center no-print border-b border-slate-800 shadow-sm">
        <div class="flex items-center space-x-3">
            <span class="font-extrabold tracking-wider text-white uppercase flex items-center gap-1.5">
                <i data-lucide="shield-check" class="w-4 h-4 text-blue-400"></i> LGU CAN-AVID
            </span>
            <span class="text-slate-600">|</span>
            <span class="text-slate-400 font-medium hidden sm:inline">Procurement Tracking System Portal</span>
        </div>
        <div class="flex items-center space-x-3">
            <!-- NETWORK STATUS TOGGLE -->
            <button onclick="toggleNetworkMode()" id="networkStatusBadge" class="flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 font-medium transition hover:bg-emerald-500/20 cursor-pointer">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                <span id="networkStatusText">Online Mode</span>
            </button>
            <span id="syncQueueBadge" class="hidden px-2 py-0.5 bg-amber-500/20 text-amber-300 border border-amber-500/30 rounded-full font-bold text-[10px]">
                <span id="syncQueueCount">0</span> Pending Sync
            </span>
            <div id="currentUserBadge" class="hidden items-center gap-2 pl-2 border-l border-slate-700">
                <span class="text-slate-300 font-bold" id="currentUserText">-</span>
                <button onclick="logout()" id="logoutBtn" class="px-2 py-1 bg-red-600/80 hover:bg-red-600 text-white rounded text-[10px] font-bold transition">Logout</button>
            </div>
        </div>
    </header>

    <!-- TOAST NOTIFICATION CONTAINER -->
    <div id="toastContainer" class="fixed top-12 right-4 z-50 space-y-2 max-w-sm w-full pointer-events-none"></div>

    <div id="app" class="flex-1 flex flex-col">
        
        <!-- AUTHENTICATION / LOGIN & REGISTRATION SECTION -->
        <section id="authSection" class="flex-1 flex items-center justify-center p-4 bg-slate-200/70">
            <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 p-6 md:p-8 w-full max-w-md relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-blue-700 via-indigo-600 to-sky-500"></div>
                
                <div class="text-center mb-5 pt-2">
                    <h1 class="text-xl font-extrabold text-slate-900 tracking-tight">Municipality of Can-avid</h1>
                    <p class="text-xs font-semibold text-blue-600 uppercase tracking-widest mt-0.5">Procurement Tracking Portal</p>
                    <p class="text-xs text-slate-500 mt-1.5">Register a new office account or sign in to access document routing</p>
                </div>

                <!-- PORTAL MODE TOGGLE TABS -->
                <div class="flex bg-slate-100 p-1 rounded-xl border border-slate-200 text-xs font-bold mb-5">
                    <button type="button" id="tabBtnLogin" onclick="switchAuthTab('login')" class="flex-1 py-2 rounded-lg text-center bg-white shadow-xs text-blue-700 font-extrabold transition">
                        Sign In / Login
                    </button>
                    <button type="button" id="tabBtnRegister" onclick="switchAuthTab('register')" class="flex-1 py-2 rounded-lg text-center text-slate-600 hover:text-slate-900 transition">
                        Register New Office
                    </button>
                </div>

                <!-- FORM 1: LOGIN PORTAL -->
                <form id="loginForm" onsubmit="handleLogin(event)" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Select Office / Department</label>
                        <select id="loginOffice" required class="w-full px-3 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm font-medium focus:ring-2 focus:ring-blue-500 focus:outline-none transition">
                            <option value="">-- Select Office / Department --</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Office Password</label>
                        <input type="password" id="loginPassword" required placeholder="••••••••" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none transition">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Security PIN Code (4-Digits)</label>
                        <input type="password" id="loginPin" maxlength="4" pattern="\d{4}" required placeholder="1234" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm tracking-widest focus:ring-2 focus:ring-blue-500 focus:outline-none transition">
                        <p class="text-[11px] text-slate-400 mt-1">Note: Use the password and PIN you created during registration.</p>
                    </div>

                    <div class="flex items-center justify-between text-xs pt-1">
                        <label class="flex items-center gap-1.5 text-slate-600 cursor-pointer">
                            <input type="checkbox" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500"> Remember session
                        </label>
                        <button type="button" onclick="openForgotPasswordModal()" class="text-blue-600 hover:text-blue-800 font-semibold">Forgot Password?</button>
                    </div>

                    <button type="submit" class="w-full py-3 bg-blue-700 hover:bg-blue-800 text-white font-bold rounded-xl shadow-lg shadow-blue-700/20 transition flex items-center justify-center gap-2 text-sm">
                        <i data-lucide="log-in" class="w-4 h-4"></i> Access Portal System
                    </button>
                </form>

                <!-- FORM 2: ACCOUNT REGISTRATION PORTAL -->
                <form id="registerForm" onsubmit="handleRegisterOffice(event)" class="space-y-3.5 hidden">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Department / Office Name</label>
                        <input type="text" id="regOfficeName" required placeholder="e.g. PLANNING & DEVELOPMENT OFFICE" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs uppercase font-bold focus:ring-2 focus:ring-blue-500 focus:outline-none transition">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Official LGU Email Address</label>
                        <input type="email" id="regEmail" required placeholder="planning@canavid.gov.ph" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs focus:ring-2 focus:ring-blue-500 focus:outline-none transition">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Role Type</label>
                        <select id="regRole" required class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs font-bold text-slate-800">
                            <!-- Populated dynamically via JS rule -->
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Set Password</label>
                            <input type="password" id="regPassword" required placeholder="••••••••" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs focus:ring-2 focus:ring-blue-500 focus:outline-none transition">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">4-Digit Security PIN</label>
                            <input type="password" id="regPin" maxlength="4" pattern="\d{4}" required placeholder="1234" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs font-mono tracking-widest text-center focus:ring-2 focus:ring-blue-500 focus:outline-none transition">
                        </div>
                    </div>

                    <div class="bg-blue-50 p-2.5 rounded-xl border border-blue-100 text-[11px] text-blue-800">
                        Only 1 system administrator is permitted. Once registered, all subsequent offices can only register as departmental viewer accounts unless the admin account is deleted.
                    </div>

                    <button type="submit" class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl shadow-lg shadow-emerald-600/20 transition flex items-center justify-center gap-2 text-xs">
                        <i data-lucide="building-2" class="w-4 h-4"></i> Register & Create Office Account
                    </button>
                </form>
            </div>
        </section>

        <!-- DASHBOARD PORTAL CONTAINER -->
        <main id="portalSection" class="hidden flex-1 flex flex-col bg-slate-100">
            
            <!-- PORTAL HEADER -->
            <div class="bg-white border-b border-slate-200 px-6 py-3 no-print shadow-xs">
                <div class="max-w-7xl mx-auto flex items-center justify-between">
                    <div class="w-12 h-12"></div>
                    <div class="text-center leading-snug">
                        <p class="text-xs text-slate-700">Republic of the Philippines</p>
                        <p class="text-[12px] text-slate-700 font-medium">Province of Eastern Samar</p>
                        <h1 class="text-[12px] font-bold text-slate-900 uppercase">MUNICIPALITY OF CAN-AVID</h1>
                        <p class="text-[9px] italic text-slate-600">National Road, Barangay 04, Poblacion, Can-avid, Eastern Samar</p>
                        <p class="text-[10px] font-bold text-slate-800 uppercase tracking-wider">Local Government Unit</p>
                    </div>
                    <div class="w-12 h-12"></div>
                </div>
            </div>

            <!-- PORTAL NAVBAR -->
            <nav class="bg-slate-50 border-b border-slate-200 px-6 py-2.5 flex flex-wrap justify-between items-center gap-4 no-print shadow-sm">
                <div class="flex items-center space-x-3">
                    <span id="portalRoleBadge" class="px-3 py-1 bg-blue-100 text-blue-800 text-xs font-extrabold rounded-full uppercase tracking-wider">
                        BAC ADMIN PORTAL
                    </span>
                    <div>
                        <h2 id="portalTitle" class="text-sm font-extrabold text-slate-900 leading-tight">Document Management & Workflow Tracking</h2>
                        <p class="text-[10px] text-slate-500 font-medium">LGU Can-avid Procurement Management Unit</p>
                    </div>
                </div>

                <!-- BAC INSPECTOR DROPDOWN -->
                <div id="bacInspectorDropdownWrapper" class="hidden items-center gap-2 bg-amber-50 border border-amber-200 px-3 py-1.5 rounded-xl text-xs font-semibold">
                    <i data-lucide="eye" class="w-4 h-4 text-amber-600"></i>
                    <span class="text-amber-900 font-bold">BAC Inspector View Mode:</span>
                    <select id="bacInspectorOfficeSelect" onchange="handleInspectorOfficeChange(this.value)" class="bg-white border border-amber-300 rounded-lg px-2 py-1 text-xs font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-500">
                        <!-- Populated by JS -->
                    </select>
                </div>

                <!-- TABS NAVIGATION -->
                <div class="flex items-center space-x-1 bg-white p-1 rounded-xl border border-slate-200 shadow-2xs" id="portalNavTabs">
                    <!-- Dynamic tabs rendered via JS -->
                </div>
            </nav>

            <!-- DASHBOARD CONTENT PANELS -->
            <div class="flex-1 p-4 md:p-6 max-w-7xl w-full mx-auto space-y-6">
                
                <!-- TAB 1: BAC PIPELINE TRACKER -->
                <section id="tabBacPipeline" class="tab-content hidden space-y-6">
                    <!-- Stats Bar -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
                            <div>
                                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Tracked PRs</p>
                                <h3 id="statTotalDocs" class="text-2xl font-black text-slate-900 mt-1">0</h3>
                            </div>
                            <div class="p-3 bg-blue-50 text-blue-600 rounded-xl border border-blue-100"><i data-lucide="file-text" class="w-6 h-6"></i></div>
                        </div>
                        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
                            <div>
                                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Active In-Transit</p>
                                <h3 id="statActiveDocs" class="text-2xl font-black text-amber-600 mt-1">0</h3>
                            </div>
                            <div class="p-3 bg-amber-50 text-amber-600 rounded-xl border border-amber-100"><i data-lucide="clock" class="w-6 h-6"></i></div>
                        </div>
                        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
                            <div>
                                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Overdue Alerts (>48h)</p>
                                <h3 id="statOverdueDocs" class="text-2xl font-black text-red-600 mt-1">0</h3>
                            </div>
                            <div class="p-3 bg-red-50 text-red-600 rounded-xl border border-red-100"><i data-lucide="alert-triangle" class="w-6 h-6 animate-pulse"></i></div>
                        </div>
                        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex items-center justify-between">
                            <div>
                                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Completed PR Value</p>
                                <h3 id="statTotalAmount" class="text-xl font-black text-emerald-700 mt-1">₱0.00</h3>
                            </div>
                            <div class="p-3 bg-emerald-50 text-emerald-600 rounded-xl border border-emerald-100"><i data-lucide="banknote" class="w-6 h-6"></i></div>
                        </div>
                    </div>

                    <!-- Visual Sequential Workflow Matrix -->
                    <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
                        <div class="flex flex-wrap justify-between items-center gap-3 mb-4">
                            <div>
                                <h3 class="font-extrabold text-slate-900 text-base flex items-center gap-2">
                                    <i data-lucide="workflow" class="w-5 h-5 text-blue-600"></i> Sequential Departmental Pipeline Matrix
                                </h3>
                                <p class="text-xs text-slate-500">Routing Sequence: BAC &rarr; BUDGET &rarr; ACCOUNTING &rarr; TREASURER &rarr; MAYOR &rarr; GSO</p>
                            </div>
                            <button onclick="openNewDocModal()" class="px-4 py-2 bg-blue-700 hover:bg-blue-800 text-white rounded-xl text-xs font-bold flex items-center gap-1.5 transition shadow-sm">
                                <i data-lucide="plus-circle" class="w-4 h-4"></i> Create New PR / PO Document
                            </button>
                        </div>

                        <!-- Department Columns -->
                        <div class="grid grid-cols-1 md:grid-cols-6 gap-3 pt-2">
                            <div class="bg-slate-50 border border-slate-200 rounded-xl p-3">
                                <div class="text-xs font-bold text-slate-700 pb-2 border-b border-slate-200 flex justify-between items-center">
                                    <span>1. BAC OFFICE</span>
                                    <span id="badge-count-BAC OFFICE" class="px-2 py-0.5 bg-blue-100 text-blue-700 rounded-full text-[10px] font-extrabold">0</span>
                                </div>
                                <div id="pipeline-BAC OFFICE" class="mt-3 space-y-2.5 min-h-[140px]"></div>
                            </div>
                            <div class="bg-slate-50 border border-slate-200 rounded-xl p-3">
                                <div class="text-xs font-bold text-slate-700 pb-2 border-b border-slate-200 flex justify-between items-center">
                                    <span>2. BUDGET</span>
                                    <span id="badge-count-BUDGET OFFICE" class="px-2 py-0.5 bg-slate-200 text-slate-700 rounded-full text-[10px] font-extrabold">0</span>
                                </div>
                                <div id="pipeline-BUDGET OFFICE" class="mt-3 space-y-2.5 min-h-[140px]"></div>
                            </div>
                            <div class="bg-slate-50 border border-slate-200 rounded-xl p-3">
                                <div class="text-xs font-bold text-slate-700 pb-2 border-b border-slate-200 flex justify-between items-center">
                                    <span>3. ACCOUNTING</span>
                                    <span id="badge-count-ACCOUNTING OFFICE" class="px-2 py-0.5 bg-slate-200 text-slate-700 rounded-full text-[10px] font-extrabold">0</span>
                                </div>
                                <div id="pipeline-ACCOUNTING OFFICE" class="mt-3 space-y-2.5 min-h-[140px]"></div>
                            </div>
                            <div class="bg-slate-50 border border-slate-200 rounded-xl p-3">
                                <div class="text-xs font-bold text-slate-700 pb-2 border-b border-slate-200 flex justify-between items-center">
                                    <span>4. TREASURER</span>
                                    <span id="badge-count-TREASURER'S OFFICE" class="px-2 py-0.5 bg-slate-200 text-slate-700 rounded-full text-[10px] font-extrabold">0</span>
                                </div>
                                <div id="pipeline-TREASURER'S OFFICE" class="mt-3 space-y-2.5 min-h-[140px]"></div>
                            </div>
                            <div class="bg-slate-50 border border-slate-200 rounded-xl p-3">
                                <div class="text-xs font-bold text-slate-700 pb-2 border-b border-slate-200 flex justify-between items-center">
                                    <span>5. MAYOR'S</span>
                                    <span id="badge-count-MAYOR'S OFFICE" class="px-2 py-0.5 bg-slate-200 text-slate-700 rounded-full text-[10px] font-extrabold">0</span>
                                </div>
                                <div id="pipeline-MAYOR'S OFFICE" class="mt-3 space-y-2.5 min-h-[140px]"></div>
                            </div>
                            <div class="bg-slate-50 border border-slate-200 rounded-xl p-3">
                                <div class="text-xs font-bold text-slate-700 pb-2 border-b border-slate-200 flex justify-between items-center">
                                    <span>6. GSO</span>
                                    <span id="badge-count-GENERAL SERVICES OFFICE" class="px-2 py-0.5 bg-slate-200 text-slate-700 rounded-full text-[10px] font-extrabold">0</span>
                                </div>
                                <div id="pipeline-GENERAL SERVICES OFFICE" class="mt-3 space-y-2.5 min-h-[140px]"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Master Table with Advanced Search & Filter -->
                    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                        <div class="p-4 border-b border-slate-200 bg-slate-50/50 flex flex-wrap justify-between items-center gap-3">
                            <div>
                                <h3 class="font-extrabold text-slate-800 text-sm">Master Procurement Control Records</h3>
                                <p class="text-[11px] text-slate-500">Filter, search, inspect timeline, export data, or delete PR records</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <button onclick="exportToCSV()" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold flex items-center gap-1 transition shadow-sm">
                                    <i data-lucide="download" class="w-3.5 h-3.5"></i> Export CSV
                                </button>
                                <button onclick="exportToJSON()" class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-xs font-bold flex items-center gap-1 transition shadow-sm">
                                    <i data-lucide="file-json" class="w-3.5 h-3.5"></i> Export JSON
                                </button>
                                <button onclick="triggerPrintMasterSummary()" class="px-3 py-1.5 bg-slate-900 hover:bg-slate-800 text-white rounded-lg text-xs font-bold flex items-center gap-1 transition shadow-sm">
                                    <i data-lucide="printer" class="w-3.5 h-3.5"></i> Master Summary Report
                                </button>
                            </div>
                        </div>

                        <!-- Advanced Filter Control Bar -->
                        <div class="p-4 bg-white border-b border-slate-200 grid grid-cols-1 md:grid-cols-5 gap-3">
                            <div class="md:col-span-2">
                                <label class="block text-[10px] font-extrabold text-slate-400 uppercase mb-1">Quick Search</label>
                                <div class="relative">
                                    <i data-lucide="search" class="w-3.5 h-3.5 absolute left-3 top-2.5 text-slate-400"></i>
                                    <input type="text" id="masterSearchKw" onkeyup="renderMasterTable()" placeholder="PR #, Title, Particulars, Office..." class="w-full pl-9 pr-3 py-1.5 bg-slate-50 border border-slate-300 rounded-lg text-xs font-medium focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                </div>
                            </div>
                            <div>
                                <label class="block text-[10px] font-extrabold text-slate-400 uppercase mb-1">Category</label>
                                <select id="masterCategoryFilter" onchange="renderMasterTable()" class="w-full px-2.5 py-1.5 bg-slate-50 border border-slate-300 rounded-lg text-xs font-medium focus:ring-2 focus:ring-blue-500">
                                    <option value="ALL">All Categories</option>
                                    <option value="Office Supplies">Office Supplies</option>
                                    <option value="Catering Services">Catering Services</option>
                                    <option value="Equipment / Infrastructure">Equipment / Infrastructure</option>
                                    <option value="General Services">General Services</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-extrabold text-slate-400 uppercase mb-1">Priority Level</label>
                                <select id="masterPriorityFilter" onchange="renderMasterTable()" class="w-full px-2.5 py-1.5 bg-slate-50 border border-slate-300 rounded-lg text-xs font-medium focus:ring-2 focus:ring-blue-500">
                                    <option value="ALL">All Priorities</option>
                                    <option value="Regular">Regular</option>
                                    <option value="High">High Priority</option>
                                    <option value="Urgent">Urgent</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-extrabold text-slate-400 uppercase mb-1">Current Department</label>
                                <select id="masterDeptFilter" onchange="renderMasterTable()" class="w-full px-2.5 py-1.5 bg-slate-50 border border-slate-300 rounded-lg text-xs font-medium focus:ring-2 focus:ring-blue-500">
                                    <option value="ALL">All Departments</option>
                                    <option value="BAC OFFICE">BAC OFFICE</option>
                                    <option value="BUDGET OFFICE">BUDGET OFFICE</option>
                                    <option value="ACCOUNTING OFFICE">ACCOUNTING OFFICE</option>
                                    <option value="TREASURER'S OFFICE">TREASURER'S OFFICE</option>
                                    <option value="MAYOR'S OFFICE">MAYOR'S OFFICE</option>
                                    <option value="GENERAL SERVICES OFFICE">GSO</option>
                                    <option value="COMPLETED">COMPLETED</option>
                                </select>
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse text-xs">
                                <thead>
                                    <tr class="bg-slate-50 text-slate-500 uppercase font-bold border-b border-slate-200">
                                        <th class="p-3">PR Code</th>
                                        <th class="p-3">Priority</th>
                                        <th class="p-3">Requesting Office & Category</th>
                                        <th class="p-3">Title & Particulars</th>
                                        <th class="p-3">Amount</th>
                                        <th class="p-3">Current Location</th>
                                        <th class="p-3">Status</th>
                                        <th class="p-3 text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="masterDocsTableBody" class="divide-y divide-slate-100">
                                    <!-- Dynamic Rows populated by JS -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>

                <!-- TAB 2: OFFICE DOCUMENT HISTORY & ACTIONS -->
                <section id="tabOfficeHistory" class="tab-content hidden space-y-6">
                    <!-- Unified Receiver & Action Bar -->
                    <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex flex-wrap justify-between items-center gap-4">
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="text-lg font-extrabold text-slate-900" id="officeHistoryTitle">Office Operations Dashboard</h3>
                                <span id="bacInspectionWarningBadge" class="hidden px-2 py-0.5 bg-amber-100 text-amber-900 border border-amber-300 rounded font-bold text-[10px]">
                                    BAC READ-ONLY INSPECTION MODE
                                </span>
                            </div>
                            <p class="text-xs text-slate-500">Scan incoming QR code, review PR details, approve or trigger document returns.</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <button id="btnReceiveScanQR" onclick="openScanQRModal()" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-extrabold flex items-center gap-2 shadow-sm transition">
                                <i data-lucide="qr-code" class="w-4 h-4"></i> Receive Document (Scan QR)
                            </button>
                        </div>
                    </div>

                    <!-- Department Filters -->
                    <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex flex-wrap items-center justify-between gap-4">
                        <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
                            <div>
                                <label class="block text-[10px] font-extrabold text-slate-400 uppercase mb-1">Filter Month</label>
                                <select id="filterMonth" onchange="renderOfficeHistoryTable()" class="px-3 py-1.5 bg-slate-50 border border-slate-300 rounded-lg text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="ALL">All Months</option>
                                    <option value="0">January</option>
                                    <option value="1">February</option>
                                    <option value="2">March</option>
                                    <option value="3">April</option>
                                    <option value="4">May</option>
                                    <option value="5">June</option>
                                    <option value="6">July</option>
                                    <option value="7">August</option>
                                    <option value="8">September</option>
                                    <option value="9">October</option>
                                    <option value="10">November</option>
                                    <option value="11">December</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-[10px] font-extrabold text-slate-400 uppercase mb-1">Filter Year</label>
                                <select id="filterYear" onchange="renderOfficeHistoryTable()" class="px-3 py-1.5 bg-slate-50 border border-slate-300 rounded-lg text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="ALL">All Years</option>
                                    <option value="2026">2026</option>
                                    <option value="2025">2025</option>
                                    <option value="2024">2024</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-[10px] font-extrabold text-slate-400 uppercase mb-1">Status Action</label>
                                <select id="filterStatus" onchange="renderOfficeHistoryTable()" class="px-3 py-1.5 bg-slate-50 border border-slate-300 rounded-lg text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="ALL">All Statuses</option>
                                    <option value="Received">Received / Pending</option>
                                    <option value="Approved">Approved</option>
                                    <option value="Returned">Returned / Revised</option>
                                </select>
                            </div>
                        </div>

                        <div class="w-full md:w-64">
                            <label class="block text-[10px] font-extrabold text-slate-400 uppercase mb-1">Search Dept Records</label>
                            <div class="relative">
                                <i data-lucide="search" class="w-3.5 h-3.5 absolute left-3 top-2.5 text-slate-400"></i>
                                <input type="text" id="searchKeyword" onkeyup="renderOfficeHistoryTable()" placeholder="PR # or Title..." class="w-full pl-9 pr-3 py-1.5 bg-slate-50 border border-slate-300 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                    </div>

                    <!-- Department Log Table -->
                    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                        <div class="p-4 border-b border-slate-200 bg-slate-50/50 flex justify-between items-center">
                            <span class="text-xs font-extrabold text-slate-700 uppercase tracking-wider">Office Document Activity Log</span>
                            <span class="text-[11px] text-slate-500 font-medium">Single log transition (Received &rarr; Approved / Returned &rarr; Forwarded)</span>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse text-xs">
                                <thead>
                                    <tr class="bg-slate-50 text-slate-500 uppercase font-bold border-b border-slate-200">
                                        <th class="p-3">PR Code</th>
                                        <th class="p-3">Priority</th>
                                        <th class="p-3">Requesting Office / Category</th>
                                        <th class="p-3">Particulars & Title</th>
                                        <th class="p-3">Amount</th>
                                        <th class="p-3">Received Date</th>
                                        <th class="p-3">Action Timestamp</th>
                                        <th class="p-3">Status Badge</th>
                                        <th class="p-3 text-right">Office Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="officeDocsTableBody" class="divide-y divide-slate-100">
                                    <!-- Dynamic Content -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>

                <!-- TAB 3: OFFICE SECURITY & PIN MANAGEMENT -->
                <section id="tabBacSettings" class="tab-content hidden space-y-6">
                    <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                        <h3 class="text-base font-extrabold text-slate-900 mb-1">Office Security Credentials & PIN Management</h3>
                        <p class="text-xs text-slate-500 mb-6">Each office is restricted to updating only its own authorized password, contact email, and 4-digit approval PIN. Deleting the Administrator account automatically resets all system records and transactions back to initial state.</p>

                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse text-xs">
                                <thead>
                                    <tr class="bg-slate-50 text-slate-500 uppercase font-bold border-b border-slate-200">
                                        <th class="p-3">Office Name</th>
                                        <th class="p-3">Role Type</th>
                                        <th class="p-3">Associated Contact Email</th>
                                        <th class="p-3">Account Password</th>
                                        <th class="p-3">Security PIN (4-Digits)</th>
                                        <th class="p-3 text-right">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="officePinTableBody" class="divide-y divide-slate-100">
                                    <!-- Dynamic rows for setting PINs & Passwords -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>
            </div>
        </main>
    </div>

    <!-- MODAL: CREATE NEW PR DOCUMENT -->
    <div id="newDocModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-100 max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-4 border-b border-slate-100 pb-3">
                <h3 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                    <i data-lucide="file-plus" class="w-5 h-5 text-blue-700"></i> Create New PR Document
                </h3>
                <button onclick="closeNewDocModal()" class="text-slate-400 hover:text-slate-600"><i data-lucide="x" class="w-5 h-5"></i></button>
            </div>

            <form onsubmit="handleCreateDoc(event)" class="space-y-4">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">PR Tracking Number</label>
                        <input type="text" id="docPrNumber" required placeholder="PR-2026-03-0103" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs font-mono font-bold">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Priority Level</label>
                        <select id="docPriority" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs font-bold">
                            <option value="Regular">Regular Priority</option>
                            <option value="High">High Priority</option>
                            <option value="Urgent">Urgent</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Procurement Category</label>
                        <select id="docCategory" required class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs font-bold">
                            <option value="Office Supplies">Office Supplies</option>
                            <option value="Catering Services">Catering Services</option>
                            <option value="Equipment / Infrastructure">Equipment / Infrastructure</option>
                            <option value="General Services">General Services</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Originating Department</label>
                        <input type="text" id="docOrigin" readonly value="BAC OFFICE" class="w-full px-3 py-2 bg-slate-200 text-slate-600 border border-slate-300 rounded-xl text-xs font-bold">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Requesting Office / Department</label>
                    <select id="docRequestingOffice" required class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs font-bold text-slate-800 focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Select Requesting Office --</option>
                        <option value="Office of the Municipal Mayor">Office of the Municipal Mayor</option>
                        <option value="BAC">BAC</option>
                        <option value="Office of the Municipal Vice Mayor">Office of the Municipal Vice Mayor</option>
                        <option value="SBM">SBM</option>
                        <option value="Accounting Office">Accounting Office</option>
                        <option value="MSDWO">MSDWO</option>
                        <option value="LYDO">LYDO</option>
                        <option value="OSCA">OSCA</option>
                        <option value="Engineering Office">Engineering Office</option>
                        <option value="IAS">IAS</option>
                        <option value="MPDC">MPDC</option>
                        <option value="GSO">GSO</option>
                        <option value="Tech4Ed/GAD">Tech4Ed/GAD</option>
                        <option value="PESO">PESO</option>
                        <option value="DILG/POPS">DILG/POPS</option>
                        <option value="Assessor's Office">Assessor's Office</option>
                        <option value="Budget Office">Budget Office</option>
                        <option value="HRMO">HRMO</option>
                        <option value="Treasurer's Office/BPLO">Treasurer's Office/BPLO</option>
                        <option value="MHO">MHO</option>
                        <option value="Tourism Office">Tourism Office</option>
                        <option value="MENRO">MENRO</option>
                        <option value="OMAS">OMAS</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Document Title</label>
                    <input type="text" id="docTitle" required placeholder="Procurement of IT Supplies & Office Furniture" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs font-medium">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Particulars / Technical Specifications</label>
                    <textarea id="docParticulars" rows="3" required placeholder="Detailed item breakdown and specs..." class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs font-medium"></textarea>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Approved Budget (PHP)</label>
                    <input type="number" step="0.01" id="docAmount" required placeholder="250000.00" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs font-mono font-bold">
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                    <button type="button" onclick="closeNewDocModal()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-blue-700 hover:bg-blue-800 text-white rounded-xl text-xs font-bold shadow-md">Generate & Save PR</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL: DOCUMENT DETAILED AUDIT TIMELINE LOG -->
    <div id="timelineModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-2xl w-full p-6 shadow-2xl border border-slate-100 max-h-[90vh] flex flex-col">
            <div class="flex justify-between items-center pb-3 border-b border-slate-200">
                <div>
                    <h3 class="text-base font-black text-slate-900 flex items-center gap-2">
                        <i data-lucide="history" class="w-5 h-5 text-blue-600"></i> Document Audit Trail & Timeline Log
                    </h3>
                    <p id="timelineModalPrSubtitle" class="text-xs font-mono font-bold text-blue-700 mt-0.5">-</p>
                </div>
                <button onclick="closeTimelineModal()" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg"><i data-lucide="x" class="w-5 h-5"></i></button>
            </div>

            <div class="py-4 space-y-3 overflow-y-auto flex-1 pr-1" id="timelineContentWrapper">
                <!-- Timeline items rendered via JS -->
            </div>

            <div class="pt-3 border-t border-slate-200 flex justify-end">
                <button onclick="closeTimelineModal()" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-xs font-bold transition">Close Audit Trail</button>
            </div>
        </div>
    </div>

    <!-- MODAL: APPROVE / RETURN ACTION WITH REMARKS & PIN CONFIRMATION -->
    <div id="actionRemarksModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-100">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-base font-extrabold text-slate-900 flex items-center gap-2" id="actionModalTitle">
                    <i data-lucide="shield-check" class="w-5 h-5 text-emerald-600"></i> Department Action Head Sign-Off
                </h3>
                <button onclick="closeActionRemarksModal()" class="text-slate-400 hover:text-slate-600"><i data-lucide="x" class="w-5 h-5"></i></button>
            </div>

            <form onsubmit="handleExecuteActionWithRemarks(event)" class="space-y-4">
                <input type="hidden" id="actionModalDocId">
                <input type="hidden" id="actionModalType">

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1" id="actionRemarksLabel">Approval Remarks / Compliance Notes</label>
                    <textarea id="actionRemarksText" rows="3" placeholder="e.g. Approved subject to BAC compliance verification..." class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs focus:ring-2 focus:ring-blue-500 focus:outline-none"></textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Office Security PIN (4-Digits)</label>
                    <input type="password" id="actionPinInput" maxlength="4" pattern="\d{4}" required placeholder="1234" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs font-mono tracking-widest focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                    <button type="button" onclick="closeActionRemarksModal()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold">Cancel</button>
                    <button type="submit" id="actionSubmitBtn" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition">Confirm Sign-Off</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL: FORGOT PASSWORD -->
    <div id="forgotPasswordModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-100">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                    <i data-lucide="key-round" class="w-5 h-5 text-blue-600"></i> Password Recovery Request
                </h3>
                <button onclick="closeForgotPasswordModal()" class="text-slate-400 hover:text-slate-600"><i data-lucide="x" class="w-5 h-5"></i></button>
            </div>
            <p class="text-xs text-slate-600 mb-4">Select your registered office to send password reset credentials to your official LGU email.</p>
            
            <form onsubmit="handleForgotPasswordSubmit(event)" class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Target Office</label>
                    <select id="resetOfficeSelect" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs font-medium">
                        <!-- Populated by JS -->
                    </select>
                </div>
                <div class="bg-blue-50 p-3 rounded-xl text-[11px] text-blue-800 border border-blue-100">
                    A temporary reset authorization code will be sent to the official registered email address.
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="closeForgotPasswordModal()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold">Send Reset Instructions</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL: SCAN QR / RECEIVE DOCUMENT -->
    <div id="scanQrModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-100 max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                    <i data-lucide="qr-code" class="w-5 h-5 text-emerald-600"></i> QR Code Document Receiver
                </h3>
                <button onclick="closeScanQRModal()" class="text-slate-400 hover:text-slate-600"><i data-lucide="x" class="w-5 h-5"></i></button>
            </div>

            <div class="space-y-4">
                <!-- Mode Switcher -->
                <div class="flex bg-slate-100 p-1 rounded-xl border border-slate-200 text-xs font-bold">
                    <button type="button" id="btnModeLive" onclick="switchScanMode('live')" class="flex-1 py-1.5 rounded-lg text-center bg-white shadow-sm text-emerald-700 font-extrabold transition">
                        <i data-lucide="camera" class="w-3.5 h-3.5 inline mr-1"></i> Live Camera
                    </button>
                    <button type="button" id="btnModeFile" onclick="switchScanMode('file')" class="flex-1 py-1.5 rounded-lg text-center text-slate-600 hover:text-slate-900 transition">
                        <i data-lucide="image" class="w-3.5 h-3.5 inline mr-1"></i> Upload Image
                    </button>
                </div>

                <!-- Live Camera Section -->
                <div id="liveCameraContainer" class="bg-slate-900 rounded-xl overflow-hidden relative flex flex-col items-center justify-center min-h-[220px]">
                    <div id="reader" class="w-full"></div>
                    <div id="liveCamFallback" class="hidden text-center p-4 text-slate-300">
                        <i data-lucide="video-off" class="w-8 h-8 text-amber-400 mx-auto mb-2"></i>
                        <p class="text-xs">Camera starting or permissions requested...</p>
                    </div>
                </div>

                <!-- Image Preview Container -->
                <div id="fileUploadContainer" class="hidden bg-slate-900 rounded-xl p-4 text-center text-white relative flex flex-col items-center justify-center min-h-[180px]">
                    <div id="previewImageWrapper" class="hidden mb-3 relative max-w-[150px]">
                        <img id="attachedQrPreview" src="" alt="Attached QR Code" class="w-full h-auto rounded-lg border border-emerald-500 shadow-md">
                        <button type="button" onclick="clearAttachedQrImage()" class="absolute -top-2 -right-2 bg-red-600 text-white rounded-full p-1 shadow hover:bg-red-700">
                            <i data-lucide="x" class="w-3.5 h-3.5"></i>
                        </button>
                    </div>
                    
                    <label class="cursor-pointer px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-extrabold flex items-center gap-1.5 transition shadow">
                        <i data-lucide="upload" class="w-4 h-4"></i> Choose QR Picture
                        <input type="file" id="qrFileInput" accept="image/*" onchange="handleQrFileUpload(event)" class="hidden">
                    </label>
                    <p class="text-[10px] text-slate-400 mt-2 font-medium">File automatically clears immediately after decoding</p>
                </div>

                <div class="relative flex py-1 items-center">
                    <div class="flex-grow border-t border-slate-200"></div>
                    <span class="flex-shrink mx-2 text-[10px] text-slate-400 uppercase font-extrabold">Or Manual Override Input</span>
                    <div class="flex-grow border-t border-slate-200"></div>
                </div>

                <!-- Manual Input for PR Tracking ID -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">PR Tracking ID</label>
                    <input type="text" id="manualPrInput" placeholder="e.g. PR-2026-02-0089 or DOC-1001" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs font-mono font-bold focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Receipt Remarks (Optional)</label>
                    <input type="text" id="receiveRemarksInput" placeholder="e.g. Received hard copy with attachments" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs font-medium focus:ring-2 focus:ring-emerald-500">
                </div>

                <button onclick="executeQRScanSimulation()" class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold rounded-xl text-xs flex items-center justify-center gap-2 transition shadow-lg shadow-emerald-600/20">
                    <i data-lucide="check-circle" class="w-4 h-4"></i> Confirm Scan & Receive Document
                </button>
            </div>
        </div>
    </div>

    <!-- PRINTABLE ROUTING SLIP MODAL -->
    <div id="printModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-xl w-full p-6 shadow-2xl border border-slate-100 max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-4 no-print border-b border-slate-100 pb-3">
                <h3 class="text-base font-extrabold text-slate-900">Printable Municipal Routing Slip</h3>
                <div class="flex gap-2">
                    <button onclick="window.print()" class="px-3 py-1.5 bg-blue-700 hover:bg-blue-800 text-white rounded-xl text-xs font-bold flex items-center gap-1">
                        <i data-lucide="printer" class="w-4 h-4"></i> Print Slip
                    </button>
                    <button onclick="closePrintModal()" class="text-slate-400 hover:text-slate-600"><i data-lucide="x" class="w-5 h-5"></i></button>
                </div>
            </div>

            <!-- PRINTABLE CARD CONTENT (ICON FREE) -->
            <div id="printableArea" class="bg-white p-6 border-2 border-slate-900 rounded-lg">
                <div class="flex items-center justify-between border-b-2 border-slate-900 pb-3 mb-4">
                    <div class="w-12 h-12"></div>
                    <div class="text-center leading-snug">
                        <p class="text-xs text-slate-900">Republic of the Philippines</p>
                        <p class="text-[12px] text-slate-800">Province of Eastern Samar</p>
                        <h3 class="text-[12px] font-bold uppercase text-slate-900">MUNICIPALITY OF CAN-AVID</h3>
                        <p class="text-[9px] italic text-slate-600">National Road, Barangay 04, Poblacion, Can-avid, Eastern Samar</p>
                        <p class="text-[10px] font-bold text-slate-900 uppercase">Local Government Unit</p>
                    </div>
                    <div class="w-12 h-12"></div>
                </div>

                <div class="text-center mb-4">
                    <h4 class="text-xs font-black uppercase tracking-widest text-slate-900 border-t border-b border-slate-900 py-1">PROCUREMENT ROUTING & SIGN-OFF SLIP</h4>
                </div>

                <div class="grid grid-cols-3 gap-4 mb-6">
                    <div class="col-span-2 space-y-2 text-xs">
                        <div><span class="font-bold text-slate-500 uppercase text-[10px]">PR Number:</span> <p id="printPrNo" class="font-mono font-black text-sm text-slate-900">-</p></div>
                        <div><span class="font-bold text-slate-500 uppercase text-[10px]">Requesting Office:</span> <p id="printRequestingOffice" class="font-bold text-slate-900 text-xs">-</p></div>
                        <div><span class="font-bold text-slate-500 uppercase text-[10px]">Priority & Category:</span> <p id="printCategory" class="font-bold text-blue-700 text-xs">-</p></div>
                        <div><span class="font-bold text-slate-500 uppercase text-[10px]">Title:</span> <p id="printTitle" class="font-bold text-slate-800">-</p></div>
                        <div><span class="font-bold text-slate-500 uppercase text-[10px]">Particulars:</span> <p id="printParticulars" class="text-slate-600 text-[11px]">-</p></div>
                        <div><span class="font-bold text-slate-500 uppercase text-[10px]">Approved Budget:</span> <p id="printAmount" class="font-black text-slate-900">-</p></div>
                    </div>
                    <div class="flex flex-col items-center justify-center border-l border-slate-300 pl-4">
                        <div id="qrcode" class="p-2 bg-white border border-slate-400 rounded-lg"></div>
                        <span class="text-[9px] font-mono text-slate-500 mt-1.5 font-bold">Scan QR to Receive</span>
                    </div>
                </div>

                <!-- Routing Log Matrix -->
                <div class="border border-slate-900 rounded overflow-hidden mt-4">
                    <table class="w-full text-left text-[11px] border-collapse">
                        <thead>
                            <tr class="bg-slate-100 uppercase font-black border-b border-slate-900 text-[9px]">
                                <th class="p-2 border-r border-slate-900">Office</th>
                                <th class="p-2 border-r border-slate-900">Received</th>
                                <th class="p-2 border-r border-slate-900">Approved</th>
                                <th class="p-2">Action / Remarks</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-900">
                            <tr>
                                <td class="p-2 font-bold border-r border-slate-900">BAC Office</td>
                                <td id="print-bac-rec" class="p-2 border-r border-slate-900 font-mono">-</td>
                                <td id="print-bac-app" class="p-2 border-r border-slate-900 font-mono">-</td>
                                <td id="print-bac-stat" class="p-2 font-semibold">-</td>
                            </tr>
                            <tr>
                                <td class="p-2 font-bold border-r border-slate-900">Budget Office</td>
                                <td id="print-budget-rec" class="p-2 border-r border-slate-900 font-mono">-</td>
                                <td id="print-budget-app" class="p-2 border-r border-slate-900 font-mono">-</td>
                                <td id="print-budget-stat" class="p-2 font-semibold">-</td>
                            </tr>
                            <tr>
                                <td class="p-2 font-bold border-r border-slate-900">Accounting Office</td>
                                <td id="print-acct-rec" class="p-2 border-r border-slate-900 font-mono">-</td>
                                <td id="print-acct-app" class="p-2 border-r border-slate-900 font-mono">-</td>
                                <td id="print-acct-stat" class="p-2 font-semibold">-</td>
                            </tr>
                            <tr>
                                <td class="p-2 font-bold border-r border-slate-900">Treasurer's Office</td>
                                <td id="print-treas-rec" class="p-2 border-r border-slate-900 font-mono">-</td>
                                <td id="print-treas-app" class="p-2 border-r border-slate-900 font-mono">-</td>
                                <td id="print-treas-stat" class="p-2 font-semibold">-</td>
                            </tr>
                            <tr>
                                <td class="p-2 font-bold border-r border-slate-900">Mayor's Office</td>
                                <td id="print-mayor-rec" class="p-2 border-r border-slate-900 font-mono">-</td>
                                <td id="print-mayor-app" class="p-2 border-r border-slate-900 font-mono">-</td>
                                <td id="print-mayor-stat" class="p-2 font-semibold">-</td>
                            </tr>
                            <tr>
                                <td class="p-2 font-bold border-r border-slate-900">General Services Office</td>
                                <td id="print-gso-rec" class="p-2 border-r border-slate-900 font-mono">-</td>
                                <td id="print-gso-app" class="p-2 border-r border-slate-900 font-mono">-</td>
                                <td id="print-gso-stat" class="p-2 font-semibold">-</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- PRINTABLE MASTER SUMMARY REPORT CONTAINER -->
    <div id="printableSummaryArea" class="print-only p-8 bg-white text-black">
        <div class="flex items-center justify-between border-b-2 border-slate-900 pb-4 mb-4">
            <div class="w-12 h-12"></div>
            <div class="text-center leading-snug">
                <p class="text-xs text-slate-900">Republic of the Philippines</p>
                <p class="text-[12px] text-slate-800">Province of Eastern Samar</p>
                <h3 class="text-[12px] font-bold uppercase text-slate-900">MUNICIPALITY OF CAN-AVID</h3>
                <p class="text-[9px] italic text-slate-600">National Road, Barangay 04, Poblacion, Can-avid, Eastern Samar</p>
                <p class="text-[10px] font-bold text-slate-900 uppercase">Local Government Unit</p>
            </div>
            <div class="w-12 h-12"></div>
        </div>

        <div class="text-center mb-6">
            <h4 class="text-xs font-black uppercase tracking-widest text-slate-900 border-t border-b border-slate-800 py-1.5">MASTER PROCUREMENT SUMMARY REPORT</h4>
            <p id="printReportTimestamp" class="text-[10px] text-slate-500 pt-1"></p>
        </div>

        <table class="w-full text-left border-collapse text-xs border border-slate-800">
            <thead>
                <tr class="bg-slate-100 uppercase font-bold border-b border-slate-800 text-[10px]">
                    <th class="p-2 border-r border-slate-800">PR Number</th>
                    <th class="p-2 border-r border-slate-800">Priority</th>
                    <th class="p-2 border-r border-slate-800">Requesting Office</th>
                    <th class="p-2 border-r border-slate-800">Category</th>
                    <th class="p-2 border-r border-slate-800">Title & Particulars</th>
                    <th class="p-2 border-r border-slate-800">Amount (PHP)</th>
                    <th class="p-2 border-r border-slate-800">Current Office</th>
                    <th class="p-2">Status</th>
                </tr>
            </thead>
            <tbody id="printableSummaryTbody" class="divide-y divide-slate-800">
                <!-- Dynamically populated on print trigger -->
            </tbody>
        </table>

        <div class="mt-8 pt-4 border-t border-slate-400 text-xs flex justify-between items-end">
            <div>
                <p class="font-bold">Prepared By:</p>
                <div class="mt-8 border-b border-slate-800 w-48"></div>
                <p class="text-[10px] text-slate-600 mt-1">BAC Secretariat / Authorized Officer</p>
            </div>
            <div>
                <p class="font-bold">Noted By:</p>
                <div class="mt-8 border-b border-slate-800 w-48"></div>
                <p class="text-[10px] text-slate-600 mt-1">BAC Chairman</p>
            </div>
        </div>
    </div>

    <!-- APPLICATION LOGIC & STATE MANAGEMENT -->
    <script>
        // STRICT PIPELINE CONSTANTS
        const WORKFLOW_PIPELINE = [
            "BAC OFFICE",
            "BUDGET OFFICE",
            "ACCOUNTING OFFICE",
            "TREASURER'S OFFICE",
            "MAYOR'S OFFICE",
            "GENERAL SERVICES OFFICE"
        ];

        // DEFAULT INITIAL STATE DATA
        const DEFAULT_CREDENTIALS = {};
        const DEFAULT_DOCUMENTS = [
            {
                id: "DOC-1001",
                prNumber: "PR-2026-02-0089",
                priority: "High",
                category: "Catering Services",
                requestingOffice: "Office of the Municipal Mayor",
                title: "Catering for Municipal Summit 2026",
                particulars: "Full board meals and snacks for 150 participants",
                amount: 125000.00,
                originatingOffice: "BAC OFFICE",
                currentLocation: "MAYOR'S OFFICE",
                createdAt: "2026-02-10T08:30:00.000Z",
                logs: [
                    { office: "BAC OFFICE", action: "APPROVED", remarks: "PR validated and verified", receivedAt: "2026-02-10T08:30:00.000Z", approvedAt: "2026-02-10T09:00:00.000Z" },
                    { office: "BUDGET OFFICE", action: "APPROVED", remarks: "Funds available under 2026 MOOE", receivedAt: "2026-02-11T10:15:00.000Z", approvedAt: "2026-02-11T14:20:00.000Z" },
                    { office: "ACCOUNTING OFFICE", action: "APPROVED", remarks: "Pre-audit cleared", receivedAt: "2026-02-12T11:00:00.000Z", approvedAt: "2026-02-12T15:00:00.000Z" },
                    { office: "TREASURER'S OFFICE", action: "APPROVED", remarks: "Advice to pay issued", receivedAt: "2026-02-13T09:30:00.000Z", approvedAt: "2026-02-13T11:45:00.000Z" },
                    { office: "MAYOR'S OFFICE", action: "RECEIVED", remarks: "Endorsement for final approval", receivedAt: "2026-02-14T08:00:00.000Z", approvedAt: null }
                ]
            }
        ];

        // Load saved office credentials or fallback to empty set
        let officeCredentials = JSON.parse(localStorage.getItem('pts_credentials')) || DEFAULT_CREDENTIALS;

        // State variables
        let currentUser = JSON.parse(localStorage.getItem('pts_current_user')) || null;
        let inspectedOffice = null;
        let isOnline = localStorage.getItem('pts_online_mode') !== 'false';
        let syncQueue = JSON.parse(localStorage.getItem('pts_sync_queue')) || [];
        let activeTab = "tabBacPipeline";
        let html5QrCodeScanner = null;

        // Documents Database
        let documents = JSON.parse(localStorage.getItem('pts_documents')) || DEFAULT_DOCUMENTS;

        // INITIALIZATION
        window.onload = function() {
            lucide.createIcons();
            updateNetworkBadge();
            populateAuthOfficeDropdowns();
            updateRegistrationRoleDropdown();
            
            if (currentUser) {
                showPortal();
            } else {
                showAuth();
            }
        };

        function saveState() {
            localStorage.setItem('pts_documents', JSON.stringify(documents));
            localStorage.setItem('pts_sync_queue', JSON.stringify(syncQueue));
            localStorage.setItem('pts_credentials', JSON.stringify(officeCredentials));
            if (currentUser) {
                localStorage.setItem('pts_current_user', JSON.stringify(currentUser));
            } else {
                localStorage.removeItem('pts_current_user');
            }
        }

        // CHECK IF AN ADMIN ALREADY EXISTS
        function hasAdminAccount() {
            return Object.values(officeCredentials).some(cred => cred.role === 'ADMIN');
        }

        // UPDATE REGISTRATION ROLE DROPDOWN BASED ON ADMIN EXISTENCE
        function updateRegistrationRoleDropdown() {
            const regRoleSelect = document.getElementById('regRole');
            if (!regRoleSelect) return;

            const adminExists = hasAdminAccount();

            if (adminExists) {
                regRoleSelect.innerHTML = `<option value="VIEWER">Departmental Office (Standard Routing View & Sign-Off)</option>`;
                regRoleSelect.disabled = true;
                regRoleSelect.className = "w-full px-3 py-2 bg-slate-200 text-slate-600 border border-slate-300 rounded-xl text-xs font-bold cursor-not-allowed";
            } else {
                regRoleSelect.innerHTML = `
                    <option value="VIEWER">Departmental Office (Standard Routing View & Sign-Off)</option>
                    <option value="ADMIN">BAC Admin Office (Master Pipeline Access)</option>
                `;
                regRoleSelect.disabled = false;
                regRoleSelect.className = "w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs font-bold text-slate-800";
            }
        }

        // POPULATE DROPDOWNS
        function populateAuthOfficeDropdowns() {
            const loginSelect = document.getElementById('loginOffice');
            const resetSelect = document.getElementById('resetOfficeSelect');
            const inspectorSelect = document.getElementById('bacInspectorOfficeSelect');

            let optionsHtml = `<option value="">-- Select Office / Department --</option>`;
            let resetHtml = ``;
            let inspectorHtml = ``;

            Object.keys(officeCredentials).forEach(office => {
                optionsHtml += `<option value="${office}">${office} ${officeCredentials[office].role === 'ADMIN' ? '(Admin Mode)' : ''}</option>`;
                resetHtml += `<option value="${office}">${office}</option>`;
                inspectorHtml += `<option value="${office}">${office} ${office === 'BAC OFFICE' ? '(Self Log)' : ''}</option>`;
            });

            if (loginSelect) loginSelect.innerHTML = optionsHtml;
            if (resetSelect) resetSelect.innerHTML = resetHtml;
            if (inspectorSelect) inspectorSelect.innerHTML = inspectorHtml;
        }

        // AUTH TAB SWITCHING
        function switchAuthTab(mode) {
            const loginForm = document.getElementById('loginForm');
            const registerForm = document.getElementById('registerForm');
            const tabBtnLogin = document.getElementById('tabBtnLogin');
            const tabBtnRegister = document.getElementById('tabBtnRegister');

            if (mode === 'login') {
                loginForm.classList.remove('hidden');
                registerForm.classList.add('hidden');
                tabBtnLogin.className = "flex-1 py-2 rounded-lg text-center bg-white shadow-xs text-blue-700 font-extrabold transition";
                tabBtnRegister.className = "flex-1 py-2 rounded-lg text-center text-slate-600 hover:text-slate-900 transition";
            } else {
                loginForm.classList.add('hidden');
                registerForm.classList.remove('hidden');
                tabBtnRegister.className = "flex-1 py-2 rounded-lg text-center bg-white shadow-xs text-emerald-700 font-extrabold transition";
                tabBtnLogin.className = "flex-1 py-2 rounded-lg text-center text-slate-600 hover:text-slate-900 transition";
                updateRegistrationRoleDropdown();
            }
        }

        // ACCOUNT REGISTRATION FUNCTION
        function handleRegisterOffice(e) {
            e.preventDefault();
            const officeName = document.getElementById('regOfficeName').value.trim().toUpperCase();
            const email = document.getElementById('regEmail').value.trim();
            const role = document.getElementById('regRole').value;
            const password = document.getElementById('regPassword').value;
            const pin = document.getElementById('regPin').value.trim();

            if (!officeName) {
                showToast("Please enter a valid Office / Department Name.", "warning");
                return;
            }

            if (officeCredentials[officeName]) {
                showToast(`An account for "${officeName}" already exists. Please login instead.`, "error");
                return;
            }

            if (role === 'ADMIN' && hasAdminAccount()) {
                showToast("An admin account already exists. Only one admin is allowed unless deleted by the controller.", "error");
                return;
            }

            if (!/^\d{4}$/.test(pin)) {
                showToast("Security PIN must be exactly 4 numeric digits.", "warning");
                return;
            }

            // Save new office credentials
            officeCredentials[officeName] = {
                password: password,
                pin: pin,
                email: email,
                role: role
            };

            saveState();
            populateAuthOfficeDropdowns();
            updateRegistrationRoleDropdown();

            showToast(`Office account "${officeName}" created successfully!`, "success");
            
            switchAuthTab('login');
            document.getElementById('loginOffice').value = officeName;
            document.getElementById('registerForm').reset();
        }

        // AUTHENTICATION LOGIN
        function handleLogin(e) {
            e.preventDefault();
            const office = document.getElementById('loginOffice').value;
            const password = document.getElementById('loginPassword').value;
            const pin = document.getElementById('loginPin').value;

            if (!office) {
                showToast("Please select an office or department.", "warning");
                return;
            }

            const creds = officeCredentials[office];
            if (creds && creds.password === password && creds.pin === pin) {
                currentUser = {
                    office: office,
                    role: creds.role,
                    email: creds.email
                };
                inspectedOffice = office;
                saveState();
                showToast(`Welcome ${office}! Authenticated successfully.`, "success");
                showPortal();
            } else {
                showToast("Invalid Password or Security PIN Code. Access denied.", "error");
            }
        }

        function logout() {
            currentUser = null;
            inspectedOffice = null;
            stopLiveScanner();
            saveState();
            showAuth();
            showToast("Logged out successfully.", "info");
        }

        function showAuth() {
            document.getElementById('authSection').classList.remove('hidden');
            document.getElementById('portalSection').classList.add('hidden');
            document.getElementById('currentUserBadge').classList.add('hidden');
            document.getElementById('logoutBtn').classList.add('hidden');
            switchAuthTab('login');
            populateAuthOfficeDropdowns();
            updateRegistrationRoleDropdown();
        }

        function showPortal() {
            document.getElementById('authSection').classList.add('hidden');
            document.getElementById('portalSection').classList.remove('hidden');
            document.getElementById('currentUserBadge').classList.remove('hidden');
            document.getElementById('currentUserText').innerText = currentUser.office;
            document.getElementById('logoutBtn').classList.remove('hidden');

            renderNavTabs();

            if (currentUser.role === 'ADMIN') {
                switchTab('tabBacPipeline');
            } else {
                switchTab('tabOfficeHistory');
            }
        }

        function toggleNetworkMode() {
            isOnline = !isOnline;
            localStorage.setItem('pts_online_mode', isOnline);
            updateNetworkBadge();
            
            if (isOnline && syncQueue.length > 0) {
                processSyncQueue();
            }
        }

        function updateNetworkBadge() {
            const badge = document.getElementById('networkStatusBadge');
            const text = document.getElementById('networkStatusText');
            const queueBadge = document.getElementById('syncQueueBadge');
            const queueCount = document.getElementById('syncQueueCount');

            if (isOnline) {
                badge.className = "flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 font-medium";
                text.innerText = "Online Mode";
            } else {
                badge.className = "flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-red-500/10 text-red-400 border border-red-500/20 font-medium";
                text.innerText = "Offline Mode";
            }

            if (syncQueue.length > 0) {
                queueBadge.classList.remove('hidden');
                queueCount.innerText = syncQueue.length;
            } else {
                queueBadge.classList.add('hidden');
            }
        }

        function processSyncQueue() {
            if (syncQueue.length === 0) return;
            showToast(`Synchronizing ${syncQueue.length} offline items...`, "info");
            
            syncQueue.forEach(item => {
                const doc = documents.find(d => d.id === item.docId);
                if (doc) {
                    if (item.type === "RECEIVE") {
                        applyReceiveAction(doc, item.office, item.remarks, item.timestamp);
                    } else if (item.type === "APPROVE") {
                        applyApproveAction(doc, item.office, item.remarks, item.timestamp);
                    }
                }
            });

            syncQueue = [];
            saveState();
            updateNetworkBadge();
            showToast("Offline queue synchronized with server successfully!", "success");
            refreshAllViews();
        }

        function refreshAllViews() {
            renderBacPipeline();
            renderMasterTable();
            renderOfficeHistoryTable();
            renderPinSettingsTable();
            updateRegistrationRoleDropdown();
        }

        function renderNavTabs() {
            const container = document.getElementById('portalNavTabs');
            const inspectorDropdown = document.getElementById('bacInspectorDropdownWrapper');
            const badge = document.getElementById('portalRoleBadge');
            const title = document.getElementById('portalTitle');

            if (currentUser.role === 'ADMIN') {
                badge.innerText = `${currentUser.office} (ADMIN PORTAL)`;
                badge.className = "px-3 py-1 bg-blue-100 text-blue-800 text-xs font-black rounded-full uppercase tracking-wider";
                title.innerText = "Master Pipeline Control & Document Management";
                
                inspectorDropdown.classList.remove('hidden');
                inspectorDropdown.classList.add('flex');

                container.innerHTML = `
                    <button onclick="switchTab('tabBacPipeline')" id="btn-tabBacPipeline" class="px-3 py-1.5 rounded-lg text-xs font-bold flex items-center gap-1.5 transition">
                        <i data-lucide="layout-dashboard" class="w-3.5 h-3.5"></i> Master Pipeline & PRs
                    </button>
                    <button onclick="switchTab('tabOfficeHistory')" id="btn-tabOfficeHistory" class="px-3 py-1.5 rounded-lg text-xs font-bold flex items-center gap-1.5 transition">
                        <i data-lucide="file-text" class="w-3.5 h-3.5"></i> Office Activity View
                    </button>
                    <button onclick="switchTab('tabBacSettings')" id="btn-tabBacSettings" class="px-3 py-1.5 rounded-lg text-xs font-bold flex items-center gap-1.5 transition">
                        <i data-lucide="shield-check" class="w-3.5 h-3.5"></i> Password & PIN Setup
                    </button>
                `;
            } else {
                badge.innerText = `${currentUser.office} PORTAL`;
                badge.className = "px-3 py-1 bg-emerald-100 text-emerald-800 text-xs font-black rounded-full uppercase tracking-wider";
                title.innerText = "Departmental Document Receiving & Sign-Off";

                inspectorDropdown.classList.add('hidden');
                inspectorDropdown.classList.remove('flex');
                inspectedOffice = currentUser.office;

                container.innerHTML = `
                    <button onclick="switchTab('tabOfficeHistory')" id="btn-tabOfficeHistory" class="px-3 py-1.5 rounded-lg text-xs font-bold flex items-center gap-1.5 transition">
                        <i data-lucide="file-check-2" class="w-3.5 h-3.5"></i> Document Log & Approval History
                    </button>
                    <button onclick="switchTab('tabBacSettings')" id="btn-tabBacSettings" class="px-3 py-1.5 rounded-lg text-xs font-bold flex items-center gap-1.5 transition">
                        <i data-lucide="key-round" class="w-3.5 h-3.5"></i> Security & PIN Setup
                    </button>
                `;
            }
            lucide.createIcons();
        }

        function handleInspectorOfficeChange(selectedOffice) {
            inspectedOffice = selectedOffice;
            if (activeTab !== 'tabOfficeHistory') {
                switchTab('tabOfficeHistory');
            } else {
                renderOfficeHistoryTable();
            }
        }

        function switchTab(tabId) {
            activeTab = tabId;
            document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
            
            const nav = document.getElementById('portalNavTabs');
            Array.from(nav.children).forEach(btn => {
                btn.className = "px-3 py-1.5 rounded-lg text-xs font-semibold flex items-center gap-1.5 transition text-slate-600 hover:text-slate-900";
            });

            const currentBtn = document.getElementById(`btn-${tabId}`);
            if (currentBtn) {
                currentBtn.className = "px-3 py-1.5 rounded-lg text-xs font-extrabold flex items-center gap-1.5 transition bg-white text-blue-700 shadow-sm border border-slate-200";
            }

            const targetSection = document.getElementById(tabId);
            if (targetSection) targetSection.classList.remove('hidden');

            refreshAllViews();
        }

        function isDocOverdue(doc) {
            if (doc.currentLocation === 'COMPLETED') return false;
            const currentLog = doc.logs.find(l => l.office === doc.currentLocation);
            if (!currentLog || !currentLog.receivedAt || currentLog.approvedAt) return false;

            const recTime = new Date(currentLog.receivedAt).getTime();
            const now = new Date().getTime();
            const hoursDiff = (now - recTime) / (1000 * 3600);
            return hoursDiff > 48;
        }

        function renderBacPipeline() {
            let totalDocs = documents.length;
            let activeDocs = 0;
            let overdueDocs = 0;
            let totalAmount = 0;

            WORKFLOW_PIPELINE.forEach(office => {
                const el = document.getElementById(`pipeline-${office}`);
                if (el) el.innerHTML = '';
                const countBadge = document.getElementById(`badge-count-${office}`);
                if (countBadge) countBadge.innerText = '0';
            });

            const counts = {};
            WORKFLOW_PIPELINE.forEach(o => counts[o] = 0);

            documents.forEach(doc => {
                if (doc.currentLocation === 'COMPLETED') {
                    totalAmount += (doc.amount || 0);
                } else {
                    activeDocs++;
                }

                if (isDocOverdue(doc)) overdueDocs++;

                const currentDept = doc.currentLocation;
                if (WORKFLOW_PIPELINE.includes(currentDept)) {
                    counts[currentDept]++;
                    const colEl = document.getElementById(`pipeline-${currentDept}`);
                    if (colEl) {
                        const card = document.createElement('div');
                        const overdue = isDocOverdue(doc);

                        card.className = `bg-white p-3 rounded-xl border ${overdue ? 'border-red-400 bg-red-50/20' : 'border-slate-200'} shadow-sm text-xs space-y-2 hover:border-blue-500 transition cursor-pointer relative`;
                        card.onclick = () => openTimelineModal(doc.id);

                        const log = doc.logs.find(l => l.office === currentDept);
                        const isApproved = log && (log.action === 'APPROVED' || log.action.startsWith('FORWARDED'));

                        let priorityColor = "bg-slate-100 text-slate-700 border-slate-200";
                        if (doc.priority === "Urgent") priorityColor = "bg-red-100 text-red-700 border-red-200 font-bold";
                        else if (doc.priority === "High") priorityColor = "bg-amber-100 text-amber-800 border-amber-200 font-bold";

                        card.innerHTML = `
                            ${overdue ? '<span class="absolute -top-2 -right-1 px-1.5 py-0.5 bg-red-600 text-white font-extrabold text-[8px] rounded-full animate-bounce">OVERDUE >48H</span>' : ''}
                            <div class="flex justify-between items-start">
                                <span class="text-blue-700 font-mono font-bold">${doc.prNumber}</span>
                                <span class="px-1.5 py-0.5 text-[9px] rounded-md ${priorityColor} border">${doc.priority || 'Regular'}</span>
                            </div>
                            <div class="font-bold text-slate-800 text-[11px] line-clamp-2">${doc.title}</div>
                            <div class="text-[10px] text-indigo-700 font-bold truncate"><i data-lucide="building" class="w-3 h-3 inline mr-0.5"></i> ${doc.requestingOffice || 'N/A'}</div>
                            <div class="flex justify-between items-center text-[10px] text-slate-500 font-mono pt-1 border-t border-slate-100">
                                <span class="font-bold text-slate-700">₱${parseFloat(doc.amount).toLocaleString('en-US', {minimumFractionDigits: 2})}</span>
                                <span class="px-1.5 py-0.5 text-[9px] rounded ${isApproved ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700'} font-bold">
                                    ${isApproved ? 'Approved' : 'Pending'}
                                </span>
                            </div>
                        `;
                        colEl.appendChild(card);
                    }
                }
            });

            WORKFLOW_PIPELINE.forEach(office => {
                const countBadge = document.getElementById(`badge-count-${office}`);
                if (countBadge) countBadge.innerText = counts[office] || 0;
            });

            document.getElementById('statTotalDocs').innerText = totalDocs;
            document.getElementById('statActiveDocs').innerText = activeDocs;
            document.getElementById('statOverdueDocs').innerText = overdueDocs;
            document.getElementById('statTotalAmount').innerText = '₱' + totalAmount.toLocaleString('en-US', {minimumFractionDigits: 2});
            
            lucide.createIcons();
        }

        function renderMasterTable() {
            const tbody = document.getElementById('masterDocsTableBody');
            if (!tbody) return;

            const kw = document.getElementById('masterSearchKw')?.value.toLowerCase() || '';
            const catFilter = document.getElementById('masterCategoryFilter')?.value || 'ALL';
            const prioFilter = document.getElementById('masterPriorityFilter')?.value || 'ALL';
            const deptFilter = document.getElementById('masterDeptFilter')?.value || 'ALL';

            const filtered = documents.filter(doc => {
                const reqOffice = (doc.requestingOffice || '').toLowerCase();
                const matchKw = !kw || doc.prNumber.toLowerCase().includes(kw) || doc.title.toLowerCase().includes(kw) || doc.particulars.toLowerCase().includes(kw) || reqOffice.includes(kw);
                const matchCat = (catFilter === 'ALL' || doc.category === catFilter);
                const matchPrio = (prioFilter === 'ALL' || (doc.priority || 'Regular') === prioFilter);
                const matchDept = (deptFilter === 'ALL' || doc.currentLocation === deptFilter);
                return matchKw && matchCat && matchPrio && matchDept;
            });

            if (filtered.length === 0) {
                tbody.innerHTML = `<tr><td colspan="8" class="p-6 text-center text-slate-400 italic font-medium">No procurement records match the specified filters.</td></tr>`;
                return;
            }

            const isAdmin = currentUser && currentUser.role === 'ADMIN';

            tbody.innerHTML = filtered.map(doc => {
                const isDone = doc.currentLocation === 'COMPLETED';
                const overdue = isDocOverdue(doc);

                let prioBadge = "bg-slate-100 text-slate-700 border-slate-200";
                if (doc.priority === 'Urgent') prioBadge = "bg-red-100 text-red-700 border-red-300 font-extrabold";
                else if (doc.priority === 'High') prioBadge = "bg-amber-100 text-amber-800 border-amber-300 font-bold";

                return `
                    <tr class="hover:bg-slate-50 transition">
                        <td class="p-3 font-mono font-extrabold text-blue-700">${doc.prNumber}</td>
                        <td class="p-3"><span class="px-2 py-0.5 rounded text-[10px] border ${prioBadge}">${doc.priority || 'Regular'}</span></td>
                        <td class="p-3">
                            <span class="block font-bold text-slate-800 text-[11px]">${doc.requestingOffice || 'N/A'}</span>
                            <span class="px-2 py-0.5 bg-slate-100 text-slate-700 rounded text-[9px] border border-slate-200 font-bold inline-block mt-0.5">${doc.category || 'General'}</span>
                        </td>
                        <td class="p-3">
                            <div class="font-bold text-slate-800">${doc.title}</div>
                            <div class="text-[11px] text-slate-500 truncate max-w-xs">${doc.particulars}</div>
                        </td>
                        <td class="p-3 font-bold text-slate-800 font-mono">₱${parseFloat(doc.amount).toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
                        <td class="p-3 font-bold text-xs ${isDone ? 'text-emerald-700' : 'text-blue-700'}">
                            ${doc.currentLocation}
                            ${overdue ? '<span class="block text-[9px] text-red-600 font-extrabold animate-pulse">SLA Overdue >48h</span>' : ''}
                        </td>
                        <td class="p-3">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold ${isDone ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : 'bg-amber-100 text-amber-800 border border-amber-200'}">
                                ${isDone ? 'Completed' : 'In Transit'}
                            </span>
                        </td>
                        <td class="p-3 text-right">
                            <div class="flex items-center justify-end gap-1.5">
                                <button onclick="openTimelineModal('${doc.id}')" title="Audit Trail" class="p-1.5 bg-blue-50 text-blue-700 hover:bg-blue-100 rounded-lg text-xs font-bold transition border border-blue-200">
                                    <i data-lucide="history" class="w-3.5 h-3.5"></i>
                                </button>
                                <button onclick="openPrintModal('${doc.id}')" title="Routing Slip" class="px-2.5 py-1.5 bg-slate-800 hover:bg-slate-900 text-white rounded-lg text-xs font-bold flex items-center gap-1 transition">
                                    <i data-lucide="printer" class="w-3.5 h-3.5"></i> Slip
                                </button>
                                ${isAdmin ? `
                                <button onclick="deletePrDocument('${doc.id}')" title="Delete PR Record" class="p-1.5 bg-red-50 text-red-700 hover:bg-red-100 rounded-lg text-xs font-bold transition border border-red-200">
                                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                </button>
                                ` : ''}
                            </div>
                        </td>
                    </tr>
                `;
            }).join('');
            lucide.createIcons();
        }

        // ADMIN DELETE PR RECORD FUNCTION (Reflects across all offices and dashboards)
        function deletePrDocument(docId) {
            const doc = documents.find(d => d.id === docId);
            if (!doc) return;

            if (!currentUser || currentUser.role !== 'ADMIN') {
                showToast("Permission denied. Only BAC Admin can delete procurement records.", "error");
                return;
            }

            if (confirm(`Are you sure you want to permanently delete PR #${doc.prNumber} ("${doc.title}")? This will remove it from all departmental dashboards and workflow pipelines.`)) {
                documents = documents.filter(d => d.id !== docId);
                saveState();
                refreshAllViews();
                showToast(`PR #${doc.prNumber} successfully deleted from system records.`, "success");
            }
        }

        function renderOfficeHistoryTable() {
            const tbody = document.getElementById('officeDocsTableBody');
            if (!tbody) return;

            const targetOffice = (currentUser.role === 'ADMIN' && inspectedOffice) ? inspectedOffice : currentUser.office;
            const isBacInspectingOthers = (currentUser.role === 'ADMIN' && targetOffice !== currentUser.office);

            document.getElementById('officeHistoryTitle').innerText = `${targetOffice} - Document Activity & Actions`;

            const receiveBtn = document.getElementById('btnReceiveScanQR');
            const inspectionBadge = document.getElementById('bacInspectionWarningBadge');

            if (isBacInspectingOthers) {
                receiveBtn.disabled = true;
                receiveBtn.className = "px-4 py-2 bg-slate-300 text-slate-500 rounded-xl text-xs font-bold cursor-not-allowed flex items-center gap-2 shadow-none";
                inspectionBadge.classList.remove('hidden');
            } else {
                receiveBtn.disabled = false;
                receiveBtn.className = "px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold flex items-center gap-2 shadow-sm transition";
                inspectionBadge.classList.add('hidden');
            }

            const monthFilter = document.getElementById('filterMonth').value;
            const yearFilter = document.getElementById('filterYear').value;
            const statusFilter = document.getElementById('filterStatus').value;
            const searchKw = document.getElementById('searchKeyword').value.toLowerCase();

            let logsToDisplay = [];

            documents.forEach(doc => {
                const officeLog = doc.logs.find(l => l.office === targetOffice);
                if (officeLog) {
                    const date = new Date(officeLog.receivedAt || doc.createdAt);
                    const docMonth = date.getMonth().toString();
                    const docYear = date.getFullYear().toString();

                    let monthMatch = (monthFilter === 'ALL' || monthFilter === docMonth);
                    let yearMatch = (yearFilter === 'ALL' || yearFilter === docYear);
                    let isApproved = (officeLog.action === 'APPROVED' || officeLog.action.startsWith('FORWARDED'));
                    let isReturned = officeLog.action === 'RETURNED';

                    let statusMatch = (statusFilter === 'ALL' || 
                        (statusFilter === 'Received' && !isApproved && !isReturned) || 
                        (statusFilter === 'Approved' && isApproved) ||
                        (statusFilter === 'Returned' && isReturned));
                    
                    let searchMatch = !searchKw || 
                        doc.prNumber.toLowerCase().includes(searchKw) || 
                        doc.title.toLowerCase().includes(searchKw) ||
                        (doc.requestingOffice || '').toLowerCase().includes(searchKw);

                    if (monthMatch && yearMatch && statusMatch && searchMatch) {
                        logsToDisplay.push({ doc, officeLog });
                    }
                }
            });

            if (logsToDisplay.length === 0) {
                tbody.innerHTML = `<tr><td colspan="9" class="p-6 text-center text-slate-400 italic font-medium">No activity records found for ${targetOffice}.</td></tr>`;
                return;
            }

            tbody.innerHTML = logsToDisplay.map(({ doc, officeLog }) => {
                const isApproved = officeLog.action === 'APPROVED' || officeLog.action.startsWith('FORWARDED');
                const isReturned = officeLog.action === 'RETURNED';
                const isForwardedCaterer = officeLog.action === 'FORWARDED_CATERER';
                const isForwardedReqOffice = officeLog.action === 'FORWARDED_REQUESTING';

                const recDate = officeLog.receivedAt ? new Date(officeLog.receivedAt).toLocaleString() : '-';
                const appDate = officeLog.approvedAt ? new Date(officeLog.approvedAt).toLocaleString() : 'Pending Sign-Off';

                let actionCellHTML = "";

                if (isBacInspectingOthers) {
                    actionCellHTML = `<span class="text-slate-400 text-[11px] font-bold italic">Inspection Mode</span>`;
                } else if (!isApproved && !isReturned) {
                    actionCellHTML = `
                        <div class="flex items-center justify-end gap-1">
                            <button onclick="promptActionRemarks('${doc.id}', 'APPROVE')" class="px-2.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold transition shadow-sm flex items-center gap-1">
                                <i data-lucide="check-check" class="w-3.5 h-3.5"></i> Approve
                            </button>
                            <button onclick="promptActionRemarks('${doc.id}', 'RETURN')" class="px-2.5 py-1.5 bg-red-600 hover:bg-red-700 text-white rounded-lg text-xs font-bold transition shadow-sm flex items-center gap-1">
                                <i data-lucide="corner-up-left" class="w-3.5 h-3.5"></i> Return
                            </button>
                        </div>
                    `;
                } else if (isReturned) {
                    actionCellHTML = `<span class="text-red-600 text-[11px] font-bold flex items-center justify-end gap-1"><i data-lucide="alert-circle" class="w-4 h-4"></i> Returned / Revision</span>`;
                } else {
                    if (targetOffice === "MAYOR'S OFFICE" && doc.category === "Catering Services") {
                        if (isForwardedCaterer) {
                            actionCellHTML = `<span class="text-indigo-600 text-[11px] font-extrabold flex items-center justify-end gap-1"><i data-lucide="utensils" class="w-4 h-4"></i> Forwarded to Caterer</span>`;
                        } else {
                            actionCellHTML = `
                                <button onclick="forwardToCaterer('${doc.id}')" class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-xs font-bold transition shadow-sm flex items-center gap-1 ml-auto">
                                    <i data-lucide="utensils" class="w-3.5 h-3.5"></i> Forward to Caterer
                                </button>
                            `;
                        }
                    } else if (targetOffice === "GENERAL SERVICES OFFICE" && doc.category === "Office Supplies") {
                        if (isForwardedReqOffice) {
                            actionCellHTML = `<span class="text-purple-600 text-[11px] font-extrabold flex items-center justify-end gap-1"><i data-lucide="package-check" class="w-4 h-4"></i> Delivered to Requesting Office</span>`;
                        } else {
                            actionCellHTML = `
                                <button onclick="forwardToRequestingOffice('${doc.id}')" class="px-3 py-1.5 bg-purple-600 hover:bg-purple-700 text-white rounded-lg text-xs font-bold transition shadow-sm flex items-center gap-1 ml-auto">
                                    <i data-lucide="package-check" class="w-3.5 h-3.5"></i> Deliver to Requesting Dept
                                </button>
                            `;
                        }
                    } else {
                        actionCellHTML = `
                            <span class="text-emerald-600 text-[11px] font-extrabold flex items-center justify-end gap-1">
                                <i data-lucide="shield-check" class="w-4 h-4"></i> Signed & Approved
                            </span>
                        `;
                    }
                }

                let statusBadgeText = `Received at ${targetOffice}`;
                if (isReturned) statusBadgeText = `Returned for Revision`;
                else if (isForwardedCaterer) statusBadgeText = "Completed - Forwarded to Caterer";
                else if (isForwardedReqOffice) statusBadgeText = "Completed - Forwarded to Requesting Dept";
                else if (isApproved) statusBadgeText = `Approved by Head`;

                let statusBadgeClass = "bg-amber-100 text-amber-800 border-amber-200";
                if (isReturned) statusBadgeClass = "bg-red-100 text-red-800 border-red-200";
                else if (isApproved) statusBadgeClass = "bg-emerald-100 text-emerald-800 border-emerald-200";

                return `
                    <tr class="hover:bg-slate-50 transition">
                        <td class="p-3 font-mono font-extrabold text-blue-700">${doc.prNumber}</td>
                        <td class="p-3 font-semibold"><span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-slate-100 border border-slate-200">${doc.priority || 'Regular'}</span></td>
                        <td class="p-3">
                            <span class="block font-bold text-slate-800 text-[11px]">${doc.requestingOffice || 'N/A'}</span>
                            <span class="px-2 py-0.5 bg-slate-100 text-slate-700 rounded text-[9px] border border-slate-200 font-bold inline-block mt-0.5">${doc.category || 'General'}</span>
                        </td>
                        <td class="p-3">
                            <div class="font-bold text-slate-800">${doc.title}</div>
                            <div class="text-[11px] text-slate-500">${doc.particulars}</div>
                            ${officeLog.remarks ? `<div class="text-[10px] text-slate-600 italic mt-0.5 bg-slate-50 p-1 rounded border border-slate-200">Remarks: ${officeLog.remarks}</div>` : ''}
                        </td>
                        <td class="p-3 font-bold text-slate-800 font-mono">₱${parseFloat(doc.amount).toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
                        <td class="p-3 text-slate-600 font-mono text-[11px]">${recDate}</td>
                        <td class="p-3 font-mono text-[11px] ${isApproved ? 'text-emerald-600 font-bold' : (isReturned ? 'text-red-600 font-bold' : 'text-slate-400 italic')}">${appDate}</td>
                        <td class="p-3">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold border ${statusBadgeClass}">
                                ${statusBadgeText}
                            </span>
                        </td>
                        <td class="p-3 text-right">
                            ${actionCellHTML}
                        </td>
                    </tr>
                `;
            }).join('');
            lucide.createIcons();
        }

        // AUDIT TIMELINE MODAL LOGIC
        function openTimelineModal(docId) {
            const doc = documents.find(d => d.id === docId);
            if (!doc) return;

            document.getElementById('timelineModalPrSubtitle').innerText = `${doc.prNumber} - ${doc.title} (${doc.requestingOffice || 'N/A'}) - ₱${parseFloat(doc.amount).toLocaleString('en-US', {minimumFractionDigits: 2})}`;
            const wrapper = document.getElementById('timelineContentWrapper');

            wrapper.innerHTML = doc.logs.map((log, index) => {
                const recText = log.receivedAt ? new Date(log.receivedAt).toLocaleString() : 'Not Received Yet';
                const appText = log.approvedAt ? new Date(log.approvedAt).toLocaleString() : 'Pending Action';

                let durationText = "";
                if (log.receivedAt && log.approvedAt) {
                    const hrs = Math.round((new Date(log.approvedAt) - new Date(log.receivedAt)) / (1000 * 3600) * 10) / 10;
                    durationText = `Time spent: ${hrs} hours`;
                }

                let iconName = "clock";
                let badgeColor = "bg-amber-100 text-amber-800 border-amber-300";

                if (log.action === 'APPROVED' || log.action.startsWith('FORWARDED')) {
                    iconName = "check-circle-2";
                    badgeColor = "bg-emerald-100 text-emerald-800 border-emerald-300";
                } else if (log.action === 'RETURNED') {
                    iconName = "alert-circle";
                    badgeColor = "bg-red-100 text-red-800 border-red-300";
                }

                return `
                    <div class="flex gap-3 relative pb-4">
                        ${index < doc.logs.length - 1 ? '<div class="absolute left-4 top-8 bottom-0 w-0.5 bg-slate-200"></div>' : ''}
                        <div class="w-8 h-8 rounded-full bg-blue-50 border border-blue-200 flex items-center justify-center text-blue-700 flex-shrink-0 z-10">
                            <i data-lucide="${iconName}" class="w-4 h-4"></i>
                        </div>
                        <div class="flex-1 bg-slate-50 border border-slate-200 p-3 rounded-xl text-xs space-y-1">
                            <div class="flex justify-between items-center font-bold">
                                <span class="text-slate-900">${log.office}</span>
                                <span class="px-2 py-0.5 rounded text-[10px] border ${badgeColor}">${log.action}</span>
                            </div>
                            <div class="text-[11px] text-slate-500 grid grid-cols-2 gap-2 pt-1 font-mono">
                                <div><span class="font-bold text-slate-400 uppercase text-[9px]">Received:</span> ${recText}</div>
                                <div><span class="font-bold text-slate-400 uppercase text-[9px]">Action Time:</span> ${appText}</div>
                            </div>
                            ${durationText ? `<div class="text-[10px] font-bold text-slate-600 font-mono pt-0.5">${durationText}</div>` : ''}
                            ${log.remarks ? `<div class="text-[11px] text-slate-700 bg-white p-2 rounded-lg border border-slate-200 mt-1 italic">"${log.remarks}"</div>` : ''}
                        </div>
                    </div>
                `;
            }).join('');

            document.getElementById('timelineModal').classList.remove('hidden');
            lucide.createIcons();
        }

        function closeTimelineModal() {
            document.getElementById('timelineModal').classList.add('hidden');
        }

        // PROMPT REMARKS & APPROVE/RETURN MODAL ENGINE
        function promptActionRemarks(docId, actionType) {
            document.getElementById('actionModalDocId').value = docId;
            document.getElementById('actionModalType').value = actionType;
            document.getElementById('actionRemarksText').value = '';
            document.getElementById('actionPinInput').value = '';

            const title = document.getElementById('actionModalTitle');
            const label = document.getElementById('actionRemarksLabel');
            const submitBtn = document.getElementById('actionSubmitBtn');

            if (actionType === 'APPROVE') {
                title.innerHTML = `<i data-lucide="shield-check" class="w-5 h-5 text-emerald-600"></i> Approve & Sign PR Document`;
                label.innerText = "Approval Remarks / Compliance Notes (Optional)";
                submitBtn.innerText = "Confirm Sign-Off & Forward";
                submitBtn.className = "px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition";
            } else {
                title.innerHTML = `<i data-lucide="corner-up-left" class="w-5 h-5 text-red-600"></i> Return Document for Revision`;
                label.innerText = "Mandatory Rejection / Revision Remarks";
                submitBtn.innerText = "Confirm Return Document";
                submitBtn.className = "px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-xl text-xs font-bold transition";
            }

            document.getElementById('actionRemarksModal').classList.remove('hidden');
            lucide.createIcons();
        }

        function closeActionRemarksModal() {
            document.getElementById('actionRemarksModal').classList.add('hidden');
        }

        function handleExecuteActionWithRemarks(e) {
            e.preventDefault();
            const docId = document.getElementById('actionModalDocId').value;
            const actionType = document.getElementById('actionModalType').value;
            const remarks = document.getElementById('actionRemarksText').value.trim();
            const pin = document.getElementById('actionPinInput').value.trim();

            const officeCreds = officeCredentials[currentUser.office];
            if (!officeCreds || officeCreds.pin !== pin) {
                showToast("INVALID SECURITY PIN CODE. Action denied.", "error");
                return;
            }

            if (actionType === 'RETURN' && !remarks) {
                showToast("Rejection remarks are mandatory when returning a document.", "warning");
                return;
            }

            if (actionType === 'APPROVE') {
                approveDocument(docId, remarks);
            } else {
                returnDocument(docId, remarks);
            }

            closeActionRemarksModal();
        }

        // SECURITY CREDENTIALS ENGINE & ADMIN RESET RULE
        function renderPinSettingsTable() {
            const tbody = document.getElementById('officePinTableBody');
            if (!tbody) return;

            const registeredOffices = Object.keys(officeCredentials);

            if (registeredOffices.length === 0) {
                tbody.innerHTML = `<tr><td colspan="6" class="p-6 text-center text-slate-400 italic font-medium">No offices registered yet. Use the 'Register New Office' tab above to create an account.</td></tr>`;
                return;
            }

            tbody.innerHTML = registeredOffices.map(office => {
                const cred = officeCredentials[office];
                const isCurrentLoggedOffice = (currentUser && currentUser.office === office);
                const isAdmin = cred.role === 'ADMIN';

                return `
                    <tr class="${isCurrentLoggedOffice ? 'bg-blue-50/60 border-l-4 border-blue-600' : 'hover:bg-slate-50'} transition">
                        <td class="p-3 font-bold text-slate-900 flex items-center gap-1.5">
                            ${office}
                            ${isCurrentLoggedOffice ? '<span class="px-1.5 py-0.5 bg-blue-600 text-white rounded text-[9px] font-bold uppercase">Logged In</span>' : ''}
                        </td>
                        <td class="p-3"><span class="px-2 py-0.5 rounded text-[10px] font-bold ${isAdmin ? 'bg-blue-100 text-blue-700' : 'bg-slate-100 text-slate-600'}">${cred.role}</span></td>
                        <td class="p-3">
                            ${isCurrentLoggedOffice ? `<input type="email" value="${cred.email || ''}" id="email-input-${office.replace(/[^a-zA-Z0-9]/g, '')}" class="w-40 px-2 py-1 bg-white border border-slate-300 rounded text-xs font-mono">` : `<span class="font-mono text-slate-500">${cred.email || 'N/A'}</span>`}
                        </td>
                        <td class="p-3">
                            ${isCurrentLoggedOffice ? `<input type="password" value="${cred.password}" id="pass-input-${office.replace(/[^a-zA-Z0-9]/g, '')}" class="w-28 px-2 py-1 bg-white border border-slate-300 rounded text-xs font-mono">` : `<span class="font-mono text-slate-400">••••••••</span>`}
                        </td>
                        <td class="p-3">
                            ${isCurrentLoggedOffice ? `<input type="password" maxlength="4" value="${cred.pin}" id="pin-input-${office.replace(/[^a-zA-Z0-9]/g, '')}" class="w-20 px-2 py-1 bg-white border border-slate-300 rounded font-mono text-center tracking-widest text-xs">` : `<span class="font-mono text-slate-400 text-center">••••</span>`}
                        </td>
                        <td class="p-3 text-right space-x-2">
                            ${isCurrentLoggedOffice ? `<button onclick="updateOfficeSecurity('${office}')" class="px-3 py-1 bg-blue-700 hover:bg-blue-800 text-white rounded-lg text-xs font-bold transition shadow-sm">Update</button>` : ''}
                            <button onclick="deleteOfficeAccount('${office}')" class="px-2.5 py-1 bg-red-600 hover:bg-red-700 text-white rounded-lg text-xs font-bold transition shadow-sm" title="Delete Account">Delete</button>
                        </td>
                    </tr>
                `;
            }).join('');
            lucide.createIcons();
        }

        function updateOfficeSecurity(office) {
            if (!currentUser || currentUser.office !== office) {
                showToast("SECURITY RESTRICTION: You are only allowed to modify your logged-in office credentials.", "error");
                return;
            }

            const cleanOfficeId = office.replace(/[^a-zA-Z0-9]/g, '');
            const newEmail = document.getElementById(`email-input-${cleanOfficeId}`).value.trim();
            const newPassword = document.getElementById(`pass-input-${cleanOfficeId}`).value.trim();
            const newPin = document.getElementById(`pin-input-${cleanOfficeId}`).value.trim();

            if (!newEmail || !newPassword || !/^\d{4}$/.test(newPin)) {
                showToast("Please provide valid inputs (PIN must be 4 digits).", "warning");
                return;
            }

            officeCredentials[office].email = newEmail;
            officeCredentials[office].password = newPassword;
            officeCredentials[office].pin = newPin;
            currentUser.email = newEmail;

            saveState();
            showToast(`Security Credentials for ${office} successfully updated!`, "success");
            renderPinSettingsTable();
        }

        function deleteOfficeAccount(office) {
            const cred = officeCredentials[office];
            const isAdmin = cred && cred.role === 'ADMIN';

            if (isAdmin) {
                if (confirm(`WARNING: You are about to delete the Administrator account ("${office}"). Doing so will instantly wipe all custom office accounts, transactions, and reset the entire system back to its initial state. Proceed?`)) {
                    // Full System Reset back to default
                    localStorage.removeItem('pts_credentials');
                    localStorage.removeItem('pts_documents');
                    localStorage.removeItem('pts_sync_queue');
                    localStorage.removeItem('pts_current_user');

                    officeCredentials = {};
                    documents = JSON.parse(JSON.stringify(DEFAULT_DOCUMENTS));
                    syncQueue = [];
                    currentUser = null;
                    inspectedOffice = null;

                    showAuth();
                    showToast("Admin account deleted. System fully reset to initial default state.", "success");
                }
            } else {
                if (currentUser && currentUser.office === office) {
                    showToast("You cannot delete your currently logged-in account from this screen. Please logout first.", "error");
                    return;
                }

                if (confirm(`Are you sure you want to delete the account for "${office}"?`)) {
                    delete officeCredentials[office];
                    saveState();
                    populateAuthOfficeDropdowns();
                    updateRegistrationRoleDropdown();
                    renderPinSettingsTable();
                    showToast(`Office account "${office}" has been deleted.`, "info");
                }
            }
        }

        // SCANNER CONTROLS
        function openScanQRModal() {
            if (currentUser.role === 'ADMIN' && inspectedOffice && inspectedOffice !== currentUser.office) {
                showToast("BAC Admin is in View-Only mode for this office.", "warning");
                return;
            }

            document.getElementById('scanQrModal').classList.remove('hidden');
            document.getElementById('manualPrInput').value = '';
            document.getElementById('receiveRemarksInput').value = '';
            clearAttachedQrImage();
            switchScanMode('live');
        }

        function closeScanQRModal() {
            stopLiveScanner();
            clearAttachedQrImage();
            document.getElementById('scanQrModal').classList.add('hidden');
        }

        function switchScanMode(mode) {
            const btnLive = document.getElementById('btnModeLive');
            const btnFile = document.getElementById('btnModeFile');
            const liveContainer = document.getElementById('liveCameraContainer');
            const fileContainer = document.getElementById('fileUploadContainer');

            if (mode === 'live') {
                btnLive.className = "flex-1 py-1.5 rounded-lg text-center bg-white shadow-sm text-emerald-700 font-extrabold transition";
                btnFile.className = "flex-1 py-1.5 rounded-lg text-center text-slate-600 hover:text-slate-900 transition";
                liveContainer.classList.remove('hidden');
                fileContainer.classList.add('hidden');
                startLiveScanner();
            } else {
                btnFile.className = "flex-1 py-1.5 rounded-lg text-center bg-white shadow-sm text-emerald-700 font-extrabold transition";
                btnLive.className = "flex-1 py-1.5 rounded-lg text-center text-slate-600 hover:text-slate-900 transition";
                fileContainer.classList.remove('hidden');
                liveContainer.classList.add('hidden');
                stopLiveScanner();
            }
        }

        function startLiveScanner() {
            if (html5QrCodeScanner) {
                stopLiveScanner();
            }

            const fallbackEl = document.getElementById('liveCamFallback');
            fallbackEl.classList.remove('hidden');

            html5QrCodeScanner = new Html5Qrcode("reader");
            const config = { fps: 10, qrbox: { width: 180, height: 180 } };

            html5QrCodeScanner.start(
                { facingMode: "environment" },
                config,
                onQrCodeSuccess,
                onQrCodeError
            ).then(() => {
                fallbackEl.classList.add('hidden');
            }).catch(err => {
                fallbackEl.classList.remove('hidden');
                fallbackEl.innerHTML = `
                    <i data-lucide="camera-off" class="w-8 h-8 text-amber-400 mx-auto mb-2"></i>
                    <p class="text-xs text-amber-300">Live camera stream unavailable or permission denied.</p>
                    <p class="text-[10px] text-slate-400 mt-1">Please use Upload Image mode or Manual Input.</p>
                `;
                lucide.createIcons();
            });
        }

        function stopLiveScanner() {
            if (html5QrCodeScanner) {
                if (html5QrCodeScanner.isScanning) {
                    html5QrCodeScanner.stop().then(() => {
                        html5QrCodeScanner.clear();
                        html5QrCodeScanner = null;
                    }).catch(err => {
                        console.warn("Scanner stop warning:", err);
                        html5QrCodeScanner = null;
                    });
                } else {
                    try { html5QrCodeScanner.clear(); } catch (e) {}
                    html5QrCodeScanner = null;
                }
            }
        }

        function onQrCodeSuccess(decodedText) {
            try {
                const parsed = JSON.parse(decodedText);
                document.getElementById('manualPrInput').value = parsed.pr || parsed.id || decodedText;
            } catch (e) {
                document.getElementById('manualPrInput').value = decodedText;
            }
            showToast(`QR Code decoded: ${document.getElementById('manualPrInput').value}`, "success");
        }

        function onQrCodeError(error) {}

        function handleQrFileUpload(event) {
            const file = event.target.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = function(e) {
                const imgPreview = document.getElementById('attachedQrPreview');
                imgPreview.src = e.target.result;
                document.getElementById('previewImageWrapper').classList.remove('hidden');
            };
            reader.readAsDataURL(file);

            const html5QrCode = new Html5Qrcode("reader");
            html5QrCode.scanFile(file, true)
                .then(qrCodeMessage => {
                    try {
                        const parsed = JSON.parse(qrCodeMessage);
                        document.getElementById('manualPrInput').value = parsed.pr || parsed.id || qrCodeMessage;
                    } catch (e) {
                        document.getElementById('manualPrInput').value = qrCodeMessage;
                    }
                    showToast(`QR Code decoded successfully!`, "success");
                })
                .catch(err => {
                    showToast("Could not decode QR code from image.", "error");
                });
        }

        function clearAttachedQrImage() {
            const input = document.getElementById('qrFileInput');
            if (input) input.value = '';
            
            const imgPreview = document.getElementById('attachedQrPreview');
            if (imgPreview) imgPreview.src = '';

            const wrapper = document.getElementById('previewImageWrapper');
            if (wrapper) wrapper.classList.add('hidden');
        }

        function executeQRScanSimulation() {
            const inputVal = document.getElementById('manualPrInput').value.trim();
            const remarks = document.getElementById('receiveRemarksInput').value.trim();

            if (!inputVal) {
                showToast("Please scan or enter a valid PR Tracking ID.", "warning");
                return;
            }

            const targetDoc = documents.find(d => 
                d.prNumber.toLowerCase() === inputVal.toLowerCase() || 
                d.id.toLowerCase() === inputVal.toLowerCase()
            );

            if (!targetDoc) {
                showToast(`No procurement record found with PR Tracking ID: "${inputVal}"`, "error");
                return;
            }

            receiveDocument(targetDoc.id, currentUser.office, remarks);
            clearAttachedQrImage();
            closeScanQRModal();
        }

        function receiveDocument(docId, receivingOffice, remarks = "") {
            const doc = documents.find(d => d.id === docId);
            if (!doc) return;

            if (currentUser.role === 'ADMIN' && inspectedOffice && inspectedOffice !== currentUser.office) {
                showToast("PERMISSIONS ERROR: BAC Office cannot receive documents for other departments.", "error");
                return;
            }

            const targetOfficeIndex = WORKFLOW_PIPELINE.indexOf(receivingOffice);
            if (targetOfficeIndex > 0) {
                const previousOfficeNeeded = WORKFLOW_PIPELINE[targetOfficeIndex - 1];
                const prevLog = doc.logs.find(l => l.office === previousOfficeNeeded);

                if (!prevLog || (prevLog.action !== 'APPROVED' && !prevLog.action.startsWith('FORWARDED'))) {
                    showToast(`WORKFLOW ERROR: PR #${doc.prNumber} is still pending sign-off at '${previousOfficeNeeded}'!`, "error");
                    return;
                }
            }

            const actionPayload = {
                type: "RECEIVE",
                docId: doc.id,
                office: receivingOffice,
                remarks: remarks,
                timestamp: new Date().toISOString()
            };

            if (!isOnline) {
                syncQueue.push(actionPayload);
                saveState();
                updateNetworkBadge();
                showToast(`[OFFLINE MODE] PR ${doc.prNumber} scan queued locally.`, "warning");
            } else {
                applyReceiveAction(doc, receivingOffice, remarks, actionPayload.timestamp);
            }

            refreshAllViews();
        }

        function applyReceiveAction(doc, office, remarks, timestamp) {
            doc.currentLocation = office;

            let existingLog = doc.logs.find(l => l.office === office);
            if (!existingLog) {
                doc.logs.push({
                    office: office,
                    action: "RECEIVED",
                    remarks: remarks || "Received hard copy package",
                    receivedAt: timestamp,
                    approvedAt: null
                });
            } else {
                existingLog.action = "RECEIVED";
                if (remarks) existingLog.remarks = remarks;
                existingLog.receivedAt = timestamp;
            }
            saveState();
            showToast(`Document ${doc.prNumber} successfully received at ${office}`, "success");
        }

        function approveDocument(docId, remarks = "") {
            const doc = documents.find(d => d.id === docId);
            if (!doc) return;

            const targetOffice = currentUser.office;
            const actionPayload = {
                type: "APPROVE",
                docId: doc.id,
                office: targetOffice,
                remarks: remarks,
                timestamp: new Date().toISOString()
            };

            if (!isOnline) {
                syncQueue.push(actionPayload);
                saveState();
                updateNetworkBadge();
                showToast(`[OFFLINE MODE] Approval recorded in sync queue.`, "warning");
            } else {
                applyApproveAction(doc, targetOffice, remarks, actionPayload.timestamp);
            }

            refreshAllViews();
        }

        function applyApproveAction(doc, office, remarks, timestamp) {
            let existingLog = doc.logs.find(l => l.office === office);
            if (existingLog) {
                existingLog.action = "APPROVED";
                if (remarks) existingLog.remarks = remarks;
                existingLog.approvedAt = timestamp;
            } else {
                doc.logs.push({
                    office: office,
                    action: "APPROVED",
                    remarks: remarks || "Signed & Approved by Department Head",
                    receivedAt: timestamp,
                    approvedAt: timestamp
                });
            }

            saveState();
            showToast(`Document ${doc.prNumber} signed and approved by ${office}!`, "success");
        }

        function returnDocument(docId, remarks) {
            const doc = documents.find(d => d.id === docId);
            if (!doc) return;

            const currentDept = currentUser.office;
            const targetOfficeIndex = WORKFLOW_PIPELINE.indexOf(currentDept);
            const prevOffice = targetOfficeIndex > 0 ? WORKFLOW_PIPELINE[targetOfficeIndex - 1] : "BAC OFFICE";

            doc.currentLocation = prevOffice;

            let existingLog = doc.logs.find(l => l.office === currentDept);
            if (existingLog) {
                existingLog.action = "RETURNED";
                existingLog.remarks = remarks;
                existingLog.approvedAt = new Date().toISOString();
            }

            saveState();
            showToast(`PR #${doc.prNumber} returned to ${prevOffice} for revision.`, "warning");
            refreshAllViews();
        }

        function forwardToCaterer(docId) {
            const doc = documents.find(d => d.id === docId);
            if (!doc) return;

            const mayorLog = doc.logs.find(l => l.office === "MAYOR'S OFFICE");
            if (mayorLog) {
                mayorLog.action = "FORWARDED_CATERER";
                doc.currentLocation = "COMPLETED";
                saveState();
                showToast(`PR #${doc.prNumber} officially FORWARDED TO THE CATERER! Workflow completed.`, "success");
                refreshAllViews();
            }
        }

        function forwardToRequestingOffice(docId) {
            const doc = documents.find(d => d.id === docId);
            if (!doc) return;

            const gsoLog = doc.logs.find(l => l.office === "GENERAL SERVICES OFFICE");
            if (gsoLog) {
                gsoLog.action = "FORWARDED_REQUESTING";
                doc.currentLocation = "COMPLETED";
                saveState();
                showToast(`PR #${doc.prNumber} officially DELIVERED TO REQUESTING DEPT! Workflow completed.`, "success");
                refreshAllViews();
            }
        }

        function handleCreateDoc(e) {
            e.preventDefault();
            const prNumber = document.getElementById('docPrNumber').value.trim();
            const priority = document.getElementById('docPriority').value;
            const category = document.getElementById('docCategory').value;
            const requestingOffice = document.getElementById('docRequestingOffice').value;
            const title = document.getElementById('docTitle').value.trim();
            const particulars = document.getElementById('docParticulars').value.trim();
            const amount = parseFloat(document.getElementById('docAmount').value);

            if (!requestingOffice) {
                showToast("Please select a Requesting Office / Department.", "warning");
                return;
            }

            const exists = documents.some(d => d.prNumber.toLowerCase() === prNumber.toLowerCase());
            if (exists) {
                showToast(`PR Number "${prNumber}" already exists in system records.`, "error");
                return;
            }

            const now = new Date().toISOString();
            const newDoc = {
                id: `DOC-${Date.now()}`,
                prNumber,
                priority,
                category,
                requestingOffice,
                title,
                particulars,
                amount,
                originatingOffice: "BAC OFFICE",
                currentLocation: "BAC OFFICE",
                createdAt: now,
                logs: [
                    { office: "BAC OFFICE", action: "APPROVED", remarks: `PR Generated on behalf of ${requestingOffice}`, receivedAt: now, approvedAt: now }
                ]
            };

            documents.unshift(newDoc);
            saveState();
            closeNewDocModal();
            showToast(`New Document PR #${prNumber} for ${requestingOffice} created successfully!`, "success");
            refreshAllViews();
        }

        function exportToCSV() {
            if (documents.length === 0) {
                showToast("No data available to export.", "warning");
                return;
            }

            let csvContent = "data:text/csv;charset=utf-8,";
            csvContent += "PR Number,Priority,Requesting Office,Category,Title,Particulars,Amount,Current Location,Created Date\n";

            documents.forEach(d => {
                const row = [
                    `"${d.prNumber}"`,
                    `"${d.priority || 'Regular'}"`,
                    `"${d.requestingOffice || 'N/A'}"`,
                    `"${d.category || ''}"`,
                    `"${(d.title || '').replace(/"/g, '""')}"`,
                    `"${(d.particulars || '').replace(/"/g, '""')}"`,
                    d.amount,
                    `"${d.currentLocation}"`,
                    `"${d.createdAt}"`
                ].join(",");
                csvContent += row + "\n";
            });

            const encodedUri = encodeURI(csvContent);
            const link = document.createElement("a");
            link.setAttribute("href", encodedUri);
            link.setAttribute("download", `Procurement_Master_Report_${new Date().toISOString().slice(0,10)}.csv`);
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            showToast("Master Records exported to CSV format successfully!", "success");
        }

        function exportToJSON() {
            if (documents.length === 0) {
                showToast("No data available to export.", "warning");
                return;
            }

            const dataStr = "data:text/json;charset=utf-8," + encodeURIComponent(JSON.stringify(documents, null, 2));
            const downloadAnchor = document.createElement('a');
            downloadAnchor.setAttribute("href", dataStr);
            downloadAnchor.setAttribute("download", `Procurement_Master_Data_${new Date().toISOString().slice(0,10)}.json`);
            document.body.appendChild(downloadAnchor);
            downloadAnchor.click();
            downloadAnchor.remove();
            showToast("Master Records exported to JSON format successfully!", "success");
        }

        function openPrintModal(docId) {
            const doc = documents.find(d => d.id === docId);
            if (!doc) return;

            document.getElementById('printPrNo').innerText = doc.prNumber;
            document.getElementById('printRequestingOffice').innerText = doc.requestingOffice || 'N/A';
            document.getElementById('printCategory').innerText = `${doc.priority || 'Regular'} Priority | ${doc.category || 'General'}`;
            document.getElementById('printTitle').innerText = doc.title;
            document.getElementById('printParticulars').innerText = doc.particulars;
            document.getElementById('printAmount').innerText = '₱' + parseFloat(doc.amount).toLocaleString('en-US', {minimumFractionDigits: 2});

            const qrContainer = document.getElementById('qrcode');
            qrContainer.innerHTML = '';
            new QRCode(qrContainer, {
                text: JSON.stringify({ id: doc.id, pr: doc.prNumber }),
                width: 90,
                height: 90
            });

            const mapOfficeRow = (officeKey, recId, appId, statId) => {
                const log = doc.logs.find(l => l.office === officeKey);
                const recEl = document.getElementById(recId);
                const appEl = document.getElementById(appId);
                const statEl = document.getElementById(statId);

                if (recEl && appEl && statEl) {
                    if (log) {
                        recEl.innerText = log.receivedAt ? new Date(log.receivedAt).toLocaleDateString() : '-';
                        appEl.innerText = log.approvedAt ? new Date(log.approvedAt).toLocaleDateString() : 'Pending';
                        
                        let statusDisplay = log.action;
                        if (officeKey === "GENERAL SERVICES OFFICE") {
                            if (log.action === "FORWARDED_REQUESTING" || doc.currentLocation === "COMPLETED") {
                                statusDisplay = "APPROVED_FORWARDED";
                            } else if (log.action === "APPROVED") {
                                statusDisplay = "APPROVED";
                            } else if (log.action === "RECEIVED") {
                                statusDisplay = "RECEIVED";
                            }
                        } else if (officeKey === "MAYOR'S OFFICE" && log.action === "FORWARDED_CATERER") {
                            statusDisplay = "APPROVED_FORWARDED";
                        } else {
                            if (log.action === "APPROVED") statusDisplay = "APPROVED";
                            else if (log.action === "RECEIVED") statusDisplay = "RECEIVED";
                            else if (log.action === "RETURNED") statusDisplay = "RETURNED";
                        }

                        statEl.innerText = statusDisplay;

                        if (statusDisplay.includes("APPROVED") || statusDisplay === "FORWARDED") {
                            statEl.className = 'font-bold text-emerald-700';
                        } else if (statusDisplay === "RETURNED") {
                            statEl.className = 'font-bold text-red-600';
                        } else {
                            statEl.className = 'font-bold text-amber-700';
                        }
                    } else {
                        recEl.innerText = '-';
                        appEl.innerText = '-';
                        statEl.innerText = 'Pending';
                        statEl.className = 'text-slate-400';
                    }
                }
            };

            mapOfficeRow("BAC OFFICE", "print-bac-rec", "print-bac-app", "print-bac-stat");
            mapOfficeRow("BUDGET OFFICE", "print-budget-rec", "print-budget-app", "print-budget-stat");
            mapOfficeRow("ACCOUNTING OFFICE", "print-acct-rec", "print-acct-app", "print-acct-stat");
            mapOfficeRow("TREASURER'S OFFICE", "print-treas-rec", "print-treas-app", "print-treas-stat");
            mapOfficeRow("MAYOR'S OFFICE", "print-mayor-rec", "print-mayor-app", "print-mayor-stat");
            mapOfficeRow("GENERAL SERVICES OFFICE", "print-gso-rec", "print-gso-app", "print-gso-stat");

            document.getElementById('printModal').classList.remove('hidden');
        }

        function closePrintModal() {
            document.getElementById('printModal').classList.add('hidden');
        }

        function triggerPrintMasterSummary() {
            const summaryTbody = document.getElementById('printableSummaryTbody');
            const timestampEl = document.getElementById('printReportTimestamp');
            
            if (timestampEl) {
                timestampEl.innerText = `Generated on: ${new Date().toLocaleString('en-US', { dateStyle: 'full', timeStyle: 'short' })}`;
            }

            if (summaryTbody) {
                if (documents.length === 0) {
                    summaryTbody.innerHTML = `<tr><td colspan="8" class="p-4 text-center italic">No procurement records available.</td></tr>`;
                } else {
                    summaryTbody.innerHTML = documents.map(doc => {
                        const isDone = doc.currentLocation === 'COMPLETED';
                        return `
                            <tr class="border-b border-slate-800 text-[11px]">
                                <td class="p-2 font-mono font-bold border-r border-slate-800">${doc.prNumber}</td>
                                <td class="p-2 border-r border-slate-800 font-bold">${doc.priority || 'Regular'}</td>
                                <td class="p-2 border-r border-slate-800 font-bold">${doc.requestingOffice || 'N/A'}</td>
                                <td class="p-2 border-r border-slate-800">${doc.category || 'General'}</td>
                                <td class="p-2 border-r border-slate-800">
                                    <div class="font-bold">${doc.title}</div>
                                    <div class="text-[10px] text-slate-600">${doc.particulars}</div>
                                </td>
                                <td class="p-2 font-bold border-r border-slate-800">₱${parseFloat(doc.amount).toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
                                <td class="p-2 border-r border-slate-800">${doc.currentLocation}</td>
                                <td class="p-2 font-bold">${isDone ? 'Completed' : 'In Transit'}</td>
                            </tr>
                        `;
                    }).join('');
                }
            }

            showToast("Searching for system printers & launching print dialog...", "info");
            setTimeout(() => { window.print(); }, 300);
        }

        function openForgotPasswordModal() { document.getElementById('forgotPasswordModal').classList.remove('hidden'); }
        function closeForgotPasswordModal() { document.getElementById('forgotPasswordModal').classList.add('hidden'); }
        function handleForgotPasswordSubmit(e) {
            e.preventDefault();
            const office = document.getElementById('resetOfficeSelect').value;
            const email = officeCredentials[office]?.email;

            if (!email) {
                showToast(`No registered email found for ${office}.`, "error");
                return;
            }

            closeForgotPasswordModal();
            showToast(`Password reset instructions sent to ${email}`, "email");
        }

        function openNewDocModal() {
            document.getElementById('newDocModal').classList.remove('hidden');
            const prNumInput = document.getElementById('docPrNumber');
            if (prNumInput && !prNumInput.value) {
                const randomNum = Math.floor(1000 + Math.random() * 9000);
                prNumInput.value = `PR-2026-03-${randomNum}`;
            }
        }

        function closeNewDocModal() {
            document.getElementById('newDocModal').classList.add('hidden');
        }

        function showToast(message, type = "info") {
            const container = document.getElementById('toastContainer');
            const toast = document.createElement('div');
            
            let bgClass = "bg-slate-900 text-white border-slate-700";
            let icon = "info";

            if (type === "success") {
                bgClass = "bg-emerald-950/90 text-emerald-100 border-emerald-700";
                icon = "check-circle";
            } else if (type === "error") {
                bgClass = "bg-red-950/90 text-red-100 border-red-700";
                icon = "alert-circle";
            } else if (type === "warning") {
                bgClass = "bg-amber-950/90 text-amber-100 border-amber-700";
                icon = "alert-triangle";
            } else if (type === "email") {
                bgClass = "bg-blue-950/90 text-blue-100 border-blue-700";
                icon = "mail";
            }

            toast.className = `p-3.5 rounded-xl border shadow-2xl flex items-start gap-3 text-xs transition-all transform translate-y-2 duration-300 pointer-events-auto ${bgClass}`;
            toast.innerHTML = `
                <i data-lucide="${icon}" class="w-4 h-4 mt-0.5 flex-shrink-0"></i>
                <div class="flex-1 font-semibold leading-relaxed">${message}</div>
                <button onclick="this.parentElement.remove()" class="text-slate-400 hover:text-white"><i data-lucide="x" class="w-3.5 h-3.5"></i></button>
            `;

            container.appendChild(toast);
            lucide.createIcons();

            setTimeout(() => {
                toast.classList.add('opacity-0', 'translate-y-[-10px]');
                setTimeout(() => toast.remove(), 300);
            }, 5000);
        }
    </script>
</body>
</html>
