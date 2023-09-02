const arrowButtonDown = document.querySelector ("#down-button");
const tricksContainer = document.getElementById ("tricks-container")
const tricksTotal = document.querySelectorAll (".trick")
const log = console.log

const footer = document.querySelector ('footer')
const arrowButtonUp = document.getElementById ('up-to-start')
let sectionHide = false;
let buttonDownArrowDisappear = false;


arrowButtonDown.addEventListener ("click", (e) => {
    switch (true) {
        case !sectionHide && tricksTotal.length > 10:
            arrowButtonUp.style.display = "block"
            footer.style.display = "block";
            tricksContainer.style.display = "block";
            tricksContainer.scrollIntoView ({behavior: 'smooth'})
            sectionHide = true
            buttonDownArrowDisappear = true
            arrowButtonDown.style.display = "none"
            break;
        case !sectionHide && tricksTotal.length <= 10:
            footer.style.display = "block";
            tricksContainer.style.display = "block";
            tricksContainer.scrollIntoView ({behavior: 'smooth'})
            buttonDownArrowDisappear = true
            break;
        default:
            footer.style.display = "none";
            tricksContainer.style.display = "none";
            window.scroll ({top: 295, behavior: 'smooth'})
            arrowButtonUp.style.display = "none"
            break;
    }

})

arrowButtonUp.addEventListener ("click", (e) => {

    if (buttonDownArrowDisappear) {
        sectionHide = false
        arrowButtonDown.style.display = "block"
        arrowButtonUp.style.display = "none"
        footer.style.display = "none";
        tricksContainer.style.display = "none";
        window.scroll ({top: 0, behavior: 'smooth'})
    }

})
