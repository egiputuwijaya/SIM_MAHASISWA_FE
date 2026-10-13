<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= htmlspecialchars($metaDescription ?? 'Portal informasi akademik terintegrasi Universitas Muhammadiyah Bengkulu untuk kelola data mahasiswa, program studi, dan fakultas.', ENT_QUOTES, 'UTF-8') ?>">
    <meta name="theme-color" content="#1e3a8a">
    <title><?= htmlspecialchars($title ?? 'SIM Mahasiswa UMB', ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="icon" type="image/png" href="<?= url('assets/logoumb.png') ?>">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'ui-sans-serif', 'system-ui'],
                        heading: ['Poppins', 'ui-sans-serif', 'system-ui'],
                    },
                    colors: {
                        academic: {
                            50: '#eff6ff', 100: '#dbeafe', 200: '#bfdbfe', 300: '#93c5fd',
                            400: '#60a5fa', 500: '#3b82f6', 600: '#2563eb', 700: '#1d4ed8',
                            800: '#1e40af', 900: '#1e3a8a',
                        },
                        elegant: {
                            50: '#f0fdf4', 100: '#dcfce7', 200: '#bbf7d0', 300: '#86efac',
                            400: '#4ade80', 500: '#22c55e', 600: '#16a34a', 700: '#15803d',
                            800: '#166534', 900: '#14532d',
                        },
                    },
                },
            },
        };
    </script>

    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
          crossorigin="anonymous" referrerpolicy="no-referrer">

    <style>
        body { font-family: 'Inter', system-ui, sans-serif; }
        h1, h2, h3, .font-heading { font-family: 'Poppins', 'Inter', sans-serif; }

        .hero-bg {
            background:
                radial-gradient(800px 420px at 85% 10%, rgba(59,130,246,0.35), transparent 60%),
                radial-gradient(600px 360px at 5% 90%, rgba(34,197,94,0.12), transparent 60%),
                linear-gradient(120deg, #0b1530 0%, #13245a 45%, #1e3a8a 100%);
        }
        .glass {
            background: rgba(255,255,255,0.07);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255,255,255,0.16);
        }
        .nav-link.is-active::after {
            content: ''; position: absolute; left: 14px; right: 14px; bottom: 2px;
            height: 2px; border-radius: 2px; background: #22c55e;
        }
        #site-navbar.is-scrolled { box-shadow: 0 6px 24px -12px rgba(15,23,42,0.25); }

        .footer-link { display: inline-flex; align-items: center; gap: .6rem; color: #cbd5e1; transition: color .2s, transform .2s; }
        .footer-link i { color: #4ade80; font-size: .75rem; width: 14px; text-align: center; }
        .footer-link:hover { color: #fff; transform: translateX(3px); }
        .contact-icon { width: 34px; height: 34px; border-radius: 8px; background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.08); display: inline-flex; align-items: center; justify-content: center; color: #93c5fd; font-size: .8rem; flex-shrink: 0; }

        /* Animasi muncul saat scroll */
        .reveal { opacity: 0; transform: translateY(18px); transition: opacity .6s ease, transform .6s ease; }
        .reveal.is-visible { opacity: 1; transform: none; }
        .card-hover { transition: transform .25s ease, box-shadow .25s ease, border-color .25s ease; }
        .card-hover:hover { transform: translateY(-4px); box-shadow: 0 18px 40px -18px rgba(30,64,175,0.35); border-color: #bfdbfe; }

        @keyframes floaty { 0%,100% { transform: translateY(0); } 50% { transform: translateY(-8px); } }
        .floaty { animation: floaty 6s ease-in-out infinite; }

        @media (prefers-reduced-motion: reduce) {
            .reveal { opacity: 1; transform: none; transition: none; }
            .floaty { animation: none; }
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased">

    <?php require __DIR__ . '/../partials/public-navbar.php'; ?>

    <main id="main-content">
        <?= $content ?? '' ?>
    </main>

    <?php require __DIR__ . '/../partials/public-footer.php'; ?>

    <script>
    (function () {
        // Navbar: bayangan saat scroll + tombol ke atas
        var nav = document.getElementById('site-navbar');
        var toTop = document.getElementById('btn-scroll-top');
        function onScroll() {
            var y = window.scrollY;
            if (nav) nav.classList.toggle('is-scrolled', y > 8);
            if (toTop) {
                var show = y > 400;
                toTop.classList.toggle('opacity-0', !show);
                toTop.classList.toggle('pointer-events-none', !show);
                toTop.classList.toggle('translate-y-3', !show);
            }
        }
        window.addEventListener('scroll', onScroll, { passive: true });
        onScroll();
        if (toTop) toTop.addEventListener('click', function () { window.scrollTo({ top: 0, behavior: 'smooth' }); });

        // Menu mobile
        var toggle = document.getElementById('nav-toggle');
        var mobile = document.getElementById('nav-mobile');
        if (toggle && mobile) {
            toggle.addEventListener('click', function () {
                var open = mobile.classList.toggle('hidden') === false;
                toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
                toggle.innerHTML = open ? '<i class="fa-solid fa-xmark text-lg"></i>' : '<i class="fa-solid fa-bars text-lg"></i>';
            });
        }

        // Reveal on scroll
        var els = document.querySelectorAll('.reveal');
        if ('IntersectionObserver' in window) {
            var io = new IntersectionObserver(function (entries) {
                entries.forEach(function (en) {
                    if (en.isIntersecting) { en.target.classList.add('is-visible'); io.unobserve(en.target); }
                });
            }, { threshold: 0.12 });
            els.forEach(function (el) { io.observe(el); });
        } else {
            els.forEach(function (el) { el.classList.add('is-visible'); });
        }

        // Animasi angka statistik
        document.querySelectorAll('[data-count]').forEach(function (el) {
            var target = parseInt(el.getAttribute('data-count'), 10);
            if (isNaN(target)) return;
            var start = null, dur = 1100;
            function step(ts) {
                if (!start) start = ts;
                var p = Math.min((ts - start) / dur, 1);
                var eased = 1 - Math.pow(1 - p, 3);
                el.textContent = Math.round(target * eased).toLocaleString('id-ID');
                if (p < 1) requestAnimationFrame(step);
            }
            requestAnimationFrame(step);
        });
    })();
    </script>
</body>
</html>
