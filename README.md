# TetrisPHP

Bienvenue sur **TetrisPHP** !  
Ce projet est une implémentation complète du jeu classique Tetris, réalisée entièrement en **PHP** côté serveur, **sans aucune utilisation de JavaScript**. L'objectif principal est de démontrer la faisabilité d'un jeu interactif en web uniquement avec PHP, et d'explorer les contraintes que cela implique ainsi que les solutions apportées pour les contourner.

## Objectif du projet

- **Challenge technique** : Créer un Tetris jouable sans JS, uniquement avec PHP et HTML.
- **Expérimentation** : Mettre en lumière les limites du web sans scripts côté client et les pousser au maximum.
- **Apprentissage** : Comprendre comment l’interactivité peut être gérée côté serveur et quelles sont les alternatives à l’usage du JavaScript.

## Contraintes rencontrées

- **Absence d’interactivité instantanée** : Sans JS, impossible d’utiliser les événements clavier ou de manipuler le DOM en temps réel.
- **Rafraîchissement de la page** : Toute action du joueur (déplacement, rotation, etc.) nécessite un rechargement de la page, souvent via des formulaires ou des requêtes GET/POST.
- **Gestion du temps** : Pas de timer côté client, la gestion du temps et de la descente des pièces se fait côté serveur, par exemple en simulant le temps via des requêtes ou en utilisant des sessions.
- **Performance** : Les interactions sont plus lentes et dépendent du temps de réponse du serveur.
- **Expérience utilisateur** : Moins fluide qu’un jeu JS, mais permet de comprendre le fonctionnement d’un jeu web sans le confort du client riche.

## Solutions et contournements

- **Utilisation de formulaires** : Chaque bouton de commande (gauche, droite, rotation, descente rapide) est un formulaire qui envoie une requête au serveur.
- **Sessions PHP** : L’état du jeu (grille, pièce courante, score, etc.) est stocké en session pour chaque utilisateur.
- **Rendu HTML** : Le plateau du Tetris est généré dynamiquement en HTML à chaque requête.
- **Simulation du temps** : Plutôt que d’avoir une boucle de jeu, la descente des pièces peut être simulée par des actions manuelles ou des rafraîchissements automatiques (meta-refresh).
- **Maintien de l’état** : Chaque action est traitée côté serveur, puis le nouvel état est affiché, imitant l’interactivité d’un jeu JS.

## Lancement du projet

1. **Cloner le dépôt** :
   ```bash
   git clone https://github.com/chemsbhd/TetrisPHP.git
   ```
2. **Placer sur un serveur PHP** (local ou distant).
3. **Accéder à index.php** via votre navigateur.

_Note : Le jeu se joue uniquement avec des boutons et des rechargements de page._

## Pour aller plus loin

Ce projet est un bon support pour comprendre :
- Les limites du web sans JavaScript.
- L’importance des scripts côté client dans l’expérience utilisateur.
- Les possibilités et astuces offertes par PHP pour simuler de l’interactivité.

N’hésitez pas à explorer le code, à proposer des améliorations ou à discuter des choix techniques dans les issues du dépôt.

---

**Auteur :** [chemsbhd](https://github.com/chemsbhd)
