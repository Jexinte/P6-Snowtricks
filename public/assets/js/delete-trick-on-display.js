const deleteTrick = document.getElementById("delete")

deleteTrick.addEventListener ("click", (e) => {
    if (window.confirm ("Êtes-vous sûr de vouloir supprimer le trick ?")) {
    } else {
        e.preventDefault ()
    }
})
