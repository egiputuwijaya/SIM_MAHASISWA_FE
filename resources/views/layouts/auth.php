<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Login ke Sistem Informasi Manajemen Mahasiswa Universitas Muhammadiyah Bengkulu">
    <meta name="theme-color" content="#1e40af">
    <title><?= htmlspecialchars($title ?? 'Login — SIM Mahasiswa', ENT_QUOTES, 'UTF-8') ?></title>

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
                    boxShadow: {
                        'soft': '0 10px 40px -12px rgba(30, 64, 175, 0.18)',
                    },
                },
            },
        };
    </script>

    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
          integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
          crossorigin="anonymous" referrerpolicy="no-referrer">

    <style>
        body { font-family: 'Inter', system-ui, sans-serif; }
        h1, h2, h3, .font-heading { font-family: 'Poppins', 'Inter', sans-serif; }
        .login-bg {
            background:
                radial-gradient(900px 480px at 85% -10%, rgba(34,197,94,0.22), transparent 60%),
                radial-gradient(700px 400px at 10% 110%, rgba(59,130,246,0.30), transparent 55%),
                linear-gradient(135deg, #1e3a8a 0%, #1e40af 45%, #166534 100%);
        }
        .glass {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.18);
        }
        input:focus { outline: none; }
    </style>
</head>
<body class="login-bg min-h-screen flex items-center justify-center px-4 py-8 antialiased">

    <?= $content ?? '' ?>

</body>
</html>
