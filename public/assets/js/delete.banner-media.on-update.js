const deleteBanner = document.querySelector ("#banner-trick .delete-banner")
deleteBanner.addEventListener ("click", (e) => {
    if (window.confirm ("Êtes-vous sûr de vouloir supprimer le média ?")) {
    } else {
        e.preventDefault ()
    }
})
