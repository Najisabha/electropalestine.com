<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'electropalestine Admin' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        :root{
            --primary:#0db777;
            --primary-dark:#0a8d5b;
            --bg-dark:#0b0d11;
            --accent:#f5d10c;
            --glass:rgba(255,255,255,.06);
            --border:rgba(255,255,255,.12);
        }
        body{
            font-family:'Cairo',sans-serif;
            background:radial-gradient(circle at top,var(--primary-dark),var(--bg-dark) 55%);
            color:#eaf6ef;
        }
        .glass{
            background:var(--glass);
            border:1px solid var(--border);
            backdrop-filter: blur(10px);
            border-radius:18px;
        }
        .brand-logo{
            width:46px;height:46px;
            border-radius:50%;
            display:grid;
            place-items:center;
            background:linear-gradient(135deg,var(--primary),var(--primary-dark));
            font-weight:800;
            color:#fff;
            box-shadow:0 0 25px rgba(13,183,119,.4);
        }
        .btn-main{
            background:linear-gradient(135deg,var(--primary),var(--primary-dark));
            border:none;
            color:#fff;
        }
        .btn-main:hover{
            filter:brightness(1.1);
            color:#fff;
        }
        .btn-outline-main{
            border:1px solid var(--primary);
            color:var(--primary);
        }
        .btn-outline-main:hover{
            background:var(--primary);
            color:#0b0d11;
        }
        nav a{ color:#d9f2e3; font-size:.9rem; }
        nav a:hover{ color:var(--accent); }
    </style>
    {{-- Chart.js للرسوم البيانية في لوحة التحكم --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="d-flex flex-column min-vh-100">
@include('admin.header')

<main class="flex-grow-1">
    {!! $slot !!}
</main>

@include('admin.footer')

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

