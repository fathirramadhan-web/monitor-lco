<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Tambah Data Monitoring</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen p-6">
    <div class="max-w-xl mx-auto bg-white rounded-lg shadow p-6">
        <h1 class="text-2xl font-bold mb-6 text-center">➕ Tambah Data Monitoring</h1>

        <!-- Tampilkan error jika ada -->
        @if ($errors->any())
            <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Form Tambah Data -->
        <form method="POST" action="{{ route('monitoring.store') }}">
            @csrf

            <!-- Tanggal -->
            <div class="mb-4">
                <label class="block font-semibold mb-1" for="date">Tanggal Aktivasi</label>
                <input type="date" id="date" name="date" class="w-full border border-gray-300 rounded px-3 py-2"
                    value="{{ old('date') }}" required>
            </div>

            <!-- Jumlah Done -->
            <div class="mb-4">
                <label class="block font-semibold mb-1" for="done">Jumlah Done</label>
                <input type="number" id="done" name="done" class="w-full border border-gray-300 rounded px-3 py-2"
                    value="{{ old('done') }}" min="0" required>
            </div>

            <!-- Target -->
            <div class="mb-4">
                <label class="block font-semibold mb-1" for="target">Target</label>
                <input type="number" id="target" name="target" class="w-full border border-gray-300 rounded px-3 py-2"
                    value="{{ old('target') }}" min="0" required>
            </div>

            <!-- Rata-rata Pemasangan (Jam) -->
            <div class="mb-4">
                <label for="jam" class="block font-semibold mb-1">Rata-rata Pemasangan (Jam)</label>
                <input type="number" step="0.1" name="jam" id="jam" class="w-full border border-gray-300 rounded px-3 py-2"
                    value="{{ old('jam') }}" required>
            </div>

            <!-- Jumlah 20mb -->
            <div class="mb-4">
                <label for="mb20" class="block font-semibold mb-1">Jumlah 20mb</label>
                <input type="number" name="mb20" id="mb20" class="w-full border border-gray-300 rounded px-3 py-2"
                    value="{{ old('mb20') }}" required>
            </div>

            <!-- Jumlah 50mb -->
            <div class="mb-6">
                <label for="mb50" class="block font-semibold mb-1">Jumlah 50mb</label>
                <input type="number" name="mb50" id="mb50" class="w-full border border-gray-300 rounded px-3 py-2"
                    value="{{ old('mb50') }}" required>
            </div>

            <!-- Tombol -->
            <div class="flex justify-between">
                <a href="{{ route('monitoring') }}" class="px-4 py-2 rounded bg-gray-500 text-white hover:bg-gray-600">
                    Kembali
                </a>
                <button type="submit" class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</body>

</html>
