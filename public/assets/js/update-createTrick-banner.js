const bannerImg = document.querySelector('#banner-trick img')
const bannerImgDefaultPath = document.querySelector('#banner-trick img').src
window.addEventListener('resize',(e) => {
    if(window.innerWidth <= 992)
    {
        bannerImg.src = "/assets/img/banner/banner-mobile.jpg"
    } else {
        bannerImg.src = bannerImgDefaultPath
    }
})

