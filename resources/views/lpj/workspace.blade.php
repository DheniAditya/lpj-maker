<x-app-layout> {{-- WRAPPER UTAMA --}}
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-8 px-4"> {{-- WARNING MODE TAMU --}}
        @guest
        <div x-data="{ open: false }" class="mb-6 bg-yellow-50 border border-yellow-200 rounded-xl overflow-hidden shadow-sm">
            <button @click="open = !open" class="w-full flex items-center justify-between p-4 bg-yellow-100 hover:bg-yellow-200 transition-colors text-left text-yellow-800">
                <div class="flex items-center gap-3">
                    <svg xmlns="http:
                            <path fill-rule=" evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                    <div>
                        <span class="font-bold text-sm block">Mode Tamu</span>
                        <span class="text-xs opacity-80">Anda belum memiliki fitur <span class="font-bold">History</span></span>
                    </div>
                </div>
                <svg xmlns="http:
                        <path fill-rule=" evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            </button>
            <div x-show="open" class="p-4 border-t border-yellow-200 text-sm text-yellow-800 bg-yellow-50">
                <p>Anda berada di mode tamu. Riwayat pekerjaan anda akan terhapus otomatis setelah halaman ini di tutup. Silahkan download LPJ anda sebelum menutup halaman.</p>
                <div class="mt-3 flex gap-3">
                    <a href="{{ route('register') }}" class="text-blue-600 font-bold underline hover:text-blue-800">Daftar Akun</a>
                    <span class="text-gray-400">atau</span>
                    <a href="{{ route('login') }}" class="text-blue-600 font-bold underline hover:text-blue-800">Login</a>
                    <p>untuk menyimpan riwayat pekerjaan anda. </p>
                </div>
            </div>
        </div>
        @endguest {{-- HEADER INFO --}}
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 mb-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <div class="mb-6">
                    {{-- Tampilan Nama Penanggungjawab (Mode Display) --}}
                    <div class="flex items-center gap-3 group" id="creator-display-container">
                        <span class="shrink-0 px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-blue-100 text-blue-700 tracking-wider">
                            Dibuat oleh:
                        </span>
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-medium text-slate-800" id="current-creator-name">
                                {{ $report->creator_name }}
                            </span> <button onclick="showEditCreator()" class="p-2 hover:bg-slate-100 rounded-full text-slate-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                </svg>
                            </button>
                        </div>
                    </div> {{-- Form Edit Nama (Mode Input - Hidden) --}}
                    <form id="form-edit-creator" action="{{ route('lpj.update-creator', $report->slug) }}" method="POST" class="hidden">
                        @csrf
                        @method('PUT')
                        <div class="flex flex-wrap items-center gap-2">
                            <div class="relative flex-1 max-w-[250px]">
                                <input type="text" name="creator_name" id="input-creator" value="{{ $report->creator_name }}"
                                    class="w-full rounded-lg border-slate-300 focus:ring-blue-500 focus:border-blue-500 text-sm py-1.5 shadow-sm px-3">
                            </div>
                            <div class="flex items-center gap-1">
                                <button type="submit" id="btn-save-creator"
                                    class="bg-blue-600 text-white px-3 py-1.5 rounded-lg text-xs font-semibold hover:bg-blue-700 transition flex items-center gap-2 shadow-sm">
                                    <span id="creator-btn-text">Simpan</span>
                                    <div id="creator-btn-loading" class="hidden animate-spin h-3 w-3 border-2 border-white border-t-transparent rounded-full"></div>
                                </button> <button type="button" onclick="hideEditCreator()"
                                    class="bg-white text-slate-600 border border-slate-200 px-3 py-1.5 rounded-lg text-xs font-semibold hover:bg-slate-50 transition">
                                    Batal
                                </button>
                            </div>
                        </div>
                    </form>
                </div> <!-- <h2 class="text-2xl font-bold text-slate-800 tracking-tight">{{ $report->title }}</h2> -->
                <div class="flex items-center gap-3 mb-6" id="title-display-container">
                    <h1 class="text-2xl font-bold text-slate-800" id="current-lpj-title">
                        {{ $report->title }}
                    </h1>
                    <button onclick="showEditForm()" class="p-2 hover:bg-slate-100 rounded-full text-slate-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                        </svg>
                    </button>
                </div>{{-- Form Edit Tanpa Reload --}}
                <form id="form-edit-title" action="{{ route('lpj.update-title', $report->slug) }}" method="POST" class="hidden mb-6">
                    @csrf
                    @method('PUT')
                    <div class="flex flex-col sm:flex-row gap-2">
                        <input type="text" name="title" id="input-title" value="{{ $report->title }}"
                            class="rounded-xl border-slate-300 focus:ring-slate-500 focus:border-slate-500 flex-1 shadow-sm">
                        <div class="flex gap-2">
                            <button type="submit" id="btn-save-title" class="bg-slate-800 text-white px-4 py-2 rounded-xl hover:bg-slate-700 transition flex-1 sm:flex-none flex items-center justify-center gap-2">
                                <span id="title-btn-text">Simpan</span>
                                <div id="title-btn-loading" class="hidden animate-spin h-4 w-4 border-2 border-white border-t-transparent rounded-full"></div>
                            </button>
                            <button type="button" onclick="hideEditForm()" class="bg-slate-200 text-slate-700 px-4 py-2 rounded-xl hover:bg-slate-300 transition">
                                Batal
                            </button>
                        </div>
                    </div>
                </form>
                <div class="flex items-center gap-4 mt-2 text-sm">
                    <div class="px-3 py-1 rounded-lg bg-slate-50 border border-slate-100">
                        <span class="text-slate-400 text-xs uppercase font-bold mr-1">Sisa:</span>
                        <span class="font-bold text-base {{ $report->balance >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                            Rp {{ number_format($report->balance, 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            </div> {{-- Tombol Download PDF --}}
            <a href="{{ route('lpj.download', $report->slug) }}"
                id="downloadBtn"
                onclick="startDownloadAnimation(this)"
                class="group bg-slate-800 text-white px-5 py-3 rounded-xl font-bold text-sm flex items-center hover:bg-slate-700 transition shadow-lg hover:shadow-xl transform hover:-translate-y-0.5"> {{-- Ikon Download Asli --}}
                <svg id="downloadIcon" class="w-5 h-5 mr-2 text-slate-400 group-hover:text-white transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                </svg> {{-- Ikon Loading (Hidden by default) --}}
                <svg id="loadingSpinner" class="hidden animate-spin h-5 w-5 mr-2 text-white" xmlns="http:
        <circle class=" opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg> <span id="btnText">Download PDF</span>
            </a>
        </div> {{-- Form & Tabel --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start"> {{-- Form Tambah (Sticky) --}}
            <div class="col-span-1 lg:sticky top-8 z-30">
                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-lg shadow-slate-200/50">
                    <h3 class="text-lg font-bold mb-4 text-slate-700 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Tambah Transaksi
                    </h3> @if(session('success'))
                    <div class="bg-emerald-50 text-emerald-700 p-3 rounded-lg text-sm mb-4 border border-emerald-100 flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        {{ session('success') }}
                    </div>
                    @endif <form id="form-transaksi" action="{{ route('lpj.entry.store', $report->slug) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        {{-- Pilihan Debit/Kredit --}}
                        <div class="grid grid-cols-2 gap-3 mb-4">
                            <div>
                                <input type="radio" name="type" value="debit" id="debit" class="hidden peer/debit">
                                <label for="debit" class="block w-full p-3 text-center border-2 border-slate-100 bg-slate-50 rounded-xl cursor-pointer peer-checked/debit:bg-emerald-500 peer-checked/debit:text-white peer-checked/debit:border-emerald-600 transition-all hover:bg-white">
                                    <span class="block font-bold text-sm">Debit</span>
                                    <span class="text-[10px] opacity-80 uppercase">Masuk</span>
                                </label>
                            </div>
                            <div>
                                <input type="radio" name="type" value="credit" id="credit" class="hidden peer/credit" checked>
                                <label for="credit" class="block w-full p-3 text-center border-2 border-slate-100 bg-slate-50 rounded-xl cursor-pointer peer-checked/credit:bg-rose-500 peer-checked/credit:text-white peer-checked/credit:border-rose-600 transition-all hover:bg-white">
                                    <span class="block font-bold text-sm">Kredit</span>
                                    <span class="text-[10px] opacity-80 uppercase">Keluar</span>
                                </label>
                            </div>
                        </div> {{-- Input Form --}}
                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-400 uppercase mb-1 ml-1">Keterangan</label>
                                <input type="text" name="description" class="w-full border-slate-200 bg-slate-50 rounded-xl p-3 text-sm focus:ring-blue-500 focus:border-blue-500 focus:bg-white transition" placeholder="Contoh: Beli Makan Siang" required>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-400 uppercase mb-1 ml-1">Nominal (Rp)</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-3 text-slate-400 font-bold">Rp</span>
                                    <input type="text" id="amount_display" class="w-full border-slate-200 bg-slate-50 rounded-xl p-3 pl-10 text-sm font-mono font-bold text-slate-700 focus:ring-blue-500 focus:border-blue-500 focus:bg-white transition" placeholder="0" inputmode="numeric" required>
                                </div>
                                <input type="hidden" name="amount" id="amount_real">
                            </div> 
                            
                            <div x-data="{ 
                                images: [], 
                                showCamera: false, 
                                stream: null,
                                isMirrored: false,    async startCamera() {
                                    this.showCamera = true;
                                    try {
                                        this.stream = await navigator.mediaDevices.getUserMedia({ video: true });
                                        $refs.videoPreview.srcObject = this.stream;
                                    } catch (error) {
                                        alert('Gagal akses kamera: ' + error.message);
                                        this.showCamera = false;
                                    }
                                },    stopCamera() {
                                    if (this.stream) {
                                        this.stream.getTracks().forEach(track => track.stop());
                                        this.stream = null;
                                    }
                                    this.showCamera = false;
                                },    addImages(files) {
                                    Array.from(files).forEach(file => {
                                        const reader = new FileReader();
                                        reader.onload = (e) => {
                                            this.images.push({
                                                file: file,
                                                preview: e.target.result
                                            });
                                            this.updateInputFiles();
                                        };
                                        reader.readAsDataURL(file);
                                    });
                                },    takePicture() {
                                    const canvas = document.createElement('canvas');
                                    const video = $refs.videoPreview;
                                    const ctx = canvas.getContext('2d');
                                    canvas.width = video.videoWidth;
                                    canvas.height = video.videoHeight;        if (this.isMirrored) {
                                        ctx.translate(canvas.width, 0);
                                        ctx.scale(-1, 1);
                                    }
                                    ctx.drawImage(video, 0, 0);        canvas.toBlob((blob) => {
                                        const file = new File([blob], `camera_${Date.now()}.jpg`, { type: 'image/jpeg' });
                                        this.addImages([file]);
                                        this.stopCamera();
                                    }, 'image/jpeg');
                                },    removeImage(index) {
                                    this.images.splice(index, 1);
                                    this.updateInputFiles();
                                },    updateInputFiles() {
                                    const dt = new DataTransfer();
                                    this.images.forEach(img => dt.items.add(img.file));
                                    $refs.fileInput.files = dt.files;
                                }
                            }">
                                <label class="block text-xs font-bold text-slate-400 uppercase mb-2 ml-1">Bukti Transaksi</label> <input type="file" name="images[]" multiple accept="image/*" class="hidden" x-ref="fileInput"
                                    @change="addImages($event.target.files)">
                                <div class="grid grid-cols-2 gap-3 mb-4">
                                    <button type="button" @click="$refs.fileInput.click()" class="h-20 border-2 border-dashed border-slate-300 rounded-xl flex flex-col items-center justify-center hover:bg-slate-50 hover:border-blue-400 transition group">
                                        <svg class="w-7 h-7 text-slate-400 group-hover:text-blue-500 mb-1" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                            <path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                                        </svg>
                                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 group-hover:text-blue-600">Upload</span>
                                    </button> <button type="button" @click="startCamera()" class="h-20 border-2 border-dashed border-slate-300 rounded-xl flex flex-col items-center justify-center hover:bg-slate-50 hover:border-blue-400 transition group">
                                        <svg class="w-7 h-7 text-slate-400 group-hover:text-blue-500 mb-1" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.5 4h-5L7 7H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-3l-2.5-3z" />
                                            <circle cx="12" cy="13" r="3" />
                                        </svg>
                                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 group-hover:text-blue-600">Kamera</span>
                                    </button>
                                </div>
                                <div class="grid grid-cols-3 gap-2" x-show="images.length > 0">
                                    <template x-for="(img, index) in images" :key="index">
                                        <div class="relative group aspect-square">
                                            <div class="w-full h-full rounded-xl border-[3px] border-slate-700 overflow-hidden bg-slate-800">
                                                <img :src="img.preview" class="w-full h-full object-cover">
                                            </div>
                                            <button type="button" @click="removeImage(index)"
                                                class="absolute -top-1.5 -right-1.5 w-5 h-5 bg-red-500 text-white rounded-full flex items-center justify-center shadow-lg hover:bg-red-600 transition">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </template>
                                </div> {{-- MODAL KAMERA TETAP SAMA --}}
                                <div x-show="showCamera" x-cloak class="fixed inset-0 z-[60] flex items-center justify-center bg-black/80 backdrop-blur-sm p-4">
                                    <div class="bg-white rounded-2xl overflow-hidden shadow-2xl w-full max-w-md">
                                        <div class="relative bg-black h-72 flex items-center justify-center">
                                            <video x-ref="videoPreview" autoplay playsinline :class="{ 'scale-x-[-1]': isMirrored }" class="w-full h-full object-cover"></video>
                                        </div>
                                        <div class="p-4 flex justify-between gap-4 bg-slate-900">
                                            <button type="button" @click="stopCamera()" class="text-white text-sm font-bold">Batal</button>
                                            <button type="button" @click="isMirrored = !isMirrored" class="text-blue-300 text-sm font-bold">Mirror</button>
                                            <button type="button" @click="takePicture()" class="px-6 py-2 bg-blue-600 rounded-full text-white font-bold hover:bg-blue-500">Jepret</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> <button type="submit" id="btn-save"
                            class="flex items-center justify-center w-full mt-6 bg-blue-600 text-white p-3 rounded-xl font-bold hover:bg-blue-700 transition shadow-lg shadow-blue-500/30 transform active:scale-95"> <span id="btn-text">Simpan Transaksi</span> {{-- Container Loading --}}
                            <span id="btn-loading" class="hidden ml-2 flex items-center">
                                <svg class="animate-spin h-5 w-5 text-white" xmlns="http:
                                    <circle class=" opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </span> </button>
                    </form>
                </div>
            </div> {{-- Tabel History --}}
            <div class="col-span-1 lg:col-span-2">
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="p-5 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                        <h3 class="font-bold text-slate-700">Riwayat Transaksi</h3>
                        <span class="text-xs font-bold bg-slate-200 text-slate-600 px-2 py-1 rounded">{{ $report->entries->count() }} Data</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-100">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="p-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Tgl</th>
                                    <th class="p-4 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">Keterangan</th>
                                    <th class="p-4 text-center text-xs font-bold text-slate-400 uppercase tracking-wider">Nota</th>
                                    <th class="p-4 text-right text-xs font-bold text-slate-400 uppercase tracking-wider">Nominal</th>
                                    <th class="p-4 text-center text-xs font-bold text-slate-400 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse ($report->entries as $entry)
                                {{-- PERUBAHAN 1: Tambahkan ID unik di tr --}}
                                <tr id="entry-{{ $entry->id }}" class="hover:bg-blue-50/50 transition duration-150">
                                    <td class="p-4 text-sm text-slate-600 whitespace-nowrap">{{ $entry->created_at->format('d/m/y') }}</td>
                                    <td class="p-4 text-sm font-medium text-slate-800">{{ $entry->description }}</td>
                                    <td class="p-4 text-center">
                                        @if($entry->receipt_image_path)
                                        <a href="{{ asset('storage/' . $entry->receipt_image_path) }}"
                                            target="_blank"
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-blue-100 text-blue-600 hover:bg-blue-200 transition"
                                            title="Lihat Nota">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </a>
                                        @else
                                        <span class="p-4 text-sm font-medium text-red-400">Belum Ada
                                        </span>
                                        @endif
                                    <td class="p-4 text-right font-mono text-sm tracking-tight">
                                        @if($entry->type == 'debit')
                                        <span class="text-emerald-600 bg-emerald-50 px-2 py-1 rounded font-bold">+ {{ number_format($entry->amount, 0, ',', '.') }}</span>
                                        @else
                                        <span class="text-rose-600 bg-rose-50 px-2 py-1 rounded font-bold">- {{ number_format($entry->amount, 0, ',', '.') }}</span>
                                        @endif
                                    </td>
                                    <td class="p-4">
                                        <div class="flex items-center justify-center gap-2">
                                            {{-- Tombol Edit (Tetap seperti semula) --}}
                                            <button onclick="openEditModal(this)"
                                                data-id="{{ $entry->id }}"
                                                data-type="{{ $entry->type }}"
                                                data-description="{{ $entry->description }}"
                                                data-amount="{{ $entry->amount }}" {{-- Angka mentah dari DB --}}
                                                data-date="{{ $entry->created_at->format('Y-m-d') }}" {{-- Wajib Y-m-d --}}
                                                data-action-url="{{ route('lpj.entry.update', $entry->id) }}"
                                                class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-full transition"> <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                                </svg>
                                            </button> {{-- PERUBAHAN 2: Tombol Hapus Baru (Tanpa Form) --}}
                                            <button type="button"
                                                class="btn-delete-entry p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-full transition"
                                                data-url="{{ route('lpj.entry.destroy', $entry->id) }}"
                                                data-id="{{ $entry->id }}"
                                                title="Hapus Transaksi">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="p-8 text-center text-slate-400">
                                        Belum ada transaksi
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div> {{-- 4. FLOATING ACTION BUTTON --}}
    <div class="fixed bottom-6 right-6 z-40">
        @auth
        <a href="{{ route('dashboard') }}" class="bg-slate-800 text-white px-6 py-3 rounded-full font-bold shadow-2xl hover:bg-slate-700 transition flex items-center gap-2 border border-slate-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Dashboard
        </a>
        @else
        <a href="{{ route('lpj.create') }}" class="bg-blue-600 text-white px-6 py-3 rounded-full font-bold shadow-2xl hover:bg-blue-500 transition flex items-center gap-2 hover:-translate-y-1 transform">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Buat Baru
        </a>
        @endauth
    </div> {{-- 5. MODAL EDIT --}}
    <div id="editModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl p-6 w-full max-w-lg transform transition-all scale-100">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-slate-800">Edit Transaksi</h3>
                <button onclick="closeEditModal()" class="text-slate-400 hover:text-rose-500 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <form id="editModalForm" method="POST" enctype="multipart/form-data">
                @csrf @method('PATCH') <div class="grid grid-cols-2 gap-3 mb-4">
                    <div>
                        <input type="radio" name="type" value="debit" id="edit_debit" class="hidden peer/edebit">
                        <label for="edit_debit" class="block w-full p-3 text-center border bg-slate-50 rounded-xl cursor-pointer peer-checked/edebit:bg-emerald-500 peer-checked/edebit:text-white transition">Debit</label>
                    </div>
                    <div>
                        <input type="radio" name="type" value="credit" id="edit_credit" class="hidden peer/ecredit">
                        <label for="edit_credit" class="block w-full p-3 text-center border bg-slate-50 rounded-xl cursor-pointer peer-checked/ecredit:bg-rose-500 peer-checked/ecredit:text-white transition">Kredit</label>
                    </div>
                </div>
                <div class="space-y-4">
                    <div>
                        <label class="text-xs font-bold text-slate-400 uppercase mb-1 ml-1">Tanggal</label>
                        <input type="date" name="created_at" id="edit_created_at" class="w-full border-slate-200 bg-slate-50 rounded-xl p-3 text-sm">
                    </div>
                    <div>
                        <label class="text-xs font-bold text-slate-400 uppercase mb-1 ml-1">Keterangan</label>
                        <input type="text" name="description" id="edit_description" class="w-full border-slate-200 bg-slate-50 rounded-xl p-3 text-sm">
                    </div>
                    <div>
                        <label class="text-xs font-bold text-slate-400 uppercase mb-1 ml-1">Nominal</label>
                        <input type="text" id="edit_amount_display" class="w-full border-slate-200 bg-slate-50 rounded-xl p-3 text-sm font-mono font-bold">
                        <input type="hidden" name="amount" id="edit_amount_real">
                    </div>
                    <div x-data="editReceiptComponent()" class="space-y-4"> <!-- LABEL -->
                        <label class="block text-xs font-bold text-slate-400 uppercase">
                            Ganti Nota (Hanya menerima file gambar)
                        </label> <!-- PILIHAN -->
                        <div class="grid grid-cols-2 gap-4"> <!-- Upload -->
                            <label class="cursor-pointer">
                                <input type="file"
                                    name="receipt_image[]"
                                    accept="image/*"
                                    multiple
                                    class="hidden"
                                    @change="handleUpload">
                                <div class="h-24 rounded-2xl border-2 border-dashed border-slate-300
                        bg-slate-100 text-slate-600
                        flex flex-col items-center justify-center
                        transition hover:bg-slate-200"> <svg class="w-6 h-6 mb-1 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1M12 4v12m0 0l-4-4m4 4l4-4" />
                                    </svg> <span class="text-sm font-medium">Upload</span>
                                </div>
                            </label> <!-- Kamera -->
                            <div @click="openCamera"
                                class="h-24 rounded-2xl border-2 border-dashed border-slate-300
                    bg-slate-100 text-slate-600
                    flex flex-col items-center justify-center
                    cursor-pointer transition hover:bg-slate-200"> <svg class="w-7 h-7 mb-1 text-slate-400"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    viewBox="0 0 24 24">
                                    <path d="M14.5 4h-5L7 7H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-3l-2.5-3z" />
                                    <circle cx="12" cy="13" r="3" />
                                </svg> <span class="text-sm font-medium">Kamera</span>
                            </div>
                        </div> <!-- CAMERA FLOATING -->
                        <div x-show="cameraOpen"
                            class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
                            <div class="bg-white p-4 rounded-2xl w-[400px] space-y-3">
                                <div class="relative rounded-xl overflow-hidden border">
                                    <video x-ref="video"
                                        autoplay
                                        playsinline
                                        class="w-full"
                                        :class="mirror ? 'scale-x-[-1]' : ''">
                                    </video>
                                </div>
                                <div class="flex flex-wrap gap-2"> <button type="button"
                                        @click="takePhoto"
                                        class="px-3 py-2 bg-emerald-500 text-white rounded-lg text-sm">
                                        Ambil
                                    </button> <button type="button"
                                        @click="toggleMirror"
                                        class="px-3 py-2 bg-slate-500 text-white rounded-lg text-sm">
                                        Mirror
                                    </button> <button type="button"
                                        @click="switchCamera"
                                        class="px-3 py-2 bg-blue-500 text-white rounded-lg text-sm">
                                        Switch
                                    </button> <button type="button"
                                        @click="closeCamera"
                                        class="px-3 py-2 bg-red-500 text-white rounded-lg text-sm">
                                        Tutup
                                    </button> </div>
                            </div>
                        </div> <!-- PREVIEW MULTIPLE -->
                        <div class="flex flex-wrap gap-4 mt-3"> <template x-for="(image, index) in images" :key="index">
                                <div class="relative w-40">
                                    <div class="bg-slate-700 p-2 rounded-xl">
                                        <img :src="image"
                                            class="w-full h-32 object-cover rounded-lg">
                                    </div> <!-- Hapus -->
                                    <button type="button"
                                        @click="removeImage(index)"
                                        class="absolute -top-2 -right-2 bg-red-500 text-white
                               w-6 h-6 rounded-full text-xs flex items-center justify-center shadow">
                                        ✕
                                    </button>
                                </div>
                            </template> </div>
                    </div>
                </div> <button type="submit" class="w-full mt-6 bg-blue-600 text-white p-3 rounded-xl font-bold hover:bg-blue-700 transition">Simpan Perubahan</button>
            </form>
        </div>
    </div> {{-- SCRIPT --}}
    <script>
        function closeEditModal() {
            editModal.classList.add('hidden');
        }

        function formatRupiah(numberString) {
            if (!numberString) return "";
            let clean = numberString.toString().replace(/[^\d]/g, '');
            return clean.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }

        function setupCurrencyInput(display, real) {
            if (!display || !real) return;
            display.addEventListener('input', (e) => {
                let val = e.target.value;
                let clean = val.replace(/[^\d]/g, '');
                real.value = clean;
                e.target.value = formatRupiah(clean);
            });
        }
        setupCurrencyInput(document.getElementById('amount_display'), document.getElementById('amount_real'));
        setupCurrencyInput(document.getElementById('edit_amount_display'), document.getElementById('edit_amount_real'));

        function openEditModal(btn) {
            const editForm = document.getElementById('editModalForm');
            const editDisplayInput = document.getElementById('edit_amount_display');
            const editRealInput = document.getElementById('edit_amount_real');
            editForm.action = btn.dataset.actionUrl;
            document.getElementById('edit_description').value = btn.dataset.description;
            document.getElementById('edit_created_at').value = btn.dataset.date;
            let rawAmount = btn.dataset.amount.toString();
            let cleanAmount = rawAmount.split('.')[0];
            editDisplayInput.value = formatRupiah(cleanAmount);
            editRealInput.value = cleanAmount;
            if (btn.dataset.type === 'debit') {
                document.getElementById('edit_debit').checked = true;
            } else {
                document.getElementById('edit_credit').checked = true;
            }
            document.getElementById('editModal').classList.remove('hidden');
        }
    </script>
</x-app-layout>
<script>
    $(document).ready(function() {
        $('#form-transaksi').on('submit', function(e) {
            e.preventDefault();
            let form = $(this);
            let formData = new FormData(this);
            let btn = $('#btn-save');
            let btnText = $('#btn-text');
            let btnLoading = $('#btn-loading');
            btn.prop('disabled', true);
            btnText.text('Menyimpan...');
            btnLoading.removeClass('hidden');
            $.ajax({
                url: form.attr('action'),
                method: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response.message,
                        timer: 600,
                        showConfirmButton: false
                    });
                    form[0].reset();
                    setTimeout(function() {
                        location.reload();
                    }, 2000);
                },
                error: function(xhr) {
                    let errors = xhr.responseJSON.errors;
                    let errorMessage = 'Terjadi kesalahan sistem.';
                    if (errors) {
                        errorMessage = Object.values(errors)[0][0];
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: errorMessage
                    });
                },
                complete: function() {
                    btn.prop('disabled', false);
                    btnText.text('Simpan Transaksi');
                    btnLoading.addClass('hidden');
                }
            });
        });
        $(document).on('click', '.btn-delete-entry', function() {
            let btn = $(this);
            let url = btn.data('url');
            let id = btn.data('id');
            Swal.fire({
                title: 'Hapus Transaksi?',
                text: "Data yang dihapus tidak bisa dikembalikan.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: url,
                        type: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Terhapus!',
                                text: response.message,
                                timer: 600,
                                showConfirmButton: false
                            });
                            $('#entry-' + id).fadeOut(500, function() {
                                $(this).remove();
                            });
                        },
                        error: function(xhr) {
                            Swal.fire('Gagal!', 'Terjadi kesalahan sistem.', 'error');
                        }
                    });
                }
            });
        });
        yyy
    });

    function startDownloadAnimation(btn) {
        const icon = document.getElementById('downloadIcon');
        const spinner = document.getElementById('loadingSpinner');
        const text = document.getElementById('btnText');
        btn.classList.add('pointer-events-none', 'opacity-80');
        icon.classList.add('hidden');
        spinner.classList.remove('hidden');
        text.innerText = 'Processing...';
        window.addEventListener('focus', () => {
            stopDownloadAnimation(btn, icon, spinner, text);
        }, {
            once: true
        });
        setTimeout(() => {
            stopDownloadAnimation(btn, icon, spinner, text);
        }, 10000);
    }

    function stopDownloadAnimation(btn, icon, spinner, text) {
        btn.classList.remove('pointer-events-none', 'opacity-80');
        icon.classList.remove('hidden');
        spinner.classList.add('hidden');
        text.innerText = 'Download PDF';
    }
    $(document).ready(function() {
        $('#form-edit-title').on('submit', function(e) {
            e.preventDefault();
            let form = $(this);
            let btn = $('#btn-save-title');
            let btnText = $('#title-btn-text');
            let btnLoading = $('#title-btn-loading');
            let newTitle = $('#input-title').val();
            btn.prop('disabled', true);
            btnText.text('Menyimpan...');
            btnLoading.removeClass('hidden');
            $.ajax({
                url: form.attr('action'),
                method: 'POST',
                data: form.serialize(),
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response.message,
                        timer: 600,
                        showConfirmButton: false
                    });
                    $('#current-lpj-title').text(response.new_title);
                    hideEditForm();
                },
                error: function(xhr) {
                    let errorMessage = xhr.responseJSON?.message || 'Gagal mengubah judul.';
                    Swal.fire('Gagal!', errorMessage, 'error');
                },
                complete: function() {
                    btn.prop('disabled', false);
                    btnText.text('Simpan');
                    btnLoading.addClass('hidden');
                }
            });
        });
    });

    function showEditForm() {
        $('#title-display-container').addClass('hidden');
        $('#form-edit-title').removeClass('hidden');
        $('#input-title').focus();
    }

    function hideEditForm() {
        $('#title-display-container').removeClass('hidden');
        $('#form-edit-title').addClass('hidden');
    }
    $(document).ready(function() {
        $('#form-edit-creator').on('submit', function(e) {
            e.preventDefault();
            let form = $(this);
            let btn = $('#btn-save-creator');
            let btnText = $('#creator-btn-text');
            let btnLoading = $('#creator-btn-loading');
            btn.prop('disabled', true);
            btnText.text('...');
            btnLoading.removeClass('hidden');
            $.ajax({
                url: form.attr('action'),
                method: 'POST',
                data: form.serialize(),
                success: function(response) {
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 800,
                        timerProgressBar: true
                    });
                    Toast.fire({
                        icon: 'success',
                        title: response.message
                    });
                    $('#current-creator-name').text(response.new_name);
                    hideEditCreator();
                },
                error: function() {
                    Swal.fire('Gagal!', 'Tidak bisa mengubah nama.', 'error');
                },
                complete: function() {
                    btn.prop('disabled', false);
                    btnText.text('Simpan');
                    btnLoading.addClass('hidden');
                }
            });
        });
    });

    function showEditCreator() {
        $('#creator-display-container').addClass('hidden');
        $('#form-edit-creator').removeClass('hidden');
        $('#input-creator').focus();
    }

    function hideEditCreator() {
        $('#creator-display-container').removeClass('hidden');
        $('#form-edit-creator').addClass('hidden');
    }

    function editReceiptComponent() {
        return {
            images: [],
            cameraOpen: false,
            stream: null,
            mirror: false,
            currentFacing: "environment",
            handleUpload(event) {
                const files = event.target.files;
                for (let file of files) {
                    const reader = new FileReader();
                    reader.onload = e => {
                        this.images.push(e.target.result);
                    };
                    reader.readAsDataURL(file);
                }
            },
            async openCamera() {
                this.cameraOpen = true;
                await this.startCamera();
            },
            async startCamera() {
                this.stream = await navigator.mediaDevices.getUserMedia({
                    video: {
                        facingMode: this.currentFacing
                    }
                });
                this.$refs.video.srcObject = this.stream;
            },
            takePhoto() {
                const canvas = document.createElement('canvas');
                const video = this.$refs.video;
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                const ctx = canvas.getContext('2d');
                if (this.mirror) {
                    ctx.translate(canvas.width, 0);
                    ctx.scale(-1, 1);
                }
                ctx.drawImage(video, 0, 0);
                this.images.push(canvas.toDataURL('image/png'));
            },
            toggleMirror() {
                this.mirror = !this.mirror;
            },
            async switchCamera() {
                this.currentFacing = this.currentFacing === "environment" ?
                    "user" :
                    "environment";
                this.stopCamera();
                await this.startCamera();
            },
            stopCamera() {
                if (this.stream) {
                    this.stream.getTracks().forEach(track => track.stop());
                }
            },
            closeCamera() {
                this.stopCamera();
                this.cameraOpen = false;
            },
            removeImage(index) {
                this.images.splice(index, 1);
            }
        }
    }
</script>