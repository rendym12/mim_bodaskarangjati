// ==============================================
// FILE: admin.js (FIXED)
// ADMIN PANEL - MI MUHAMMADIYAH BODASKARANGJATI
// VERSI: 3.2 - FIX GURU STAFF MODAL
// ==============================================

// ==============================================
// GLOBAL VARIABLES
// ==============================================
let currentDeleteId = null;

// ==============================================
// MAIN INITIALIZATION
// ==============================================
document.addEventListener('DOMContentLoaded', function() {
    console.log('✅ Admin JS v3.2 Loaded');
    
    // Inisialisasi semua modul
    initGlobalFunctions();
    initMobileSidebar();
    initProfileDropdown();
    initDeleteModal();
    initDeleteButtons();
    initModalCloseButtons();
    initAutoCloseAlerts();
    initFileUpload();
    initTableSearch();
    initFormValidation();
    initNumberInputs();
    initSmoothScroll();
    initIOSFix();
    initTouchScroll();
    initBackButtons();
    
    // Inisialisasi halaman spesifik
    if (document.querySelector('.login-page')) initLoginPage();
    if (document.querySelector('.dashboard-page')) initDashboard();
    if (document.querySelector('.visi-misi-page')) initVisiMisi();
    if (document.querySelector('.sejarah-page')) initSejarahPage();
    if (document.querySelector('.sambutan-page')) initSambutanPage();
    if (document.querySelector('.prestasi-page')) initPrestasiPage();
    if (document.querySelector('.ppdb-page')) initPpdbPage();
    if (document.querySelector('.pengumuman-page')) initPengumumanPage();
    if (document.querySelector('.pembiasaan-page')) initPembiasaanPage();
    if (document.querySelector('.kontak-page')) initKontakPage();
    if (document.querySelector('.hero-slider-page')) initHeroSliderPage();
    if (document.querySelector('.guru-page')) initGuruStaffPage();
    if (document.querySelector('.galeri-video-page')) initGaleriVideoPage();
    if (document.querySelector('.galeri-foto-page')) initGaleriFotoPage();
    if (document.querySelector('.ekstra-page')) initEkstrakurikulerPage();
    if (document.querySelector('.agenda-page')) initAgendaPage();
    if (document.querySelector('.sarana-page')) initSaranaPage();
    if (document.querySelector('.users-page')) initUsersPage();
    
    // 👇 TAMBAHKAN INI 👇
    if (document.querySelector('.data-table')) {
        initDataSiswaPage();
        initDataSiswaDelete();
    }
});

// ==============================================
// GLOBAL FUNCTIONS
// ==============================================
function initGlobalFunctions() {
    // Fungsi utama confirm delete untuk semua modul
    window.confirmDeleteItem = function(id, name, module, hasFile = false, fileType = '') {
        console.log('🗑️ Delete clicked - ID:', id, 'Name:', name, 'Module:', module, 'HasFile:', hasFile);
        
        const modal = document.getElementById('deleteModal');
        const deleteItemName = document.getElementById('deleteItemName');
        const itemType = document.getElementById('itemType');
        const fileWarningContainer = document.getElementById('fileWarningContainer');
        const fileWarningText = document.getElementById('fileWarningText');
        const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
        
        if (!modal) {
            console.error('❌ Modal not found!');
            if (confirm('Yakin ingin menghapus "' + name + '"?')) {
                window.location.href = 'index.php?delete=' + id;
            }
            return;
        }
        
        // Set nama item
        if (deleteItemName) deleteItemName.innerText = '"' + name + '"';
        
        // Set tipe item berdasarkan module
        if (itemType) {
            const typeMap = {
                'sarana': 'Sarana Prasarana',
                'pembiasaan': 'Pembiasaan Pagi',
                'foto': 'Galeri Foto',
                'video': 'Galeri Video',
                'admin': 'Admin',
                'guru': 'Guru/Staff',
                'prestasi': 'Prestasi',
                'pengumuman': 'Pengumuman',
                'agenda': 'Agenda',
                'ekstrakurikuler': 'Ekstrakurikuler',
                'slider': 'Hero Slider'
            };
            itemType.innerText = typeMap[module] || module;
        }
        
        // Tampilkan peringatan file jika ada
        if (fileWarningContainer && fileWarningText) {
            if (hasFile === true || hasFile === 'true') {
                const warningMap = {
                    'sarana': 'Sarana ini memiliki GAMBAR yang akan ikut terhapus!',
                    'pembiasaan': 'Pembiasaan ini memiliki GAMBAR yang akan ikut terhapus!',
                    'foto': 'Foto ini memiliki file gambar yang akan ikut terhapus!',
                    'video': 'Video ini memiliki THUMBNAIL yang akan ikut terhapus!',
                    'admin': 'Admin ini memiliki FOTO PROFIL yang akan ikut terhapus!',
                    'guru': 'Data ini memiliki FOTO yang akan ikut terhapus!',
                    'prestasi': 'Prestasi ini memiliki GAMBAR yang akan ikut terhapus!',
                    'pengumuman': fileType === 'gambar' ? 'Pengumuman ini memiliki GAMBAR yang akan ikut terhapus!' : 
                                   fileType === 'lampiran' ? 'Pengumuman ini memiliki LAMPIRAN yang akan ikut terhapus!' :
                                   'Pengumuman ini memiliki GAMBAR dan LAMPIRAN yang akan ikut terhapus!',
                    'slider': 'Slider ini memiliki GAMBAR yang akan ikut terhapus!'
                };
                fileWarningText.innerText = warningMap[module] || 'Data ini memiliki file yang akan ikut terhapus!';
                fileWarningContainer.style.display = 'block';
            } else {
                fileWarningContainer.style.display = 'none';
            }
        }
        
        // Set link hapus
        if (confirmDeleteBtn) confirmDeleteBtn.href = 'index.php?delete=' + id;
        
        // Tampilkan modal
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    };
    
    // Fungsi close modal
    window.closeModal = function() {
        const modal = document.getElementById('deleteModal');
        if (modal) {
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }
    };
}

// ==============================================
// DELETE BUTTONS HANDLER
// ==============================================
function initDeleteButtons() {
    const deleteButtons = document.querySelectorAll('.btn-delete');
    console.log('🔍 Found ' + deleteButtons.length + ' delete buttons');
    
    deleteButtons.forEach(function(button) {
        // Hapus event listener lama untuk menghindari duplikasi
        button.removeEventListener('click', handleDeleteClick);
        button.addEventListener('click', handleDeleteClick);
    });
}

function handleDeleteClick(event) {
    event.preventDefault();
    const button = event.currentTarget;
    
    const id = button.getAttribute('data-id');
    const name = button.getAttribute('data-name');
    const module = button.getAttribute('data-module') || getModuleFromPage();
    
    // Cek berbagai kemungkinan attribute untuk file
    let hasFile = false;
    let fileType = '';
    
    if (button.getAttribute('data-has-gambar') === 'true') { hasFile = true; fileType = 'gambar'; }
    if (button.getAttribute('data-has-file') === 'true') { hasFile = true; fileType = 'file'; }
    if (button.getAttribute('data-has-thumbnail') === 'true') { hasFile = true; fileType = 'thumbnail'; }
    if (button.getAttribute('data-has-foto') === 'true') { hasFile = true; fileType = 'foto'; }
    if (button.getAttribute('data-has-lampiran') === 'true') { hasFile = true; fileType = 'lampiran'; }
    
    if (id && name) {
        confirmDeleteItem(id, name, module, hasFile, fileType);
    } else {
        console.error('❌ Missing data attributes', button);
    }
}

function getModuleFromPage() {
    if (document.querySelector('.sarana-page')) return 'sarana';
    if (document.querySelector('.pembiasaan-page')) return 'pembiasaan';
    if (document.querySelector('.galeri-foto-page')) return 'foto';
    if (document.querySelector('.galeri-video-page')) return 'video';
    if (document.querySelector('.users-page')) return 'admin';
    if (document.querySelector('.guru-page')) return 'guru';
    if (document.querySelector('.prestasi-page')) return 'prestasi';
    if (document.querySelector('.pengumuman-page')) return 'pengumuman';
    if (document.querySelector('.agenda-page')) return 'agenda';
    if (document.querySelector('.ekstra-page')) return 'ekstrakurikuler';
    if (document.querySelector('.hero-slider-page')) return 'slider';
    return 'general';
}

// ==============================================
// MODAL CLOSE BUTTONS
// ==============================================
function initModalCloseButtons() {
    const modalCloseBtns = document.querySelectorAll('.modal-close, #btnCloseModal, .btn-secondary');
    modalCloseBtns.forEach(function(btn) {
        btn.removeEventListener('click', closeModalHandler);
        btn.addEventListener('click', closeModalHandler);
    });
    
    // Tutup modal jika klik di luar
    window.onclick = function(event) {
        const modal = document.getElementById('deleteModal');
        if (event.target == modal && modal) closeModal();
    };
    
    // Tutup modal dengan tombol ESC
    document.removeEventListener('keydown', escHandler);
    document.addEventListener('keydown', escHandler);
}

function closeModalHandler(e) {
    e.preventDefault();
    closeModal();
}

function escHandler(event) {
    if (event.key === 'Escape') closeModal();
}

// ==============================================
// DELETE MODAL INIT
// ==============================================
function initDeleteModal() {
    if (typeof window.confirmDeleteItem === 'undefined') {
        window.confirmDeleteItem = function(id, name, module, hasFile) {
            if (confirm('Yakin ingin menghapus "' + name + '"?')) {
                window.location.href = 'index.php?delete=' + id;
            }
        };
    }
    if (typeof window.closeModal === 'undefined') {
        window.closeModal = function() {
            const modal = document.getElementById('deleteModal');
            if (modal) modal.style.display = 'none';
        };
    }
}

// ==============================================
// AUTO CLOSE ALERTS
// ==============================================
function initAutoCloseAlerts() {
    const alerts = document.querySelectorAll('.notification-container .alert, .alert-dismissible');
    
    alerts.forEach(function(alert) {
        setTimeout(function() {
            if (alert && alert.style.display !== 'none') {
                alert.style.transition = 'opacity 0.5s ease';
                alert.style.opacity = '0';
                setTimeout(function() {
                    if (alert) alert.style.display = 'none';
                }, 500);
            }
        }, 5000);
        
        const closeBtn = alert.querySelector('.close');
        if (closeBtn) {
            closeBtn.removeEventListener('click', function() {});
            closeBtn.addEventListener('click', function() {
                alert.style.display = 'none';
            });
        }
    });
}

// ==============================================
// 1. MOBILE SIDEBAR TOGGLE
// ==============================================
function initMobileSidebar() {
    const sidebar = document.getElementById('adminSidebar');
    const closeBtn = document.getElementById('sidebarClose');
    const mobileMenuBtn = document.getElementById('mobileMenuBtn');
    const floatingToggle = document.getElementById('floatingMenuToggle');
    
    if (!sidebar) return;
    
    let overlay = document.getElementById('sidebarOverlay');
    if (!overlay) {
        overlay = document.createElement('div');
        overlay.id = 'sidebarOverlay';
        overlay.className = 'sidebar-overlay';
        document.body.appendChild(overlay);
    }
    
    function openSidebar() {
        sidebar.classList.add('show');
        overlay.classList.add('show');
        document.body.style.overflow = 'hidden';
        if (mobileMenuBtn) mobileMenuBtn.style.display = 'none';
        if (floatingToggle) floatingToggle.style.display = 'none';
    }
    
    function closeSidebar() {
        sidebar.classList.remove('show');
        overlay.classList.remove('show');
        document.body.style.overflow = '';
        if (window.innerWidth <= 992) {
            if (mobileMenuBtn) mobileMenuBtn.style.display = 'flex';
            if (floatingToggle) floatingToggle.style.display = 'flex';
        }
    }
    
    if (mobileMenuBtn) {
        mobileMenuBtn.removeEventListener('click', openSidebar);
        mobileMenuBtn.addEventListener('click', openSidebar);
    }
    if (floatingToggle) {
        floatingToggle.removeEventListener('click', openSidebar);
        floatingToggle.addEventListener('click', openSidebar);
    }
    if (closeBtn) {
        closeBtn.removeEventListener('click', closeSidebar);
        closeBtn.addEventListener('click', closeSidebar);
    }
    if (overlay) {
        overlay.removeEventListener('click', closeSidebar);
        overlay.addEventListener('click', closeSidebar);
    }
    
    window.removeEventListener('resize', handleResize);
    window.addEventListener('resize', handleResize);
    
    function handleResize() {
        if (window.innerWidth > 992) {
            closeSidebar();
            if (mobileMenuBtn) mobileMenuBtn.style.display = 'none';
            if (floatingToggle) floatingToggle.style.display = 'none';
        } else {
            if (!sidebar.classList.contains('show')) {
                if (mobileMenuBtn) mobileMenuBtn.style.display = 'flex';
                if (floatingToggle) floatingToggle.style.display = 'flex';
            }
        }
    }
    
    console.log('✅ Mobile sidebar initialized');
}

// ==============================================
// 2. PROFILE DROPDOWN
// ==============================================
function initProfileDropdown() {
    const profileBtn = document.getElementById('profileDropdown');
    const profileDropdown = document.getElementById('profileDropdownMenu');
    
    if (profileBtn && profileDropdown) {
        profileBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            profileDropdown.classList.toggle('show');
            const chevron = this.querySelector('i.fa-chevron-down');
            if (chevron) {
                chevron.style.transform = profileDropdown.classList.contains('show') ? 'rotate(180deg)' : 'rotate(0deg)';
            }
        });
        
        document.addEventListener('click', function() {
            profileDropdown.classList.remove('show');
            const chevron = profileBtn.querySelector('i.fa-chevron-down');
            if (chevron) chevron.style.transform = 'rotate(0deg)';
        });
        
        profileDropdown.addEventListener('click', function(e) { e.stopPropagation(); });
    }
    
    // Mobile Profile Dropdown
    const mobileProfileBtn = document.getElementById('mobileProfileBtn');
    const mobileProfileDropdown = document.getElementById('mobileProfileDropdown');
    
    if (mobileProfileBtn && mobileProfileDropdown) {
        mobileProfileBtn.removeEventListener('click', mobileProfileHandler);
        mobileProfileBtn.addEventListener('click', mobileProfileHandler);
        
        function mobileProfileHandler(e) {
            e.preventDefault();
            e.stopPropagation();
            mobileProfileDropdown.classList.toggle('show');
            const chevron = this.querySelector('i');
            if (chevron) {
                chevron.style.transform = mobileProfileDropdown.classList.contains('show') ? 'rotate(180deg)' : 'rotate(0deg)';
            }
        }
        
        document.addEventListener('click', function(e) {
            if (!mobileProfileBtn.contains(e.target) && !mobileProfileDropdown.contains(e.target)) {
                mobileProfileDropdown.classList.remove('show');
                const chevron = mobileProfileBtn.querySelector('i');
                if (chevron) chevron.style.transform = 'rotate(0deg)';
            }
        });
        
        mobileProfileDropdown.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    }
    
    console.log('✅ Profile dropdown initialized');
}

// ==============================================
// 3. FILE UPLOAD
// ==============================================
function initFileUpload() {
    document.querySelectorAll('.file-upload, .file-input-wrapper').forEach(wrapper => {
        const fileInput = wrapper.querySelector('input[type="file"]');
        if (fileInput) {
            wrapper.addEventListener('click', function(e) {
                if (e.target !== fileInput) fileInput.click();
            });
        }
    });
}

// ==============================================
// 4. TABLE SEARCH
// ==============================================
function initTableSearch() {
    const searchInput = document.getElementById('searchInput');
    const filterSelect = document.getElementById('statusFilter');
    const tableRows = document.querySelectorAll('.table tbody tr');
    
    if (!tableRows.length) return;
    
    function filterTable() {
        const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';
        const filterStatus = filterSelect ? filterSelect.value : '';
        
        tableRows.forEach(row => {
            let showRow = true;
            if (searchTerm) showRow = row.textContent.toLowerCase().includes(searchTerm);
            if (filterStatus && showRow) {
                const statusCell = row.querySelector('.status-badge');
                if (statusCell) {
                    const rowStatus = statusCell.classList.contains('aktif') ? 'aktif' : 'nonaktif';
                    showRow = filterStatus === rowStatus;
                }
            }
            row.style.display = showRow ? '' : 'none';
        });
    }
    
    if (searchInput) {
        let debounceTimer;
        searchInput.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(filterTable, 300);
        });
    }
    if (filterSelect) filterSelect.addEventListener('change', filterTable);
}

// ==============================================
// 5. FORM VALIDATION
// ==============================================
function initFormValidation() {
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function(e) {
            let isValid = true;
            form.querySelectorAll('[required]').forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('is-invalid');
                    let error = field.parentElement.querySelector('.invalid-feedback');
                    if (!error) {
                        error = document.createElement('div');
                        error.className = 'invalid-feedback';
                        error.style.cssText = 'color:#dc3545;font-size:0.8rem;margin-top:5px';
                        field.parentElement.appendChild(error);
                    }
                    error.textContent = 'Field ini harus diisi';
                } else {
                    field.classList.remove('is-invalid');
                    const error = field.parentElement.querySelector('.invalid-feedback');
                    if (error) error.remove();
                }
            });
            if (!isValid) {
                e.preventDefault();
                alert('Mohon lengkapi form dengan benar');
            }
        });
    });
}

// ==============================================
// 6. NUMBER INPUTS
// ==============================================
function initNumberInputs() {
    document.querySelectorAll('input[type="number"]').forEach(input => {
        if (input.closest('.number-input-container')) return;
        
        const container = document.createElement('div');
        container.className = 'number-input-container';
        container.style.cssText = 'display:flex;align-items:center';
        input.parentNode.insertBefore(container, input);
        container.appendChild(input);
        
        const minusBtn = document.createElement('button');
        minusBtn.type = 'button';
        minusBtn.className = 'number-minus';
        minusBtn.innerHTML = '<i class="fas fa-minus"></i>';
        minusBtn.style.cssText = 'width:40px;height:40px;border:1px solid #e2e8f0;background:white;border-radius:10px 0 0 10px;cursor:pointer';
        
        const plusBtn = document.createElement('button');
        plusBtn.type = 'button';
        plusBtn.className = 'number-plus';
        plusBtn.innerHTML = '<i class="fas fa-plus"></i>';
        plusBtn.style.cssText = 'width:40px;height:40px;border:1px solid #e2e8f0;background:white;border-radius:0 10px 10px 0;cursor:pointer';
        
        container.insertBefore(minusBtn, input);
        container.appendChild(plusBtn);
        
        input.style.cssText = 'border-radius:0;border-left:none;border-right:none;text-align:center;width:80px';
        
        minusBtn.addEventListener('click', () => {
            let val = parseInt(input.value) || 0;
            const min = input.min ? parseInt(input.min) : 0;
            if (val > min) input.value = val - 1;
        });
        
        plusBtn.addEventListener('click', () => {
            let val = parseInt(input.value) || 0;
            const max = input.max ? parseInt(input.max) : Infinity;
            if (val < max) input.value = val + 1;
        });
    });
}

// ==============================================
// 7. SMOOTH SCROLL
// ==============================================
function initSmoothScroll() {
    document.querySelectorAll('a[href^="#"]:not([href="#"])').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });
}

// ==============================================
// 8. IOS FIX
// ==============================================
function initIOSFix() {
    if (/iPhone|iPad|iPod/i.test(navigator.userAgent)) {
        document.querySelectorAll('input, select, textarea').forEach(el => {
            el.addEventListener('focus', () => el.style.fontSize = '16px');
            el.addEventListener('blur', () => el.style.fontSize = '');
        });
    }
}

// ==============================================
// 9. TOUCH SCROLL
// ==============================================
function initTouchScroll() {
    if (/iPhone|iPad|iPod/i.test(navigator.userAgent)) {
        document.querySelectorAll('.table-container, .card-body').forEach(container => {
            container.style.webkitOverflowScrolling = 'touch';
        });
    }
}

// ==============================================
// 10. LOGIN PAGE
// ==============================================
function initLoginPage() {
    const loginForm = document.getElementById('loginForm');
    const btnLogin = document.getElementById('btnLogin');
    if (loginForm && btnLogin) {
        loginForm.addEventListener('submit', function() {
            btnLogin.classList.add('loading');
            btnLogin.innerHTML = '<i class="fas fa-spinner fa-spin"></i> LOGIN...';
            btnLogin.disabled = true;
        });
    }
}

// ==============================================
// 11. DASHBOARD
// ==============================================
function initDashboard() {
    const profileBtn = document.getElementById('profileDropdown');
    const profileDropdown = document.getElementById('profileDropdownMenu');
    
    if (profileBtn && profileDropdown) {
        profileBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            profileDropdown.classList.toggle('show');
            const chevron = this.querySelector('i.fa-chevron-down');
            if (chevron) chevron.style.transform = profileDropdown.classList.contains('show') ? 'rotate(180deg)' : 'rotate(0deg)';
        });
        
        document.addEventListener('click', function() {
            profileDropdown.classList.remove('show');
            const chevron = profileBtn.querySelector('i.fa-chevron-down');
            if (chevron) chevron.style.transform = 'rotate(0deg)';
        });
        
        profileDropdown.addEventListener('click', e => e.stopPropagation());
    }
}


// ==============================================
// GURU STAFF PAGE - FIXED VERSION (SEDERHANA)
// ==============================================

function initGuruStaffPage() {
    'use strict';
    
    console.log('👥 Initializing Guru Staff page...');
    
    // ========== MODAL STRUKTUR ORGANISASI ==========
    const strukturModal = document.getElementById('strukturModal');
    const btnKelolaStruktur = document.getElementById('btnKelolaStruktur');
    
    if (!strukturModal || !btnKelolaStruktur) {
        console.error('❌ Struktur modal or button not found!');
        return;
    }
    
    // Elemen-elemen
    const strukturUploadArea = document.getElementById('strukturUploadArea');
    const strukturInput = document.getElementById('strukturInput');
    const strukturPreviewArea = document.getElementById('strukturPreviewArea');
    const strukturPreviewImg = document.getElementById('strukturPreviewImg');
    const cancelPreviewBtn = document.getElementById('cancelPreviewBtn');
    const selectAnotherBtn = document.getElementById('selectAnotherBtn');
    const btnCloseStrukturModal = document.getElementById('btnCloseStrukturModal');
    const modalClose = strukturModal.querySelector('.modal-close');
    const currentStrukturWrapper = document.getElementById('currentStrukturWrapper');
    const noStrukturWrapper = document.getElementById('noStrukturWrapper');
    const currentStrukturImg = document.getElementById('currentStrukturImg');
    
    // Fungsi reset preview
    function resetStrukturPreview() {
        console.log('🔄 Reset preview...');
        
        if (strukturPreviewArea) {
            strukturPreviewArea.style.display = 'none';
        }
        if (strukturInput) {
            strukturInput.value = '';
        }
        if (strukturPreviewImg) {
            strukturPreviewImg.src = '#';
            // Reset gaya yang mungkin tersisa
            strukturPreviewImg.style.maxWidth = '100%';
            strukturPreviewImg.style.maxHeight = '230px';
            strukturPreviewImg.style.width = 'auto';
            strukturPreviewImg.style.height = 'auto';
        }
        
        // Tampilkan kembali current struktur jika ada
        if (currentStrukturWrapper && noStrukturWrapper) {
            const hasStruktur = currentStrukturImg && currentStrukturImg.src && 
                               currentStrukturImg.src !== '#' && 
                               !currentStrukturImg.src.includes('undefined') &&
                               currentStrukturImg.src !== window.location.href;
            
            if (hasStruktur) {
                currentStrukturWrapper.style.display = 'block';
                noStrukturWrapper.style.display = 'none';
            } else {
                currentStrukturWrapper.style.display = 'none';
                noStrukturWrapper.style.display = 'block';
            }
        }
    }
    
    // Fungsi buka modal
    function openStrukturModal() {
        console.log('🔓 Membuka modal struktur...');
        strukturModal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        resetStrukturPreview();
    }
    
    // Fungsi tutup modal
    function closeStrukturModal() {
        console.log('🔒 Menutup modal struktur...');
        strukturModal.style.display = 'none';
        document.body.style.overflow = 'auto';
        resetStrukturPreview();
    }
    
    // Event tombol Kelola Struktur
    btnKelolaStruktur.onclick = function(e) {
        e.preventDefault();
        e.stopPropagation();
        openStrukturModal();
    };
    
    // Event close modal
    if (modalClose) modalClose.onclick = closeStrukturModal;
    if (btnCloseStrukturModal) btnCloseStrukturModal.onclick = closeStrukturModal;
    
    // Klik di luar modal
    strukturModal.onclick = function(e) {
        if (e.target === strukturModal) {
            closeStrukturModal();
        }
    };
    
    // ========== PREVIEW GAMBAR ==========
    if (strukturUploadArea && strukturInput) {
        
        // Klik area upload
        strukturUploadArea.onclick = function(e) {
            e.stopPropagation();
            strukturInput.click();
        };
        
        // 🔥 PREVIEW FILE - PERBAIKAN UTAMA
        strukturInput.onchange = function(e) {
            const file = e.target.files[0];
            console.log('📁 File selected:', file ? file.name : 'none');
            
            if (!file) return;
            
            // Validasi file
            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
            if (!allowedTypes.includes(file.type)) {
                alert('❌ Format file harus JPG, JPEG, PNG, atau WEBP!');
                strukturInput.value = '';
                return;
            }
            
            const maxSize = 5 * 1024 * 1024;
            if (file.size > maxSize) {
                alert('❌ Ukuran file maksimal 5MB!');
                strukturInput.value = '';
                return;
            }
            
            const reader = new FileReader();
            reader.onload = function(event) {
                console.log('✅ File loaded, showing preview');
                
                if (strukturPreviewImg) {
                    strukturPreviewImg.src = event.target.result;
                    
                    // 🔥 FORCE UKURAN GAMBAR - INI YANG PENTING!
                    strukturPreviewImg.style.maxWidth = '100%';
                    strukturPreviewImg.style.maxHeight = '230px';
                    strukturPreviewImg.style.width = 'auto';
                    strukturPreviewImg.style.height = 'auto';
                    strukturPreviewImg.style.objectFit = 'contain';
                }
                
                if (strukturPreviewArea) {
                    strukturPreviewArea.style.display = 'block';
                }
                
                // Sembunyikan current struktur sementara
                if (currentStrukturWrapper) {
                    currentStrukturWrapper.style.display = 'none';
                }
                if (noStrukturWrapper) {
                    noStrukturWrapper.style.display = 'none';
                }
                
                console.log('✅ Preview displayed with correct size');
            };
            reader.onerror = function() {
                console.error('❌ Error reading file');
                alert('Gagal membaca file');
            };
            reader.readAsDataURL(file);
        };
    }
    
    // Tombol Batal Preview
    if (cancelPreviewBtn) {
        cancelPreviewBtn.onclick = function() {
            resetStrukturPreview();
        };
    }
    
    // Tombol Pilih Gambar Lain
    if (selectAnotherBtn) {
        selectAnotherBtn.onclick = function() {
            resetStrukturPreview();
            if (strukturInput) strukturInput.click();
        };
    }
    
    // Hapus Struktur
    const deleteStrukturBtn = document.getElementById('btnDeleteStruktur');
    if (deleteStrukturBtn) {
        deleteStrukturBtn.onclick = function(e) {
            e.preventDefault();
            if (confirm('Yakin ingin menghapus gambar struktur organisasi?')) {
                window.location.href = '?delete_struktur=1';
            }
        };
    }
    
    // Submit Struktur
    const submitStrukturBtn = document.getElementById('submitStrukturBtn');
    const strukturForm = document.getElementById('strukturForm');
    
    if (submitStrukturBtn && strukturForm) {
        submitStrukturBtn.onclick = function(e) {
            if (strukturInput && strukturInput.files.length === 0) {
                e.preventDefault();
                alert('Silakan pilih gambar terlebih dahulu!');
                return false;
            }
            strukturForm.submit();
        };
    }
    
    console.log('🎉 Guru Staff page initialization COMPLETE!');
}

// ==============================================
// 23. GALERI FOTO PAGE
// ==============================================
function initGaleriFotoPage() {
    console.log('📸 Galeri Foto page initialized');
    
    // File upload preview
    const form = document.getElementById('fotoForm');
    const fileInput = document.getElementById('file_foto');
    const previewContainer = document.getElementById('previewContainer');
    const previewImage = document.getElementById('previewImage');
    const fileUploadArea = document.getElementById('fileUploadArea');
    
    if (fileInput && previewImage && fileUploadArea) {
        if (fileUploadArea.onclick) {
            // Hapus event listener lama jika ada
            fileUploadArea.removeEventListener('click', fileUploadArea.clickHandler);
        }
        
        const handleFileUploadClick = function() {
            fileInput.click();
        };
        
        fileUploadArea.onclick = handleFileUploadClick;
        fileUploadArea.clickHandler = handleFileUploadClick;
        
        fileInput.onchange = function(e) {
            const file = e.target.files[0];
            if (file && previewImage && previewContainer) {
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
                if (!allowedTypes.includes(file.type)) {
                    alert('❌ Format file harus JPG, JPEG, PNG, atau GIF!');
                    fileInput.value = '';
                    return;
                }
                
                const maxSize = 2 * 1024 * 1024;
                if (file.size > maxSize) {
                    alert('❌ Ukuran file maksimal 2MB!');
                    fileInput.value = '';
                    return;
                }
                
                const reader = new FileReader();
                reader.onload = function(event) {
                    previewImage.src = event.target.result;
                    previewContainer.style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        };
    }
    
    // Modal delete handler
    const deleteModal = document.getElementById('deleteModal');
    
    // Pastikan modal tidak null sebelum melanjutkan
    if (!deleteModal) {
        console.warn('Delete modal not found');
        return;
    }
    
    const deleteButtons = document.querySelectorAll('.btn-delete');
    
    function showDeleteModal(id, name, hasFile, fileName) {
        const deleteItemName = document.getElementById('deleteItemName');
        const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
        const fileWarningContainer = document.getElementById('fileWarningContainer');
        const fileWarningText = document.getElementById('fileWarningText');
        const fileList = document.getElementById('fileList');
        
        if (deleteItemName) deleteItemName.innerText = name;
        if (confirmDeleteBtn) confirmDeleteBtn.href = 'index.php?delete=' + id;
        
        if (fileWarningContainer && fileWarningText) {
            if (hasFile === 'true' && fileName) {
                fileWarningText.innerText = 'Foto ini memiliki file yang akan ikut terhapus!';
                if (fileList) {
                    fileList.innerHTML = `
                        <div style="margin-top: 5px;">
                            <i class="fas fa-image"></i> 
                            <strong>File:</strong> ${fileName}
                        </div>
                    `;
                }
                fileWarningContainer.style.display = 'block';
            } else {
                fileWarningContainer.style.display = 'none';
            }
        }
        
        if (deleteModal) {
            deleteModal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }
    }
    
    function closeDeleteModal() {
        if (deleteModal) {
            deleteModal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }
    }
    
    // Hapus event listener lama dan tambahkan yang baru
    deleteButtons.forEach(button => {
        // Hapus event listener lama jika ada
        const oldClick = button.onclick;
        if (oldClick) {
            button.removeEventListener('click', oldClick);
        }
        
        // Tambahkan event listener baru
        button.onclick = function(e) {
            e.preventDefault();
            e.stopPropagation();
            const id = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');
            const hasFile = this.getAttribute('data-has-file');
            const fileName = this.getAttribute('data-file-name');
            console.log('Delete button clicked:', {id, name, hasFile, fileName}); // Debug
            showDeleteModal(id, name, hasFile, fileName);
        };
    });
    
    const modalClose = deleteModal.querySelector('.modal-close');
    const btnCloseModal = document.getElementById('btnCloseModal');
    
    if (modalClose) {
        // Hapus event listener lama
        const oldCloseClick = modalClose.onclick;
        if (oldCloseClick) {
            modalClose.removeEventListener('click', oldCloseClick);
        }
        modalClose.onclick = closeDeleteModal;
    }
    
    if (btnCloseModal) {
        const oldBtnClick = btnCloseModal.onclick;
        if (oldBtnClick) {
            btnCloseModal.removeEventListener('click', oldBtnClick);
        }
        btnCloseModal.onclick = closeDeleteModal;
    }
    
    if (deleteModal) {
        deleteModal.onclick = function(e) {
            if (e.target === deleteModal) closeDeleteModal();
        };
    }
    
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && deleteModal && deleteModal.style.display === 'flex') {
            closeDeleteModal();
        }
    });
    
    const submitBtn = document.getElementById('btnSubmit');
    if (form && submitBtn) {
        // Hapus event listener lama
        const oldSubmit = form.onsubmit;
        if (oldSubmit) {
            form.removeEventListener('submit', oldSubmit);
        }
        
        form.addEventListener('submit', function() {
            submitBtn.classList.add('loading');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
            submitBtn.disabled = true;
        });
    }
}

// Panggil fungsi saat halaman siap
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initGaleriFotoPage);
} else {
    initGaleriFotoPage();
}

// ==============================================
// 24. EKSTRAKURIKULER PAGE
// ==============================================
function initEkstrakurikulerPage() {
    console.log('⚽ Ekstrakurikuler page initialized');
    
    const deleteModal = document.getElementById('deleteModal');
    const deleteButtons = document.querySelectorAll('.btn-delete');
    
    function showDeleteModal(id, name) {
        const deleteItemName = document.getElementById('deleteItemName');
        const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
        
        if (deleteItemName) deleteItemName.innerText = name;
        if (confirmDeleteBtn) confirmDeleteBtn.href = 'index.php?delete=' + id;
        
        if (deleteModal) {
            deleteModal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }
    }
    
    function closeDeleteModal() {
        if (deleteModal) {
            deleteModal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }
    }
    
    deleteButtons.forEach(button => {
        button.onclick = function(e) {
            e.preventDefault();
            const id = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');
            showDeleteModal(id, name);
        };
    });
    
    const modalClose = deleteModal ? deleteModal.querySelector('.modal-close') : null;
    const btnCloseModal = document.getElementById('btnCloseModal');
    
    if (modalClose) modalClose.onclick = closeDeleteModal;
    if (btnCloseModal) btnCloseModal.onclick = closeDeleteModal;
    
    if (deleteModal) {
        deleteModal.onclick = function(e) {
            if (e.target === deleteModal) closeDeleteModal();
        };
    }
    
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && deleteModal && deleteModal.style.display === 'flex') {
            closeDeleteModal();
        }
    });
}

// ==============================================
// 25. AGENDA PAGE
// ==============================================
function initAgendaPage() {
    console.log('📅 Agenda page initialized');
}

// ==============================================
// 26. SARANA PAGE
// ==============================================
function initSaranaPage() {
    console.log('🏢 Sarana page initialized');
    
    // File upload preview
    const gambarInput = document.getElementById('gambar');
    const previewContainer = document.getElementById('preview-container');
    const previewImage = document.getElementById('preview-image');
    
    if (gambarInput && previewImage && previewContainer) {
        gambarInput.onchange = function(e) {
            const file = e.target.files[0];
            if (file) {
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
                if (!allowedTypes.includes(file.type)) {
                    alert('❌ Format file harus JPG, JPEG, PNG, atau GIF!');
                    gambarInput.value = '';
                    return;
                }
                
                const maxSize = 2 * 1024 * 1024;
                if (file.size > maxSize) {
                    alert('❌ Ukuran file maksimal 2MB!');
                    gambarInput.value = '';
                    return;
                }
                
                const reader = new FileReader();
                reader.onload = function(event) {
                    previewImage.src = event.target.result;
                    previewContainer.style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        };
    }
    
    // Modal delete handler
    const deleteModal = document.getElementById('deleteModal');
    const deleteButtons = document.querySelectorAll('.btn-delete');
    
    function showDeleteModal(id, name, hasGambar) {
        const deleteItemName = document.getElementById('deleteItemName');
        const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
        const fileWarningContainer = document.getElementById('fileWarningContainer');
        const fileWarningText = document.getElementById('fileWarningText');
        
        if (deleteItemName) deleteItemName.innerText = name;
        if (confirmDeleteBtn) confirmDeleteBtn.href = 'index.php?delete=' + id;
        
        if (fileWarningContainer && fileWarningText) {
            if (hasGambar === 'true') {
                fileWarningText.innerText = 'Sarana ini memiliki GAMBAR yang akan ikut terhapus!';
                fileWarningContainer.style.display = 'block';
            } else {
                fileWarningContainer.style.display = 'none';
            }
        }
        
        if (deleteModal) {
            deleteModal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }
    }
    
    function closeDeleteModal() {
        if (deleteModal) {
            deleteModal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }
    }
    
    deleteButtons.forEach(button => {
        button.onclick = function(e) {
            e.preventDefault();
            const id = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');
            const hasGambar = this.getAttribute('data-has-gambar') || this.getAttribute('data-hasgambar');
            showDeleteModal(id, name, hasGambar);
        };
    });
    
    const modalClose = deleteModal ? deleteModal.querySelector('.modal-close') : null;
    const btnCloseModal = document.getElementById('btnCloseModal');
    
    if (modalClose) modalClose.onclick = closeDeleteModal;
    if (btnCloseModal) btnCloseModal.onclick = closeDeleteModal;
    
    if (deleteModal) {
        deleteModal.onclick = function(e) {
            if (e.target === deleteModal) closeDeleteModal();
        };
    }
    
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && deleteModal && deleteModal.style.display === 'flex') {
            closeDeleteModal();
        }
    });
}

// ==============================================
// 27. USERS PAGE
// ==============================================
function initUsersPage() {
    console.log('👥 Users page initialized');
    
    // ========== FILE UPLOAD PREVIEW ==========
    const fotoInput = document.getElementById('foto');
    const previewImage = document.getElementById('previewImage');
    const previewContainer = document.getElementById('previewContainer');
    const fileUploadArea = document.getElementById('fileUploadArea');
    
    if (fotoInput && previewImage && fileUploadArea) {
        fileUploadArea.addEventListener('click', () => fotoInput.click());
        
        fotoInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file && previewImage && previewContainer) {
                const reader = new FileReader();
                reader.onload = e => { 
                    previewImage.src = e.target.result; 
                    previewContainer.style.display = 'block'; 
                };
                reader.readAsDataURL(file);
            }
        });
    }
    
    // ========== FORM SUBMIT HANDLER ==========
    const form = document.getElementById('userForm');
    const submitBtn = document.getElementById('btnSubmit');
    if (form && submitBtn) {
        form.addEventListener('submit', function() {
            submitBtn.classList.add('loading');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
            submitBtn.disabled = true;
        });
    }
    
    // ========== EDIT BUTTON - biarkan link biasa bekerja ==========
    const editButtons = document.querySelectorAll('.users-page .btn-edit');
    console.log('🔍 Found ' + editButtons.length + ' edit buttons');
    // Tombol Edit sudah berupa <a href> biasa, tidak perlu event handler tambahan
    
    // ========== DELETE BUTTON HANDLER ==========
    const deleteButtons = document.querySelectorAll('.users-page .btn-delete');
    const deleteModal = document.getElementById('deleteModal');
    const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
    const closeModalBtn = document.getElementById('btnCloseModal');
    const modalCloseSpan = document.querySelector('#deleteModal .modal-close');
    const deleteItemName = document.getElementById('deleteItemName');
    const fileWarningContainer = document.getElementById('fileWarningContainer');
    const fileWarningText = document.getElementById('fileWarningText');
    
    if (!deleteModal) return;
    
    function showDeleteModal(id, name, hasFoto) {
        if (deleteItemName) deleteItemName.innerHTML = '<strong>' + name + '</strong>';
        if (confirmDeleteBtn) confirmDeleteBtn.href = 'index.php?delete=' + id;
        if (fileWarningContainer && fileWarningText) {
            if (hasFoto === 'true') {
                fileWarningText.innerHTML = '<i class="fas fa-camera"></i> Akun ini memiliki FOTO PROFIL yang akan ikut terhapus!';
                fileWarningContainer.style.display = 'block';
            } else {
                fileWarningContainer.style.display = 'none';
            }
        }
        deleteModal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
    
    function closeDeleteModal() {
        deleteModal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }
    
    deleteButtons.forEach(function(button) {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const id = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');
            const hasFoto = this.getAttribute('data-has-foto') || 'false';
            if (id && name) showDeleteModal(id, name, hasFoto);
        });
    });
    
    if (modalCloseSpan) modalCloseSpan.addEventListener('click', closeDeleteModal);
    if (closeModalBtn) closeModalBtn.addEventListener('click', closeDeleteModal);
    
    deleteModal.addEventListener('click', function(e) {
        if (e.target === deleteModal) closeDeleteModal();
    });
    
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && deleteModal.style.display === 'flex') closeDeleteModal();
    });
    
    console.log('✅ Users page initialized');
}

// ==============================================
// 28. BUTTON KEMBALI
// ==============================================
function initBackButtons() {
    const backButtons = document.querySelectorAll('.btn-secondary, a[href*="index.php"], a[href*="../index.php"], .btn-back');
    
    backButtons.forEach(function(btn) {
        const isBackButton = (btn.textContent && (btn.textContent.includes('Kembali') || btn.textContent.includes('kembali'))) ||
                            (btn.innerHTML && btn.innerHTML.includes('fa-arrow-left'));
        
        if (isBackButton) {
            btn.removeEventListener('click', backClickHandler);
            btn.addEventListener('click', backClickHandler);
        }
    });
}

function backClickHandler(e) {
    const btn = e.currentTarget;
    const href = btn.getAttribute('href');
    
    e.preventDefault();
    
    if (href && href !== '#' && href !== 'javascript:void(0)') {
        window.location.href = href;
    } else if (document.referrer && document.referrer.includes(window.location.hostname)) {
        window.history.back();
    } else {
        window.location.href = 'index.php';
    }
}

// ==============================================
// VISI MISI PAGE
// ==============================================
function initVisiMisi() {
    console.log('👁️ Visi Misi page initialized');
    
    const form = document.getElementById('visiMisiForm');
    if (!form) return;
    
    if (typeof CKEDITOR !== 'undefined') {
        if (document.getElementById('editor_visi')) {
            CKEDITOR.replace('editor_visi', { height: 200 });
        }
        if (document.getElementById('editor_misi')) {
            CKEDITOR.replace('editor_misi', { height: 300 });
        }
    }
    
    const submitBtn = document.getElementById('btnSubmit');
    if (submitBtn) {
        form.addEventListener('submit', function() {
            if (typeof CKEDITOR !== 'undefined') {
                if (CKEDITOR.instances.editor_visi) CKEDITOR.instances.editor_visi.updateElement();
                if (CKEDITOR.instances.editor_misi) CKEDITOR.instances.editor_misi.updateElement();
            }
            submitBtn.classList.add('loading');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
            submitBtn.disabled = true;
        });
    }
}

// ==============================================
// SEJARAH PAGE
// ==============================================
function initSejarahPage() {
    console.log('📜 Sejarah page initialized');
    
    const form = document.getElementById('sejarahForm');
    if (!form) return;
    
    if (typeof CKEDITOR !== 'undefined' && document.getElementById('editor')) {
        CKEDITOR.replace('editor', { height: 300 });
    }
    
    const gambarInput = document.getElementById('gambar');
    const previewImage = document.getElementById('previewImage');
    const previewContainer = document.getElementById('previewContainer');
    const fileUploadArea = document.getElementById('fileUploadArea');
    const currentFile = document.getElementById('currentFile');
    
    if (gambarInput && fileUploadArea) {
        fileUploadArea.onclick = function() { gambarInput.click(); };
        
        gambarInput.onchange = function(e) {
            const file = e.target.files[0];
            if (file && previewImage && previewContainer) {
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
                if (!allowedTypes.includes(file.type)) {
                    alert('❌ Format file harus JPG, JPEG, PNG, GIF, atau WEBP!');
                    gambarInput.value = '';
                    return;
                }
                
                const maxSize = 2 * 1024 * 1024;
                if (file.size > maxSize) {
                    alert('❌ Ukuran file maksimal 2MB!');
                    gambarInput.value = '';
                    return;
                }
                
                const reader = new FileReader();
                reader.onload = function(event) {
                    previewImage.src = event.target.result;
                    previewContainer.style.display = 'block';
                    if (currentFile) currentFile.style.display = 'none';
                };
                reader.readAsDataURL(file);
            }
        };
    }
    
    const submitBtn = document.getElementById('btnSubmit');
    if (submitBtn) {
        form.addEventListener('submit', function() {
            if (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances.editor) {
                CKEDITOR.instances.editor.updateElement();
            }
            submitBtn.classList.add('loading');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
            submitBtn.disabled = true;
        });
    }
}

// ==============================================
// SAMBUTAN PAGE
// ==============================================
function initSambutanPage() {
    console.log('🎤 Sambutan page initialized');
    
    const form = document.getElementById('sambutanForm');
    if (!form) return;
    
    if (typeof CKEDITOR !== 'undefined' && document.getElementById('editor')) {
        CKEDITOR.replace('editor', { height: 300 });
    }
    
    const fotoInput = document.getElementById('foto');
    const previewImage = document.getElementById('previewImage');
    const previewContainer = document.getElementById('previewContainer');
    const fileUploadArea = document.getElementById('fileUploadArea');
    const currentFile = document.getElementById('currentFile');
    
    if (fotoInput && fileUploadArea) {
        fileUploadArea.onclick = function() { fotoInput.click(); };
        
        fotoInput.onchange = function(e) {
            const file = e.target.files[0];
            if (file && previewImage && previewContainer) {
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
                if (!allowedTypes.includes(file.type)) {
                    alert('❌ Format file harus JPG, JPEG, PNG, atau WEBP!');
                    fotoInput.value = '';
                    return;
                }
                
                const maxSize = 2 * 1024 * 1024;
                if (file.size > maxSize) {
                    alert('❌ Ukuran file maksimal 2MB!');
                    fotoInput.value = '';
                    return;
                }
                
                const reader = new FileReader();
                reader.onload = function(event) {
                    previewImage.src = event.target.result;
                    previewContainer.style.display = 'block';
                    if (currentFile) currentFile.style.display = 'none';
                };
                reader.readAsDataURL(file);
            }
        };
    }
    
    const submitBtn = document.getElementById('btnSubmit');
    if (submitBtn) {
        form.addEventListener('submit', function() {
            if (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances.editor) {
                CKEDITOR.instances.editor.updateElement();
            }
            submitBtn.classList.add('loading');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
            submitBtn.disabled = true;
        });
    }
}

// ==============================================
// PRESTASI PAGE - FULL VERSION
// ==============================================

function initPrestasiPage() {
    console.log('🏆 Prestasi page initialized');
    
    // File upload handler
    const uploadArea = document.getElementById('uploadArea');
    const gambarInput = document.getElementById('gambarInput');
    const previewContainer = document.getElementById('previewContainer');
    const previewImage = document.getElementById('previewImage');
    const removePreviewBtn = document.getElementById('removePreviewBtn');
    
    if (uploadArea && gambarInput) {
        uploadArea.addEventListener('click', () => gambarInput.click());
        
        gambarInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file && previewImage && previewContainer) {
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp', 'image/gif'];
                if (!allowedTypes.includes(file.type)) {
                    alert('❌ Format file harus JPG, JPEG, PNG, WEBP, atau GIF!');
                    gambarInput.value = '';
                    return;
                }
                
                const maxSize = 5 * 1024 * 1024;
                if (file.size > maxSize) {
                    alert('❌ Ukuran file maksimal 5MB!');
                    gambarInput.value = '';
                    return;
                }
                
                const reader = new FileReader();
                reader.onload = e => { 
                    previewImage.src = e.target.result; 
                    previewContainer.style.display = 'block'; 
                    uploadArea.style.display = 'none';
                };
                reader.readAsDataURL(file);
            }
        });
        
        if (removePreviewBtn) {
            removePreviewBtn.addEventListener('click', function() {
                gambarInput.value = '';
                if (previewContainer) previewContainer.style.display = 'none';
                if (uploadArea) uploadArea.style.display = 'block';
            });
        }
    }
    
    // ========== MODAL DELETE HANDLER ==========
    let deleteId = null;
    let hasGambar = false;
    const deleteModal = document.getElementById('deleteModal');
    const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
    const closeModalBtns = document.querySelectorAll('.modal-close, #btnCloseModal');
    const fileWarningContainer = document.getElementById('fileWarningContainer');
    
    // Delete button di tabel
    document.querySelectorAll('.btn-delete').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            deleteId = this.dataset.id;
            hasGambar = this.dataset.hasGambar === 'true';
            
            const itemName = this.dataset.name;
            document.getElementById('deleteItemName').innerHTML = `<i class="fas fa-trophy"></i> ${itemName}`;
            
            if (fileWarningContainer) {
                fileWarningContainer.style.display = hasGambar ? 'block' : 'none';
            }
            
            if (deleteModal) deleteModal.style.display = 'flex';
        });
    });
    
    // Delete button di detail page
    document.querySelectorAll('.btn-delete-detail').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            deleteId = this.dataset.id;
            hasGambar = this.dataset.hasGambar === 'true';
            
            const itemName = this.dataset.name;
            document.getElementById('deleteItemName').innerHTML = `<i class="fas fa-trophy"></i> ${itemName}`;
            
            if (fileWarningContainer) {
                fileWarningContainer.style.display = hasGambar ? 'block' : 'none';
            }
            
            if (deleteModal) deleteModal.style.display = 'flex';
        });
    });
    
    // Confirm delete
    if (confirmDeleteBtn) {
        confirmDeleteBtn.addEventListener('click', function(e) {
            if (deleteId) {
                window.location.href = `index.php?delete=${deleteId}`;
            }
        });
    }
    
    // Close modal
    closeModalBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            if (deleteModal) deleteModal.style.display = 'none';
            deleteId = null;
        });
    });
    
    // Close modal when clicking outside
    if (deleteModal) {
        window.addEventListener('click', function(e) {
            if (e.target === deleteModal) {
                deleteModal.style.display = 'none';
                deleteId = null;
            }
        });
    }
}

// ==============================================
// PPDB PAGE
// ==============================================
function initPpdbPage() {
    const form = document.getElementById('ppdbForm');
    if (!form) return;
    
    let syaratCounter = document.querySelectorAll('#syaratContainer .syarat-item').length || 2;
    
    const addSyaratBtn = document.getElementById('addSyaratBtn');
    if (addSyaratBtn) {
        addSyaratBtn.addEventListener('click', function() {
            const container = document.getElementById('syaratContainer');
            const newItem = document.createElement('div');
            newItem.className = 'syarat-item';
            newItem.innerHTML = `
                <div class="syarat-header"><h4>Syarat ${syaratCounter + 1}</h4><button type="button" class="btn-remove-syarat">Hapus</button></div>
                <div class="syarat-fields">
                    <div class="form-row"><div class="form-group"><label>Icon</label><input type="text" name="syarat_icon[]" class="form-control" value="fa-check"></div>
                    <div class="form-group"><label>Judul</label><input type="text" name="syarat_title[]" class="form-control" required></div></div>
                    <div class="form-group"><label>Deskripsi</label><textarea name="syarat_desc[]" class="form-control" rows="2"></textarea></div>
                    <div class="form-group"><label>Daftar Berkas</label><div class="files-container" data-index="${syaratCounter}"></div>
                    <button type="button" class="btn-add-file" data-index="${syaratCounter}">Tambah Berkas</button></div>
                </div>
            `;
            container.appendChild(newItem);
            syaratCounter++;
        });
    }
    
    const qrInput = document.getElementById('qr_code');
    if (qrInput) {
        const previewQr = document.getElementById('previewQr');
        const previewQrContainer = document.getElementById('previewQrContainer');
        qrInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file && previewQr) {
                const reader = new FileReader();
                reader.onload = e => { previewQr.src = e.target.result; if (previewQrContainer) previewQrContainer.style.display = 'block'; };
                reader.readAsDataURL(file);
            }
        });
    }
    
    const submitBtn = document.getElementById('btnSubmit');
    if (submitBtn) {
        form.addEventListener('submit', function() {
            submitBtn.classList.add('loading');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
            submitBtn.disabled = true;
        });
    }
}

// ==============================================
// PENGUMUMAN PAGE
// ==============================================
function initPengumumanPage() {
    console.log('📢 Pengumuman page initialized');
}

// ==============================================
// PEMBIASAAN PAGE
// ==============================================
function initPembiasaanPage() {
    console.log('🌅 Pembiasaan page initialized');
    
    const deleteModal = document.getElementById('deleteModal');
    const deleteButtons = document.querySelectorAll('.btn-delete');
    
    function showDeleteModal(id, name) {
        const deleteItemName = document.getElementById('deleteItemName');
        const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
        const fileWarningContainer = document.getElementById('fileWarningContainer');
        
        if (deleteItemName) deleteItemName.innerText = name;
        if (confirmDeleteBtn) confirmDeleteBtn.href = 'index.php?delete=' + id;
        if (fileWarningContainer) fileWarningContainer.style.display = 'none';
        
        if (deleteModal) {
            deleteModal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }
    }
    
    function closeDeleteModal() {
        if (deleteModal) {
            deleteModal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }
    }
    
    deleteButtons.forEach(button => {
        button.onclick = function(e) {
            e.preventDefault();
            const id = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');
            showDeleteModal(id, name);
        };
    });
    
    const modalClose = deleteModal ? deleteModal.querySelector('.modal-close') : null;
    const btnCloseModal = document.getElementById('btnCloseModal');
    
    if (modalClose) modalClose.onclick = closeDeleteModal;
    if (btnCloseModal) btnCloseModal.onclick = closeDeleteModal;
    
    if (deleteModal) {
        deleteModal.onclick = function(e) {
            if (e.target === deleteModal) closeDeleteModal();
        };
    }
    
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && deleteModal && deleteModal.style.display === 'flex') {
            closeDeleteModal();
        }
    });
}

// ==============================================
// KONTAK PAGE
// ==============================================
function initKontakPage() {
    console.log('📞 Kontak page initialized');
    
    const form = document.getElementById('kontakForm');
    const submitBtn = document.getElementById('btnSubmit');
    
    if (form && submitBtn) {
        form.addEventListener('submit', function() {
            submitBtn.classList.add('loading');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
            submitBtn.disabled = true;
        });
    }
}

// ==============================================
// HERO SLIDER PAGE
// ==============================================
function initHeroSliderPage() {
    console.log('🎠 Hero Slider page initialized');
    
    const gambarInput = document.getElementById('gambarInput');
    const previewContainer = document.getElementById('previewContainer');
    const previewImage = document.getElementById('previewImage');
    const fileUploadArea = document.getElementById('fileUploadArea');
    const fileInfo = document.getElementById('fileInfo');
    const fileName = document.getElementById('fileName');
    const fileSize = document.getElementById('fileSize');
    const previewFileSize = document.getElementById('previewFileSize');
    
    if (gambarInput && fileUploadArea) {
        fileUploadArea.onclick = function() { gambarInput.click(); };
        
        gambarInput.onchange = function(e) {
            const file = e.target.files[0];
            if (!file) return;
            
            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
            if (!allowedTypes.includes(file.type)) {
                alert('❌ Format file harus JPG, JPEG, PNG, GIF, atau WEBP!');
                gambarInput.value = '';
                return;
            }
            
            const maxSize = 10 * 1024 * 1024;
            if (file.size > maxSize) {
                alert(`❌ Ukuran file maksimal 10MB!`);
                gambarInput.value = '';
                return;
            }
            
            if (fileInfo) {
                fileName.innerText = file.name;
                fileSize.innerText = formatFileSize(file.size);
                fileInfo.style.display = 'block';
            }
            
            if (previewContainer && previewImage) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    previewImage.src = event.target.result;
                    previewContainer.style.display = 'block';
                    if (previewFileSize) previewFileSize.innerText = formatFileSize(file.size);
                };
                reader.readAsDataURL(file);
            }
        };
    }
    
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
    
    const deleteModal = document.getElementById('deleteModal');
    const deleteButtons = document.querySelectorAll('.btn-delete');
    
    function showDeleteModal(id, name, hasGambar) {
        const deleteItemName = document.getElementById('deleteItemName');
        const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
        const fileWarningContainer = document.getElementById('fileWarningContainer');
        const fileWarningText = document.getElementById('fileWarningText');
        
        if (deleteItemName) deleteItemName.innerText = name;
        if (confirmDeleteBtn) confirmDeleteBtn.href = 'index.php?delete=' + id;
        
        if (fileWarningContainer && fileWarningText) {
            if (hasGambar === 'true') {
                fileWarningText.innerText = 'Slide ini memiliki GAMBAR yang akan ikut terhapus!';
                fileWarningContainer.style.display = 'block';
            } else {
                fileWarningContainer.style.display = 'none';
            }
        }
        
        if (deleteModal) {
            deleteModal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }
    }
    
    function closeDeleteModal() {
        if (deleteModal) {
            deleteModal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }
    }
    
    deleteButtons.forEach(button => {
        button.onclick = function(e) {
            e.preventDefault();
            const id = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');
            const hasGambar = this.getAttribute('data-has-gambar');
            showDeleteModal(id, name, hasGambar);
        };
    });
    
    const modalClose = deleteModal ? deleteModal.querySelector('.modal-close') : null;
    const btnCloseModal = document.getElementById('btnCloseModal');
    
    if (modalClose) modalClose.onclick = closeDeleteModal;
    if (btnCloseModal) btnCloseModal.onclick = closeDeleteModal;
    
    if (deleteModal) {
        deleteModal.onclick = function(e) {
            if (e.target === deleteModal) closeDeleteModal();
        };
    }
    
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && deleteModal && deleteModal.style.display === 'flex') {
            closeDeleteModal();
        }
    });
    
    const submitBtn = document.getElementById('btnSubmit');
    const form = document.getElementById('sliderForm');
    
    if (form && submitBtn) {
        form.addEventListener('submit', function() {
            submitBtn.classList.add('loading');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
            submitBtn.disabled = true;
        });
    }
}

// ==============================================
// OBSERVER FOR DYNAMIC BUTTONS
// ==============================================
if (typeof MutationObserver !== 'undefined') {
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.addedNodes.length) {
                initDeleteButtons();
                initModalCloseButtons();
                initBackButtons();
            }
        });
    });
    
    observer.observe(document.body, { childList: true, subtree: true });
}

// ==============================================
// LOADING SPINNER STYLE
// ==============================================
if (!document.querySelector('#loading-style')) {
    const style = document.createElement('style');
    style.id = 'loading-style';
    style.textContent = `@keyframes spin { to { transform: rotate(360deg); } }`;
    document.head.appendChild(style);
}
// ==============================================
// DATA SISWA PAGE - FULL WORKING VERSION
// ==============================================

function initDataSiswaPage() {
    console.log('📊 Data Siswa page initialized');
    
    // Cek apakah kita di halaman data siswa
    if (!document.querySelector('.data-table')) return;
    
    // Fungsi untuk format number dengan pemisah ribuan
    function formatNumber(num) {
        return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }
    
    // Fungsi untuk menghitung ulang semua total
    window.recalculateTotals = function() {
        let totalLaki = 0;
        let totalPerempuan = 0;
        
        for (let i = 1; i <= 6; i++) {
            let lakiInput = document.getElementById('laki_' + i);
            let perempuanInput = document.getElementById('perempuan_' + i);
            let totalCell = document.getElementById('total_' + i);
            
            if (lakiInput && perempuanInput && totalCell) {
                let laki = parseInt(lakiInput.value) || 0;
                let perempuan = parseInt(perempuanInput.value) || 0;
                let total = laki + perempuan;
                
                totalCell.innerHTML = '<strong>' + formatNumber(total) + '</strong>';
                
                totalLaki += laki;
                totalPerempuan += perempuan;
            }
        }
        
        let totalSemua = totalLaki + totalPerempuan;
        
        // Update footer tabel
        let totalLakiElem = document.getElementById('total_laki_semua');
        let totalPerempuanElem = document.getElementById('total_perempuan_semua');
        let totalSemuaElem = document.getElementById('total_semua');
        
        if (totalLakiElem) totalLakiElem.innerHTML = formatNumber(totalLaki);
        if (totalPerempuanElem) totalPerempuanElem.innerHTML = formatNumber(totalPerempuan);
        if (totalSemuaElem) totalSemuaElem.innerHTML = formatNumber(totalSemua);
        
        // Update summary card
        let summaryTotal = document.getElementById('summary_total');
        let summaryLaki = document.getElementById('summary_laki');
        let summaryPerempuan = document.getElementById('summary_perempuan');
        
        if (summaryTotal) summaryTotal.innerHTML = formatNumber(totalSemua) + ' Siswa';
        if (summaryLaki) summaryLaki.innerHTML = formatNumber(totalLaki);
        if (summaryPerempuan) summaryPerempuan.innerHTML = formatNumber(totalPerempuan);
    };
    
    // Ambil semua input number di tabel data siswa
    const inputs = document.querySelectorAll('#dataSiswaTable input[type="number"]');
    console.log('Ditemukan ' + inputs.length + ' input number');
    
    // Pasang event listener ke setiap input
    inputs.forEach(input => {
        // Hapus event listener lama
        input.removeEventListener('input', window.recalculateTotals);
        input.removeEventListener('change', window.recalculateTotals);
        
        // Tambahkan event listener baru
        input.addEventListener('input', window.recalculateTotals);
        input.addEventListener('change', window.recalculateTotals);
        
        console.log('Event terpasang untuk:', input.id);
    });
    
    // Panggil sekali untuk inisialisasi
    window.recalculateTotals();
    
    console.log('✅ Data Siswa auto-calculate siap!');
}

// ==============================================
// DATA SISWA DELETE CONFIRMATION
// ==============================================

function initDataSiswaDelete() {
    const deleteButtons = document.querySelectorAll('.data-table .btn-delete');
    const deleteModal = document.getElementById('deleteModal');
    
    if (!deleteModal || deleteButtons.length === 0) return;
    
    function showDeleteModal(id, kelas, tahun) {
        const deleteItemName = document.getElementById('deleteItemName');
        const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
        const itemType = document.getElementById('itemType');
        const fileWarningContainer = document.getElementById('fileWarningContainer');
        
        if (deleteItemName) deleteItemName.innerText = '"Kelas ' + kelas + ' - ' + tahun + '"';
        if (itemType) itemType.innerText = 'Data Siswa';
        if (confirmDeleteBtn) confirmDeleteBtn.href = 'index.php?delete=' + id;
        if (fileWarningContainer) fileWarningContainer.style.display = 'none';
        
        deleteModal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
    
    function handleDataSiswaDelete(e) {
        e.preventDefault();
        const id = this.getAttribute('data-id');
        const kelas = this.getAttribute('data-kelas');
        const tahun = this.getAttribute('data-tahun');
        
        if (id && kelas) {
            showDeleteModal(id, kelas, tahun);
        } else {
            if (confirm('Yakin ingin menghapus data ini?')) {
                window.location.href = this.href;
            }
        }
    }
    
    deleteButtons.forEach(button => {
        button.removeEventListener('click', handleDataSiswaDelete);
        button.addEventListener('click', handleDataSiswaDelete);
    });
}

// ==============================================
// INISIALISASI SAAT HALAMAN SIAP
// ==============================================

// Jalankan saat halaman selesai dimuat
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function() {
        if (document.querySelector('.data-table')) {
            initDataSiswaPage();
            initDataSiswaDelete();
        }
    });
} else {
    if (document.querySelector('.data-table')) {
        initDataSiswaPage();
        initDataSiswaDelete();
    }
}