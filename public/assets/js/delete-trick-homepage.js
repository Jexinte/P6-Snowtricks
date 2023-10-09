const deleteTricks = document.querySelectorAll (".delete-trick")


deleteTricks.forEach (button => {
    button.addEventListener ("click", (e) => {
        if (window.confirm ("Êtes-vous sûr de vouloir supprimer le trick ?")) {
        } else {
            e.preventDefault ()
        }
    })
})
