# Spesifikasi Prototype Figma dan Implementasi Website

## Sumber desain terbaru

- File Figma: https://www.figma.com/design/4d8ybjHjJVnq3dXAIbBhvi/Untitled?node-id=0-1&t=4mhwoTrrmp3cKy7j-1
- Prototype utama: https://www.figma.com/proto/4d8ybjHjJVnq3dXAIbBhvi/Untitled?node-id=9-2&t=CSSSNe71BRj1PioT-1&scaling=min-zoom&content-scaling=fixed&page-id=0%3A1&starting-point-node-id=9%3A2
- Prototype frame project: https://www.figma.com/proto/4d8ybjHjJVnq3dXAIbBhvi/Untitled?node-id=7-8&t=rkto6FxshtVyxEo6-1
- Prototype frame tambahan: https://www.figma.com/proto/4d8ybjHjJVnq3dXAIbBhvi/Untitled?node-id=8-42&t=rkto6FxshtVyxEo6-1

Versi terbaru menggunakan hero emerald gelap, tipografi editorial, kartu visual putih, aksen amber, serta latar linen/sage pada section publik.

Figma menjadi acuan untuk copy UI publik dan sistem desain. Data layanan, proyek, dan artikel tetap dikelola melalui database; jangan mengganti data dinamis dengan teks prototype yang hanya berfungsi sebagai contoh.

## Status implementasi website

- Hero utama menggunakan emerald gelap, copy editorial, kartu visual putih, dan aksen amber.
- Navigasi publik mengikuti struktur Studio, Services, Portfolio, dan Insights.
- Section homepage mencakup about, services, selected work, insights, process, dan project inquiry.
- Portfolio mendukung filter kategori dan tautan ke project detail.
- Project inquiry memiliki tiga tahap: Contact, Project, dan Details.
- Progress inquiry menampilkan tahap aktif serta tombol Back dan Continue.
- Review step menampilkan ringkasan input sebelum pengiriman.
- Form menampilkan pesan validasi per field, loading saat submit, dan success/error server.
- Layout memiliki adaptasi mobile untuk navigasi, hero, kartu konten, dan inquiry.

## Frame dan alur

- Home desktop dan mobile.
- Portfolio: menelusuri dan memfilter proyek.
- Project detail: studi kasus dan galeri.
- Project inquiry: alur tiga langkah, Contact, Project, dan Details.
- Insights: artikel dan konten editorial.
- Admin dashboard: pengelolaan konten dan inquiry.

Alur publik utama: Home → Portfolio → Project Detail, serta Home → Project Inquiry dan Home → Insights. Dashboard admin tetap memiliki alur akses dan autentikasi tersendiri.

## Copy acuan

- Home: “Creative studio”, “Design that feels like you.”, “Thoughtful brands and digital experiences.”, “Explore our work”, dan “Small studio. Big ideas.”
- Project inquiry: “Tell us about your project.” dengan tahap Contact, Project, dan Details.
- Langkah Contact menampilkan Name, Email, Business / Brand, Project type, Budget range, dan Timeline.
- Project detail menyajikan case study dan gallery.

Copy halaman lain menggunakan bahasa Inggris untuk label navigasi dan antarmuka. Konten proyek, layanan, dan artikel yang berasal dari database tetap mengikuti isi yang dimasukkan admin.

## Interaksi

- Navigasi publik berisi Studio, Services, Portfolio, dan Insights; CTA utama membuka Project Inquiry.
- Menu mobile membuka dan menutup navigasi dengan perubahan `aria-expanded`.
- Portfolio dapat difilter berdasarkan kategori dan setiap kartu membuka detail proyek.
- Kartu insight membuka artikel terkait.
- Project Inquiry memiliki tiga tahap, memvalidasi field wajib, mempertahankan isian saat kembali, dan mengirim ke `api/submit_inquiry.php`.
- Widget chat tersedia pada halaman publik.

## Sistem desain

- Display: Playfair Display. Body: DM Sans.
- Primer emerald: `#1E4D38`; aksen amber: `#D4973B`; latar linen: `#F4EFE6`; sage: `#BBD9C5`.
- Tombol utama berbentuk pill dengan panah.
- Kartu memiliki sudut membulat dan bayangan lembut.
- Layout responsif untuk desktop dan mobile.
