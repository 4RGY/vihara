/* ===================================================================
 * Mueller 1.0.0 - Main JS
 *
 * ------------------------------------------------------------------- */

(function(html) {

    'use strict';

    const cfg = {
        
        // MailChimp URL
        mailChimpURL : 'https://facebook.us1.list-manage.com/subscribe/post?u=1abf75f6981256963a47d197a&amp;id=37c6d8f4d6' 

    };


   /* preloader
    * -------------------------------------------------- */
    const ssPreloader = function() {

        const siteBody = document.querySelector('body');
        const preloader = document.querySelector('#preloader');
        if (!preloader) return;

        html.classList.add('ss-preload');
        
        window.addEventListener('load', function() {
            html.classList.remove('ss-preload');
            html.classList.add('ss-loaded');

            preloader.addEventListener('transitionend', function afterTransition(e) {
                if (e.target.matches('#preloader'))  {
                    siteBody.classList.add('ss-show');
                    e.target.style.display = 'none';
                    preloader.removeEventListener(e.type, afterTransition);
                }
            });
        });

        // window.addEventListener('beforeunload' , function() {
        //     siteBody.classList.remove('ss-show');
        // });

    }; // end ssPreloader


   /* move header
    * -------------------------------------------------- */
    const ssMoveHeader = function () {

        const hdr = document.querySelector('.s-header');
        const hero = document.querySelector('#intro');
        let triggerHeight;

        if (!(hdr && hero)) return;

        setTimeout(function() {
            triggerHeight = hero.offsetHeight - 170;
        }, 300);

        window.addEventListener('scroll', function () {

            let loc = window.scrollY;

            if (loc > triggerHeight) {
                hdr.classList.add('sticky');
            } else {
                hdr.classList.remove('sticky');
            }

            if (loc > triggerHeight + 20) {
                hdr.classList.add('offset');
            } else {
                hdr.classList.remove('offset');
            }

            if (loc > triggerHeight + 150) {
                hdr.classList.add('scrolling');
            } else {
                hdr.classList.remove('scrolling');
            }

        });

    }; // end ssMoveHeader


   /* mobile menu
    * ---------------------------------------------------- */ 
    const ssMobileMenu = function() {

        const toggleButton = document.querySelector('.s-header__menu-toggle');
        const mainNavWrap = document.querySelector('.s-header__nav');
        const siteBody = document.querySelector('body');

        if (!(toggleButton && mainNavWrap)) return;

        toggleButton.addEventListener('click', function(event) {
            event.preventDefault();
            toggleButton.classList.toggle('is-clicked');
            siteBody.classList.toggle('menu-is-open');
        });

        mainNavWrap.querySelectorAll('.s-header__nav a').forEach(function(link) {

            link.addEventListener("click", function(event) {

                // at 800px and below
                if (window.matchMedia('(max-width: 800px)').matches) {
                    toggleButton.classList.toggle('is-clicked');
                    siteBody.classList.toggle('menu-is-open');
                }
            });
        });

        window.addEventListener('resize', function() {

            // above 800px
            if (window.matchMedia('(min-width: 801px)').matches) {
                if (siteBody.classList.contains('menu-is-open')) siteBody.classList.remove('menu-is-open');
                if (toggleButton.classList.contains('is-clicked')) toggleButton.classList.remove('is-clicked');
            }
        });

    }; // end ssMobileMenu


    /* highlight active menu link on pagescroll
    * ------------------------------------------------------ */
    const ssScrollSpy = function() {

        const sections = document.querySelectorAll('.target-section');

        // Add an event listener listening for scroll
        window.addEventListener('scroll', navHighlight);

        function navHighlight() {
        
            // Get current scroll position
            let scrollY = window.pageYOffset;
        
            // Loop through sections to get height(including padding and border), 
            // top and ID values for each
            sections.forEach(function(current) {
                const sectionHeight = current.offsetHeight;
                const sectionTop = current.offsetTop - 50;
                const sectionId = current.getAttribute('id');
            
               /* If our current scroll position enters the space where current section 
                * on screen is, add .current class to parent element(li) of the thecorresponding 
                * navigation link, else remove it. To know which link is active, we use 
                * sectionId variable we are getting while looping through sections as 
                * an selector
                */
                if (scrollY > sectionTop && scrollY <= sectionTop + sectionHeight) {
                    document.querySelector('.s-header__nav a[href*=' + sectionId + ']').parentNode.classList.add('current');
                } else {
                    document.querySelector('.s-header__nav a[href*=' + sectionId + ']').parentNode.classList.remove('current');
                }
            });
        }

    }; // end ssScrollSpy


   /* masonry
    * ------------------------------------------------------ */
     const ssMasonry = function() {

        const containerBricks = document.querySelector('.bricks-wrapper');
        if (!containerBricks) return;

        imagesLoaded(containerBricks, function() {

            const msnry = new Masonry(containerBricks, {
                itemSelector: '.entry',
                columnWidth: '.grid-sizer',
                percentPosition: true,
                resize: true
            });

        });

    }; // end ssMasonry


   /* swiper
    * ------------------------------------------------------ */ 
    const ssSwiper = function() {

        const testimonialsSwiper = new Swiper('.s-testimonials__slider', {

            slidesPerView: 1,
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
            breakpoints: {
                // when window width is > 400px
                401: {
                    slidesPerView: 1,
                    spaceBetween: 20
                },
                // when window width is > 800px
                801: {
                    slidesPerView: 2,
                    spaceBetween: 50
                },
                // when window width is > 1180px
                1181: {
                    slidesPerView: 2,
                    spaceBetween: 100
                }
            }
        });

    }; // end ssSwiper


        // Additional interactivity can be added here
        document.addEventListener('DOMContentLoaded', function () {
            // Example of showing more detailed information when clicking on items
            const items = document.querySelectorAll('.list-block__item');

            items.forEach(item => {
                item.addEventListener('click', function (e) {
                    // Only trigger if not clicking the button
                    if (!e.target.classList.contains('view-detail')) {
                        const title = this.querySelector('.h5').textContent;
                        console.log(`Clicked on ${title}`);
                        // Here you could show a modal with more information
                    }
                });
            });
        });

   /* mailchimp form
    * ---------------------------------------------------- */ 
    const ssMailChimpForm = function() {

        const mcForm = document.querySelector('#mc-form');

        if (!mcForm) return;

        // Add novalidate attribute
        mcForm.setAttribute('novalidate', true);

        // Field validation
        function hasError(field) {

            // Don't validate submits, buttons, file and reset inputs, and disabled fields
            if (field.disabled || field.type === 'file' || field.type === 'reset' || field.type === 'submit' || field.type === 'button') return;

            // Get validity
            let validity = field.validity;

            // If valid, return null
            if (validity.valid) return;

            // If field is required and empty
            if (validity.valueMissing) return 'Please enter an email address.';

            // If not the right type
            if (validity.typeMismatch) {
                if (field.type === 'email') return 'Please enter a valid email address.';
            }

            // If pattern doesn't match
            if (validity.patternMismatch) {

                // If pattern info is included, return custom error
                if (field.hasAttribute('title')) return field.getAttribute('title');

                // Otherwise, generic error
                return 'Please match the requested format.';
            }

            // If all else fails, return a generic catchall error
            return 'The value you entered for this field is invalid.';

        };

        // Show error message
        function showError(field, error) {

            // Get field id or name
            let id = field.id || field.name;
            if (!id) return;

            let errorMessage = field.form.querySelector('.mc-status');

            // Update error message
            errorMessage.classList.remove('success-message');
            errorMessage.classList.add('error-message');
            errorMessage.innerHTML = error;

        };

        // Display form status (callback function for JSONP)
        window.displayMailChimpStatus = function (data) {

            // Make sure the data is in the right format and that there's a status container
            if (!data.result || !data.msg || !mcStatus ) return;

            // Update our status message
            mcStatus.innerHTML = data.msg;

            // If error, add error class
            if (data.result === 'error') {
                mcStatus.classList.remove('success-message');
                mcStatus.classList.add('error-message');
                return;
            }

            // Otherwise, add success class
            mcStatus.classList.remove('error-message');
            mcStatus.classList.add('success-message');
        };

        // Submit the form 
        function submitMailChimpForm(form) {

            let url = cfg.mailChimpURL;
            let emailField = form.querySelector('#mce-EMAIL');
            let serialize = '&' + encodeURIComponent(emailField.name) + '=' + encodeURIComponent(emailField.value);

            if (url == '') return;

            url = url.replace('/post?u=', '/post-json?u=');
            url += serialize + '&c=displayMailChimpStatus';

            // Create script with url and callback (if specified)
            var ref = window.document.getElementsByTagName( 'script' )[ 0 ];
            var script = window.document.createElement( 'script' );
            script.src = url;

            // Create global variable for the status container
            window.mcStatus = form.querySelector('.mc-status');
            window.mcStatus.classList.remove('error-message', 'success-message')
            window.mcStatus.innerText = 'Submitting...';

            // Insert script tag into the DOM
            ref.parentNode.insertBefore( script, ref );

            // After the script is loaded (and executed), remove it
            script.onload = function () {
                this.remove();
            };

        };

        // Check email field on submit
        mcForm.addEventListener('submit', function (event) {

            event.preventDefault();

            let emailField = event.target.querySelector('#mce-EMAIL');
            let error = hasError(emailField);

            if (error) {
                showError(emailField, error);
                emailField.focus();
                return;
            }

            submitMailChimpForm(this);

        }, false);

    }; // end ssMailChimpForm


   /* Lightbox
    * ------------------------------------------------------ */
    const ssLightbox = function() {

        // video lightbox
        const videoLightbox = function() {

            const videoLink = document.querySelector('.s-intro__content-video-btn');
            if (!videoLink) return;
    
            videoLink.addEventListener('click', function(event) {
    
                const vLink = this.getAttribute('href');
                const iframe = "<iframe src='" + vLink + "' frameborder='0'></iframe>";
    
                event.preventDefault();
    
                const instance = basicLightbox.create(iframe);
                instance.show()
    
            });
    
        };

        // portfolio lightbox
        const folioLightbox = function() {

            const folioLinks = document.querySelectorAll('.brick .entry__link');
            const modals = [];
    
            folioLinks.forEach(function(link) {
                let modalbox = link.getAttribute('href');
                let instance = basicLightbox.create(
                    document.querySelector(modalbox),
                    {
                        onShow: function(instance) {
                            //detect Escape key press
                            document.addEventListener("keydown", function(event) {
                                event = event || window.event;
                                if (event.key === "Escape") {
                                    instance.close();
                                }
                            });
                        }
                    }
                )
                modals.push(instance);
            });
    
            folioLinks.forEach(function(link, index) {
                link.addEventListener("click", function(event) {
                    event.preventDefault();
                    modals[index].show();
                });
            });
    
        };

        videoLightbox();
        folioLightbox();

    }; // ssLightbox


   /* alert boxes
    * ------------------------------------------------------ */
    const ssAlertBoxes = function() {

        const boxes = document.querySelectorAll('.alert-box');
  
        boxes.forEach(function(box){

            box.addEventListener('click', function(event) {
                if (event.target.matches('.alert-box__close')) {
                    event.stopPropagation();
                    event.target.parentElement.classList.add('hideit');

                    setTimeout(function(){
                        box.style.display = 'none';
                    }, 500)
                }
            });
        })

    }; // end ssAlertBoxes


    /* Back to Top
    * ------------------------------------------------------ */
    const ssBackToTop = function() {

        const pxShow = 900;
        const goTopButton = document.querySelector(".ss-go-top");

        if (!goTopButton) return;

        // Show or hide the button
        if (window.scrollY >= pxShow) goTopButton.classList.add("link-is-visible");

        window.addEventListener('scroll', function() {
            if (window.scrollY >= pxShow) {
                if(!goTopButton.classList.contains('link-is-visible')) goTopButton.classList.add("link-is-visible")
            } else {
                goTopButton.classList.remove("link-is-visible")
            }
        });

    }; // end ssBackToTop


   /* smoothscroll
    * ------------------------------------------------------ */
    const ssMoveTo = function(){

        const easeFunctions = {
            easeInQuad: function (t, b, c, d) {
                t /= d;
                return c * t * t + b;
            },
            easeOutQuad: function (t, b, c, d) {
                t /= d;
                return -c * t* (t - 2) + b;
            },
            easeInOutQuad: function (t, b, c, d) {
                t /= d/2;
                if (t < 1) return c/2*t*t + b;
                t--;
                return -c/2 * (t*(t-2) - 1) + b;
            },
            easeInOutCubic: function (t, b, c, d) {
                t /= d/2;
                if (t < 1) return c/2*t*t*t + b;
                t -= 2;
                return c/2*(t*t*t + 2) + b;
            }
        }

        const triggers = document.querySelectorAll('.smoothscroll');
        
        const moveTo = new MoveTo({
            tolerance: 0,
            duration: 1200,
            easing: 'easeInOutCubic',
            container: window
        }, easeFunctions);

        triggers.forEach(function(trigger) {
            moveTo.registerTrigger(trigger);
        });

    }; // end ssMoveTo


   /* Initialize
    * ------------------------------------------------------ */
    (function ssInit() {

        ssPreloader();
        ssMoveHeader();
        ssMobileMenu();
        ssScrollSpy();
        ssMasonry();
        ssSwiper();
        ssMailChimpForm();
        ssLightbox();
        ssAlertBoxes();
        ssBackToTop();
        ssMoveTo();

    })();

})(document.documentElement);

// Scroll Reveal Animation
function revealOnScroll() {
    const reveals = document.querySelectorAll('.wg-reveal');
    
    for (let i = 0; i < reveals.length; i++) {
        const windowHeight = window.innerHeight;
        const elementTop = reveals[i].getBoundingClientRect().top;
        const elementVisible = 150;
        
        if (elementTop < windowHeight - elementVisible) {
            reveals[i].classList.add('revealed');
        }
    }
}

// Interactive hover effects
document.addEventListener('DOMContentLoaded', function() {
    // Timeline content click interaction
    const timelineItems = document.querySelectorAll('.wg-timeline-content');
    timelineItems.forEach(item => {
        item.addEventListener('click', function() {
            // Add ripple effect
            const ripple = document.createElement('div');
            ripple.style.position = 'absolute';
            ripple.style.borderRadius = '50%';
            ripple.style.background = 'rgba(211, 168, 74, 0.3)';
            ripple.style.transform = 'scale(0)';
            ripple.style.animation = 'ripple 0.6s linear';
            ripple.style.left = '50%';
            ripple.style.top = '50%';
            ripple.style.width = '20px';
            ripple.style.height = '20px';
            ripple.style.marginLeft = '-10px';
            ripple.style.marginTop = '-10px';
            
            this.appendChild(ripple);
            
            setTimeout(() => {
                ripple.remove();
            }, 600);
        });
    });

    // Feature items glow effect
    const featureItems = document.querySelectorAll('.wg-feature-item');
    featureItems.forEach(item => {
        item.addEventListener('mouseenter', function() {
            this.style.boxShadow = '0 25px 80px rgba(211, 168, 74, 0.2)';
        });
        
        item.addEventListener('mouseleave', function() {
            this.style.boxShadow = '0 10px 40px rgba(0, 0, 0, 0.08)';
        });
    });
});

// Add ripple animation keyframe
const style = document.createElement('style');
style.textContent = `
    @keyframes ripple {
        to {
            transform: scale(4);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);

window.addEventListener('scroll', revealOnScroll);
revealOnScroll(); // Initial check

// Image carousel functionality
let currentSlideIndex = {};

function initCarousel(modalId) {
    if (!currentSlideIndex[modalId]) {
        currentSlideIndex[modalId] = 0;
    }
}

function showSlide(modalId, index) {
    const track = document.getElementById(`carousel-track-${modalId}`);
    const indicators = document.getElementById(`indicators-${modalId}`);
    const slides = track.children;
    const indicatorElements = indicators.children;
    
    if (index >= slides.length) currentSlideIndex[modalId] = 0;
    if (index < 0) currentSlideIndex[modalId] = slides.length - 1;
    
    track.style.transform = `translateX(-${currentSlideIndex[modalId] * 100}%)`;
    
    // Update indicators
    for (let i = 0; i < indicatorElements.length; i++) {
        indicatorElements[i].classList.remove('active');
    }
    indicatorElements[currentSlideIndex[modalId]].classList.add('active');
}

function nextSlide(modalId) {
    initCarousel(modalId);
    currentSlideIndex[modalId]++;
    showSlide(modalId, currentSlideIndex[modalId]);
}

function prevSlide(modalId) {
    initCarousel(modalId);
    currentSlideIndex[modalId]--;
    showSlide(modalId, currentSlideIndex[modalId]);
}

function currentSlide(modalId, index) {
    initCarousel(modalId);
    currentSlideIndex[modalId] = index - 1;
    showSlide(modalId, currentSlideIndex[modalId]);
}

// Auto-play carousel (optional)
function autoPlayCarousel(modalId, interval = 5000) {
    setInterval(() => {
        nextSlide(modalId);
    }, interval);
}

// Initialize when modal opens
function openModal(modalId) {
    document.getElementById(`${modalId}-modal`).style.display = 'block';
    initCarousel(modalId);
    document.body.style.overflow = 'hidden'; // Prevent background scrolling
}

function closeModal(modalId) {
    document.getElementById(`${modalId}-modal`).style.display = 'none';
    document.body.style.overflow = 'auto'; // Restore scrolling
}

// Close modal when clicking outside
window.onclick = function(event) {
    if (event.target.classList.contains('vw-modal')) {
        event.target.style.display = 'none';
        document.body.style.overflow = 'auto';
    }
}

// Keyboard navigation
document.addEventListener('keydown', function(event) {
    const openModal = document.querySelector('.vw-modal[style*="block"]');
    if (openModal) {
        const modalId = openModal.id.replace('-modal', '');
        
        switch(event.key) {
            case 'Escape':
                closeModal(modalId);
                break;
            case 'ArrowLeft':
                prevSlide(modalId);
                break;
            case 'ArrowRight':
                nextSlide(modalId);
                break;
        }
    }
});

// Array berisi daftar fasilitas berdasarkan urutan yang terlihat di HTML
const facilities = [
    'watu-gong',
    'gerbang-sanchi',
    'plaza-borobudur',
    'asoka',
    'bodhi',
    'avalokitesvara',
    'b_parinibbana',
    'b_sivali',
    'r_berdiri',
    'dhammasala',
    'samupadda',
    'tbm',
    'meditasi',
    'bhikkhu'
    // Tambahkan ID modal fasilitas lainnya di sini sesuai urutan
];

// Variabel untuk melacak fasilitas yang sedang aktif
let currentFacilityIndex = 0;

// Fungsi untuk mendapatkan index fasilitas yang sedang aktif
function getCurrentFacilityIndex() {
    // Cari modal yang sedang terbuka (memiliki display: block atau class active)
    for (let i = 0; i < facilities.length; i++) {
        const modal = document.getElementById(facilities[i] + '-modal');
        if (modal && (modal.style.display === 'block' || modal.classList.contains('active'))) {
            return i;
        }
    }
    return 0; // Default ke index 0 jika tidak ada yang aktif
}

// Fungsi untuk berpindah ke fasilitas sebelumnya
function previousFacility() {
    currentFacilityIndex = getCurrentFacilityIndex();
    
    // Tutup modal yang sedang aktif
    closeModal(facilities[currentFacilityIndex]);
    
    // Pindah ke fasilitas sebelumnya (dengan wrapping)
    currentFacilityIndex = (currentFacilityIndex - 1 + facilities.length) % facilities.length;
    
    // Buka modal fasilitas sebelumnya
    openModal(facilities[currentFacilityIndex]);
}

// Fungsi untuk berpindah ke fasilitas selanjutnya
function nextFacility() {
    currentFacilityIndex = getCurrentFacilityIndex();
    
    // Tutup modal yang sedang aktif
    closeModal(facilities[currentFacilityIndex]);
    
    // Pindah ke fasilitas selanjutnya (dengan wrapping)
    currentFacilityIndex = (currentFacilityIndex + 1) % facilities.length;
    
    // Buka modal fasilitas selanjutnya
    openModal(facilities[currentFacilityIndex]);
}

// Fungsi helper untuk membuka modal
function openModal(facilityId) {
    const modal = document.getElementById(facilityId + '-modal');
    if (modal) {
        modal.style.display = 'block';
        // Atau gunakan class jika menggunakan CSS classes
        // modal.classList.add('active');
        
        // Disable scroll pada body
        document.body.style.overflow = 'hidden';
    }
}

// Fungsi untuk menutup modal (pastikan fungsi ini sudah ada)
function closeModal(facilityId) {
    const modal = document.getElementById(facilityId + '-modal');
    if (modal) {
        modal.style.display = 'none';
        // Atau gunakan class jika menggunakan CSS classes
        // modal.classList.remove('active');
        
        // Enable scroll pada body
        document.body.style.overflow = 'auto';
    }
}

// Alternative implementation jika Anda ingin navigasi yang lebih smooth
function previousFacilitySmooth() {
    currentFacilityIndex = getCurrentFacilityIndex();
    const prevIndex = (currentFacilityIndex - 1 + facilities.length) % facilities.length;
    
    // Fade out current modal
    const currentModal = document.getElementById(facilities[currentFacilityIndex] + '-modal');
    const nextModal = document.getElementById(facilities[prevIndex] + '-modal');
    
    if (currentModal && nextModal) {
        currentModal.style.opacity = '0';
        
        setTimeout(() => {
            currentModal.style.display = 'none';
            nextModal.style.display = 'block';
            nextModal.style.opacity = '0';
            
            // Fade in next modal
            setTimeout(() => {
                nextModal.style.opacity = '1';
            }, 10);
        }, 200);
    }
}

function nextFacilitySmooth() {
    currentFacilityIndex = getCurrentFacilityIndex();
    const nextIndex = (currentFacilityIndex + 1) % facilities.length;
    
    // Fade out current modal
    const currentModal = document.getElementById(facilities[currentFacilityIndex] + '-modal');
    const nextModal = document.getElementById(facilities[nextIndex] + '-modal');
    
    if (currentModal && nextModal) {
        currentModal.style.opacity = '0';
        
        setTimeout(() => {
            currentModal.style.display = 'none';
            nextModal.style.display = 'block';
            nextModal.style.opacity = '0';
            
            // Fade in next modal
            setTimeout(() => {
                nextModal.style.opacity = '1';
            }, 10);
        }, 200);
    }
}

// Fungsi untuk keyboard navigation (opsional)
document.addEventListener('keydown', function(event) {
    // Cek apakah ada modal yang sedang terbuka
    const activeModal = facilities.find(facilityId => {
        const modal = document.getElementById(facilityId + '-modal');
        return modal && (modal.style.display === 'block' || modal.classList.contains('active'));
    });
    
    if (activeModal) {
        switch(event.key) {
            case 'ArrowLeft':
                event.preventDefault();
                previousFacility();
                break;
            case 'ArrowRight':
                event.preventDefault();
                nextFacility();
                break;
            case 'Escape':
                event.preventDefault();
                closeModal(activeModal);
                break;
        }
    }
});

document.addEventListener('DOMContentLoaded', function() {
    const eventsGrid = document.getElementById('eventsGrid');
    const seeMoreBtn = document.getElementById('seeMoreBtn');
    const btnText = seeMoreBtn.querySelector('span:first-child');
    const btnIcon = seeMoreBtn.querySelector('.fa');
    let isExpanded = false;

    // Check if mobile view
    function isMobile() {
        return window.innerWidth <= 576;
    }

    // Update grid display based on screen size
    function updateGridDisplay() {
        if (isMobile()) {
            eventsGrid.classList.add('mobile-view');
            if (!isExpanded) {
                eventsGrid.classList.add('collapsed');
            }
            seeMoreBtn.parentElement.style.display = 'block';
        } else {
            eventsGrid.classList.remove('mobile-view', 'collapsed');
            seeMoreBtn.parentElement.style.display = 'none';
        }
    }

    // Handle see more button click
    seeMoreBtn.addEventListener('click', function() {
        if (isExpanded) {
            eventsGrid.classList.add('collapsed');
            btnText.textContent = 'Lihat Semua Kegiatan';
            btnIcon.style.transform = 'rotate(0deg)';
            isExpanded = false;
            
            // Smooth scroll to top of section
            document.getElementById('kegiatan').scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        } else {
            eventsGrid.classList.remove('collapsed');
            btnText.textContent = 'Lihat Lebih Sedikit';
            btnIcon.style.transform = 'rotate(180deg)';
            isExpanded = true;

            // Add animation to newly visible cards
            const hiddenCards = eventsGrid.querySelectorAll('.event-card:nth-child(n+4)');
            hiddenCards.forEach((card, index) => {
                card.classList.add('show-animation');
                card.style.animationDelay = `${index * 0.1}s`;
            });
        }
    });

    // Handle window resize
    window.addEventListener('resize', updateGridDisplay);
    
    // Initial setup
    updateGridDisplay();

    // Add hover effects and smooth animations
    const eventCards = document.querySelectorAll('.event-card');
    eventCards.forEach((card, index) => {
        // Staggered animation on load
        card.style.animationDelay = `${index * 0.1}s`;
        
        // Enhanced hover effects
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-10px) scale(1.02)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });

    // Intersection Observer for scroll animations
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, {
        threshold: 0.1,
        rootMargin: '50px'
    });

    // Observe all event cards
    eventCards.forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(card);
    });
});

// js/berita.js

class BeritaManager {
    constructor() {
        this.init();
    }

    init() {
        this.setupLazyLoading();
        this.setupSmoothScrolling();
        this.setupImageErrorHandling();
        this.setupLoadMoreButton();
    }

    // Lazy loading untuk gambar
    setupLazyLoading() {
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.src = img.dataset.src;
                        img.classList.remove('lazy');
                        observer.unobserve(img);
                    }
                });
            });

            document.querySelectorAll('img[data-src]').forEach(img => {
                imageObserver.observe(img);
            });
        }
    }

    // Smooth scrolling untuk pagination
    setupSmoothScrolling() {
        document.querySelectorAll('.pagination a').forEach(link => {
            link.addEventListener('click', (e) => {
                // Scroll to top of news section
                const newsSection = document.getElementById('berita');
                if (newsSection) {
                    setTimeout(() => {
                        newsSection.scrollIntoView({ 
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }, 100);
                }
            });
        });
    }

    // Handle error gambar
    setupImageErrorHandling() {
        document.querySelectorAll('.news-list__img img').forEach(img => {
            img.addEventListener('error', function() {
                this.src = 'images/placeholder-news.jpg';
                this.alt = 'Gambar tidak tersedia';
            });
        });
    }

    // Load more functionality
    setupLoadMoreButton() {
        const loadMoreBtn = document.getElementById('loadMoreBtn');
        if (loadMoreBtn) {
            loadMoreBtn.addEventListener('click', this.loadMoreNews.bind(this));
        }
    }

    async loadMoreNews() {
        const loadMoreBtn = document.getElementById('loadMoreBtn');
        const newsList = document.querySelector('.news-list');
        
        try {
            loadMoreBtn.textContent = 'Memuat...';
            loadMoreBtn.disabled = true;

            // Get current page from button data attribute
            const currentPage = parseInt(loadMoreBtn.dataset.page) || 1;
            const nextPage = currentPage + 1;

            const response = await fetch(`ajax/load_more_berita.php?page=${nextPage}`);
            const data = await response.json();

            if (data.success && data.berita.length > 0) {
                // Append new news items
                data.berita.forEach(item => {
                    const newsItem = this.createNewsElement(item);
                    newsList.appendChild(newsItem);
                });

                // Update button page
                loadMoreBtn.dataset.page = nextPage;
                
                // Hide button if no more data
                if (!data.hasMore) {
                    loadMoreBtn.style.display = 'none';
                }
            } else {
                loadMoreBtn.style.display = 'none';
            }

        } catch (error) {
            console.error('Error loading more news:', error);
            this.showError('Gagal memuat berita. Silakan coba lagi.');
        } finally {
            loadMoreBtn.textContent = 'Muat Lebih Banyak';
            loadMoreBtn.disabled = false;
        }
    }

    createNewsElement(item) {
        const newsItem = document.createElement('div');
        newsItem.className = 'column news-list__item';
        
        newsItem.innerHTML = `
            <div class="news-list__img">
                <img src="images/${item.gambar_utama}" alt="${item.judul}" loading="lazy">
            </div>
            <div class="news-list__content">
                <div class="news-list__meta">
                    <span>${this.formatTanggal(item.tanggal_publish)}</span>
                    <span>${item.nama_kategori}</span>
                </div>
                <h4>${item.judul}</h4>
                <p>${item.excerpt}</p>
                <a href="detail_berita.php?slug=${item.slug}" class="news-list__more">Baca Selengkapnya</a>
            </div>
        `;

        // Setup image error handling for new element
        const img = newsItem.querySelector('img');
        img.addEventListener('error', function() {
            this.src = 'images/placeholder-news.jpg';
            this.alt = 'Gambar tidak tersedia';
        });

        return newsItem;
    }

    formatTanggal(tanggal) {
        const bulan = [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];
        
        const date = new Date(tanggal);
        const hari = date.getDate();
        const bulanNama = bulan[date.getMonth()];
        const tahun = date.getFullYear();
        
        return `${hari} ${bulanNama} ${tahun}`;
    }

    showError(message) {
        // Create error notification
        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-notification';
        errorDiv.textContent = message;
        errorDiv.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: #ff4757;
            color: white;
            padding: 1rem 2rem;
            border-radius: 8px;
            z-index: 1000;
            box-shadow: 0 4px 15px rgba(255, 71, 87, 0.3);
        `;

        document.body.appendChild(errorDiv);

        // Remove after 5 seconds
        setTimeout(() => {
            errorDiv.remove();
        }, 5000);
    }

    // Search functionality (if needed)
    setupSearch() {
        const searchInput = document.getElementById('searchBerita');
        if (searchInput) {
            let searchTimeout;
            
            searchInput.addEventListener('input', (e) => {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    this.searchBerita(e.target.value);
                }, 500);
            });
        }
    }

    async searchBerita(query) {
        if (query.length < 3) return;

        try {
            const response = await fetch(`ajax/search_berita.php?q=${encodeURIComponent(query)}`);
            const data = await response.json();

            this.updateNewsDisplay(data.berita);
        } catch (error) {
            console.error('Search error:', error);
        }
    }

    updateNewsDisplay(beritaList) {
        const newsList = document.querySelector('.news-list');
        newsList.innerHTML = '';

        if (beritaList.length === 0) {
            newsList.innerHTML = '<div class="no-data">Tidak ada berita yang ditemukan.</div>';
            return;
        }

        beritaList.forEach(item => {
            const newsItem = this.createNewsElement(item);
            newsList.appendChild(newsItem);
        });
    }

    // Animation for news items
    animateNewsItems() {
        const newsItems = document.querySelectorAll('.news-list__item');
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry, index) => {
                if (entry.isIntersecting) {
                    setTimeout(() => {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }, index * 100);
                }
            });
        }, { threshold: 0.1 });

        newsItems.forEach(item => {
            item.style.opacity = '0';
            item.style.transform = 'translateY(30px)';
            item.style.transition = 'all 0.6s ease';
            observer.observe(item);
        });
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    const beritaManager = new BeritaManager();
    beritaManager.animateNewsItems();
});

// Utility functions
window.BeritaUtils = {
    truncateText: (text, maxLength = 150) => {
        if (text.length <= maxLength) return text;
        return text.substr(0, maxLength) + '...';
    },

    formatFileSize: (bytes) => {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    },

    debounce: (func, wait) => {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
};