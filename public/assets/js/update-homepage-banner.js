const bannerImg = document.querySelector('#banner img')
const bannerImgDefaultPath = document.querySelector('#banner img').src
window.addEventListener('resize',() => {
    if(window.innerWidth <= 992)
    {
        bannerImg.src = "/assets/img/banner/banner-mobile.jpg"
    } else {
        bannerImg.src = bannerImgDefaultPath
    }
})

