const images = document.querySelectorAll('.image');

images.forEach(image => {
    image.addEventListener('click', () => {
        if (!image.classList.contains('image-fullscreen')) {
            image.classList.add('image-fullscreen');
        } else {
            image.classList.remove('image-fullscreen');
        }
    });
})

