import "./alpine/components/navigation";
import "./alpine/components/slide-over";
import Masonry from "masonry-layout";


// Inisialisasi Masonry
var upCominng = document.querySelector('.cf');
var masonry = new Masonry(upCominng, {
  itemSelector: '.cf-upcoming',
});

