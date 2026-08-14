document.addEventListener('DOMContentLoaded', () => {
    
    // --- 1. Loading Screen ---
    const loader = document.getElementById('loader');
    if (loader) {
        window.addEventListener('load', () => {
            setTimeout(() => {
                loader.classList.add('hidden');
                setTimeout(() => {
                    loader.style.display = 'none';
                    // Trigger initial animations
                    ScrollTrigger.refresh();
                }, 800);
            }, 1000);
        });
    }

    // --- 2. Smooth Scrolling (Lenis) ---
    const lenis = new Lenis({
        duration: 1.2,
        easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)),
        smooth: true,
    });

    function raf(time) {
        lenis.raf(time);
        requestAnimationFrame(raf);
    }
    requestAnimationFrame(raf);

    // --- 3. Custom Cursor ---
    const cursorDot = document.querySelector('.cursor-dot');
    const cursorOutline = document.querySelector('.cursor-outline');

    if (cursorDot && cursorOutline && !('ontouchstart' in window)) {
        window.addEventListener('mousemove', (e) => {
            const posX = e.clientX;
            const posY = e.clientY;

            cursorDot.style.left = `${posX}px`;
            cursorDot.style.top = `${posY}px`;

            // GSAP for smooth outline follow
            gsap.to(cursorOutline, {
                x: posX,
                y: posY,
                duration: 0.15,
                ease: "power2.out"
            });
        });

        // Hover effects
        document.querySelectorAll('a, button, .btn').forEach(el => {
            el.addEventListener('mouseenter', () => {
                cursorOutline.style.width = '60px';
                cursorOutline.style.height = '60px';
                cursorOutline.style.backgroundColor = 'rgba(1, 112, 185, 0.1)';
            });
            el.addEventListener('mouseleave', () => {
                cursorOutline.style.width = '40px';
                cursorOutline.style.height = '40px';
                cursorOutline.style.backgroundColor = 'transparent';
            });
        });
    }

    // --- 4. Theme Toggle ---
    const themeToggle = document.getElementById('theme-toggle');
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    let currentTheme = localStorage.getItem('theme') || (prefersDark ? 'dark' : 'light');
    
    setTheme(currentTheme);

    if (themeToggle) {
        themeToggle.addEventListener('click', () => {
            currentTheme = currentTheme === 'dark' ? 'light' : 'dark';
            setTheme(currentTheme);
        });
    }

    function setTheme(theme) {
        document.documentElement.setAttribute('data-theme', theme);
        localStorage.setItem('theme', theme);
        if (themeToggle) {
            themeToggle.innerHTML = theme === 'dark' ? '<i class="ph ph-sun"></i>' : '<i class="ph ph-moon"></i>';
        }
    }

    // --- 5. Mobile Menu ---
    const menuBtn = document.getElementById('mobile-menu-btn');
    const navLinks = document.getElementById('nav-links');

    if (menuBtn && navLinks) {
        menuBtn.addEventListener('click', () => {
            navLinks.classList.toggle('active');
            menuBtn.innerHTML = navLinks.classList.contains('active') ? '<i class="ph ph-x"></i>' : '<i class="ph ph-list"></i>';
        });

        navLinks.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => {
                navLinks.classList.remove('active');
                menuBtn.innerHTML = '<i class="ph ph-list"></i>';
            });
        });
    }

    // --- 6. Sticky Header ---
    const header = document.getElementById('header');
    if (header) {
        window.addEventListener('scroll', () => {
            if (window.scrollY > 50) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        });
    }

    // --- 7. GSAP Scroll Animations ---
    gsap.registerPlugin(ScrollTrigger);

    const revealElements = document.querySelectorAll('.gsap-reveal');
    revealElements.forEach((el) => {
        gsap.fromTo(el, 
            { y: 50, opacity: 0, visibility: 'hidden' },
            {
                y: 0,
                opacity: 1,
                visibility: 'visible',
                duration: 1,
                ease: "power3.out",
                scrollTrigger: {
                    trigger: el,
                    start: "top 85%", // Trigger when top of element hits 85% of viewport
                    toggleActions: "play none none reverse"
                }
            }
        );
    });

    // Setup portfolio rendering if container exists
    const portfolioGrid = document.getElementById('portfolio-grid');
    if (portfolioGrid && typeof portfolioData !== 'undefined') {
        renderPortfolio(portfolioData);
    }
});

function renderPortfolio(data) {
    const grid = document.getElementById('portfolio-grid');
    const lang = localStorage.getItem('lang') || 'en';
    
    grid.innerHTML = '';
    data.forEach(item => {
        const title = lang === 'en' ? item.titleEn : item.titleAr;
        const cat = lang === 'en' ? item.categoryEn : item.categoryAr;
        const img = item.image ? `<img src="${item.image}" alt="${title}">` : `<div class="portfolio-placeholder">${title}</div>`;
        
        const card = document.createElement('a');
        card.href = item.link;
        card.target = "_blank";
        card.className = `portfolio-card glass-panel ${item.category}`;
        card.innerHTML = `
            <div class="portfolio-img-wrap">${img}</div>
            <div class="portfolio-info">
                <h3>${title}</h3>
                <span class="portfolio-tag">${cat}</span>
            </div>
        `;
        grid.appendChild(card);
    });
    
    // Setup animations for the new cards without ScrollTrigger (since it's at the top of the page)
    gsap.fromTo(grid.children, 
        { y: 50, opacity: 0 },
        {
            y: 0,
            opacity: 1,
            duration: 0.6,
            stagger: 0.1,
            ease: "power2.out"
        }
    );
    
    ScrollTrigger.refresh();
}
