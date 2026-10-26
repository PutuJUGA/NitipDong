/**
 * PresidenKu - Single Page Application
 * Implementasi REST API & Fetch API (Praktikum Pemrograman Web - Week 4)
 * 
 * - Task 01: Connect to API (fetch API & parsing JSON)
 * - Task 02: Display API Data (DOM manipulation dinamis dari API ke interface PresidenKu)
 * - Task 03: Handle API State (feedback loading spinner, success data, error handling & tombol Coba Lagi)
 */

// ========================================================
// 1. ELEMEN DOM & STATE SELEKSI
// ========================================================
const programContainer = document.getElementById('programContainer');
const loadingSpinner = document.getElementById('loadingSpinner');
const errorContainer = document.getElementById('errorContainer');
const errorMessageEl = document.getElementById('errorMessage');
const btnRetry = document.getElementById('btnRetry');
const btnFetchNormal = document.getElementById('btnFetchNormal');
const btnFetchError = document.getElementById('btnFetchError');
const statusDot = document.getElementById('statusDot');
const statusText = document.getElementById('statusText');
const searchInput = document.getElementById('searchInput');
const categorySelect = document.getElementById('categorySelect');

// State aplikasi
let currentEndpoint = 'program.json';
let allPrograms = [];

// ========================================================
// TASK 03: HANDLE API STATE (FEEDBACK UI)
// ========================================================
function setApiState(state, errorMsg = '') {
  if (state === 'loading') {
    // Tampilkan Loading Spinner
    loadingSpinner.classList.remove('hidden');
    errorContainer.classList.add('hidden');
    programContainer.classList.add('hidden');

    // Update status badge
    statusDot.className = 'w-2.5 h-2.5 rounded-full bg-amber-500 animate-ping';
    statusText.textContent = 'Mengambil data dari API...';
  } else if (state === 'error') {
    // Tampilkan Error Container & Tombol Coba Lagi
    loadingSpinner.classList.add('hidden');
    errorContainer.classList.remove('hidden');
    programContainer.classList.add('hidden');
    errorMessageEl.textContent = errorMsg;

    // Update status badge
    statusDot.className = 'w-2.5 h-2.5 rounded-full bg-rose-500';
    statusText.textContent = 'Gagal memuat API (Error)';
  } else if (state === 'success') {
    // Tampilkan Data Sukses
    loadingSpinner.classList.add('hidden');
    errorContainer.classList.add('hidden');
    programContainer.classList.remove('hidden');

    // Update status badge
    statusDot.className = 'w-2.5 h-2.5 rounded-full bg-emerald-500';
    statusText.textContent = `Berhasil (${allPrograms.length} Program Dimuat)`;
  }
}

// ========================================================
// TASK 01: CONNECT TO API (FETCH API & PARSING JSON)
// ========================================================
async function fetchProgramData(endpoint = 'program.json') {
  currentEndpoint = endpoint;
  setApiState('loading');

  try {
    // Simulasi latency 500ms agar feedback loading spinner terlihat jelas
    await new Promise((resolve) => setTimeout(resolve, 500));

    // Eksekusi Fetch API
    const response = await fetch(endpoint);

    // Periksa status HTTP response
    if (!response.ok) {
      throw new Error(`HTTP Error: ${response.status} (${response.statusText || 'Gagal menghubungi server'})`);
    }

    // Parsing JSON
    const data = await response.json();

    // Deteksi jika response berupa mock error dari file JSON (seperti error-404.json)
    if (data.status && data.status >= 400) {
      throw new Error(data.message || 'Data tidak ditemukan di server.');
    }

    // Simpan ke state
    allPrograms = Array.isArray(data) ? data : [];
    
    // Task 03: Set Success State
    setApiState('success');

    // Task 02: Render data ke DOM
    renderPrograms();
  } catch (error) {
    console.error('[API Fetch Error]:', error);
    // Task 03: Set Error State
    setApiState('error', error.message || 'Terjadi gangguan saat mengambil data API.');
  }
}

// ========================================================
// TASK 02: DISPLAY API DATA (DOM MANIPULATION DINAMIS)
// ========================================================
function renderPrograms() {
  // Bersihkan elemen DOM kontainer terlebih dahulu
  programContainer.innerHTML = '';

  const query = searchInput.value.toLowerCase().trim();
  const selectedCategory = categorySelect.value;

  // Filter data sesuai input pencarian dan kategori
  const filtered = allPrograms.filter((item) => {
    const matchCategory = selectedCategory === 'Semua' || item.kategori === selectedCategory;
    const matchQuery =
      item.judul.toLowerCase().includes(query) ||
      item.kandidat.toLowerCase().includes(query) ||
      item.deskripsi.toLowerCase().includes(query);
    return matchCategory && matchQuery;
  });

  // Jika tidak ada data yang cocok dengan filter
  if (filtered.length === 0) {
    const emptyCard = document.createElement('div');
    emptyCard.className = 'col-span-full py-12 text-center bg-white rounded-2xl border border-dashed border-slate-300 text-slate-500';
    emptyCard.innerHTML = `
      <p class="font-bold text-base text-slate-700">Tidak ada program kerja yang cocok.</p>
      <p class="text-xs text-slate-400 mt-1">Silakan coba kata kunci atau kategori filter yang lain.</p>
    `;
    programContainer.appendChild(emptyCard);
    return;
  }

  // Manipulasi DOM secara dinamis untuk setiap item program kerja
  filtered.forEach((program) => {
    // 1. Buat elemen kartu baru menggunakan document.createElement
    const card = document.createElement('article');
    card.className = 'bg-white rounded-2xl p-6 border border-slate-200/90 shadow-xs hover:shadow-lg hover:-translate-y-1 transition duration-200 flex flex-col justify-between';

    // 2. Isi konten kartu dengan data dari API NitipDong
    card.innerHTML = `
      <div>
        <div class="flex items-start justify-between gap-3 mb-3">
          <div class="w-12 h-12 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center text-2xl shadow-inner">
            ${program.icon || '🍱'}
          </div>
          <div class="text-right">
            <span class="inline-block px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-orange-100 text-orange-700">
              #${program.nomorUrut}
            </span>
            <p class="text-[11px] text-slate-400 font-semibold mt-1">${program.kategori}</p>
          </div>
        </div>

        <h3 class="font-bold text-slate-900 text-base leading-snug hover:text-orange-500 transition">
          ${program.judul}
        </h3>

        <p class="text-xs text-slate-500 font-medium mt-1 mb-3">
          📍 ${program.kandidat}
        </p>

        <p class="text-xs text-slate-600 leading-relaxed mb-4 line-clamp-3">
          ${program.deskripsi}
        </p>
      </div>

      <div class="pt-4 border-t border-slate-100 space-y-2">
        <div class="flex items-center justify-between text-xs">
          <span class="text-slate-400 font-medium">Estimasi Waktu:</span>
          <span class="font-bold text-slate-700">${program.target}</span>
        </div>
        <div class="flex items-center justify-between text-xs">
          <span class="text-slate-400 font-medium">Harga Menu:</span>
          <span class="font-extrabold text-orange-600 text-sm">${program.anggaran}</span>
        </div>
        <div class="flex items-center justify-between text-xs pt-1">
          <span class="text-slate-400 font-medium">Status:</span>
          <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-orange-50 text-orange-700 border border-orange-200">
            ${program.status}
          </span>
        </div>
      </div>
    `;

    // 3. Masukkan kartu ke dalam kontainer (appendChild)
    programContainer.appendChild(card);
  });
}

// ========================================================
// EVENT LISTENERS & INTERAKTIVITAS
// ========================================================

// Task 03: Tombol Coba Lagi (Retry Button)
btnRetry.addEventListener('click', () => {
  // Panggil ulang fetch dengan endpoint normal
  fetchProgramData('program.json');
});

// Tombol uji fetch normal
btnFetchNormal.addEventListener('click', () => {
  fetchProgramData('program.json');
});

// Tombol uji simulasi error 404
btnFetchError.addEventListener('click', () => {
  fetchProgramData('error-404.json');
});

// Filter pencarian teks
searchInput.addEventListener('input', () => {
  renderPrograms();
});

// Filter kategori dropdown
categorySelect.addEventListener('change', () => {
  renderPrograms();
});

// Eksekusi pemanggilan pertama saat halaman dimuat
document.addEventListener('DOMContentLoaded', () => {
  fetchProgramData('program.json');
});
