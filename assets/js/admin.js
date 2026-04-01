// ==============================================
// FILE: admin.js
// ADMIN PANEL - MI MUHAMMADIYAH BODASKARANGJATI
// VERSI: 3.0 - RAPIKAN & FIX DELETE ALL MODULES
// ==============================================

// ==============================================
// GLOBAL VARIABLES
// ==============================================
let currentDeleteId = null;

// ==============================================
// MAIN INITIALIZATION
// ==============================================
document.addEventListener('DOMContentLoaded', function() {
    console.log('✅ Admin JS v3.0 Loaded');
    
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
    // Pastikan fungsi sudah ada
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
    const mobileToggle = document.getElementById('mobileMenuToggle');
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
        if (mobileToggle) mobileToggle.style.display = 'none';
        if (floatingToggle) floatingToggle.style.display = 'none';
    }
    
    function closeSidebar() {
        sidebar.classList.remove('show');
        overlay.classList.remove('show');
        document.body.style.overflow = '';
        if (window.innerWidth <= 992) {
            if (mobileToggle) mobileToggle.style.display = 'flex';
            if (floatingToggle) floatingToggle.style.display = 'flex';
        }
    }
    
    if (mobileToggle) mobileToggle.addEventListener('click', openSidebar);
    if (floatingToggle) floatingToggle.addEventListener('click', openSidebar);
    if (closeBtn) closeBtn.addEventListener('click', closeSidebar);
    overlay.addEventListener('click', closeSidebar);
    
    window.addEventListener('resize', function() {
        if (window.innerWidth > 992) {
            closeSidebar();
            if (mobileToggle) mobileToggle.style.display = 'none';
            if (floatingToggle) floatingToggle.style.display = 'none';
        } else {
            if (!sidebar.classList.contains('show')) {
                if (mobileToggle) mobileToggle.style.display = 'flex';
                if (floatingToggle) floatingToggle.style.display = 'flex';
            }
        }
    });
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
// 12. VISI MISI PAGE
// ==============================================
function initVisiMisi() {
    const form = document.getElementById('visiMisiForm');
    if (!form) return;
    
    if (typeof CKEDITOR !== 'undefined') {
        if (document.getElementById('editor_visi')) CKEDITOR.replace('editor_visi', { height: 200 });
        if (document.getElementById('editor_misi')) CKEDITOR.replace('editor_misi', { height: 300 });
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
// 13. SEJARAH PAGE
// ==============================================
function initSejarahPage() {
    const form = document.getElementById('sejarahForm');
    if (!form) return;
    
    if (typeof CKEDITOR !== 'undefined' && document.getElementById('editor')) {
        CKEDITOR.replace('editor', { height: 300 });
    }
    
    const gambarInput = document.getElementById('gambar');
    if (gambarInput) {
        const previewImage = document.getElementById('previewImage');
        const previewContainer = document.getElementById('previewContainer');
        const fileUploadArea = document.getElementById('fileUploadArea');
        
        if (fileUploadArea) fileUploadArea.addEventListener('click', () => gambarInput.click());
        
        gambarInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file && previewImage && previewContainer) {
                const reader = new FileReader();
                reader.onload = e => { previewImage.src = e.target.result; previewContainer.style.display = 'block'; };
                reader.readAsDataURL(file);
            }
        });
    }
    
    const submitBtn = document.getElementById('btnSubmit');
    if (submitBtn) {
        form.addEventListener('submit', function() {
            if (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances.editor) CKEDITOR.instances.editor.updateElement();
            submitBtn.classList.add('loading');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
            submitBtn.disabled = true;
        });
    }
}

// ==============================================
// 14. SAMBUTAN PAGE
// ==============================================
function initSambutanPage() {
    const form = document.getElementById('sambutanForm');
    if (!form) return;
    
    if (typeof CKEDITOR !== 'undefined' && document.getElementById('editor')) {
        CKEDITOR.replace('editor', { height: 300 });
    }
    
    const fotoInput = document.getElementById('foto');
    if (fotoInput) {
        const previewImage = document.getElementById('previewImage');
        const previewContainer = document.getElementById('previewContainer');
        const fileUploadArea = document.getElementById('fileUploadArea');
        
        if (fileUploadArea) fileUploadArea.addEventListener('click', () => fotoInput.click());
        
        fotoInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file && previewImage && previewContainer) {
                const reader = new FileReader();
                reader.onload = e => { previewImage.src = e.target.result; previewContainer.style.display = 'block'; };
                reader.readAsDataURL(file);
            }
        });
    }
    
    const submitBtn = document.getElementById('btnSubmit');
    if (submitBtn) {
        form.addEventListener('submit', function() {
            if (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances.editor) CKEDITOR.instances.editor.updateElement();
            submitBtn.classList.add('loading');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
            submitBtn.disabled = true;
        });
    }
}

// ==============================================
// 15. PRESTASI PAGE
// ==============================================
function initPrestasiPage() {
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
                const reader = new FileReader();
                reader.onload = e => { previewImage.src = e.target.result; previewContainer.style.display = 'block'; uploadArea.style.display = 'none'; };
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
}

// ==============================================
// 16. PPDB PAGE
// ==============================================
function initPpdbPage() {
    const form = document.getElementById('ppdbForm');
    if (!form) return;
    
    let syaratCounter = document.querySelectorAll('#syaratContainer .syarat-item').length || 2;
    
    function addFileItem(container, fileIndex) {
        const fileItem = document.createElement('div');
        fileItem.className = 'file-item';
        fileItem.style.cssText = 'display:flex;gap:10px;margin-bottom:10px;align-items:center';
        fileItem.innerHTML = `
            <input type="text" name="syarat_files[${fileIndex}][icon][]" class="form-control" value="fa-file-alt" placeholder="Icon" style="width:100px">
            <input type="text" name="syarat_files[${fileIndex}][name][]" class="form-control" placeholder="Nama berkas" style="flex:1">
            <button type="button" class="btn-remove-file" style="background:#ef4444;color:white;border:none;border-radius:5px;width:32px;height:32px;cursor:pointer"><i class="fas fa-times"></i></button>
        `;
        container.appendChild(fileItem);
        fileItem.querySelector('.btn-remove-file').addEventListener('click', () => fileItem.remove());
    }
    
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
// 17. PENGUMUMAN PAGE
// ==============================================
function initPengumumanPage() {
    console.log('📢 Pengumuman page initialized');
}

// ==============================================
// 18. PEMBIASAAN PAGE
// ==============================================
function initPembiasaanPage() {
    console.log('🌅 Pembiasaan page initialized');
}

// ==============================================
// 19. KONTAK PAGE
// ==============================================
function initKontakPage() {
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
// 20. HERO SLIDER PAGE
// ==============================================
function initHeroSliderPage() {
    const gambarInput = document.getElementById('gambarInput');
    if (!gambarInput) return;
    
    const previewContainer = document.getElementById('previewContainer');
    const previewImage = document.getElementById('previewImage');
    const fileUploadArea = document.getElementById('fileUploadArea');
    
    if (fileUploadArea) fileUploadArea.addEventListener('click', () => gambarInput.click());
    
    gambarInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (!file) return;
        
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        if (!allowedTypes.includes(file.type)) {
            alert('❌ Hanya file JPG, PNG, GIF, WEBP yang diperbolehkan!');
            this.value = '';
            return;
        }
        
        const maxSize = 10 * 1024 * 1024;
        if (file.size > maxSize) {
            alert(`❌ Ukuran file maksimal 10MB!`);
            this.value = '';
            return;
        }
        
        if (previewContainer && previewImage) {
            const reader = new FileReader();
            reader.onload = e => { previewImage.src = e.target.result; previewContainer.style.display = 'block'; };
            reader.readAsDataURL(file);
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
// 21. GURU STAFF PAGE
// ==============================================
function initGuruStaffPage() {
    const strukturModal = document.getElementById('strukturModal');
    const btnKelolaStruktur = document.getElementById('btnKelolaStruktur');
    
    if (btnKelolaStruktur && strukturModal) {
        btnKelolaStruktur.addEventListener('click', () => strukturModal.style.display = 'flex');
        
        const closeModal = () => strukturModal.style.display = 'none';
        strukturModal.querySelectorAll('.modal-close, #btnCloseStrukturModal').forEach(btn => btn.addEventListener('click', closeModal));
        strukturModal.addEventListener('click', e => { if (e.target === strukturModal) closeModal(); });
    }
    
    const strukturInput = document.getElementById('strukturInput');
    if (strukturInput) {
        const previewImg = document.getElementById('strukturPreviewImg');
        const preview = document.getElementById('strukturPreview');
        strukturInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file && previewImg && preview) {
                const reader = new FileReader();
                reader.onload = e => { previewImg.src = e.target.result; preview.style.display = 'block'; };
                reader.readAsDataURL(file);
            }
        });
    }
}

// ==============================================
// 22. GALERI VIDEO PAGE
// ==============================================
function initGaleriVideoPage() {
    console.log('🎬 Galeri Video page initialized');
    
    const form = document.getElementById('videoForm');
    const thumbnailInput = document.getElementById('thumbnail');
    const previewContainer = document.getElementById('previewContainer');
    const previewImage = document.getElementById('previewImage');
    const fileUploadArea = document.getElementById('fileUploadArea');
    
    if (thumbnailInput && previewImage && fileUploadArea) {
        fileUploadArea.addEventListener('click', () => thumbnailInput.click());
        
        thumbnailInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file && previewImage && previewContainer) {
                const reader = new FileReader();
                reader.onload = e => { previewImage.src = e.target.result; previewContainer.style.display = 'block'; };
                reader.readAsDataURL(file);
            }
        });
    }
    
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
// 23. GALERI FOTO PAGE
// ==============================================
function initGaleriFotoPage() {
    console.log('📸 Galeri Foto page initialized');
    
    const form = document.getElementById('fotoForm');
    const fileInput = document.getElementById('file_foto');
    const previewContainer = document.getElementById('previewContainer');
    const previewImage = document.getElementById('previewImage');
    const fileUploadArea = document.getElementById('fileUploadArea');
    
    if (fileInput && previewImage && fileUploadArea) {
        fileUploadArea.addEventListener('click', () => fileInput.click());
        
        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file && previewImage && previewContainer) {
                const reader = new FileReader();
                reader.onload = e => { previewImage.src = e.target.result; previewContainer.style.display = 'block'; };
                reader.readAsDataURL(file);
            }
        });
    }
    
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
// 24. EKSTRAKURIKULER PAGE
// ==============================================
function initEkstrakurikulerPage() {
    console.log('⚽ Ekstrakurikuler page initialized');
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
}

// ==============================================
// 27. USERS PAGE
// ==============================================
function initUsersPage() {
    console.log('👥 Users page initialized');
    
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
                reader.onload = e => { previewImage.src = e.target.result; previewContainer.style.display = 'block'; };
                reader.readAsDataURL(file);
            }
        });
    }
    
    const form = document.getElementById('userForm');
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
// 28. MOBILE HEADER
// ==============================================
function initMobileHeader() {
    const mobileMenuBtn = document.getElementById('mobileMenuBtn');
    const mobileProfileBtn = document.getElementById('mobileProfileBtn');
    const mobileProfileDropdown = document.getElementById('mobileProfileDropdown');
    const sidebar = document.getElementById('adminSidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const sidebarClose = document.getElementById('sidebarClose');
    
    function openSidebar() {
        if (sidebar) sidebar.classList.add('show');
        if (overlay) overlay.classList.add('show');
        document.body.style.overflow = 'hidden';
    }
    
    function closeSidebar() {
        if (sidebar) sidebar.classList.remove('show');
        if (overlay) overlay.classList.remove('show');
        document.body.style.overflow = '';
    }
    
    if (mobileMenuBtn) mobileMenuBtn.addEventListener('click', openSidebar);
    if (sidebarClose) sidebarClose.addEventListener('click', closeSidebar);
    if (overlay) overlay.addEventListener('click', closeSidebar);
    
    if (mobileProfileBtn && mobileProfileDropdown) {
        mobileProfileBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            mobileProfileDropdown.classList.toggle('show');
            const chevron = this.querySelector('i');
            if (chevron) chevron.style.transform = mobileProfileDropdown.classList.contains('show') ? 'rotate(180deg)' : 'rotate(0deg)';
        });
        
        document.addEventListener('click', function(e) {
            if (!mobileProfileBtn.contains(e.target) && !mobileProfileDropdown.contains(e.target)) {
                mobileProfileDropdown.classList.remove('show');
                const chevron = mobileProfileBtn.querySelector('i');
                if (chevron) chevron.style.transform = 'rotate(0deg)';
            }
        });
        
        mobileProfileDropdown.addEventListener('click', e => e.stopPropagation());
    }
    
    window.addEventListener('resize', function() {
        if (window.innerWidth > 992) closeSidebar();
    });
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
// FIX: BUTTON KEMBALI DI SEMUA HALAMAN
// ==============================================

// Fungsi untuk kembali ke halaman sebelumnya
window.goBack = function(defaultUrl) {
    console.log('🔙 Go back called, defaultUrl:', defaultUrl);
    
    // Cek apakah ada halaman sebelumnya (referrer dari domain yang sama)
    if (document.referrer && document.referrer !== '' && document.referrer.includes(window.location.hostname)) {
        window.history.back();
    } else {
        // Jika tidak ada history, arahkan ke defaultUrl
        window.location.href = defaultUrl || 'index.php';
    }
    return false;
};

// Inisialisasi semua tombol kembali
function initBackButtons() {
    // Cari semua link dan button yang merupakan tombol kembali
    const backElements = document.querySelectorAll('.btn-secondary, a[href*="index.php"], a[href*="../index.php"], a[href="#"]');
    
    backElements.forEach(function(element) {
        // Cek apakah ini tombol kembali (berdasarkan teks atau icon)
        const isBackButton = (element.textContent && (element.textContent.includes('Kembali') || element.textContent.includes('kembali'))) ||
                            (element.innerHTML && element.innerHTML.includes('fa-arrow-left'));
        
        if (isBackButton) {
            // Hapus event listener lama
            element.removeEventListener('click', handleBackButton);
            // Tambah event listener baru
            element.addEventListener('click', handleBackButton);
            
            // Jika href-nya "#", ubah supaya tidak scroll ke atas
            if (element.getAttribute('href') === '#') {
                element.setAttribute('href', 'javascript:void(0)');
            }
            
            console.log('✅ Tombol kembali ditemukan dan diperbaiki');
        }
    });
}

// Handler untuk tombol kembali
function handleBackButton(event) {
    const element = event.currentTarget;
    const href = element.getAttribute('href');
    
    console.log('🔘 Tombol kembali diklik, href:', href);
    
    // Cegah aksi default dulu
    event.preventDefault();
    
    // Jika ada href yang valid (bukan # atau javascript:void(0))
    if (href && href !== '#' && href !== 'javascript:void(0)' && href !== '') {
        // Arahkan ke href tersebut
        window.location.href = href;
    } else {
        // Gunakan fungsi goBack
        window.goBack('index.php');
    }
    
    return false;
}

// ==============================================
// FIX: TOMBOL KEMBALI DI DETAIL PAGE
// ==============================================

// Fungsi khusus untuk tombol kembali di detail page
window.backToIndex = function() {
    window.location.href = 'index.php';
    return false;
};

// Inisialisasi saat DOM siap
document.addEventListener('DOMContentLoaded', function() {
    console.log('🔧 Initializing back buttons...');
    initBackButtons();
});

// Observer untuk tombol yang ditambahkan secara dinamis
if (typeof MutationObserver !== 'undefined') {
    const backObserver = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.addedNodes.length) {
                initBackButtons();
            }
        });
    });
    
    backObserver.observe(document.body, {
        childList: true,
        subtree: true
    });
}