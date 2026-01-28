<!doctype html>
<html lang="id">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Data Panitia</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Gloock&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
      tailwind.config = {
        theme: {
          extend: {
            fontFamily: {
              display: ["Gloock", "serif"],
              body: ["Plus Jakarta Sans", "system-ui", "sans-serif"]
            },
            colors: {
              ramadhan: {
                50: "#f5fff8",
                100: "#e2ffea",
                200: "#b7f6cc",
                300: "#83e7ac",
                400: "#4fd48d",
                500: "#22b56d",
                600: "#149457",
                700: "#0f7446",
                800: "#0e5c3a",
                900: "#0b442c"
              }
            },
            keyframes: {
              float: {
                "0%, 100%": { transform: "translateY(0px)" },
                "50%": { transform: "translateY(-10px)" }
              }
            },
            animation: {
              float: "float 6s ease-in-out infinite"
            }
          }
        }
      };
    </script>
    <style>
      body {
        background-image:
          radial-gradient(circle at 10% 15%, rgba(34, 197, 94, 0.18), transparent 45%),
          radial-gradient(circle at 90% 20%, rgba(16, 185, 129, 0.18), transparent 40%),
          radial-gradient(circle at 45% 85%, rgba(20, 148, 87, 0.2), transparent 50%),
          linear-gradient(135deg, #ffffff 0%, #f2fff6 50%, #e8fff1 100%);
      }
      .table-shadow {
        box-shadow: 0 24px 60px rgba(15, 60, 38, 0.18);
      }
    </style>
  </head>
  <body class="min-h-screen font-body text-slate-900">
    <main class="relative overflow-hidden">
      <div class="absolute inset-0 pointer-events-none">
        <div class="absolute -top-20 -left-16 h-56 w-56 rounded-full bg-ramadhan-300/40 blur-3xl animate-float"></div>
        <div class="absolute top-24 right-10 h-40 w-40 rounded-full bg-ramadhan-200/40 blur-2xl"></div>
        <div class="absolute bottom-8 left-1/3 h-64 w-64 rounded-full bg-ramadhan-800/20 blur-3xl"></div>
      </div>

      <section class="relative mx-auto w-full max-w-6xl px-5 pb-16 pt-10 sm:px-6 lg:px-10">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
          <div class="space-y-3">
            <span class="inline-flex items-center gap-2 rounded-full border border-ramadhan-200 bg-white/80 px-4 py-1 text-xs uppercase tracking-[0.3em] text-ramadhan-700">
              Ramadhan 1447 H
            </span>
            <h1 class="font-display text-3xl leading-tight sm:text-4xl lg:text-5xl">
              Data Panitia & QR Kehadiran
            </h1>
            <p class="max-w-2xl text-sm text-slate-600 sm:text-base">
              Cari nama Panitia dan unduh QR Code kehadiran langsung dari tabel. Daftar diperbarui otomatis sesuai data terkini.
            </p>
          </div>

          <div class="rounded-3xl border border-ramadhan-100 bg-white/80 px-4 py-4 shadow-lg shadow-ramadhan-200/50 backdrop-blur sm:px-5">
            <div class="text-xs text-slate-500">Total data</div>
            <div class="text-2xl font-semibold text-ramadhan-700">
              <span id="totalCount"><?= count($guru ?? []); ?></span>
            </div>
          </div>
        </div>

        <div class="mt-10 grid gap-4 lg:grid-cols-[1.4fr,1fr]">
          <div class="rounded-3xl border border-ramadhan-100 bg-white/90 px-5 py-5 shadow-lg shadow-ramadhan-200/40 backdrop-blur">
            <label class="text-sm font-semibold text-ramadhan-700" for="searchGuru">Cari Nama Panitia</label>
            <div class="mt-3 flex items-center gap-3 rounded-2xl border border-ramadhan-200 bg-white px-4 py-3 shadow-inner shadow-ramadhan-100/60">
              <span class="text-ramadhan-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m1.85-5.65a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
              </span>
              <input id="searchGuru" type="text" autocomplete="off" placeholder="Contoh: Nur Aini" class="w-full border-none bg-transparent text-base text-slate-900 placeholder:text-slate-400 focus:outline-none">
            </div>
            <p class="mt-3 text-xs text-slate-500">Hasil pencarian: <span id="resultCount"><?= count($guru ?? []); ?></span> guru</p>
          </div>

          <div class="rounded-3xl border border-ramadhan-100 bg-gradient-to-br from-ramadhan-50 via-white to-ramadhan-100/70 px-5 py-5 shadow-lg shadow-ramadhan-200/40">
            <div class="flex items-start gap-3">
              <span class="inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-ramadhan-500 text-white">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v2m0 12v2m8-8h-2M6 12H4m12.364-6.364l-1.414 1.414M7.05 16.95l-1.414 1.414m0-12.728L7.05 7.05m10.314 10.314l1.414 1.414M8 12a4 4 0 108 0 4 4 0 00-8 0z" />
                </svg>
              </span>
              <div>
                <p class="text-sm font-semibold text-ramadhan-800">Panduan Singkat</p>
                <p class="mt-1 text-xs text-slate-600">Ketik nama Panitia, lalu klik tombol QR pada baris yang sesuai untuk mengunduh QR Code.</p>
              </div>
            </div>
          </div>
        </div>

        <div class="mt-8 overflow-hidden rounded-3xl border border-ramadhan-100 bg-white/95 table-shadow">
          <div class="overflow-x-auto">
            <table class="w-full min-w-[760px] text-left text-sm">
              <thead class="bg-ramadhan-500 text-white">
                <tr>
                  <th class="px-5 py-4 text-xs font-semibold uppercase tracking-widest">No</th>
                  <th class="px-5 py-4 text-xs font-semibold uppercase tracking-widest">NUPTK</th>
                  <th class="px-5 py-4 text-xs font-semibold uppercase tracking-widest">Nama</th>
                  <th class="px-5 py-4 text-xs font-semibold uppercase tracking-widest">No HP</th>
                  <th class="px-5 py-4 text-xs font-semibold uppercase tracking-widest">Alamat</th>
                  <th class="px-5 py-4 text-xs font-semibold uppercase tracking-widest">Jadwal</th>
                  <th class="px-5 py-4 text-xs font-semibold uppercase tracking-widest">QR Code</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-ramadhan-100">
                <?php if (!empty($guru)) : ?>
                  <?php $i = 1; ?>
                  <?php foreach ($guru as $value) : ?>
                    <?php
                      $namaGuru = $value['nama_guru'] ?? $value['nama'] ?? '-';
                      $nuptk = $value['nuptk'] ?? '-';
                      $noHp = $value['no_hp'] ?? '-';
                      $alamat = $value['alamat'] ?? '-';
                      $kelas = $value['kelas'] ?? $value['nama_kelas'] ?? '-';
                      $idGuru = $value['id_guru'] ?? '';
                    ?>
                    <tr data-guru-row data-name="<?= strtolower($namaGuru); ?>" class="hover:bg-ramadhan-50/70">
                      <td class="px-5 py-4 text-slate-600">
                        <span data-row-number><?= $i; ?></span>
                      </td>
                      <td class="px-5 py-4 font-semibold text-ramadhan-700"><?= $nuptk; ?></td>
                      <td class="px-5 py-4 font-semibold text-slate-900"><?= $namaGuru; ?></td>
                      <td class="px-5 py-4 text-slate-600"><?= $noHp; ?></td>
                      <td class="px-5 py-4 text-slate-600"><?= $alamat; ?></td>
                      <td class="px-5 py-4 text-slate-600"><?= $kelas; ?></td>
                      <td class="px-5 py-4">
                        <?php if (!empty($idGuru)) : ?>
                          <a href="<?= base_url('qr/guru/' . $idGuru . '/download-template'); ?>" class="inline-flex items-center gap-2 rounded-full bg-ramadhan-500 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white shadow-lg shadow-ramadhan-300/40 transition hover:-translate-y-0.5 hover:bg-ramadhan-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                              <path d="M3 3h8v8H3V3zm2 2v4h4V5H5zM3 13h8v8H3v-8zm2 2v4h4v-4H5zM13 3h8v8h-8V3zm2 2v4h4V5h-4zM13 13h3v3h-3v-3zm5 0h3v3h-3v-3zm-5 5h8v3h-8v-3z" />
                            </svg>
                            QR
                          </a>
                        <?php else : ?>
                          <span class="text-xs text-slate-400">Tidak tersedia</span>
                        <?php endif; ?>
                      </td>
                    </tr>
                    <?php $i++; ?>
                  <?php endforeach; ?>
                <?php endif; ?>
                <tr id="emptyState" class="<?= !empty($guru) ? 'hidden' : ''; ?>">
                  <td colspan="7" class="px-5 py-10 text-center text-sm text-slate-500">Data guru tidak ditemukan.</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </section>
    </main>

    <script>
      const searchInput = document.getElementById("searchGuru");
      const rows = Array.from(document.querySelectorAll("[data-guru-row]"));
      const resultCount = document.getElementById("resultCount");
      const emptyState = document.getElementById("emptyState");

      const normalize = (value) => (value || "").toLowerCase().replace(/\s+/g, " ").trim();

      const filterRows = () => {
        const query = normalize(searchInput.value);
        let visible = 0;

        rows.forEach((row) => {
          const name = normalize(row.dataset.name);
          const match = name.includes(query);
          row.classList.toggle("hidden", !match);

          if (match) {
            visible += 1;
            const numberCell = row.querySelector("[data-row-number]");
            if (numberCell) {
              numberCell.textContent = visible;
            }
          }
        });

        if (resultCount) {
          resultCount.textContent = visible;
        }
        if (emptyState) {
          emptyState.classList.toggle("hidden", visible !== 0);
        }
      };

      if (searchInput) {
        searchInput.addEventListener("input", filterRows);
      }
    </script>
  </body>
</html>
