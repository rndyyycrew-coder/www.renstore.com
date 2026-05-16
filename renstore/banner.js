const slides = document.querySelector('.slides');
const slide = document.querySelectorAll('.slide');

let index = 0;
let autoSlide;

/* Tampilkan slide */
function showSlide() {
    slides.style.transform = `translateX(-${index * 100}%)`;
}

/* Jalankan auto slide */
function startSlide() {

    autoSlide = setInterval(() => {

        index++;

        if(index >= slide.length){
            index = 0;
        }

        showSlide();

    }, 5000);

}

/* Reset timer saat tombol diklik */
function resetSlide() {

    clearInterval(autoSlide);

    startSlide();

}

/* Tombol kanan */
document.querySelector('.next').addEventListener('click', () => {

    index++;

    if(index >= slide.length){
        index = 0;
    }

    showSlide();

    resetSlide();

});

/* Tombol kiri */
document.querySelector('.prev').addEventListener('click', () => {

    index--;

    if(index < 0){
        index = slide.length - 1;
    }

    showSlide();

    resetSlide();

});

/* Mulai slider */
startSlide();