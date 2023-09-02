const deleteTricks = document.querySelectorAll(".delete-trick")

deleteTricks.forEach(button => {
    button.addEventListener("click",(e) => {
        window.confirm("Êtes-vous sûr de vouloir supprimer le trick ?")
    })
})
