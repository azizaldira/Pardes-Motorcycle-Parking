<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Halaman Tidak Ditemukan</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f4f6f9;
            color: #1f2937;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            text-align: center;
        }
        .error-card {
            background: white;
            padding: 3rem;
            border-radius: 1rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 90%;
        }
        .error-icon {
            font-size: 4rem;
            color: #f59e0b;
            margin-bottom: 1.5rem;
        }
        h1 {
            font-size: 2rem;
            font-weight: 700;
            margin: 0 0 0.5rem;
        }
        p {
            color: #6b7280;
            margin-bottom: 2rem;
        }
        .btn {
            display: inline-block;
            background-color: #2563eb;
            color: white;
            text-decoration: none;
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            font-weight: 500;
            transition: background-color 0.2s;
        }
        .btn:hover {
            background-color: #1d4ed8;
        }
    </style>
</head>
<body>
    <div class="error-card">
        <i class="fas fa-map-signs error-icon"></i>
        <h1>Halaman Tidak Ditemukan</h1>
        <p>Maaf, alamat URL yang Anda tuju sepertinya tidak ada di sistem kami atau telah dipindahkan.</p>
        <a href="{{ url('/') }}" class="btn"><i class="fas fa-home" style="margin-right: 0.5rem;"></i> Kembali ke Beranda</a>
    </div>
</body>
</html>
