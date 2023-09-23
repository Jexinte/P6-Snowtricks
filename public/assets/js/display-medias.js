const button = document.getElementById('see-medias')
const medias = document.querySelector('#trick-content .medias')
button.addEventListener('click',(e) => {
    if(medias.style.display === "flex")
    {
        medias.style.display = "none"
    } else {
        medias.style.display = "flex"
    }
})
