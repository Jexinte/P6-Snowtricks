La création d'utilisateur fonctionne mais il manque certaines choses comme :
~~- Hachage de mot de passe~~
~~- La vérification en base de données que le nom de l'utilisateur n'existe déjà pas~~
~~- La vérification en base de données que l'adresse mail n'existe déjà pas~~


# Général 
- Rendre le code plus lisible là ou il ne l'est pas
- Il faudra ajouter le champ "status" dans le diagramme de classe pour la classe "user"
- Vérifier pourquoi la partie from() ne met pas l'adresse souhaité
- Voir du côté des Assert s'il est en effet possible d'en définir des personnalisés ainsi si c'est possible alors faire le nécessaire 
- Afficher un message de confirmation comme quoi l'utilisateur à bien réinitialiser son mdp ?
# Urgent

- Suite à une petite vérification je vais devoir faire une petite modification pour le token pour la partie inscription à faire sur la branche signUp puis ensuite je repasserai sur la fonctionnalité login


# Mot de passe oublié
 Lorsqu'on clique sur le lien ce dernier est dans un premier temps censé déclenché un envoi de mail.
 
  À la suite de ça il faut faire disparaître le formulaire puis afficher un message disant qu'un lien a été envoyé avec tout le tralala
# Responsive
- S'occuper des templates suivants :
- [] Inscription
- [] Connexion
- [] Mot de passe oublié
- [] Réinitialisation de mot de passe
