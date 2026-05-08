<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Mesin Absensi - SMK Jakarta 1</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="{{ asset('js/face-api.min.js') }}"></script>
</head>

<body class="bg-gray-900 text-white min-h-screen flex flex-col items-center justify-center p-4">

    <div class="max-w-3xl w-full bg-gray-800 rounded-2xl shadow-2xl overflow-hidden flex flex-col md:flex-row">

        <div class="w-full md:w-2/3 bg-black relative flex items-center justify-center min-h-[400px]">
            <video id="video" autoplay muted playsinline
                class="absolute top-0 left-0 w-full h-full object-cover"></video>
            <canvas id="overlay" class="absolute top-0 left-0 w-full h-full"></canvas>

            <div id="loader"
                class="absolute inset-0 flex flex-col items-center justify-center bg-black bg-opacity-80 z-10">
                <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-b-4 border-blue-500 mb-4"></div>
                <p id="loader-text" class="text-lg font-semibold tracking-wider animate-pulse">Menyiapkan Mesin AI...
                </p>
            </div>
        </div>

        <div
            class="w-full md:w-1/3 p-6 flex flex-col justify-center items-center text-center bg-gray-800 border-t md:border-t-0 md:border-l border-gray-700">
            <h1 class="text-2xl font-bold text-blue-400 mb-1">SMK Jakarta 1</h1>
            <p class="text-gray-400 text-sm mb-8">Sistem Absensi Otomatis</p>

            <div id="status-box"
                class="w-full p-4 rounded-xl border-2 border-gray-600 bg-gray-700 transition-all duration-300">
                <div id="status-icon" class="text-4xl mb-2">📷</div>
                <h2 id="status-title" class="text-xl font-bold text-white mb-1">Menunggu Wajah...</h2>
                <p id="status-desc" class="text-sm text-gray-300">Silakan menatap ke arah kamera</p>
            </div>

            <a href="/admin" class="mt-8 text-xs text-gray-500 hover:text-white transition-colors">Kembali ke Panel
                Admin</a>
        </div>
    </div>

    <script>
        const studentsData = @json($students);
    </script>

    <script>
        const video = document.getElementById('video');
        const overlay = document.getElementById('overlay');
        const loader = document.getElementById('loader');
        const loaderText = document.getElementById('loader-text');

        const statusBox = document.getElementById('status-box');
        const statusIcon = document.getElementById('status-icon');
        const statusTitle = document.getElementById('status-title');
        const statusDesc = document.getElementById('status-desc');

        let faceMatcher;
        let isProcessing = false; // Mencegah spam API

        // 1. Load Model & Data Wajah
        async function initSystem() {
            try {
                const MODEL_URL = '/models';

                loaderText.innerText = "Memuat Model AI...";
                await Promise.all([
                    faceapi.nets.ssdMobilenetv1.loadFromUri(MODEL_URL),
                    faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL),
                    faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL)
                ]);

                loaderText.innerText = "Membaca Data Wajah Siswa...";

                // Buat LabeledFaceDescriptors dari data database
                const labeledDescriptors = studentsData.map(student => {
                    // Konversi array kembali menjadi Float32Array sesuai standar AI
                    const descriptorArray = new Float32Array(student.face_descriptor);
                    // Kita gabungkan ID dan Nama untuk nanti di-split
                    return new faceapi.LabeledFaceDescriptors(
                        student.id + '|' + student.user.name,
                        [descriptorArray]
                    );
                });

                if (labeledDescriptors.length === 0) {
                    alert("Belum ada data wajah siswa di database!");
                } else {
                    // Inisialisasi FaceMatcher dengan threshold kecocokan 0.55 (makin kecil makin ketat)
                    faceMatcher = new faceapi.FaceMatcher(labeledDescriptors, 0.55);
                }

                startVideo();

            } catch (error) {
                console.error(error);
                loaderText.innerText = "Error memuat sistem!";
                loaderText.classList.replace('text-white', 'text-red-500');
            }
        }

        // 2. Nyalakan Kamera
        function startVideo() {
            navigator.mediaDevices.getUserMedia({
                    video: {}
                })
                .then(stream => {
                    video.srcObject = stream;
                    loader.style.display = 'none';
                })
                .catch(err => {
                    alert("Gagal mengakses kamera: " + err);
                });
        }

        // 3. Looping Deteksi saat video menyala
        video.addEventListener('play', () => {
            // Sesuaikan ukuran canvas dengan video
            const displaySize = {
                width: video.videoWidth,
                height: video.videoHeight
            };
            faceapi.matchDimensions(overlay, displaySize);

            setInterval(async () => {
                if (!faceMatcher) return;

                // Deteksi semua wajah di depan kamera
                const detections = await faceapi.detectAllFaces(video)
                    .withFaceLandmarks()
                    .withFaceDescriptors();

                const resizedDetections = faceapi.resizeResults(detections, displaySize);

                // Bersihkan canvas dari kotak sebelumnya
                overlay.getContext('2d').clearRect(0, 0, overlay.width, overlay.height);

                // Gambar kotak wajah untuk visual
                faceapi.draw.drawDetections(overlay, resizedDetections);

                // Proses setiap wajah yang terdeteksi
                for (const detection of resizedDetections) {
                    const bestMatch = faceMatcher.findBestMatch(detection.descriptor);

                    if (bestMatch.label !== 'unknown' && !isProcessing) {
                        const [studentId, studentName] = bestMatch.label.split('|');

                        // Kunci agar tidak melakukan request berkali-kali untuk orang yang sama
                        isProcessing = true;
                        processAttendance(studentId, studentName);
                    }
                }
            }, 1000); // Lakukan scan setiap 1 detik untuk menghemat performa
        });

        // 4. Kirim Data ke Laravel
        function processAttendance(studentId, studentName) {
            updateUI('loading', `Memproses...`, studentName);

            fetch("{{ route('absensi.store') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        student_id: studentId
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        updateUI('success', 'Berhasil Absen!', studentName);
                    } else {
                        updateUI('warning', 'Sudah Absen!', data.message);
                    }

                    // Beri jeda 3 detik sebelum bisa scan wajah lain
                    setTimeout(() => {
                        resetUI();
                        isProcessing = false;
                    }, 3000);
                })
                .catch(err => {
                    updateUI('error', 'Terjadi Kesalahan', 'Gagal menghubungi server');
                    setTimeout(() => {
                        resetUI();
                        isProcessing = false;
                    }, 3000);
                });
        }

        // 5. Fungsi Mengubah Tampilan UI
        function updateUI(type, title, desc) {
            statusBox.className = "w-full p-4 rounded-xl border-2 transition-all duration-300";

            if (type === 'success') {
                statusBox.classList.add('border-green-500', 'bg-green-900', 'bg-opacity-50');
                statusIcon.innerText = "✅";
                statusTitle.classList.replace('text-white', 'text-green-400');
            } else if (type === 'warning') {
                statusBox.classList.add('border-yellow-500', 'bg-yellow-900', 'bg-opacity-50');
                statusIcon.innerText = "⚠️";
                statusTitle.classList.replace('text-white', 'text-yellow-400');
            } else if (type === 'loading') {
                statusBox.classList.add('border-blue-500', 'bg-blue-900', 'bg-opacity-50');
                statusIcon.innerText = "⏳";
                statusTitle.classList.replace('text-white', 'text-blue-400');
            } else {
                statusBox.classList.add('border-red-500', 'bg-red-900', 'bg-opacity-50');
                statusIcon.innerText = "❌";
                statusTitle.classList.replace('text-white', 'text-red-400');
            }

            statusTitle.innerText = title;
            statusDesc.innerText = desc;
        }

        function resetUI() {
            statusBox.className = "w-full p-4 rounded-xl border-2 border-gray-600 bg-gray-700 transition-all duration-300";
            statusIcon.innerText = "📷";
            statusTitle.className = "text-xl font-bold text-white mb-1";
            statusTitle.innerText = "Menunggu Wajah...";
            statusDesc.innerText = "Silakan menatap ke arah kamera";
        }

        // Mulai sistem
        initSystem();
    </script>
</body>

</html>
