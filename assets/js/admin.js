// ==============================================
// FILE: admin.js
// ADMIN PANEL - MI MUHAMMADIYAH BODASKARANGJATI
// VERSI: 2.4 - PREMIUM (COMPLETE)
// ==============================================

// Menunggu DOM siap
document.addEventListener('DOMContentLoaded', function() {
    console.log('✅ Admin JS Premium loaded');
    
    // ==============================================
    // 0. LOGIN PAGE - BUTTON LOADING STATE
    // ==============================================
    const loginForm = document.getElementById('loginForm');
    const btnLogin = document.getElementById('btnLogin');
    
    if (loginForm && btnLogin) {
        loginForm.addEventListener('submit', function() {
            btnLogin.classList.add('loading');
            btnLogin.innerHTML = '<i class="fas fa-spinner fa-spin"></i> LOGIN...';
            btnLogin.disabled = true;
        });
    }
    
    // ==============================================
    // DASHBOARD INIT
    // ==============================================
    initDashboard();
    
    // ==============================================
    // VISI MISI INIT
    // ==============================================
    initVisiMisi();
    
    // ==============================================
    // USERS PAGE INIT
    // ==============================================
    initUsersPage();
    
    // ==============================================
    // SEJARAH PAGE INIT
    // ==============================================
    initSejarahPage();
    
    // ==============================================
    // SARANA PAGE INIT
    // ==============================================
    initSaranaPage();
    
    // ==============================================
    // SAMBUTAN PAGE INIT
    // ==============================================
    initSambutanPage();
    
    // ==============================================
    // PRESTASI PAGE INIT
    // ==============================================
    initPrestasiPage();
    
    // ==============================================
    // PPDB PAGE INIT
    // ==============================================
    initPpdbPage();
    
    // ==============================================
    // PENGUMUMAN PAGE INIT
    // ==============================================
    initPengumumanPage();
    
    // ==============================================
    // PEMBIASAAN PAGE INIT
    // ==============================================
    initPembiasaanPage();
    initPembiasaanDetailPage();
    
    // ==============================================
    // KONTAK PAGE INIT
    // ==============================================
    initKontakPage();
    
    // ==============================================
    // HERO SLIDER PAGE INIT
    // ==============================================
    initHeroSliderPage();
    
    // ==============================================
    // GURU STAFF PAGE INIT
    // ==============================================
    initGuruStaffPage();
    
    // ==============================================
    // GALERI VIDEO PAGE INIT
    // ==============================================
    initGaleriVideoPage();
    initGaleriVideoDetailPage();
    
    // ==============================================
    // GALERI FOTO PAGE INIT
    // ==============================================
    initGaleriFotoPage();
    initGaleriFotoDetailPage();
    
    // ==============================================
    // EKSTRAKURIKULER PAGE INIT
    // ==============================================
    initEkstrakurikulerPage();
    initEkstrakurikulerDetailPage();
    
    // ==============================================
    // AGENDA PAGE INIT
    // ==============================================
    initAgendaPage();
    
    // ==============================================
    // AUTO CLOSE ALERTS
    // ==============================================
    initAutoCloseAlertsWithFade();
    
    // Inisialisasi semua modul
    initMobileSidebar();
    initProfileDropdown();
    initAutoCloseAlerts();
    initDeleteModal();
    initFileUpload();
    initTableSearch();
    initFormValidation();
    initNumberInputs();
    initTooltips();
    initSmoothScroll();
    initResponsiveTables();
    initIOSFix();
    initTouchScroll();
    initHeroSliderPreview();
});

// ==============================================
// 1. MOBILE SIDEBAR TOGGLE
// ==============================================
function initMobileSidebar() {
    const sidebar = document.getElementById('adminSidebar');
    const closeBtn = document.getElementById('sidebarClose');
    const mobileToggle = document.getElementById('mobileMenuToggle');      // Untuk topbar mobile
    const floatingToggle = document.getElementById('floatingMenuToggle');  // Untuk floating button
    
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
        // Sembunyikan kedua tombol saat sidebar terbuka
        if (mobileToggle) mobileToggle.style.display = 'none';
        if (floatingToggle) floatingToggle.style.display = 'none';
    }
    
    function closeSidebar() {
        sidebar.classList.remove('show');
        overlay.classList.remove('show');
        document.body.style.overflow = '';
        if (window.innerWidth <= 992) {
            // Tampilkan kembali tombol yang sesuai
            if (mobileToggle) mobileToggle.style.display = 'flex';
            if (floatingToggle) floatingToggle.style.display = 'flex';
        }
    }
    
    // Event listener untuk topbar mobile toggle
    if (mobileToggle) {
        mobileToggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            openSidebar();
        });
    }
    
    // Event listener untuk floating toggle
    if (floatingToggle) {
        floatingToggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            openSidebar();
        });
    }
    
    if (closeBtn) {
        closeBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            closeSidebar();
        });
    }
    
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
                chevron.style.transform = profileDropdown.classList.contains('show') 
                    ? 'rotate(180deg)' 
                    : 'rotate(0deg)';
            }
        });
        
        document.addEventListener('click', function() {
            profileDropdown.classList.remove('show');
            const chevron = profileBtn.querySelector('i.fa-chevron-down');
            if (chevron) chevron.style.transform = 'rotate(0deg)';
        });
        
        profileDropdown.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    }
}

// ==============================================
// 3. AUTO CLOSE ALERT
// ==============================================
function initAutoCloseAlerts() {
    const alerts = document.querySelectorAll('.notification-container .alert, .alert-dismissible');
    
    alerts.forEach(function(alert) {
        setTimeout(function() {
            if (alert.style.display !== 'none') {
                alert.style.transition = 'opacity 0.5s ease';
                alert.style.opacity = '0';
                setTimeout(function() {
                    if (alert.style.display !== 'none') {
                        alert.style.display = 'none';
                    }
                }, 500);
            }
        }, 5000);
        
        const closeBtn = alert.querySelector('.close');
        if (closeBtn) {
            closeBtn.addEventListener('click', function() {
                alert.style.display = 'none';
            });
        }
    });
}

// ==============================================
// 4. MODAL CONFIRM DELETE
// ==============================================
let deleteId = null;

function initDeleteModal() {
    window.confirmDelete = function(id, name, type, hasFile = false) {
        deleteId = id;
        
        const modal = document.getElementById('deleteModal');
        const itemType = document.getElementById('itemType');
        const deleteItemName = document.getElementById('deleteItemName');
        const fileWarningContainer = document.getElementById('fileWarningContainer');
        const fileWarningText = document.getElementById('fileWarningText');
        const confirmBtn = document.getElementById('confirmDeleteBtn');
        
        if (!modal || !itemType || !deleteItemName || !confirmBtn) return false;
        
        let typeText = '';
        let fileWarningMsg = '';
        
        switch(type) {
            case 'agenda': typeText = 'agenda'; break;
            case 'pengumuman': typeText = 'pengumuman'; fileWarningMsg = 'Pengumuman ini memiliki gambar/lampiran yang akan ikut terhapus.'; break;
            case 'guru': typeText = 'guru/staff'; fileWarningMsg = 'Data ini memiliki foto yang akan ikut terhapus.'; break;
            case 'prestasi': typeText = 'prestasi'; fileWarningMsg = 'Gambar prestasi akan ikut terhapus.'; break;
            case 'foto': typeText = 'foto'; fileWarningMsg = 'File gambar akan ikut terhapus.'; break;
            case 'video': typeText = 'video'; fileWarningMsg = 'File thumbnail akan ikut terhapus.'; break;
            case 'slider': typeText = 'slide'; fileWarningMsg = 'Gambar slider akan ikut terhapus.'; break;
            default: typeText = type;
        }
        
        itemType.innerText = typeText;
        deleteItemName.innerText = '"' + name + '"';
        
        if (fileWarningContainer && fileWarningText) {
            if (hasFile && fileWarningMsg) {
                fileWarningText.innerText = fileWarningMsg;
                fileWarningContainer.style.display = 'block';
            } else {
                fileWarningContainer.style.display = 'none';
            }
        }
        
        confirmBtn.href = '?delete=' + id;
        modal.style.display = 'flex';
        return false;
    };
    
    window.closeModal = function() {
        const modal = document.getElementById('deleteModal');
        if (modal) modal.style.display = 'none';
        deleteId = null;
    };
    
    window.onclick = function(event) {
        const modal = document.getElementById('deleteModal');
        if (event.target == modal) window.closeModal();
    };
}

// ==============================================
// 5. FILE UPLOAD CLICK HANDLER
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
// 6. TABLE SEARCH & FILTER
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
            
            if (searchTerm) {
                const text = row.textContent.toLowerCase();
                showRow = showRow && text.includes(searchTerm);
            }
            
            if (filterStatus && showRow) {
                const statusCell = row.querySelector('.status-badge');
                if (statusCell) {
                    const rowStatus = statusCell.classList.contains('aktif') ? 'aktif' : 
                                     statusCell.classList.contains('nonaktif') ? 'nonaktif' : '';
                    showRow = showRow && (filterStatus === rowStatus);
                }
            }
            
            row.style.display = showRow ? '' : 'none';
        });
    }
    
    if (searchInput) {
        searchInput.addEventListener('keyup', function(e) {
            if (e.key === 'Enter') filterTable();
        });
        
        let debounceTimer;
        searchInput.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(filterTable, 300);
        });
    }
    
    if (filterSelect) {
        filterSelect.addEventListener('change', filterTable);
    }
}

// ==============================================
// 7. FORM VALIDATION
// ==============================================
function initFormValidation() {
    const forms = document.querySelectorAll('form');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            let isValid = true;
            const requiredFields = form.querySelectorAll('[required]');
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('is-invalid');
                    
                    let errorElement = field.parentElement.querySelector('.invalid-feedback');
                    if (!errorElement) {
                        errorElement = document.createElement('div');
                        errorElement.className = 'invalid-feedback';
                        errorElement.style.color = '#dc3545';
                        errorElement.style.fontSize = '0.8rem';
                        errorElement.style.marginTop = '5px';
                        field.parentElement.appendChild(errorElement);
                    }
                    errorElement.textContent = 'Field ini harus diisi';
                } else {
                    field.classList.remove('is-invalid');
                    const errorElement = field.parentElement.querySelector('.invalid-feedback');
                    if (errorElement) errorElement.remove();
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
// 8. NUMBER INPUTS WITH PLUS/MINUS
// ==============================================
function initNumberInputs() {
    document.querySelectorAll('input[type="number"]').forEach(input => {
        if (input.closest('.number-input-container')) return;
        
        const container = document.createElement('div');
        container.className = 'number-input-container';
        container.style.display = 'flex';
        container.style.alignItems = 'center';
        input.parentNode.insertBefore(container, input);
        container.appendChild(input);
        
        const minusBtn = document.createElement('button');
        minusBtn.type = 'button';
        minusBtn.className = 'number-minus';
        minusBtn.innerHTML = '<i class="fas fa-minus"></i>';
        minusBtn.style.width = '40px';
        minusBtn.style.height = '40px';
        minusBtn.style.border = '1px solid #e2e8f0';
        minusBtn.style.background = 'white';
        minusBtn.style.borderRadius = '10px 0 0 10px';
        minusBtn.style.cursor = 'pointer';
        
        const plusBtn = document.createElement('button');
        plusBtn.type = 'button';
        plusBtn.className = 'number-plus';
        plusBtn.innerHTML = '<i class="fas fa-plus"></i>';
        plusBtn.style.width = '40px';
        plusBtn.style.height = '40px';
        plusBtn.style.border = '1px solid #e2e8f0';
        plusBtn.style.background = 'white';
        plusBtn.style.borderRadius = '0 10px 10px 0';
        plusBtn.style.cursor = 'pointer';
        
        container.insertBefore(minusBtn, input);
        container.appendChild(plusBtn);
        
        input.style.borderRadius = '0';
        input.style.borderLeft = 'none';
        input.style.borderRight = 'none';
        input.style.textAlign = 'center';
        input.style.width = '80px';
        
        minusBtn.addEventListener('click', function() {
            let value = parseInt(input.value) || 0;
            const min = input.min ? parseInt(input.min) : 0;
            if (value > min) {
                input.value = value - 1;
                input.dispatchEvent(new Event('input', { bubbles: true }));
            }
        });
        
        plusBtn.addEventListener('click', function() {
            let value = parseInt(input.value) || 0;
            const max = input.max ? parseInt(input.max) : Infinity;
            if (value < max) {
                input.value = value + 1;
                input.dispatchEvent(new Event('input', { bubbles: true }));
            }
        });
    });
}

// ==============================================
// 9. TOOLTIPS
// ==============================================
function initTooltips() {}

// ==============================================
// 10. SMOOTH SCROLL
// ==============================================
function initSmoothScroll() {
    document.querySelectorAll('a[href^="#"]:not([href="#"])').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            const target = document.querySelector(href);
            if (target) {
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });
}

// ==============================================
// 11. RESPONSIVE TABLES
// ==============================================
function initResponsiveTables() {}

// ==============================================
// 12. FIX IOS ZOOM ON INPUT
// ==============================================
function initIOSFix() {
    if (/iPhone|iPad|iPod/i.test(navigator.userAgent)) {
        document.querySelectorAll('input, select, textarea').forEach(el => {
            el.addEventListener('focus', function() { this.style.fontSize = '16px'; });
            el.addEventListener('blur', function() { this.style.fontSize = ''; });
        });
    }
}

// ==============================================
// 13. TOUCH SCROLL IMPROVEMENT
// ==============================================
function initTouchScroll() {
    document.querySelectorAll('.table-container, .card-body').forEach(container => {
        if (/iPhone|iPad|iPod/i.test(navigator.userAgent)) {
            container.style.webkitOverflowScrolling = 'touch';
        }
    });
}

// ==============================================
// 14. HERO SLIDER PREVIEW (10MB)
// ==============================================
function initHeroSliderPreview() {
    const gambarInput = document.getElementById('gambarInput');
    if (!gambarInput) return;
    
    const previewContainer = document.getElementById('previewContainer');
    const previewImage = document.getElementById('previewImage');
    const fileInfo = document.getElementById('fileInfo');
    const fileName = document.getElementById('fileName');
    const fileSize = document.getElementById('fileSize');
    const fileUploadArea = document.getElementById('fileUploadArea');
    
    if (fileUploadArea) {
        fileUploadArea.addEventListener('click', function() { gambarInput.click(); });
    }
    
    gambarInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (!file) {
            if (previewContainer) previewContainer.style.display = 'none';
            if (fileInfo) fileInfo.style.display = 'none';
            return;
        }
        
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        if (!allowedTypes.includes(file.type)) {
            alert('❌ Hanya file JPG, PNG, GIF, WEBP yang diperbolehkan!');
            this.value = '';
            if (previewContainer) previewContainer.style.display = 'none';
            if (fileInfo) fileInfo.style.display = 'none';
            return;
        }
        
        const maxSize = 10 * 1024 * 1024;
        if (file.size > maxSize) {
            const fileSizeMB = (file.size / (1024 * 1024)).toFixed(2);
            alert(`❌ Ukuran file maksimal 10MB! (Ukuran file Anda: ${fileSizeMB}MB)`);
            this.value = '';
            if (previewContainer) previewContainer.style.display = 'none';
            if (fileInfo) fileInfo.style.display = 'none';
            return;
        }
        
        if (fileInfo && fileName && fileSize) {
            const fileSizeKB = (file.size / 1024).toFixed(2);
            const fileSizeDisplay = fileSizeKB > 1024 ? (file.size / (1024 * 1024)).toFixed(2) + ' MB' : fileSizeKB + ' KB';
            fileName.textContent = file.name;
            fileSize.textContent = 'Ukuran: ' + fileSizeDisplay;
            fileInfo.style.display = 'block';
        }
        
        if (previewContainer && previewImage) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImage.src = e.target.result;
                previewContainer.style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
        
        if (fileUploadArea) {
            fileUploadArea.style.background = '#e8f5e9';
            fileUploadArea.style.borderColor = '#28a745';
            const p = fileUploadArea.querySelector('p');
            if (p) p.innerHTML = '<i class="fas fa-check-circle"></i> File siap: ' + file.name;
        }
    });
}

// ==============================================
// 15. DASHBOARD - PROFILE DROPDOWN & ALERTS
// ==============================================
function initDashboard() {
    // Profile Dropdown
    const profileBtn = document.getElementById('profileDropdown');
    const profileDropdown = document.getElementById('profileDropdownMenu');
    
    if (profileBtn && profileDropdown) {
        profileBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            profileDropdown.classList.toggle('show');
            
            const chevron = this.querySelector('i.fa-chevron-down');
            if (chevron) {
                chevron.style.transform = profileDropdown.classList.contains('show') 
                    ? 'rotate(180deg)' 
                    : 'rotate(0deg)';
            }
        });
        
        document.addEventListener('click', function() {
            profileDropdown.classList.remove('show');
            if (profileBtn) {
                const chevron = profileBtn.querySelector('i.fa-chevron-down');
                if (chevron) chevron.style.transform = 'rotate(0deg)';
            }
        });
        
        profileDropdown.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    }
    
    // Auto-hide alerts
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            if (alert.style.display !== 'none') {
                alert.style.transition = 'opacity 0.3s ease';
                alert.style.opacity = '0';
                setTimeout(function() {
                    if (alert.style.display !== 'none') {
                        alert.style.display = 'none';
                    }
                }, 300);
            }
        });
    }, 5000);
}

// ==============================================
// 16. VISI MISI PAGE - CKEDITOR & FORM HANDLER
// ==============================================
function initVisiMisi() {
    // Cek apakah halaman visi misi
    const visiMisiForm = document.getElementById('visiMisiForm');
    const editorVisi = document.getElementById('editor_visi');
    const editorMisi = document.getElementById('editor_misi');
    
    if (!visiMisiForm && !editorVisi && !editorMisi) return;
    
    // Inisialisasi CKEditor untuk Visi
    if (typeof CKEDITOR !== 'undefined' && editorVisi) {
        CKEDITOR.replace('editor_visi', { 
            height: 200,
            toolbar: [
                { name: 'document', items: ['Source'] },
                { name: 'clipboard', items: ['Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo'] },
                { name: 'editing', items: ['Find', 'Replace', '-', 'SelectAll'] },
                { name: 'basicstyles', items: ['Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'CopyFormatting', 'RemoveFormat'] },
                { name: 'paragraph', items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote'] },
                { name: 'links', items: ['Link', 'Unlink'] },
                { name: 'insert', items: ['Image', 'Table', 'HorizontalRule', 'SpecialChar'] },
                { name: 'styles', items: ['Format', 'Font', 'FontSize'] },
                { name: 'colors', items: ['TextColor', 'BGColor'] },
                { name: 'tools', items: ['Maximize'] }
            ]
        });
    }
    
    // Inisialisasi CKEditor untuk Misi
    if (typeof CKEDITOR !== 'undefined' && editorMisi) {
        CKEDITOR.replace('editor_misi', { 
            height: 300,
            toolbar: [
                { name: 'document', items: ['Source'] },
                { name: 'clipboard', items: ['Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo'] },
                { name: 'editing', items: ['Find', 'Replace', '-', 'SelectAll'] },
                { name: 'basicstyles', items: ['Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'CopyFormatting', 'RemoveFormat'] },
                { name: 'paragraph', items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote'] },
                { name: 'links', items: ['Link', 'Unlink'] },
                { name: 'insert', items: ['Image', 'Table', 'HorizontalRule', 'SpecialChar'] },
                { name: 'styles', items: ['Format', 'Font', 'FontSize'] },
                { name: 'colors', items: ['TextColor', 'BGColor'] },
                { name: 'tools', items: ['Maximize'] }
            ]
        });
    }
    
    // Loading state untuk submit button
    const submitBtn = document.getElementById('btnSubmit');
    if (visiMisiForm && submitBtn) {
        visiMisiForm.addEventListener('submit', function() {
            // Update CKEditor content ke textarea sebelum submit
            if (typeof CKEDITOR !== 'undefined') {
                if (CKEDITOR.instances.editor_visi) {
                    CKEDITOR.instances.editor_visi.updateElement();
                }
                if (CKEDITOR.instances.editor_misi) {
                    CKEDITOR.instances.editor_misi.updateElement();
                }
            }
            
            submitBtn.classList.add('loading');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
            submitBtn.disabled = true;
        });
    }
}

// ==============================================
// 17. AUTO CLOSE ALERTS (GLOBAL)
// ==============================================
function initAutoCloseAlertsGlobal() {
    const alerts = document.querySelectorAll('.notification-container .alert');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            if (alert.style.display !== 'none') {
                alert.style.transition = 'opacity 0.3s ease';
                alert.style.opacity = '0';
                setTimeout(function() {
                    if (alert.style.display !== 'none') {
                        alert.style.display = 'none';
                    }
                }, 300);
            }
        }, 5000);
        
        // Close button handler
        const closeBtn = alert.querySelector('.close');
        if (closeBtn) {
            closeBtn.addEventListener('click', function() {
                alert.style.display = 'none';
            });
        }
    });
}

// ==============================================
// 18. USERS PAGE - MODAL DELETE & PREVIEW
// ==============================================
function initUsersPage() {
    // ========== MODAL DELETE ==========
    window.confirmDelete = function(id, name, hasFoto = false) {
        const deleteItemName = document.getElementById('deleteItemName');
        const fileWarningContainer = document.getElementById('fileWarningContainer');
        const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
        
        if (deleteItemName) deleteItemName.innerText = '"' + name + '"';
        
        if (fileWarningContainer) {
            fileWarningContainer.style.display = hasFoto ? 'block' : 'none';
        }
        
        if (confirmDeleteBtn) confirmDeleteBtn.href = 'index.php?delete=' + id;
        
        const modal = document.getElementById('deleteModal');
        if (modal) modal.style.display = 'flex';
    };
    
    window.closeModal = function() {
        const modal = document.getElementById('deleteModal');
        if (modal) modal.style.display = 'none';
    };
    
    // Klik di luar modal
    window.onclick = function(event) {
        const modal = document.getElementById('deleteModal');
        if (event.target == modal && modal) {
            window.closeModal();
        }
    };
    
    // ========== FILE UPLOAD PREVIEW ==========
    const fotoInput = document.getElementById('foto');
    const previewImage = document.getElementById('previewImage');
    const previewContainer = document.getElementById('previewContainer');
    const fileUploadArea = document.getElementById('fileUploadArea');
    
    if (fotoInput && previewImage && fileUploadArea) {
        // Klik area upload
        fileUploadArea.addEventListener('click', function(e) {
            if (e.target !== fotoInput) {
                fotoInput.click();
            }
        });
        
        // Preview gambar
        fotoInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    if (previewContainer) previewContainer.style.display = 'block';
                    if (previewImage) previewImage.style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        });
        
        // Jika ada preview image yang sudah ada (edit page)
        if (previewImage.src && previewImage.src !== window.location.href) {
            previewImage.style.display = 'block';
            if (previewContainer) previewContainer.style.display = 'block';
        }
    }
    
    // ========== FORM LOADING STATE ==========
    const userForm = document.getElementById('userForm');
    const submitBtn = document.getElementById('btnSubmit');
    
    if (userForm && submitBtn) {
        userForm.addEventListener('submit', function() {
            submitBtn.classList.add('loading');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
            submitBtn.disabled = true;
        });
    }
    
    // ========== RESET BUTTON ==========
    const resetBtn = document.getElementById('btnReset');
    if (resetBtn && userForm) {
        resetBtn.addEventListener('click', function() {
            setTimeout(function() {
                const defaultPreview = previewImage?.getAttribute('data-default-src');
                if (defaultPreview && previewImage) {
                    previewImage.src = defaultPreview;
                }
            }, 100);
        });
    }
    
    // Simpan default preview untuk edit page
    if (previewImage && previewImage.src && previewImage.src !== window.location.href) {
        previewImage.setAttribute('data-default-src', previewImage.src);
    }
}

// ==============================================
// 19. AUTO CLOSE ALERTS WITH FADE OUT
// ==============================================
function initAutoCloseAlertsWithFade() {
    const alerts = document.querySelectorAll('.notification-container .alert, .alert-dismissible');
    
    alerts.forEach(function(alert) {
        setTimeout(function() {
            if (alert.style.display !== 'none') {
                alert.style.transition = 'opacity 0.5s ease';
                alert.style.opacity = '0';
                setTimeout(function() {
                    if (alert.style.display !== 'none') {
                        alert.style.display = 'none';
                    }
                }, 500);
            }
        }, 5000);
        
        const closeBtn = alert.querySelector('.close');
        if (closeBtn) {
            closeBtn.addEventListener('click', function() {
                alert.style.display = 'none';
            });
        }
    });
}

// ==============================================
// GLOBAL FUNCTIONS
// ==============================================
window.showNotification = function(message, type = 'info') {
    if (typeof window.showNotification === 'function') {
        window.showNotification(message, type);
    } else {
        alert(message);
    }
};

window.showLoading = function() {
    const loader = document.getElementById('loadingOverlay');
    if (loader) {
        loader.style.display = 'flex';
    } else {
        const newLoader = document.createElement('div');
        newLoader.id = 'loadingOverlay';
        newLoader.style.cssText = `
            position: fixed; top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(255,255,255,0.8); display: flex; align-items: center;
            justify-content: center; z-index: 1000000;
        `;
        const spinner = document.createElement('div');
        spinner.style.cssText = `
            width: 50px; height: 50px; border: 4px solid #e2e8f0;
            border-top-color: #0B3D91; border-radius: 50%;
            animation: spin 1s linear infinite;
        `;
        newLoader.appendChild(spinner);
        document.body.appendChild(newLoader);
    }
};

window.hideLoading = function() {
    const loader = document.getElementById('loadingOverlay');
    if (loader) loader.style.display = 'none';
};

if (!document.querySelector('#loading-style')) {
    const style = document.createElement('style');
    style.id = 'loading-style';
    style.textContent = `@keyframes spin { to { transform: rotate(360deg); } }`;
    document.head.appendChild(style);
}

window.confirmDelete = window.confirmDelete || function(id, name, type) {
    if (confirm(`Yakin ingin menghapus "${name}"?`)) {
        window.location.href = `?delete=${id}`;
    }
    return false;
};

window.closeModal = window.closeModal || function() {
    const modal = document.getElementById('deleteModal');
    if (modal) modal.style.display = 'none';
};

// ==============================================
// 20. SEJARAH PAGE - CKEDITOR & PREVIEW
// ==============================================
function initSejarahPage() {
    // Cek apakah halaman sejarah
    const sejarahForm = document.getElementById('sejarahForm');
    const editorElement = document.getElementById('editor');
    
    if (!sejarahForm && !editorElement) return;
    
    // Inisialisasi CKEditor
    if (typeof CKEDITOR !== 'undefined' && editorElement) {
        CKEDITOR.replace('editor', { 
            height: 300,
            toolbar: [
                { name: 'document', items: ['Source'] },
                { name: 'clipboard', items: ['Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo'] },
                { name: 'editing', items: ['Find', 'Replace', '-', 'SelectAll'] },
                { name: 'basicstyles', items: ['Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'CopyFormatting', 'RemoveFormat'] },
                { name: 'paragraph', items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote'] },
                { name: 'links', items: ['Link', 'Unlink'] },
                { name: 'insert', items: ['Image', 'Table', 'HorizontalRule', 'SpecialChar'] },
                { name: 'styles', items: ['Format', 'Font', 'FontSize'] },
                { name: 'colors', items: ['TextColor', 'BGColor'] },
                { name: 'tools', items: ['Maximize'] }
            ]
        });
    }
    
    // ========== FILE UPLOAD PREVIEW ==========
    const gambarInput = document.getElementById('gambar');
    const previewImage = document.getElementById('previewImage');
    const previewContainer = document.getElementById('previewContainer');
    const fileUploadArea = document.getElementById('fileUploadArea');
    
    if (gambarInput && previewImage && fileUploadArea) {
        // Klik area upload
        fileUploadArea.addEventListener('click', function(e) {
            if (e.target !== gambarInput) {
                gambarInput.click();
            }
        });
        
        // Preview gambar
        gambarInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    if (previewContainer) previewContainer.style.display = 'block';
                    if (previewImage) previewImage.style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else {
                if (previewContainer) previewContainer.style.display = 'none';
            }
        });
    }
    
    // ========== FORM LOADING STATE ==========
    const submitBtn = document.getElementById('btnSubmit');
    
    if (sejarahForm && submitBtn) {
        sejarahForm.addEventListener('submit', function() {
            // Update CKEditor content ke textarea sebelum submit
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
// 21. SARANA PAGE - MODAL DELETE & AUTO CLOSE
// ==============================================
function initSaranaPage() {
    // Cek apakah halaman sarana
    const saranaTable = document.querySelector('.sarana-page .table');
    if (!saranaTable) return;
    
    // ========== MODAL DELETE ==========
    // Override confirmDelete untuk sarana
    window.confirmDelete = function(id, name, type, hasGambar = false) {
        const deleteItemName = document.getElementById('deleteItemName');
        const itemType = document.getElementById('itemType');
        const fileWarningContainer = document.getElementById('fileWarningContainer');
        const fileWarningText = document.getElementById('fileWarningText');
        const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
        
        if (deleteItemName) deleteItemName.innerText = '"' + name + '"';
        if (itemType) itemType.innerText = type === 'sarana' ? 'sarana' : type;
        
        // Tampilkan warning gambar jika ada
        if (fileWarningContainer && fileWarningText) {
            if (hasGambar) {
                fileWarningText.innerText = 'Sarana ini memiliki GAMBAR yang akan ikut terhapus.';
                fileWarningContainer.style.display = 'block';
            } else {
                fileWarningContainer.style.display = 'none';
            }
        }
        
        if (confirmDeleteBtn) confirmDeleteBtn.href = 'index.php?delete=' + id;
        
        const modal = document.getElementById('deleteModal');
        if (modal) modal.style.display = 'flex';
    };
    
    window.closeModal = function() {
        const modal = document.getElementById('deleteModal');
        if (modal) modal.style.display = 'none';
    };
    
    // Klik di luar modal
    window.onclick = function(event) {
        const modal = document.getElementById('deleteModal');
        if (event.target == modal && modal) {
            window.closeModal();
        }
    };
}
// ==============================================
// 22. SAMBUTAN PAGE - CKEDITOR & PREVIEW
// ==============================================
function initSambutanPage() {
    // Cek apakah halaman sambutan
    const sambutanForm = document.getElementById('sambutanForm');
    const editorElement = document.getElementById('editor');
    
    if (!sambutanForm && !editorElement) return;
    
    // Inisialisasi CKEditor
    if (typeof CKEDITOR !== 'undefined' && editorElement) {
        CKEDITOR.replace('editor', { 
            height: 300,
            toolbar: [
                { name: 'document', items: ['Source'] },
                { name: 'clipboard', items: ['Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo'] },
                { name: 'editing', items: ['Find', 'Replace', '-', 'SelectAll'] },
                { name: 'basicstyles', items: ['Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'CopyFormatting', 'RemoveFormat'] },
                { name: 'paragraph', items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote'] },
                { name: 'links', items: ['Link', 'Unlink'] },
                { name: 'insert', items: ['Image', 'Table', 'HorizontalRule', 'SpecialChar'] },
                { name: 'styles', items: ['Format', 'Font', 'FontSize'] },
                { name: 'colors', items: ['TextColor', 'BGColor'] },
                { name: 'tools', items: ['Maximize'] }
            ]
        });
    }
    
    // ========== FILE UPLOAD PREVIEW ==========
    const fotoInput = document.getElementById('foto');
    const previewImage = document.getElementById('previewImage');
    const previewContainer = document.getElementById('previewContainer');
    const fileUploadArea = document.getElementById('fileUploadArea');
    
    if (fotoInput && previewImage && fileUploadArea) {
        // Klik area upload
        fileUploadArea.addEventListener('click', function(e) {
            if (e.target !== fotoInput) {
                fotoInput.click();
            }
        });
        
        // Preview gambar
        fotoInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    if (previewContainer) previewContainer.style.display = 'block';
                    if (previewImage) previewImage.style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else {
                if (previewContainer) previewContainer.style.display = 'none';
            }
        });
    }
    
    // ========== FORM LOADING STATE ==========
    const submitBtn = document.getElementById('btnSubmit');
    
    if (sambutanForm && submitBtn) {
        sambutanForm.addEventListener('submit', function() {
            // Update CKEditor content ke textarea sebelum submit
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
// 23. PRESTASI PAGE - MODAL DELETE & AUTO CLOSE
// ==============================================
function initPrestasiPage() {
    // Cek apakah halaman prestasi
    const prestasiTable = document.querySelector('.prestasi-page .table');
    if (!prestasiTable) return;
    
    // ========== MODAL DELETE ==========
    // Override confirmDelete untuk prestasi
    window.confirmDelete = function(id, name, type, hasGambar = false) {
        const deleteItemName = document.getElementById('deleteItemName');
        const itemType = document.getElementById('itemType');
        const fileWarningContainer = document.getElementById('fileWarningContainer');
        const fileWarningText = document.getElementById('fileWarningText');
        const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
        
        if (deleteItemName) deleteItemName.innerText = '"' + name + '"';
        if (itemType) itemType.innerText = type === 'prestasi' ? 'prestasi' : type;
        
        // Tampilkan warning gambar jika ada
        if (fileWarningContainer && fileWarningText) {
            if (hasGambar) {
                fileWarningText.innerText = 'Prestasi ini memiliki GAMBAR yang akan ikut terhapus.';
                fileWarningContainer.style.display = 'block';
            } else {
                fileWarningContainer.style.display = 'none';
            }
        }
        
        if (confirmDeleteBtn) confirmDeleteBtn.href = 'index.php?delete=' + id;
        
        const modal = document.getElementById('deleteModal');
        if (modal) modal.style.display = 'flex';
    };
    
    window.closeModal = function() {
        const modal = document.getElementById('deleteModal');
        if (modal) modal.style.display = 'none';
    };
    
    // Klik di luar modal
    window.onclick = function(event) {
        const modal = document.getElementById('deleteModal');
        if (event.target == modal && modal) {
            window.closeModal();
        }
    };
}

// ==============================================
// 24. PPDB PAGE - DYNAMIC FORM & PREVIEW
// ==============================================
function initPpdbPage() {
    // Cek apakah halaman ppdb
    const ppdbForm = document.getElementById('ppdbForm');
    if (!ppdbForm) return;
    
    // Hitung jumlah syarat yang sudah ada dari DOM
    let syaratCounter = document.querySelectorAll('#syaratContainer .syarat-item').length;
    // Jika belum ada syarat, set default 2
    if (syaratCounter === 0) syaratCounter = 2;
    
    // Function to create new syarat item
    function createSyaratItem(index) {
        return `
            <div class="syarat-item" data-index="${index}">
                <div class="syarat-header">
                    <h4><i class="fas fa-edit"></i> Syarat ${index + 1}</h4>
                    <button type="button" class="btn-remove-syarat" data-index="${index}">
                        <i class="fas fa-trash-alt"></i> Hapus
                    </button>
                </div>
                <div class="syarat-fields">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Icon (FontAwesome)</label>
                            <input type="text" name="syarat_icon[]" class="form-control" value="fa-check" placeholder="fa-edit">
                            <small>Contoh: fa-edit, fa-folder-open, fa-file-alt</small>
                        </div>
                        <div class="form-group">
                            <label>Judul</label>
                            <input type="text" name="syarat_title[]" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Deskripsi</label>
                        <textarea name="syarat_desc[]" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Options (Online/Offline)</label>
                            <select name="syarat_options[${index}][]" class="form-control" multiple>
                                <option value="online">Online</option>
                                <option value="offline">Offline</option>
                            </select>
                            <small>Ctrl+klik untuk pilih lebih dari satu</small>
                        </div>
                        <div class="form-group">
                            <label>
                                <input type="checkbox" name="syarat_required[${index}]" value="1" checked> Wajib
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Catatan (opsional)</label>
                        <input type="text" name="syarat_note[]" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Daftar Berkas</label>
                        <div class="files-container" data-index="${index}"></div>
                        <button type="button" class="btn-add-file" data-index="${index}">
                            <i class="fas fa-plus"></i> Tambah Berkas
                        </button>
                    </div>
                </div>
            </div>
        `;
    }
    
    // Function to add file item
    function addFileItem(container, fileIndex, fileIcon = 'fa-file-alt', fileName = '') {
        const fileItem = document.createElement('div');
        fileItem.className = 'file-item';
        fileItem.style.display = 'flex';
        fileItem.style.gap = '10px';
        fileItem.style.marginBottom = '10px';
        fileItem.style.alignItems = 'center';
        fileItem.innerHTML = `
            <input type="text" name="syarat_files[${fileIndex}][icon][]" class="form-control file-icon" value="${fileIcon}" placeholder="Icon" style="width: 100px;">
            <input type="text" name="syarat_files[${fileIndex}][name][]" class="form-control file-name" value="${fileName}" placeholder="Nama berkas" style="flex: 1;">
            <button type="button" class="btn-remove-file" style="background: #ef4444; color: white; border: none; border-radius: 5px; width: 32px; height: 32px; cursor: pointer;">
                <i class="fas fa-times"></i>
            </button>
        `;
        container.appendChild(fileItem);
        
        fileItem.querySelector('.btn-remove-file').addEventListener('click', function() {
            fileItem.remove();
        });
    }
    
    // Add Syarat Button
    const addSyaratBtn = document.getElementById('addSyaratBtn');
    if (addSyaratBtn) {
        addSyaratBtn.addEventListener('click', function() {
            const container = document.getElementById('syaratContainer');
            const newItem = createSyaratItem(syaratCounter);
            container.insertAdjacentHTML('beforeend', newItem);
            
            // Setup remove button for new item
            const newSyarat = container.lastElementChild;
            const removeBtn = newSyarat.querySelector('.btn-remove-syarat');
            if (removeBtn) {
                removeBtn.addEventListener('click', function() {
                    newSyarat.remove();
                });
            }
            
            // Setup add file button for new item
            const addFileBtn = newSyarat.querySelector('.btn-add-file');
            if (addFileBtn) {
                const fileIndex = syaratCounter;
                addFileBtn.addEventListener('click', function() {
                    const filesContainer = this.parentElement.querySelector('.files-container');
                    addFileItem(filesContainer, fileIndex);
                });
            }
            
            syaratCounter++;
        });
    }
    
    // Setup existing remove syarat buttons
    document.querySelectorAll('.btn-remove-syarat').forEach(btn => {
        btn.addEventListener('click', function() {
            this.closest('.syarat-item').remove();
        });
    });
    
    // Setup existing add file buttons
    document.querySelectorAll('.btn-add-file').forEach(btn => {
        btn.addEventListener('click', function() {
            const fileIndex = this.getAttribute('data-index');
            const filesContainer = this.parentElement.querySelector('.files-container');
            if (filesContainer) {
                addFileItem(filesContainer, fileIndex);
            }
        });
    });
    
    // Setup existing remove file buttons
    document.querySelectorAll('.btn-remove-file').forEach(btn => {
        btn.addEventListener('click', function() {
            this.closest('.file-item').remove();
        });
    });
    
    // Preview QR Code
    const qrInput = document.getElementById('qr_code');
    const qrUploadArea = document.getElementById('qrUploadArea');
    const previewQr = document.getElementById('previewQr');
    const previewQrContainer = document.getElementById('previewQrContainer');
    
    if (qrInput && qrUploadArea) {
        qrUploadArea.addEventListener('click', function(e) {
            if (e.target !== qrInput) {
                qrInput.click();
            }
        });
        
        qrInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    if (previewQr) {
                        previewQr.src = e.target.result;
                        previewQr.style.display = 'block';
                    }
                    if (previewQrContainer) {
                        previewQrContainer.style.display = 'block';
                    }
                };
                reader.readAsDataURL(file);
            } else {
                if (previewQrContainer) previewQrContainer.style.display = 'none';
            }
        });
    }
    
    // Form loading state
    const submitBtn = document.getElementById('btnSubmit');
    if (ppdbForm && submitBtn) {
        ppdbForm.addEventListener('submit', function() {
            submitBtn.classList.add('loading');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
            submitBtn.disabled = true;
        });
    }
}
// ==============================================
// 25. PENGUMUMAN PAGE - MODAL DELETE & ALERTS
// ==============================================
function initPengumumanPage() {
    // Cek apakah halaman pengumuman
    const pengumumanTable = document.querySelector('.pengumuman-page .table');
    if (!pengumumanTable) return;
    
    // Fungsi konfirmasi hapus untuk pengumuman
    window.confirmDeletePengumuman = function(id, name, hasGambar = false, hasLampiran = false) {
        const deleteItemName = document.getElementById('deleteItemName');
        const itemType = document.getElementById('itemType');
        const fileWarningContainer = document.getElementById('fileWarningContainer');
        const fileWarningText = document.getElementById('fileWarningText');
        const fileList = document.getElementById('fileList');
        const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
        
        if (deleteItemName) deleteItemName.innerText = '"' + name + '"';
        if (itemType) itemType.innerText = 'pengumuman';
        
        // Tampilkan warning file jika ada
        let fileWarningMsg = '';
        let fileListHtml = '';
        
        if (fileWarningContainer && fileWarningText && fileList) {
            if (hasGambar && hasLampiran) {
                fileWarningMsg = 'Pengumuman ini memiliki GAMBAR dan LAMPIRAN yang akan ikut terhapus.';
                fileListHtml = '<div><i class="fas fa-paperclip"></i> Gambar</div><div><i class="fas fa-paperclip"></i> File Lampiran</div>';
                fileWarningContainer.style.display = 'block';
            } else if (hasGambar) {
                fileWarningMsg = 'Pengumuman ini memiliki GAMBAR yang akan ikut terhapus.';
                fileListHtml = '<div><i class="fas fa-paperclip"></i> Gambar</div>';
                fileWarningContainer.style.display = 'block';
            } else if (hasLampiran) {
                fileWarningMsg = 'Pengumuman ini memiliki LAMPIRAN yang akan ikut terhapus.';
                fileListHtml = '<div><i class="fas fa-paperclip"></i> File Lampiran</div>';
                fileWarningContainer.style.display = 'block';
            } else {
                fileWarningContainer.style.display = 'none';
            }
            
            fileWarningText.innerText = fileWarningMsg;
            fileList.innerHTML = fileListHtml;
        }
        
        if (confirmDeleteBtn) confirmDeleteBtn.href = 'index.php?delete=' + id;
        
        const modal = document.getElementById('deleteModal');
        if (modal) modal.style.display = 'flex';
    };
    
    window.closeModal = function() {
        const modal = document.getElementById('deleteModal');
        if (modal) modal.style.display = 'none';
    };
    
    // Klik di luar modal
    window.onclick = function(event) {
        const modal = document.getElementById('deleteModal');
        if (event.target == modal && modal) {
            window.closeModal();
        }
    };
}

// ==============================================
// 26. PEMBIASAAN PAGE - MODAL DELETE & ALERTS
// ==============================================
function initPembiasaanPage() {
    // Cek apakah halaman pembiasaan
    const pembiasaanTable = document.querySelector('.pembiasaan-page .table');
    if (!pembiasaanTable) return;
    
    // Fungsi konfirmasi hapus untuk pembiasaan
    window.confirmDeletePembiasaan = function(id, name) {
        const deleteItemName = document.getElementById('deleteItemName');
        const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
        
        if (deleteItemName) deleteItemName.innerText = '"' + name + '"';
        if (confirmDeleteBtn) confirmDeleteBtn.href = 'index.php?delete=' + id;
        
        const modal = document.getElementById('deleteModal');
        if (modal) modal.style.display = 'flex';
    };
    
    window.closeModal = function() {
        const modal = document.getElementById('deleteModal');
        if (modal) modal.style.display = 'none';
    };
    
    // Klik di luar modal
    window.onclick = function(event) {
        const modal = document.getElementById('deleteModal');
        if (event.target == modal && modal) {
            window.closeModal();
        }
    };
}

// ==============================================
// 27. PEMBIASAAN DETAIL PAGE - MODAL DELETE
// ==============================================
function initPembiasaanDetailPage() {
    // Cek apakah halaman detail pembiasaan
    const detailPage = document.querySelector('.pembiasaan-page .card');
    if (!detailPage) return;
    
    // Fungsi konfirmasi hapus untuk detail pembiasaan
    window.confirmDeletePembiasaan = function(id, name) {
        const deleteItemName = document.getElementById('deleteItemName');
        const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
        
        if (deleteItemName) deleteItemName.innerText = '"' + name + '"';
        if (confirmDeleteBtn) confirmDeleteBtn.href = 'index.php?delete=' + id;
        
        const modal = document.getElementById('deleteModal');
        if (modal) modal.style.display = 'flex';
    };
    
    window.closeModal = function() {
        const modal = document.getElementById('deleteModal');
        if (modal) modal.style.display = 'none';
    };
    
    // Klik di luar modal
    window.onclick = function(event) {
        const modal = document.getElementById('deleteModal');
        if (event.target == modal && modal) {
            window.closeModal();
        }
    };
}

// ==============================================
// 28. KONTAK PAGE - AUTO CLOSE ALERTS & FORM
// ==============================================
function initKontakPage() {
    // Cek apakah halaman kontak
    const kontakForm = document.getElementById('kontakForm');
    if (!kontakForm) return;
    
    // Form loading state
    const submitBtn = document.getElementById('btnSubmit');
    if (kontakForm && submitBtn) {
        kontakForm.addEventListener('submit', function() {
            submitBtn.classList.add('loading');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
            submitBtn.disabled = true;
        });
    }
}

// ==============================================
// 29. HERO SLIDER PAGE - PREVIEW & UPLOAD
// ==============================================
function initHeroSliderPage() {
    // Cek apakah halaman hero slider
    const sliderForm = document.getElementById('sliderForm');
    const gambarInput = document.getElementById('gambarInput');
    
    if (!sliderForm) return;
    
    // Preview gambar
    if (gambarInput) {
        const previewContainer = document.getElementById('previewContainer');
        const previewImage = document.getElementById('previewImage');
        const fileInfo = document.getElementById('fileInfo');
        const fileName = document.getElementById('fileName');
        const fileSize = document.getElementById('fileSize');
        const previewFileSize = document.getElementById('previewFileSize');
        const fileUploadArea = document.getElementById('fileUploadArea');
        
        if (fileUploadArea) {
            fileUploadArea.addEventListener('click', function(e) {
                if (e.target !== gambarInput) {
                    gambarInput.click();
                }
            });
            
            fileUploadArea.addEventListener('mouseenter', function() {
                this.style.background = '#e3f2fd';
                this.style.borderColor = '#FFD700';
            });
            
            fileUploadArea.addEventListener('mouseleave', function() {
                this.style.background = '#f0f7ff';
                this.style.borderColor = '#0B3D91';
            });
        }
        
        gambarInput.addEventListener('dragover', function(e) {
            e.preventDefault();
            if (fileUploadArea) {
                fileUploadArea.style.background = '#e3f2fd';
                fileUploadArea.style.borderColor = '#FFD700';
            }
        });
        
        gambarInput.addEventListener('dragleave', function(e) {
            e.preventDefault();
            if (fileUploadArea) {
                fileUploadArea.style.background = '#f0f7ff';
                fileUploadArea.style.borderColor = '#0B3D91';
            }
        });
        
        gambarInput.addEventListener('drop', function(e) {
            e.preventDefault();
            if (fileUploadArea) {
                fileUploadArea.style.background = '#f0f7ff';
                fileUploadArea.style.borderColor = '#0B3D91';
            }
        });
        
        gambarInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (!file) {
                if (previewContainer) previewContainer.style.display = 'none';
                if (fileInfo) fileInfo.style.display = 'none';
                return;
            }
            
            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
            if (!allowedTypes.includes(file.type)) {
                alert('❌ Hanya file JPG, PNG, GIF, WEBP yang diperbolehkan!');
                this.value = '';
                if (previewContainer) previewContainer.style.display = 'none';
                if (fileInfo) fileInfo.style.display = 'none';
                return;
            }
            
            const maxSize = 10 * 1024 * 1024;
            if (file.size > maxSize) {
                const fileSizeMB = (file.size / (1024 * 1024)).toFixed(2);
                alert(`❌ Ukuran file maksimal 10MB! (Ukuran file Anda: ${fileSizeMB}MB)`);
                this.value = '';
                if (previewContainer) previewContainer.style.display = 'none';
                if (fileInfo) fileInfo.style.display = 'none';
                return;
            }
            
            if (fileInfo && fileName && fileSize) {
                const fileSizeKB = (file.size / 1024).toFixed(2);
                const fileSizeDisplay = fileSizeKB > 1024 ? 
                    (file.size / (1024 * 1024)).toFixed(2) + ' MB' : 
                    fileSizeKB + ' KB';
                
                fileName.textContent = file.name;
                fileSize.textContent = 'Ukuran: ' + fileSizeDisplay;
                fileInfo.style.display = 'block';
            }
            
            if (previewContainer && previewImage) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    if (previewFileSize) {
                        const fileSizeKB = (file.size / 1024).toFixed(2);
                        const fileSizeDisplay = fileSizeKB > 1024 ? 
                            (file.size / (1024 * 1024)).toFixed(2) + ' MB' : 
                            fileSizeKB + ' KB';
                        previewFileSize.textContent = fileSizeDisplay;
                    }
                    previewContainer.style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
            
            if (fileUploadArea) {
                fileUploadArea.style.background = '#e8f5e9';
                fileUploadArea.style.borderColor = '#28a745';
                const p = fileUploadArea.querySelector('p');
                if (p) {
                    p.innerHTML = '<i class="fas fa-check-circle"></i> File siap: ' + file.name;
                }
            }
        });
    }
    
    // Form loading state
    const submitBtn = document.getElementById('btnSubmit');
    if (sliderForm && submitBtn) {
        sliderForm.addEventListener('submit', function() {
            submitBtn.classList.add('loading');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
            submitBtn.disabled = true;
        });
    }
}
// ==============================================
// 30. GURU STAFF PAGE - MODAL DELETE
// ==============================================
function initGuruStaffPage() {
    // Cek apakah halaman guru staff
    const guruTable = document.querySelector('.guru-page .table');
    if (!guruTable) return;
    
    // Fungsi konfirmasi hapus untuk guru staff
    window.confirmDeleteGuru = function(id, name, hasFoto = false) {
        const deleteItemName = document.getElementById('deleteItemName');
        const itemType = document.getElementById('itemType');
        const fileWarning = document.getElementById('fileWarning');
        const fileWarningText = document.getElementById('fileWarningText');
        const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
        
        if (deleteItemName) deleteItemName.innerText = '"' + name + '"';
        if (itemType) itemType.innerText = 'guru/staff';
        
        if (fileWarning && fileWarningText) {
            if (hasFoto) {
                fileWarningText.innerText = 'Data ini memiliki foto yang akan ikut terhapus.';
                fileWarning.style.display = 'block';
            } else {
                fileWarning.style.display = 'none';
            }
        }
        
        if (confirmDeleteBtn) confirmDeleteBtn.href = 'index.php?delete=' + id;
        
        const modal = document.getElementById('deleteModal');
        if (modal) modal.style.display = 'flex';
    };
    
    window.closeModal = function() {
        const modal = document.getElementById('deleteModal');
        if (modal) modal.style.display = 'none';
    };
    
    window.onclick = function(event) {
        const modal = document.getElementById('deleteModal');
        if (event.target == modal && modal) {
            window.closeModal();
        }
    };
}

// ==============================================
// 31. GALERI VIDEO PAGE - MODAL DELETE & PREVIEW
// ==============================================
function initGaleriVideoPage() {
    // Cek apakah halaman galeri video
    const videoTable = document.querySelector('.galeri-video-page .table');
    if (!videoTable) return;
    
    // Fungsi konfirmasi hapus untuk video
    window.confirmDeleteVideo = function(id, name, hasThumbnail = false) {
        const deleteItemName = document.getElementById('deleteItemName');
        const itemType = document.getElementById('itemType');
        const fileWarningContainer = document.getElementById('fileWarningContainer');
        const fileWarningText = document.getElementById('fileWarningText');
        const fileList = document.getElementById('fileList');
        const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
        
        if (deleteItemName) deleteItemName.innerText = '"' + name + '"';
        if (itemType) itemType.innerText = 'video';
        
        if (fileWarningContainer && fileWarningText && fileList) {
            if (hasThumbnail) {
                fileWarningText.innerText = 'Video ini memiliki file thumbnail yang akan ikut terhapus.';
                fileList.innerHTML = '<div><i class="fas fa-image"></i> Thumbnail</div>';
                fileWarningContainer.style.display = 'block';
            } else {
                fileWarningContainer.style.display = 'none';
            }
        }
        
        if (confirmDeleteBtn) confirmDeleteBtn.href = 'index.php?delete=' + id;
        
        const modal = document.getElementById('deleteModal');
        if (modal) modal.style.display = 'flex';
    };
    
    window.closeModal = function() {
        const modal = document.getElementById('deleteModal');
        if (modal) modal.style.display = 'none';
    };
    
    window.onclick = function(event) {
        const modal = document.getElementById('deleteModal');
        if (event.target == modal && modal) {
            window.closeModal();
        }
    };
    
    // Preview thumbnail untuk edit/tambah
    const videoForm = document.getElementById('videoForm');
    const thumbnailInput = document.getElementById('thumbnail');
    const previewImage = document.getElementById('previewImage');
    const previewContainer = document.getElementById('previewContainer');
    const fileUploadArea = document.getElementById('fileUploadArea');
    
    if (videoForm && thumbnailInput) {
        if (fileUploadArea) {
            fileUploadArea.addEventListener('click', function(e) {
                if (e.target !== thumbnailInput) {
                    thumbnailInput.click();
                }
            });
        }
        
        thumbnailInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    if (previewContainer) previewContainer.style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else {
                if (previewContainer) previewContainer.style.display = 'none';
            }
        });
    }
    
    // Form loading state
    const submitBtn = document.getElementById('btnSubmit');
    if (videoForm && submitBtn) {
        videoForm.addEventListener('submit', function() {
            submitBtn.classList.add('loading');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
            submitBtn.disabled = true;
        });
    }
}

// ==============================================
// 32. GALERI VIDEO DETAIL PAGE - MODAL DELETE
// ==============================================
function initGaleriVideoDetailPage() {
    // Cek apakah halaman detail video
    const detailPage = document.querySelector('.galeri-video-page .card');
    if (!detailPage) return;
    
    window.confirmDeleteVideo = function(id, name, hasThumbnail = false) {
        const deleteItemName = document.getElementById('deleteItemName');
        const fileWarningContainer = document.getElementById('fileWarningContainer');
        const fileWarningText = document.getElementById('fileWarningText');
        const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
        
        if (deleteItemName) deleteItemName.innerText = '"' + name + '"';
        
        if (fileWarningContainer && fileWarningText) {
            if (hasThumbnail) {
                fileWarningText.innerText = 'Video ini memiliki thumbnail yang akan ikut terhapus.';
                fileWarningContainer.style.display = 'block';
            } else {
                fileWarningContainer.style.display = 'none';
            }
        }
        
        if (confirmDeleteBtn) confirmDeleteBtn.href = 'index.php?delete=' + id;
        
        const modal = document.getElementById('deleteModal');
        if (modal) modal.style.display = 'flex';
    };
    
    window.closeModal = function() {
        const modal = document.getElementById('deleteModal');
        if (modal) modal.style.display = 'none';
    };
    
    window.onclick = function(event) {
        const modal = document.getElementById('deleteModal');
        if (event.target == modal && modal) {
            window.closeModal();
        }
    };
}

// ==============================================
// 33. GALERI FOTO PAGE - MODAL DELETE & PREVIEW
// ==============================================
function initGaleriFotoPage() {
    // Cek apakah halaman galeri foto
    const fotoTable = document.querySelector('.galeri-foto-page .table');
    if (!fotoTable) return;
    
    // Fungsi konfirmasi hapus untuk foto
    window.confirmDeleteFoto = function(id, name, hasFile = false) {
        const deleteItemName = document.getElementById('deleteItemName');
        const itemType = document.getElementById('itemType');
        const fileWarningContainer = document.getElementById('fileWarningContainer');
        const fileWarningText = document.getElementById('fileWarningText');
        const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
        
        if (deleteItemName) deleteItemName.innerText = '"' + name + '"';
        if (itemType) itemType.innerText = 'foto';
        
        if (fileWarningContainer && fileWarningText) {
            if (hasFile) {
                fileWarningText.innerText = 'Foto ini memiliki file gambar yang akan ikut terhapus.';
                fileWarningContainer.style.display = 'block';
            } else {
                fileWarningContainer.style.display = 'none';
            }
        }
        
        if (confirmDeleteBtn) confirmDeleteBtn.href = 'index.php?delete=' + id;
        
        const modal = document.getElementById('deleteModal');
        if (modal) modal.style.display = 'flex';
    };
    
    window.closeModal = function() {
        const modal = document.getElementById('deleteModal');
        if (modal) modal.style.display = 'none';
    };
    
    window.onclick = function(event) {
        const modal = document.getElementById('deleteModal');
        if (event.target == modal && modal) {
            window.closeModal();
        }
    };
    
    // Preview untuk edit/tambah
    const fotoForm = document.getElementById('fotoForm');
    const fileInput = document.getElementById('file_foto');
    const previewImage = document.getElementById('previewImage');
    const previewContainer = document.getElementById('previewContainer');
    const fileUploadArea = document.getElementById('fileUploadArea');
    
    if (fotoForm && fileInput) {
        if (fileUploadArea) {
            fileUploadArea.addEventListener('click', function(e) {
                if (e.target !== fileInput) {
                    fileInput.click();
                }
            });
        }
        
        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    if (previewContainer) previewContainer.style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else {
                if (previewContainer) previewContainer.style.display = 'none';
            }
        });
    }
    
    // Form loading state
    const submitBtn = document.getElementById('btnSubmit');
    if (fotoForm && submitBtn) {
        fotoForm.addEventListener('submit', function() {
            submitBtn.classList.add('loading');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
            submitBtn.disabled = true;
        });
    }
}

// ==============================================
// 34. GALERI FOTO DETAIL PAGE - MODAL DELETE
// ==============================================
function initGaleriFotoDetailPage() {
    const detailPage = document.querySelector('.galeri-foto-page .card');
    if (!detailPage) return;
    
    window.confirmDeleteFoto = function(id, name, hasFile = false) {
        const deleteItemName = document.getElementById('deleteItemName');
        const fileWarningContainer = document.getElementById('fileWarningContainer');
        const fileWarningText = document.getElementById('fileWarningText');
        const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
        
        if (deleteItemName) deleteItemName.innerText = '"' + name + '"';
        
        if (fileWarningContainer && fileWarningText) {
            if (hasFile) {
                fileWarningText.innerText = 'File gambar akan ikut terhapus!';
                fileWarningContainer.style.display = 'block';
            } else {
                fileWarningContainer.style.display = 'none';
            }
        }
        
        if (confirmDeleteBtn) confirmDeleteBtn.href = 'index.php?delete=' + id;
        
        const modal = document.getElementById('deleteModal');
        if (modal) modal.style.display = 'flex';
    };
    
    window.closeModal = function() {
        const modal = document.getElementById('deleteModal');
        if (modal) modal.style.display = 'none';
    };
    
    window.onclick = function(event) {
        const modal = document.getElementById('deleteModal');
        if (event.target == modal && modal) {
            window.closeModal();
        }
    };
}

// ==============================================
// 35. EKSTRAKURIKULER PAGE - MODAL DELETE
// ==============================================
function initEkstrakurikulerPage() {
    const ekstraTable = document.querySelector('.ekstra-page .table');
    if (!ekstraTable) return;
    
    window.confirmDeleteEkstra = function(id, name, hasGambar = false) {
        const deleteItemName = document.getElementById('deleteItemName');
        const fileWarningContainer = document.getElementById('fileWarningContainer');
        const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
        
        if (deleteItemName) deleteItemName.innerText = '"' + name + '"';
        
        if (fileWarningContainer) {
            if (hasGambar) {
                fileWarningContainer.style.display = 'block';
            } else {
                fileWarningContainer.style.display = 'none';
            }
        }
        
        if (confirmDeleteBtn) confirmDeleteBtn.href = 'index.php?delete=' + id;
        
        const modal = document.getElementById('deleteModal');
        if (modal) modal.style.display = 'flex';
    };
    
    window.closeModal = function() {
        const modal = document.getElementById('deleteModal');
        if (modal) modal.style.display = 'none';
    };
    
    window.onclick = function(event) {
        const modal = document.getElementById('deleteModal');
        if (event.target == modal && modal) {
            window.closeModal();
        }
    };
}

// ==============================================
// 36. EKSTRAKURIKULER DETAIL PAGE - MODAL DELETE
// ==============================================
function initEkstrakurikulerDetailPage() {
    const detailPage = document.querySelector('.ekstra-page .detail-card');
    if (!detailPage) return;
    
    window.confirmDeleteEkstra = function(id, name) {
        const deleteItemName = document.getElementById('deleteItemName');
        const itemType = document.getElementById('itemType');
        const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
        
        if (deleteItemName) deleteItemName.innerText = '"' + name + '"';
        if (itemType) itemType.innerText = 'ekstrakurikuler';
        if (confirmDeleteBtn) confirmDeleteBtn.href = 'index.php?delete=' + id;
        
        const modal = document.getElementById('deleteModal');
        if (modal) modal.style.display = 'flex';
    };
    
    window.closeModal = function() {
        const modal = document.getElementById('deleteModal');
        if (modal) modal.style.display = 'none';
    };
    
    window.onclick = function(event) {
        const modal = document.getElementById('deleteModal');
        if (event.target == modal && modal) {
            window.closeModal();
        }
    };
}

// ==============================================
// 37. AGENDA PAGE - MODAL DELETE
// ==============================================
function initAgendaPage() {
    const agendaTable = document.querySelector('.agenda-page .table');
    if (!agendaTable) return;
    
    window.confirmDeleteAgenda = function(id, name) {
        const deleteItemName = document.getElementById('deleteItemName');
        const itemType = document.getElementById('itemType');
        const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
        
        if (deleteItemName) deleteItemName.innerText = '"' + name + '"';
        if (itemType) itemType.innerText = 'agenda';
        if (confirmDeleteBtn) confirmDeleteBtn.href = 'index.php?delete=' + id;
        
        const modal = document.getElementById('deleteModal');
        if (modal) modal.style.display = 'flex';
    };
    
    window.closeModal = function() {
        const modal = document.getElementById('deleteModal');
        if (modal) modal.style.display = 'none';
    };
    
    window.onclick = function(event) {
        const modal = document.getElementById('deleteModal');
        if (event.target == modal && modal) {
            window.closeModal();
        }
    };
}