@echo off
chcp 65001 > nul

REM Définir le dépôt GitHub (modifier avec ton repo)
SET REPO=nasutance/reservations

REM Créer les issues pour le projet

gh issue create --repo %REPO% --title "Créer la page du catalogue des spectacles" --body "Affichage des spectacles avec pagination, tri et filtres." --label "UI/UX,Frontend"
gh issue create --repo %REPO% --title "Créer la page de gestion du profil utilisateur" --body "Modifier ses informations personnelles.\nChanger son mot de passe." --label "Feature,Frontend"

gh issue create --repo %REPO% --title "Créer l’API pour la gestion des spectacles" --body "CRUD API pour les spectacles (GET, POST, PUT, DELETE)." --label "API,Backend"
gh issue create --repo %REPO% --title "Créer l’API pour la gestion des réservations" --body "CRUD API pour les réservations (GET, POST, PUT, DELETE)." --label "API,Backend"

echo "Toutes les issues ont été créées avec succès !"
