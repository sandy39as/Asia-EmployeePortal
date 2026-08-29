<div id="createLeaveModal" class="fixed inset-0 z-[110] hidden items-center justify-center bg-slate-900/50 px-3 py-4 backdrop-blur-xs sm:px-4">
    <div id="createLeaveModalBox" class="max-h-[92vh] w-full max-w-lg scale-95 overflow-y-auto rounded-2xl border border-[#e2e8f0] bg-white opacity-0 shadow-2xl transition-all duration-200">
        
        {{-- HEADER --}}
        <div class="sticky top-0 z-20 flex items-center justify-between border-b border-[#e2e8f0] bg-white/95 px-5 py-4 backdrop-blur">
            <div>
                <h3 class="text-base font-extrabold text-slate-900">Buat Pengajuan</h3>
                <p class="text-xs text-slate-500 font-medium">Izin, cuti, atau sakit.</p>
            </div>
            <button type="button" data-close-create-leave class="p-1 rounded-lg text-slate-400 hover:text-slate-700">✕</button>
        </div>

        <form id="createLeaveForm" method="POST" action="{{ route('leave-requests.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="p-5 space-y-4">
                
                {{-- INFO USER MINI --}}
                <div class="p-3.5 rounded-xl bg-[#f8fafc] border border-[#e2e8f0]">
                    <div class="font-extrabold text-slate-900 text-sm sm:text-base">{{ $employee->nama }}</div>
                    <div class="text-xs text-slate-500 font-bold mt-0.5">{{ $employee->employee_code }} • {{ $employee->jabatan ?? '-' }}</div>
                </div>

                <div id="createLeaveError" class="hidden rounded-xl border border-rose-200 bg-rose-50 p-3 text-xs sm:text-sm font-bold text-rose-800"></div>

                {{-- JENIS PENGAJUAN --}}
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-2">Jenis Pengajuan</label>
                    <div class="grid grid-cols-3 gap-2.5">
                        <label class="cursor-pointer">
                            <input type="radio" name="jenis" value="izin" class="peer sr-only" required>
                            <div class="rounded-xl border border-[#d1d5db] bg-white py-3 text-center text-sm font-extrabold text-slate-700 peer-checked:border-slate-900 peer-checked:bg-slate-900 peer-checked:text-white transition">
                                Izin
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="jenis" value="cuti" class="peer sr-only">
                            <div class="rounded-xl border border-[#d1d5db] bg-white py-3 text-center text-sm font-extrabold text-slate-700 peer-checked:border-slate-900 peer-checked:bg-slate-900 peer-checked:text-white transition">
                                Cuti
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="jenis" value="sakit" class="peer sr-only">
                            <div class="rounded-xl border border-[#d1d5db] bg-white py-3 text-center text-sm font-extrabold text-slate-700 peer-checked:border-slate-900 peer-checked:bg-slate-900 peer-checked:text-white transition">
                                Sakit
                            </div>
                        </label>
                    </div>
                </div>

                {{-- DURASI --}}
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Durasi</label>
                    <select name="durasi_type" id="modalDurasiType" class="w-full rounded-xl border border-[#d1d5db] bg-white px-3.5 py-2.5 text-sm font-bold text-slate-800 focus:border-slate-500 focus:outline-none" required>
                        <option value="full_day">Sehari Penuh / Beberapa Hari</option>
                        <option value="hourly">Beberapa Jam</option>
                    </select>
                </div>

                {{-- TANGGAL --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Tanggal Mulai</label>
                        <input type="date" name="tanggal_mulai" id="modalTanggalMulai" class="w-full rounded-xl border border-[#d1d5db] bg-white px-3.5 py-2.5 text-sm text-slate-800 focus:border-slate-500 focus:outline-none font-medium" required>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Tanggal Selesai</label>
                        <input type="date" name="tanggal_selesai" id="modalTanggalSelesai" class="w-full rounded-xl border border-[#d1d5db] bg-white px-3.5 py-2.5 text-sm text-slate-800 focus:border-slate-500 focus:outline-none font-medium" required>
                    </div>
                </div>

                {{-- JAM --}}
                <div id="modalHourlyFields" class="hidden grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Jam Mulai</label>
                        <input type="time" name="jam_mulai" class="w-full rounded-xl border border-[#d1d5db] bg-white px-3.5 py-2.5 text-sm text-slate-800 focus:border-slate-500 focus:outline-none font-medium">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Jam Selesai</label>
                        <input type="time" name="jam_selesai" class="w-full rounded-xl border border-[#d1d5db] bg-white px-3.5 py-2.5 text-sm text-slate-800 focus:border-slate-500 focus:outline-none font-medium">
                    </div>
                </div>

                {{-- ALASAN --}}
                <div>
                    <div class="flex justify-between items-center mb-1.5">
                        <label class="text-xs font-bold uppercase tracking-wider text-slate-600">Alasan</label>
                        <span id="leaveReasonCounter" class="text-xs text-slate-400 font-bold">0/2000</span>
                    </div>
                    <textarea name="alasan" id="leaveReason" rows="3" maxlength="2000" placeholder="Tulis alasan..." class="w-full rounded-xl border border-[#d1d5db] bg-white px-3.5 py-2.5 text-sm text-slate-800 placeholder-slate-400 focus:border-slate-500 focus:outline-none font-medium" required></textarea>
                </div>

                {{-- LAMPIRAN --}}
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Lampiran (Opsional)</label>
                    <label class="block cursor-pointer rounded-xl border border-dashed border-[#d1d5db] bg-[#f8fafc] p-3.5 hover:bg-slate-100 transition">
                        <input type="file" name="lampiran" id="leaveAttachment" accept=".pdf,.jpg,.jpeg,.png" class="hidden">
                        <div class="flex items-center gap-3">
                            <span class="text-xs sm:text-sm font-bold text-slate-700" id="leaveAttachmentName">Pilih dokumen / foto</span>
                            <span class="text-xs text-slate-400 font-semibold ml-auto">Maks 5MB</span>
                        </div>
                    </label>
                </div>

            </div>

            {{-- FOOTER --}}
            <div class="sticky bottom-0 z-20 flex justify-end gap-2 border-t border-[#e2e8f0] bg-white/95 px-5 py-3.5 backdrop-blur">
                <button type="button" data-close-create-leave class="rounded-xl border border-[#d1d5db] px-5 py-2 text-xs sm:text-sm font-bold text-slate-700 hover:bg-slate-50">Batal</button>
                <button type="submit" id="submitLeaveRequest" class="rounded-xl bg-slate-900 hover:bg-black px-6 py-2 text-xs sm:text-sm font-extrabold text-white transition">Kirim Pengajuan</button>
            </div>
        </form>
    </div>
</div>

<div id="employeePortalToast" class="pointer-events-none fixed right-4 top-4 z-[200] hidden max-w-sm rounded-xl bg-slate-900 px-5 py-3 text-sm font-bold text-white shadow-2xl transition duration-300"></div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const modal = document.getElementById('createLeaveModal');
        const box = document.getElementById('createLeaveModalBox');
        const form = document.getElementById('createLeaveForm');
        const errorBox = document.getElementById('createLeaveError');
        const durasi = document.getElementById('modalDurasiType');
        const mulai = document.getElementById('modalTanggalMulai');
        const selesai = document.getElementById('modalTanggalSelesai');
        const hourly = document.getElementById('modalHourlyFields');
        const submitBtn = document.getElementById('submitLeaveRequest');
        const reason = document.getElementById('leaveReason');
        const reasonCounter = document.getElementById('leaveReasonCounter');
        const attachment = document.getElementById('leaveAttachment');
        const attachmentName = document.getElementById('leaveAttachmentName');

        function openModal() {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            setTimeout(() => box.classList.remove('scale-95', 'opacity-0'), 10);
            document.body.classList.add('overflow-hidden');
        }

        function closeModal() {
            box.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                modal.classList.remove('flex');
                modal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }, 180);
        }

        document.addEventListener('click', (e) => {
            if (e.target.closest('[data-open-create-leave]')) openModal();
            if (e.target.closest('[data-close-create-leave]') || e.target === modal) closeModal();
        });

        durasi?.addEventListener('change', () => {
            if (durasi.value === 'hourly') {
                hourly.classList.remove('hidden');
                if (mulai.value) selesai.value = mulai.value;
                selesai.readOnly = true;
            } else {
                hourly.classList.add('hidden');
                selesai.readOnly = false;
            }
        });

        mulai?.addEventListener('change', () => {
            selesai.min = mulai.value;
            if (durasi.value === 'hourly') selesai.value = mulai.value;
        });

        reason?.addEventListener('input', () => {
            reasonCounter.textContent = `${reason.value.length}/2000`;
        });

        attachment?.addEventListener('change', () => {
            attachmentName.textContent = attachment.files?.[0]?.name || 'Pilih dokumen / foto';
        });

        form?.addEventListener('submit', async (e) => {
            e.preventDefault();
            errorBox.classList.add('hidden');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = 'Mengirim...';

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: new FormData(form),
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await response.json();

                if (!response.ok) {
                    const messages = data.errors ? Object.values(data.errors).flat() : [data.message || 'Gagal mengirim pengajuan.'];
                    errorBox.innerHTML = messages.map(m => `<div>• ${m}</div>`).join('');
                    errorBox.classList.remove('hidden');
                    return;
                }

                sessionStorage.setItem('employeePortalToast', data.message || 'Pengajuan berhasil dikirim.');
                window.location.reload();
            } catch (err) {
                errorBox.textContent = 'Terjadi kesalahan. Coba beberapa saat lagi.';
                errorBox.classList.remove('hidden');
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        });

        const toastMsg = sessionStorage.getItem('employeePortalToast');
        if (toastMsg) {
            sessionStorage.removeItem('employeePortalToast');
            const toast = document.getElementById('employeePortalToast');
            toast.textContent = toastMsg;
            toast.classList.remove('hidden');
            setTimeout(() => toast.classList.add('hidden'), 3500);
        }
    });
</script>
