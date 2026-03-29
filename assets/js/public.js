// ==============================================
// PUBLIC.JS - MI Muhammadiyah Bodaskarangjati
// Semua JavaScript dari file PHP dipindahkan ke sini
// ==============================================

document.addEventListener('DOMContentLoaded', function() {
    
    // ==============================================
    // 1. HERO SLIDER (dari index.php)
    // ==============================================
    const slides = document.querySelectorAll('.slide');
    const prevBtn = document.querySelector('.slider-prev');
    const nextBtn = document.querySelector('.slider-next');
    const dotsContainer = document.querySelector('.slider-dots');
    let currentSlide = 0;
    let slideInterval;
    
    if(slides.length > 1 && dotsContainer) {
        // Buat dots
        slides.forEach((_, index) => {
            const dot = document.createElement('span');
            dot.classList.add('dot');
            if(index === 0) dot.classList.add('active');
            dot.addEventListener('click', () => goToSlide(index));
            dotsContainer.appendChild(dot);
        });
        
        const dots = document.querySelectorAll('.dot');
        
        function goToSlide(n) {
            slides[currentSlide].classList.remove('active');
            if(dots[currentSlide]) dots[currentSlide].classList.remove('active');
            currentSlide = (n + slides.length) % slides.length;
            slides[currentSlide].classList.add('active');
            if(dots[currentSlide]) dots[currentSlide].classList.add('active');
        }
        
        function nextSlide() {
            goToSlide(currentSlide + 1);
        }
        
        function prevSlide() {
            goToSlide(currentSlide - 1);
        }
        
        function startAutoSlide() {
            if(slideInterval) clearInterval(slideInterval);
            slideInterval = setInterval(nextSlide, 5000);
        }
        
        if(prevBtn) prevBtn.addEventListener('click', function() { prevSlide(); startAutoSlide(); });
        if(nextBtn) nextBtn.addEventListener('click', function() { nextSlide(); startAutoSlide(); });
        
        startAutoSlide();
        
        // Pause on hover
        const sliderContainer = document.querySelector('.hero-slider');
        if(sliderContainer) {
            sliderContainer.addEventListener('mouseenter', () => clearInterval(slideInterval));
            sliderContainer.addEventListener('mouseleave', startAutoSlide);
        }
    }
    
    // ==============================================
    // 2. SCROLL TO TOP (dari index.php)
    // ==============================================
    const scrollTop = document.querySelector('.scroll-top');
    if(scrollTop) {
        window.addEventListener('scroll', function() {
            if(window.pageYOffset > 300) {
                scrollTop.classList.add('show');
            } else {
                scrollTop.classList.remove('show');
            }
        });
        
        scrollTop.addEventListener('click', function(e) {
            e.preventDefault();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }
    
    // ==============================================
    // 3. HAMBURGER MENU & DROPDOWN MOBILE (dari footer)
    // ==============================================
    
    // ==================== HAMBURGER MENU ====================
    const hamburger = document.getElementById('hamburger');
    const navMenu = document.getElementById('navMenu');
    
    if (hamburger && navMenu) {
        // Buat overlay
        let overlay = document.querySelector('.menu-overlay');
        if (!overlay) {
            overlay = document.createElement('div');
            overlay.className = 'menu-overlay';
            document.body.appendChild(overlay);
        }
        
        // Fungsi untuk toggle menu utama
        function toggleMainMenu() {
            hamburger.classList.toggle('active');
            navMenu.classList.toggle('active');
            overlay.classList.toggle('active');
            
            if (navMenu.classList.contains('active')) {
                document.body.style.overflow = 'hidden';
            } else {
                document.body.style.overflow = '';
                // Tutup semua dropdown saat menu utama ditutup
                var allContents = document.querySelectorAll('.dropdown-content');
                for (var i = 0; i < allContents.length; i++) {
                    allContents[i].classList.remove('active');
                }
                var allIcons = document.querySelectorAll('.dropdown-link i');
                for (var j = 0; j < allIcons.length; j++) {
                    allIcons[j].style.transform = '';
                }
            }
        }
        
        // Event click hamburger
        hamburger.onclick = function(e) {
            e.stopPropagation();
            toggleMainMenu();
        };
        
        // Event click overlay
        overlay.onclick = function() {
            if (navMenu.classList.contains('active')) {
                toggleMainMenu();
            }
        };
    }
    
    // ==================== DROPDOWN MOBILE ====================
    var dropdownLinks = document.querySelectorAll('.dropdown-link');
    
    for (var i = 0; i < dropdownLinks.length; i++) {
        (function(index) {
            var link = dropdownLinks[index];
            
            link.onclick = function(e) {
                // Cek apakah di layar mobile (max 992px)
                if (window.innerWidth <= 992) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    // Cari dropdown content
                    var parent = this.parentNode;
                    var content = parent.querySelector('.dropdown-content');
                    var icon = this.querySelector('i');
                    
                    if (content) {
                        // Cek apakah sedang aktif
                        var isActive = content.classList.contains('active');
                        
                        // Tutup semua dropdown lain
                        var allContents = document.querySelectorAll('.dropdown-content');
                        for (var j = 0; j < allContents.length; j++) {
                            if (allContents[j] !== content) {
                                allContents[j].classList.remove('active');
                            }
                        }
                        
                        // Reset semua icon
                        var allIcons = document.querySelectorAll('.dropdown-link i');
                        for (var k = 0; k < allIcons.length; k++) {
                            if (allIcons[k] !== icon) {
                                allIcons[k].style.transform = '';
                            }
                        }
                        
                        // Toggle yang ini
                        if (isActive) {
                            content.classList.remove('active');
                            if (icon) icon.style.transform = '';
                        } else {
                            content.classList.add('active');
                            if (icon) icon.style.transform = 'rotate(180deg)';
                        }
                    }
                }
            };
        })(i);
    }
    
    // ==================== RESIZE WINDOW ====================
    window.onresize = function() {
        if (window.innerWidth > 992) {
            var nav = document.getElementById('navMenu');
            var ham = document.getElementById('hamburger');
            var ov = document.querySelector('.menu-overlay');
            
            if (nav && nav.classList.contains('active')) {
                nav.classList.remove('active');
                if (ham) ham.classList.remove('active');
                if (ov) ov.classList.remove('active');
                document.body.style.overflow = '';
            }
            
            // Reset semua dropdown
            var allContents = document.querySelectorAll('.dropdown-content');
            for (var i = 0; i < allContents.length; i++) {
                allContents[i].classList.remove('active');
            }
            
            var allIcons = document.querySelectorAll('.dropdown-link i');
            for (var j = 0; j < allIcons.length; j++) {
                allIcons[j].style.transform = '';
            }
        }
    };
    
    // ==============================================
    // 4. KONTAK PAGE - TAMBAHKAN CLASS PADA BODY
    // ==============================================
    const currentPath = window.location.pathname;
    if (currentPath.includes('kontak.php')) {
        document.body.classList.add('kontak-page');
    }
    
    // ==============================================
    // 5. PPDB COUNTDOWN TIMER (dari ppdb.php)
    // ==============================================
    const countdownTimer = document.getElementById('countdownTimer');
    
    if (countdownTimer) {
        const targetDateStr = document.body.getAttribute('data-ppdb-target-date');
        
        if (targetDateStr) {
            function updateCountdown() {
                const targetDate = new Date(targetDateStr).getTime();
                const now = new Date().getTime();
                const distance = targetDate - now;
                
                const daysElement = document.getElementById('days');
                const hoursElement = document.getElementById('hours');
                const minutesElement = document.getElementById('minutes');
                const secondsElement = document.getElementById('seconds');
                
                if (distance < 0) {
                    if (daysElement) daysElement.innerText = '00';
                    if (hoursElement) hoursElement.innerText = '00';
                    if (minutesElement) minutesElement.innerText = '00';
                    if (secondsElement) secondsElement.innerText = '00';
                    return;
                }
                
                if (daysElement) {
                    daysElement.innerText = Math.floor(distance / (1000 * 60 * 60 * 24)).toString().padStart(2, '0');
                }
                if (hoursElement) {
                    hoursElement.innerText = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)).toString().padStart(2, '0');
                }
                if (minutesElement) {
                    minutesElement.innerText = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60)).toString().padStart(2, '0');
                }
                if (secondsElement) {
                    secondsElement.innerText = Math.floor((distance % (1000 * 60)) / 1000).toString().padStart(2, '0');
                }
            }
            
            updateCountdown();
            setInterval(updateCountdown, 1000);
        }
    }
    
    // ==============================================
    // 6. SEJARAH PAGE - TIMELINE SCROLL (dari sejarah.php)
    // ==============================================
    const timelineLinks = document.querySelectorAll('.timeline-item');
    const sejarahCards = document.querySelectorAll('.sejarah-card');
    
    if (timelineLinks.length > 0) {
        timelineLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                timelineLinks.forEach(l => l.classList.remove('active'));
                this.classList.add('active');
                
                const targetId = this.getAttribute('href');
                const targetElement = document.querySelector(targetId);
                
                if (targetElement) {
                    targetElement.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    }
    
    if (sejarahCards.length > 0 && timelineLinks.length > 0) {
        window.addEventListener('scroll', function() {
            let currentActive = '';
            
            sejarahCards.forEach(card => {
                const cardTop = card.offsetTop - 100;
                const cardBottom = cardTop + card.offsetHeight;
                
                if (window.scrollY >= cardTop && window.scrollY < cardBottom) {
                    currentActive = card.getAttribute('id');
                }
            });
            
            if (currentActive) {
                timelineLinks.forEach(link => {
                    link.classList.remove('active');
                    if (link.getAttribute('href') === '#' + currentActive) {
                        link.classList.add('active');
                    }
                });
            }
        });
    }
    
    // ==============================================
    // 7. PRESTASI PAGE - FILTER TAHUN (dari prestasi.php)
    // ==============================================
    const filterBtns = document.querySelectorAll('.filter-btn');
    const prestasiCards = document.querySelectorAll('.prestasi-card');
    
    if (filterBtns.length > 0 && prestasiCards.length > 0) {
        filterBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                // Hapus active class dari semua button
                filterBtns.forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                
                const filterValue = this.getAttribute('data-filter');
                
                prestasiCards.forEach(card => {
                    if (filterValue === 'all') {
                        card.style.display = 'block';
                        setTimeout(() => {
                            card.style.opacity = '1';
                            card.style.transform = 'scale(1)';
                        }, 10);
                    } else {
                        const cardTahun = card.getAttribute('data-tahun');
                        if (cardTahun === filterValue) {
                            card.style.display = 'block';
                            setTimeout(() => {
                                card.style.opacity = '1';
                                card.style.transform = 'scale(1)';
                            }, 10);
                        } else {
                            card.style.opacity = '0';
                            card.style.transform = 'scale(0.8)';
                            setTimeout(() => {
                                card.style.display = 'none';
                            }, 300);
                        }
                    }
                });
            });
        });
    }
    
    // ==============================================
    // 8. VIDEO PAGE - MODAL FUNCTION (dari video.php)
    // ==============================================
    
    // Fungsi untuk mengekstrak YouTube ID
    function extractYouTubeId(url) {
        let videoId = '';
        if (url.includes('youtube.com') || url.includes('youtu.be')) {
            const match = url.match(/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/);
            videoId = match ? match[1] : '';
        }
        return videoId;
    }
    
    // Fungsi global untuk membuka modal video
    window.openVideoModal = function(url, title, description) {
        const modal = document.getElementById('videoModal');
        const container = document.getElementById('modalVideoContainer');
        const modalTitle = document.getElementById('modalVideoTitle');
        const modalDesc = document.getElementById('modalVideoDescription');
        
        const videoId = extractYouTubeId(url);
        
        // Set modal content
        if (modalTitle) modalTitle.textContent = title;
        if (modalDesc) modalDesc.textContent = description;
        
        // Set video embed
        if (container) {
            if (videoId) {
                container.innerHTML = `
                    <iframe src="https://www.youtube.com/embed/${videoId}?autoplay=1" 
                            frameborder="0" 
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                            allowfullscreen>
                    </iframe>
                `;
            } else {
                container.innerHTML = `<p class="text-muted">Video tidak dapat diputar</p>`;
            }
        }
        
        if (modal) {
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }
    };
    
    // Fungsi global untuk menutup modal video
    window.closeVideoModal = function() {
        const modal = document.getElementById('videoModal');
        const container = document.getElementById('modalVideoContainer');
        
        if (modal) modal.style.display = 'none';
        if (container) container.innerHTML = '';
        document.body.style.overflow = 'auto';
    };
    
    // Event listener untuk escape key pada video modal
    document.addEventListener('keydown', function(e) {
        const videoModal = document.getElementById('videoModal');
        if (e.key === 'Escape' && videoModal && videoModal.style.display === 'flex') {
            window.closeVideoModal();
        }
    });
    
    // ==============================================
    // 9. FOTO PAGE - LIGHTBOX FUNCTION (dari foto.php)
    // ==============================================
    
    let currentImageIndex = 0;
    let galleryImages = [];
    
    // Kumpulkan semua gambar galeri
    function initGalleryImages() {
        galleryImages = Array.from(document.querySelectorAll('.gallery-image img')).map(img => ({
            src: img.src,
            title: img.alt,
            caption: img.closest('.gallery-card')?.querySelector('.gallery-caption p')?.textContent || ''
        }));
    }
    
    // Fungsi global untuk membuka lightbox
    window.openLightbox = function(img) {
        const lightbox = document.getElementById('lightbox');
        const lightboxImg = document.getElementById('lightbox-img');
        const lightboxCaption = document.getElementById('lightbox-caption');
        
        // Cari index gambar yang diklik
        currentImageIndex = galleryImages.findIndex(item => item.src === img.src);
        
        // Set gambar dan caption
        if (lightboxImg) lightboxImg.src = img.src;
        if (lightboxCaption) {
            lightboxCaption.innerHTML = `
                <strong>${img.alt}</strong>
                ${galleryImages[currentImageIndex]?.caption ? '<br>' + galleryImages[currentImageIndex].caption : ''}
            `;
        }
        
        if (lightbox) {
            lightbox.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }
    };
    
    // Fungsi global untuk menutup lightbox
    window.closeLightbox = function() {
        const lightbox = document.getElementById('lightbox');
        if (lightbox) lightbox.style.display = 'none';
        document.body.style.overflow = 'auto';
    };
    
    // Fungsi global untuk navigasi gambar
    window.changeImage = function(direction) {
        currentImageIndex = (currentImageIndex + direction + galleryImages.length) % galleryImages.length;
        const lightboxImg = document.getElementById('lightbox-img');
        const lightboxCaption = document.getElementById('lightbox-caption');
        
        if (lightboxImg) lightboxImg.src = galleryImages[currentImageIndex].src;
        if (lightboxCaption) {
            lightboxCaption.innerHTML = `
                <strong>${galleryImages[currentImageIndex].title}</strong>
                ${galleryImages[currentImageIndex].caption ? '<br>' + galleryImages[currentImageIndex].caption : ''}
            `;
        }
    };
    
    // Inisialisasi gallery images
    initGalleryImages();
    
    // Keyboard navigation untuk lightbox
    document.addEventListener('keydown', function(e) {
        const lightbox = document.getElementById('lightbox');
        if (lightbox && lightbox.style.display === 'flex') {
            if (e.key === 'ArrowLeft') window.changeImage(-1);
            if (e.key === 'ArrowRight') window.changeImage(1);
            if (e.key === 'Escape') window.closeLightbox();
        }
    });
    
    // ==============================================
    // 10. AOS INITIALIZATION (jika menggunakan AOS)
    // ==============================================
    if (typeof AOS !== 'undefined') {
        AOS.init({
            duration: 800,
            once: true,
            offset: 100
        });
    }
    
});