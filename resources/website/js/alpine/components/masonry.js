import Masonry from "masonry-layout";

document.addEventListener("alpine:init", () => {
    Alpine.directive("masonry", (el, { expression }, { effect, cleanup }) => {
        let masonryInstance = null;

        // melakukan Inisialisasi Masonry saat elemen dimasukkan ke dalam DOM
        effect(() => {
            // Menggunakan elemen yang sudah  diambil sebelumnya
            masonryInstance = new Masonry(el, {
                itemSelector: expression, // Gunakan ekspresi untuk itemSelector
            });
        });

        // Bersihkan instance Masonry saat elemen dihapus dari DOM
        cleanup(() => {
            if (masonryInstance) {
                masonryInstance.destroy();
                masonryInstance = null;
            }
        });
    }).before("bind");
});
