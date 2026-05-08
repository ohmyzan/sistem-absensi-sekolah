<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Rekam Wajah - {{ $student->user->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="{{ asset('js/face-api.min.js') }}"></script>
</head>

<body class="bg-gray-900 text-white flex flex-col items-center justify-center min-h-screen p-4">

    <div class="max-w-md w-full bg-gray-800 rounded-xl shadow-2xl p-6 text-center">
        <h1 class="text-2xl font-bold mb-2">Registrasi Wajah</h1>
        <p class="text-gray-400 mb-6">Siswa: <span class="text-blue-400 font-semibold">{{ $student->user->name }}</span>
        </p>

        <div class="relative inline-block overflow-hidden rounded-lg border-4 border-gray-700 bg-black w-full"
            style="height: 300px;">
            <video id="video" autoplay muted playsinline class="w-full h-full object-cover"></video>
            <canvas id="overlay" class="absolute top-0 left-0 w-full h-full"></canvas>

            <div id="loader" class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-75 z-10">
                <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-blue-500"></div>
                <p class="ml-3 text-sm">Memuat AI...</p>
            </div>
        </div>

        <div id="status" class="mt-4 p-3 rounded-lg bg-gray-700 text-sm italic text-gray-300">
            Harap tunggu, sedang memuat kamera...
        </div>

        <button id="capture-btn" disabled
            class="mt-6 w-full bg-blue-600 hover:bg-blue-700 disabled:bg-gray-600 text-white font-bold py-3 px-6 rounded-lg transition-all transform active:scale-95">
            REKAM WAJAH
        </button>

        <a href="/admin/students" class="mt-4 block text-sm text-gray-500 hover:text-gray-300">Kembali ke Dashboard</a>
    </div>

    <script>
        const video = document.getElementById('video');
        const statusDiv = document.getElementById('status');
        const captureBtn = document.getElementById('capture-btn');
        const loader = document.getElementById('loader');

        // 1. Load Model AI
        async function loadModels() {
            const MODEL_URL = '/models';
            try {
                await Promise.all([
                    faceapi.nets.ssdMobilenetv1.loadFromUri(MODEL_URL),
                    faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL),
                    faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL)
                ]);
                statusDiv.innerText = "Model AI siap. Izinkan akses kamera.";
                startVideo();
            } catch (error) {
                statusDiv.innerText = "Gagal memuat model AI. Pastikan folder public/models sudah benar.";
                console.error(error);
            }
        }

        // 2. Akses Webcam
        function startVideo() {
            navigator.mediaDevices.getUserMedia({
                    video: {}
                })
                .then(stream => {
                    video.srcObject = stream;
                    loader.style.display = 'none';
                    captureBtn.disabled = false;
                    statusDiv.innerText = "Kamera aktif. Posisikan wajah tepat di tengah.";
                })
                .catch(err => {
                    statusDiv.innerText = "Gagal akses kamera: " + err;
                });
        }

        // 3. Logika Capture dan Simpan
        captureBtn.addEventListener('click', async () => {
            statusDiv.innerText = "Sedang memproses wajah...";
            captureBtn.disabled = true;

            const detection = await faceapi.detectSingleFace(video)
                .withFaceLandmarks()
                .withFaceDescriptor();

            if (detection) {
                const descriptor = Array.from(detection.descriptor); // Ubah Float32Array ke Array biasa

                // Kirim ke Backend menggunakan Fetch API
                fetch("{{ route('rekam-wajah.store', $student->id) }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            face_descriptor: descriptor
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            alert("Berhasil! Wajah sudah tersimpan.");
                            window.location.href = "/admin/students";
                        }
                    })
                    .catch(err => {
                        alert("Gagal menyimpan ke server.");
                        captureBtn.disabled = false;
                    });

            } else {
                statusDiv.innerText = "Wajah tidak terdeteksi. Coba lagi di tempat terang.";
                captureBtn.disabled = false;
            }
        });

        loadModels();
    </script>
</body>

</html>
