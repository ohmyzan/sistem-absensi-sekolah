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
        let isProcessing = false;

        // --- PENGATURAN LOKASI (GEOFENCING) ---
        const TARGET_LAT = -6.154613;
        const TARGET_LNG = 106.689156;
        const MAX_RADIUS_METERS = 50;

        let isLocationValid = false;

        function getDistanceFromLatLonInM(lat1, lon1, lat2, lon2) {
            const R = 6371000;
            const dLat = (lat2 - lat1) * (Math.PI / 180);
            const dLon = (lon2 - lon1) * (Math.PI / 180);
            const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                Math.cos(lat1 * (Math.PI / 180)) * Math.cos(lat2 * (Math.PI / 180)) *
                Math.sin(dLon / 2) * Math.sin(dLon / 2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            return Math.round(R * c);
        }

        // 1. Cek Lokasi
        function checkLocation() {
            loaderText.innerText = "Memeriksa Lokasi GPS...";

            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        const userLat = position.coords.latitude;
                        const userLng = position.coords.longitude;
                        const distance = getDistanceFromLatLonInM(userLat, userLng, TARGET_LAT, TARGET_LNG);

                        if (distance <= MAX_RADIUS_METERS) {
                            isLocationValid = true;
                            initSystem();
                        } else {
                            loader.style.display = 'none';
                            updateUI('error', 'Di Luar Area',
                                `Jarak Anda ${distance} meter dari target. Maksimal ${MAX_RADIUS_METERS}m.`);
                        }
                    },
                    (error) => {
                        loader.style.display = 'none';
                        updateUI('error', 'Akses Lokasi Ditolak',
                            'Izinkan akses lokasi GPS di browser Anda untuk absen.');
                    }, {
                        enableHighAccuracy: true
                    }
                );
            } else {
                updateUI('error', 'Tidak Didukung', 'Browser Anda tidak mendukung fitur GPS.');
            }
        }

        // 2. Load Model & Data Wajah
        async function initSystem() {
            try {
                const MODEL_URL = '/models';

                loaderText.innerText = "Memuat Model AI...";

                // TAMBAHAN: Kita muat juga FaceExpressionNet di sini
                await Promise.all([
                    faceapi.nets.ssdMobilenetv1.loadFromUri(MODEL_URL),
                    faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL),
                    faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL),
                    faceapi.nets.faceExpressionNet.loadFromUri(MODEL_URL) // <--- Ini model Anti-Kecurangan
                ]);

                loaderText.innerText = "Membaca Data Wajah Siswa...";

                const labeledDescriptors = studentsData.map(student => {
                    const descriptorArray = new Float32Array(student.face_descriptor);
                    return new faceapi.LabeledFaceDescriptors(
                        student.id + '|' + student.user.name,
                        [descriptorArray]
                    );
                });

                if (labeledDescriptors.length === 0) {
                    alert("Belum ada data wajah siswa di database!");
                } else {
                    faceMatcher = new faceapi.FaceMatcher(labeledDescriptors, 0.55);
                }

                startVideo();

            } catch (error) {
                console.error(error);
                loaderText.innerText = "Error memuat sistem!";
                loaderText.classList.replace('text-white', 'text-red-500');
            }
        }

        // 3. Nyalakan Kamera
        function startVideo() {
            navigator.mediaDevices.getUserMedia({
                    video: {}
                })
                .then(stream => {
                    video.srcObject = stream;
                    loader.style.display = 'none';
                    resetUI();
                })
                .catch(err => {
                    alert("Gagal mengakses kamera: " + err);
                });
        }

        // 4. Looping Deteksi
        video.addEventListener('play', () => {
            const displaySize = {
                width: video.videoWidth,
                height: video.videoHeight
            };
            faceapi.matchDimensions(overlay, displaySize);

            setInterval(async () => {
                if (!faceMatcher || !isLocationValid) return;

                // TAMBAHAN: Kita tambahkan withFaceExpressions()
                const detections = await faceapi.detectAllFaces(video)
                    .withFaceLandmarks()
                    .withFaceExpressions() // <--- Baca ekspresi wajah
                    .withFaceDescriptors();

                const resizedDetections = faceapi.resizeResults(detections, displaySize);

                overlay.getContext('2d').clearRect(0, 0, overlay.width, overlay.height);
                faceapi.draw.drawDetections(overlay, resizedDetections);

                let faceInFrame = false;

                for (const detection of resizedDetections) {
                    const bestMatch = faceMatcher.findBestMatch(detection.descriptor);

                    if (bestMatch.label !== 'unknown') {
                        faceInFrame = true;

                        if (!isProcessing) {
                            const [studentId, studentName] = bestMatch.label.split('|');

                            // LOGIKA ANTI-KECURANGAN (LIVENESS DETECTION)
                            // Cek apakah probabilitas ekspresi senyum (happy) di atas 60% (0.6)
                            const isSmiling = detection.expressions.happy > 0.6;

                            if (isSmiling) {
                                isProcessing = true;
                                processAttendance(studentId, studentName);
                            } else {
                                // Jika wajah cocok tapi belum senyum, suruh senyum!
                                updateUI('warning', 'Wajah Dikenali',
                                    `Halo ${studentName}, silakan TERSENYUM untuk memvalidasi absen.`
                                    );
                            }
                        }
                    }
                }

                // Reset UI otomatis jika wajah hilang dari kamera dan belum diproses
                if (!faceInFrame && !isProcessing && statusBox.classList.contains(
                    'border-yellow-500')) {
                    resetUI();
                }

            }, 1000);
        });

        // 5. Kirim Data ke Laravel
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

        // 6. Fungsi Mengubah Tampilan UI
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
            if (!isLocationValid) return;
            statusBox.className = "w-full p-4 rounded-xl border-2 border-gray-600 bg-gray-700 transition-all duration-300";
            statusIcon.innerText = "📷";
            statusTitle.className = "text-xl font-bold text-white mb-1";
            statusTitle.innerText = "Menunggu Wajah...";
            statusDesc.innerText = "Silakan menatap ke arah kamera";
        }

        checkLocation();
    </script>
</body>

</html>
