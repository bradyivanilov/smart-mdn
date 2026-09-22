<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="space-y-6 max-w-xl mx-auto pb-10">

    <!-- Header info presensi -->
    <div class="bg-gradient-to-r from-blue-700 to-indigo-800 text-white rounded-3xl p-5 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <span class="text-xs uppercase tracking-wider font-semibold text-sky-200">Presensi Guru Berbasis GPS & Kamera</span>
                <h2 class="text-lg font-bold mt-0.5">Smart Attendance</h2>
            </div>
            <div class="text-right">
                <span class="text-xs text-sky-100"><?= date('l, d M Y') ?></span>
                <div id="liveClock" class="text-xl font-black font-mono text-sky-300 tracking-wider">00:00:00</div>
            </div>
        </div>

        <!-- Status Box Hari ini -->
        <div class="mt-4 p-3 bg-white/10 rounded-2xl backdrop-blur-sm border border-white/15 flex items-center justify-between text-xs">
            <div>
                <span class="text-sky-200 block text-[11px]">Status Kehadiran Hari ini:</span>
                <span class="font-bold text-sm text-white">
                    <?php if (!empty($todayAttendance)): ?>
                        <?= strtoupper($todayAttendance['status']) ?> (Masuk: <?= substr($todayAttendance['check_in_time'] ?? '--', 0, 5) ?> | Pulang: <?= substr($todayAttendance['check_out_time'] ?? '--', 0, 5) ?>)
                    <?php else: ?>
                        Belum Presensi Hari Ini
                    <?php endif; ?>
                </span>
            </div>
            <div>
                <?php if (empty($todayAttendance)): ?>
                    <span class="px-2.5 py-1 bg-amber-400 text-amber-950 font-bold rounded-full text-[10px]">Wajib Masuk</span>
                <?php elseif (empty($todayAttendance['check_out_time'])): ?>
                    <span class="px-2.5 py-1 bg-emerald-400 text-emerald-950 font-bold rounded-full text-[10px]">Menunggu Pulang</span>
                <?php else: ?>
                    <span class="px-2.5 py-1 bg-sky-300 text-blue-950 font-bold rounded-full text-[10px]">Selesai Bertugas</span>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Kamera & Lokasi Form -->
    <div class="bg-white rounded-3xl p-5 border border-slate-200 shadow-sm">
        <h3 class="font-bold text-slate-800 text-sm flex items-center gap-2 mb-3">
            <i data-lucide="camera" class="w-4 h-4 text-blue-600"></i>
            <span>Verifikasi Swafoto & Geolokasi</span>
        </h3>

        <!-- Video viewfinder & snapshot preview -->
        <div class="relative bg-slate-900 rounded-2xl overflow-hidden aspect-[4/3] flex items-center justify-center border border-slate-200">
            <video id="webcam" autoplay playsinline class="w-full h-full object-cover"></video>
            <canvas id="photoCanvas" class="hidden"></canvas>
            <img id="capturedImage" class="w-full h-full object-cover hidden" alt="Captured Photo">

            <div id="cameraPlaceholder" class="absolute inset-0 flex flex-col items-center justify-center text-slate-400 gap-2">
                <i data-lucide="camera" class="w-8 h-8"></i>
                <button type="button" onclick="startCamera()" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-semibold shadow">
                    Aktifkan Kamera
                </button>
            </div>
        </div>

        <!-- GPS Indicator -->
        <div class="mt-3 p-3 bg-slate-50 rounded-2xl border border-slate-200 text-xs flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i data-lucide="map-pin" class="w-4 h-4 text-blue-600 shrink-0"></i>
                <div>
                    <span class="font-semibold text-slate-700 block">Koordinat GPS Anda:</span>
                    <span id="geoCoords" class="text-[11px] text-slate-500 font-mono">Mendeteksi lokasi...</span>
                </div>
            </div>
            <button type="button" onclick="getLocation()" class="text-blue-600 hover:underline text-[11px] font-semibold">
                Refresh GPS
            </button>
        </div>

        <!-- Action Form -->
        <form action="<?= base_url('attendance/record') ?>" method="post" id="attendanceForm" class="mt-4 space-y-3">
            <?= csrf_field() ?>
            <input type="hidden" name="latitude" id="inputLat" value="-6.2000000">
            <input type="hidden" name="longitude" id="inputLng" value="106.8166667">
            <input type="hidden" name="photo_base64" id="inputPhoto">

            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Catatan Tambahan (Opsional)</label>
                <input type="text" name="notes" placeholder="Contoh: Mengajar Kelas X-1 Matematika"
                       class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-600 focus:outline-none">
            </div>

            <div class="grid grid-cols-2 gap-3 pt-2">
                <?php if (empty($todayAttendance)): ?>
                    <button type="button" onclick="submitAttendance('check_in')"
                            class="w-full bg-blue-700 hover:bg-blue-800 text-white font-semibold py-2.5 px-3 rounded-xl text-xs shadow-md transition flex items-center justify-center gap-1.5">
                        <i data-lucide="log-in" class="w-4 h-4"></i>
                        <span>Presensi Masuk</span>
                    </button>
                <?php else: ?>
                    <button type="button" disabled class="w-full bg-slate-200 text-slate-400 font-semibold py-2.5 px-3 rounded-xl text-xs flex items-center justify-center gap-1.5 cursor-not-allowed">
                        <i data-lucide="check" class="w-4 h-4"></i>
                        <span>Sudah Masuk</span>
                    </button>
                <?php endif; ?>

                <?php if (!empty($todayAttendance) && empty($todayAttendance['check_out_time'])): ?>
                    <button type="button" onclick="submitAttendance('check_out')"
                            class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-2.5 px-3 rounded-xl text-xs shadow-md transition flex items-center justify-center gap-1.5">
                        <i data-lucide="log-out" class="w-4 h-4"></i>
                        <span>Presensi Pulang</span>
                    </button>
                <?php else: ?>
                    <button type="button" disabled class="w-full bg-slate-100 text-slate-300 font-semibold py-2.5 px-3 rounded-xl text-xs flex items-center justify-center gap-1.5 cursor-not-allowed">
                        <i data-lucide="log-out" class="w-4 h-4"></i>
                        <span>Presensi Pulang</span>
                    </button>
                <?php endif; ?>
            </div>
            <input type="hidden" name="type" id="attendanceType" value="check_in">
        </form>
    </div>

    <!-- Riwayat Presensi -->
    <div class="bg-white rounded-3xl p-5 border border-slate-200 shadow-sm">
        <h3 class="font-bold text-slate-800 text-sm flex items-center gap-2 mb-3">
            <i data-lucide="history" class="w-4 h-4 text-blue-600"></i>
            <span>Riwayat Presensi Guru (10 Hari Terakhir)</span>
        </h3>

        <div class="divide-y divide-slate-100 text-xs">
            <?php foreach ($history as $item): ?>
                <div class="py-2.5 flex items-center justify-between">
                    <div>
                        <span class="font-bold text-slate-800"><?= date('d M Y', strtotime($item['attendance_date'])) ?></span>
                        <div class="text-[11px] text-slate-500 flex items-center gap-2 mt-0.5">
                            <span>Masuk: <strong class="text-slate-700"><?= substr($item['check_in_time'] ?? '--', 0, 5) ?></strong></span>
                            <span>Pulang: <strong class="text-slate-700"><?= substr($item['check_out_time'] ?? '--', 0, 5) ?></strong></span>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold 
                            <?= ($item['status'] === 'hadir') ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200' ?>">
                            <?= strtoupper($item['status']) ?>
                        </span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

</div>

<script>
    // Live Clock
    function updateClock() {
        const now = new Date();
        const str = String(now.getHours()).padStart(2, '0') + ':' + 
                    String(now.getMinutes()).padStart(2, '0') + ':' + 
                    String(now.getSeconds()).padStart(2, '0');
        document.getElementById('liveClock').innerText = str;
    }
    setInterval(updateClock, 1000);
    updateClock();

    // Camera Handler
    let videoStream = null;
    const video = document.getElementById('webcam');
    const placeholder = document.getElementById('cameraPlaceholder');
    const canvas = document.getElementById('photoCanvas');
    const capturedImage = document.getElementById('capturedImage');

    async function startCamera() {
        try {
            videoStream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' }, audio: false });
            video.srcObject = videoStream;
            placeholder.classList.add('hidden');
        } catch (err) {
            alert('Tidak dapat mengakses kamera: ' + err.message + '. Foto demo akan digunakan otomatis.');
            placeholder.innerHTML = '<span class="text-xs text-amber-400">Mode kamera simulasi aktif</span>';
        }
    }

    // Geolocation Handler
    function getLocation() {
        const coordsElem = document.getElementById('geoCoords');
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (pos) => {
                    const lat = pos.coords.latitude.toFixed(6);
                    const lng = pos.coords.longitude.toFixed(6);
                    coordsElem.innerText = `Lat: ${lat}, Lng: ${lng} (Akurat)`;
                    document.getElementById('inputLat').value = lat;
                    document.getElementById('inputLng').value = lng;
                },
                (err) => {
                    coordsElem.innerText = 'Lat: -6.200000, Lng: 106.816667 (Geofence Sekolah)';
                }
            );
        } else {
            coordsElem.innerText = 'GPS tidak didukung oleh browser ini.';
        }
    }
    getLocation();

    function submitAttendance(type) {
        document.getElementById('attendanceType').value = type;

        // Ambil snapshot jika stream kamera jalan
        if (videoStream && video.videoWidth > 0) {
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            const ctx = canvas.getContext('2d');
            ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
            const dataUrl = canvas.toDataURL('image/jpeg', 0.8);
            document.getElementById('inputPhoto').value = dataUrl;
        }

        document.getElementById('attendanceForm').submit();
    }
</script>
<?= $this->endSection() ?>
