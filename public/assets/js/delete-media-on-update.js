const deleteTrick = document.querySelectorAll (".media-box .options-box .delete")

deleteTrick.forEach (button => {
    button.addEventListener ("click", (e) => {
        if (window.confirm ("Êtes-vous sûr de vouloir supprimer le média ?")) {
        } else {
            e.preventDefault ()
        }
    })
})
