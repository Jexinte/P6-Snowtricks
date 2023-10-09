const button = document.getElementById ('see-medias')
const medias = document.querySelector ('#trick-content .medias')
const mediasTotal = document.querySelectorAll ('#trick-content .medias .media-box')
if (mediasTotal.length >= 1) {
    button.addEventListener ('click', (e) => {
        if (medias.style.display === "flex") {
            medias.style.display = "none"
        } else {
            medias.style.display = "flex"
        }
    })
} else {
    button.style.display = "none"
}


