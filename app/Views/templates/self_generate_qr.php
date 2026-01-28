<!doctype html>
<html lang="id">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Generate QR Buka Bersama</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600;700;800&family=Manrope:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
      tailwind.config = {
        theme: {
          extend: {
            fontFamily: {
              display: ["Fraunces", "serif"],
              body: ["Manrope", "system-ui", "sans-serif"]
            },
            colors: {
              sunset: {
                50: "#fff6ed",
                100: "#ffe8d2",
                200: "#ffd0a0",
                300: "#ffb26d",
                400: "#ff8d3a",
                500: "#f16b17",
                600: "#d55308",
                700: "#a73f08",
                800: "#7b2f0b",
                900: "#542209"
              },
              night: {
                800: "#1e1b4b",
                900: "#11132f"
              }
            },
            keyframes: {
              float: {
                "0%, 100%": { transform: "translateY(0px)" },
                "50%": { transform: "translateY(-12px)" }
              },
              glow: {
                "0%, 100%": { opacity: "0.6" },
                "50%": { opacity: "1" }
              }
            },
            animation: {
              float: "float 6s ease-in-out infinite",
              glow: "glow 3.5s ease-in-out infinite"
            }
          }
        }
      };
    </script>
    <style>
      body {
        background-image:
          radial-gradient(circle at 15% 10%, rgba(14, 165, 233, 0.18), transparent 45%),
          radial-gradient(circle at 85% 15%, rgba(34, 197, 94, 0.18), transparent 40%),
          radial-gradient(circle at 40% 85%, rgba(16, 185, 129, 0.2), transparent 55%),
          linear-gradient(135deg, #ffffff 0%, #eef7ff 45%, #e9fff4 100%);
      }
    </style>
  </head>
  <body class="font-body text-slate-900 min-h-screen">
    <main class="relative overflow-hidden">
      <div class="absolute inset-0 pointer-events-none">
        <div class="absolute -top-28 -left-20 h-64 w-64 rounded-full bg-sunset-400/30 blur-3xl animate-glow"></div>
        <div class="absolute top-24 right-10 h-52 w-52 rounded-full bg-amber-300/20 blur-2xl animate-glow"></div>
        <div class="absolute bottom-10 left-1/3 h-72 w-72 rounded-full bg-night-900/60 blur-3xl"></div>
        <div class="absolute left-1/2 top-32 h-24 w-24 -translate-x-1/2 rounded-full border border-amber-200/50 bg-amber-100/10 shadow-[0_0_30px_rgba(251,191,36,0.35)]"></div>
        <div class="absolute left-1/2 top-32 h-24 w-24 -translate-x-1/2 rounded-full border border-transparent bg-gradient-to-b from-amber-200/70 to-transparent blur-sm"></div>
      </div>

      <section class="relative mx-auto flex w-full max-w-6xl flex-col-reverse gap-10 px-5 pb-16 pt-8 sm:px-6 lg:flex-row lg:items-center lg:px-10">
        <div class="flex-1 space-y-6 lg:space-y-7">
          <div class="flex items-center gap-3">
            <span class="rounded-full border border-emerald-200 bg-emerald-50 px-4 py-1 text-xs uppercase tracking-[0.3em] text-emerald-700">Ramadhan Kareem</span>
            <span class="text-xs text-slate-500">Buka Bersama 1447 H</span>
          </div>
          <h1 class="font-display text-3xl leading-tight sm:text-5xl lg:text-6xl">
            Unduh QR Undangan Kehadiran Buka Bersama
          </h1>
          <p class="max-w-xl text-sm text-slate-600 sm:text-lg">
            Masukkan nomor HP karyawan untuk mendapatkan QR Code kehadiran. Sekali klik, QR akan langsung terunduh.
          </p>
          <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
            <div class="rounded-2xl border border-emerald-100 bg-white/80 px-4 py-3 text-sm sm:min-w-[220px]">
              <span class="block text-emerald-700/80">Lokasi</span>
              <span class="font-semibold">
                Museum Monumen Pangeran Diponegoro Sasana Wiratama <br>
                <a class="mt-2 inline-flex items-center gap-2 text-sm text-sky-600 hover:text-sky-700" target="_blank" rel="noopener noreferrer" href="https://maps.app.goo.gl/9P2V7QeVhZSqhA3k6">
                    <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-sky-100 text-sky-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 01.553-.894L9 2m0 18l6-3m-6 3V2m6 15l5.447 2.724A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 13l-6 3" />
                        </svg>
                    </span>
                  Link Lokasi
                </a>
              </span>
            </div>
            <div class="rounded-2xl border border-emerald-100 bg-white/80 px-4 py-3 text-sm sm:min-w-[190px]">
              <span class="block text-emerald-700/80">Waktu</span>
              <span class="font-semibold">Sesuai yang tertera di undangan masing-masing</span>
            </div>
          </div>
        </div>

        <div class="relative flex-1">
          <div class="absolute -right-6 -top-6 hidden h-24 w-24 rounded-full border border-white/20 bg-white/10 lg:block"></div>
          <div class="absolute -left-10 bottom-0 hidden h-28 w-28 rounded-full bg-amber-300/10 blur-2xl lg:block"></div>
          <div class="relative overflow-hidden rounded-3xl border border-emerald-100/70 bg-white/85 p-6 shadow-[0_30px_60px_rgba(15,23,42,0.15)] backdrop-blur sm:p-8">
            <div class="flex items-center justify-between gap-3 sm:gap-4">
              <img src="<?= base_url('uploads/logo/logo_6951e9280661f8-73637506.png'); ?>" alt="Manna Kampus" style="width: 8rem;" class="h-9 w-20 object-contain sm:h-12 sm:w-auto">
              <div class="flex-1 text-right text-xs text-slate-500">
                <p class="uppercase tracking-[0.25em]">QR BUKBER</p>
                <p class="font-semibold text-slate-900">Manna Kampus</p>
              </div>
            </div>

            <?php if (session()->getFlashdata('msg')) : ?>
              <div class="mt-6 rounded-2xl border <?= session()->getFlashdata('error') ? 'border-rose-200 bg-rose-100 text-slate-900' : 'border-emerald-200 bg-emerald-100 text-emerald-800'; ?> px-4 py-3 text-sm">
                <?= session()->getFlashdata('msg') ?>
              </div>
            <?php endif; ?>

            <form class="mt-7 space-y-5" action="<?= base_url('qr/siswa/download'); ?>" method="get">
              <label class="block text-sm font-semibold text-emerald-700" for="no_hp">Nomor HP Pegawai</label>
              <div class="relative">
                <input id="no_hp" type="tel" name="no_hp" required autocomplete="off" inputmode="tel" placeholder="Contoh: 081234567890" class="w-full rounded-2xl border border-emerald-200 bg-white px-4 py-3.5 text-base text-slate-900 placeholder:text-slate-400 shadow-inner shadow-emerald-100/60 focus:border-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-300/50">
                <span class="pointer-events-none absolute right-4 top-3.5 text-xs text-slate-400">HP</span>
              </div>
              <button type="submit" class="group flex w-full items-center justify-between rounded-2xl bg-gradient-to-r from-emerald-500 via-teal-500 to-sky-500 px-5 py-3.5 text-base font-semibold text-white shadow-lg shadow-emerald-300/40 transition hover:translate-y-[-1px] hover:shadow-emerald-300/60">
                <span>Download QR</span>
                <span class="text-lg transition group-hover:translate-x-1">-&gt;</span>
              </button>
            </form>

            <div class="mt-6 flex items-center gap-3 text-xs text-slate-500">
              <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-emerald-50 text-sm font-semibold text-emerald-600">QR</span>
              <p>QR Code akan terunduh otomatis setelah nomor HP tervalidasi.</p>
            </div>
          </div>
        </div>
      </section>
    </main>
  </body>
</html>
