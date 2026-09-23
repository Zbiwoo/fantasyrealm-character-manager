# Checklist de livraison - FantasyRealm Character Manager

## Application utilisateur

- [x] Page d'accueil
- [x] Inscription
- [x] Connexion / déconnexion
- [x] Réinitialisation du mot de passe par email
- [x] Consultation des personnages partagés
- [x] Filtres des personnages
- [x] Fiche détaillée d'un personnage
- [x] Création d'un personnage
- [x] Gestion de ses personnages
- [x] Personnalisation enregistrée en base de données
- [ ] Finalisation de l'affichage visuel des traits du personnage
- [x] Équipements et pouvoirs
- [x] Partage / retrait du partage
- [x] Suppression d'un personnage
- [x] Commentaires et notes
- [x] Formulaire de contact
- [x] Mentions légales
- [x] CGV

---

## Espace employé

- [x] Accès réservé aux employés et administrateurs
- [x] Consultation des personnages en attente
- [x] Validation des noms
- [x] Refus des personnages
- [x] Consultation des commentaires en attente
- [x] Validation des commentaires
- [x] Refus des commentaires
- [x] Email après validation / refus d'un personnage
- [x] Motif de refus communiqué au propriétaire
- [x] Gestion des équipements et pouvoirs
- [x] Suppression d'un personnage par un employé
- [x] Suspension d'un utilisateur
- [x] Suppression d'un utilisateur

---

## Administration

- [x] Compte administrateur créé
- [x] Accès administrateur à l'espace employé
- [x] Création d'un compte employé
- [x] Modification du mot de passe d'un employé
- [x] Suspension d'un employé
- [x] Suppression d'un employé
- [x] Consultation des journaux d'activité

---

## Bases de données

- [x] Base relationnelle MySQL
- [x] Relations entre les principales tables
- [x] Export SQL présent dans `database/fantasyrealm.sql`
- [x] MongoDB pour la journalisation
- [ ] Export SQL final après les dernières modifications

---

## Documentation technique

- [x] Présentation du projet
- [x] Environnement de développement
- [x] Choix techniques
- [x] Organisation des données
- [x] MCD
- [x] Diagramme de classes
- [x] Diagramme de cas d'utilisation
- [x] Diagramme de séquence
- [x] Diagramme de déploiement
- [x] Documentation sécurité
- [x] Documentation RGPD
- [x] Documentation RGAA
- [ ] Vérification finale de la documentation

---

## Conception graphique

- [x] Charte graphique
- [x] Palette de couleurs
- [x] Typographies
- [x] Wireframes desktop
- [x] Wireframes mobile
- [x] Maquettes desktop
- [x] Maquettes mobile
- [x] Exports des maquettes et wireframes

---

## Manuel utilisateur

- [x] Manuel utilisateur V1
- [ ] Captures finales de l'application
- [ ] Mise à jour avec les fonctionnalités finales employé / administrateur
- [ ] Vérification du PDF final

---

## Gestion de projet

- [x] Analyse du besoin
- [x] Organisation des étapes
- [x] Priorisation
- [x] Difficultés rencontrées
- [x] Planning et suivi
- [ ] Ajouter les liens définitifs du projet

---

## Git et GitHub

- [x] `.gitignore`
- [x] `mail-config.example.php`
- [x] Vérifier que `mail-config.php` n'est pas suivi par Git
- [x] Initialiser / vérifier le dépôt Git
- [x] Branche `main`
- [x] Branche `develop`
- [x] Branche `feature/finalisation-documentation`
- [x] Premier commit
- [x] Dépôt GitHub public
- [ ] Commit final après les dernières modifications
- [x] Dépôt distant configuré
- [ ] Ajouter l'URL GitHub dans la documentation

---

## Déploiement

- [ ] Choisir / configurer l'hébergement
- [ ] Mettre les fichiers en ligne
- [ ] Importer la base de données
- [ ] Adapter la configuration de la base de données
- [ ] Adapter l'URL de réinitialisation du mot de passe
- [ ] Configurer MongoDB en production
- [ ] Tester l'envoi des emails en production
- [ ] Tester le site en ligne
- [ ] Ajouter l'URL du site dans la documentation

---

## Vérifications sécurité / accessibilité

- [ ] Vérifier les textes alternatifs des images importantes
- [ ] Vérifier les labels des formulaires
- [ ] Vérifier la navigation au clavier
- [ ] Vérifier les contrastes principaux
- [ ] Vérifier les messages d'erreur
- [ ] Vérifier l'affichage mobile
- [x] Vérifier que les informations SMTP privées sont exclues de Git

---

## Tests finaux

- [ ] Tester un compte utilisateur
- [ ] Tester un compte employé
- [ ] Tester le compte administrateur
- [ ] Tester inscription / connexion / déconnexion
- [ ] Tester mot de passe oublié
- [ ] Tester création et personnalisation d'un personnage
- [ ] Tester la personnalisation visuelle finale
- [ ] Tester validation / refus
- [ ] Tester les emails de validation / refus
- [ ] Tester partage / retrait du partage
- [ ] Tester commentaires et modération
- [ ] Tester formulaire de contact
- [ ] Tester les journaux MongoDB
- [ ] Tester tous les liens de navigation
- [ ] Vérifier qu'aucune erreur PHP n'apparaît
- [ ] Vérifier les pages desktop et mobile

---

## Avant l'envoi définitif

- [ ] Finalisation de la personnalisation visuelle
- [ ] Export SQL définitif
- [ ] README principal à jour
- [x] Documentation technique à jour
- [x] Gestion de projet à jour
- [ ] Manuel utilisateur final
- [ ] Liens GitHub et site déployé ajoutés
- [ ] Aucun mot de passe ou identifiant privé dans le dépôt
- [ ] Dernier test complet
- [ ] Commit final
- [ ] Push final sur GitHub
- [ ] Déploiement final
- [ ] LIVRAISON ECF