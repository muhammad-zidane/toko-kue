document.addEventListener('DOMContentLoaded', () => {
    // 1. Loading & Fade-in
    const loader = document.getElementById('page-loader');
    const content = document.querySelector('.fade-in-content');

    window.addEventListener('load', () => {
        if (loader) {
            loader.style.opacity = '0';
            loader.style.visibility = 'hidden';
        }
        if (content) {
            content.classList.add('visible');
        }
    });

    // 2. Scroll Animation (IntersectionObserver)
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const scrollObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('active');
                observer.unobserve(entry.target); // Menghemat memori setelah animasi selesai
            }
        });
    }, observerOptions);

    document.querySelectorAll('.scroll-reveal').forEach(el => {
        scrollObserver.observe(el);
    });

    // Stagger effect for grids
    const staggerObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach((entry, index) => {
            if (entry.isIntersecting) {
                setTimeout(() => {
                    entry.target.classList.add('active');
                }, index * 100);
                observer.unobserve(entry.target); // Menghemat memori setelah animasi selesai
            }
        });
    }, observerOptions);

    document.querySelectorAll('.stagger-item').forEach(el => {
        staggerObserver.observe(el);
    });

    // 4. Page Transition (Fade-out on click)
    document.querySelectorAll('a').forEach(link => {
        // Only internal links, not those with target="_blank" or hash links
        if (
            link.hostname === window.location.hostname &&
            !link.hash &&
            link.target !== '_blank'
        ) {
            link.addEventListener('click', (e) => {
                // Jangan blokir jika pengguna ingin membuka di tab/jendela baru
                if (e.ctrlKey || e.metaKey || e.shiftKey || e.button !== 0) return;

                e.preventDefault();
                const targetUrl = link.href;
                document.body.classList.add('page-fade-out');
                setTimeout(() => {
                    window.location.href = targetUrl;
                }, 300);
            });
        }
    });
});
