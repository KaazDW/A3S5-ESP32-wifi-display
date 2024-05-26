# LEDify -> Affichage déroulant à LED

## Notre Projet

Notre projet vise à créer un affichage à LED dynamique et interactif en utilisant un ESP32. Inspiré par les panneaux d'affichage des pharmacies, notre système permettra de saisir un mot ou un message via une interface utilisateur, puis de l'afficher instantanément sur un panneau à LED.

L'ESP32 servira de cerveau du système, gérant à la fois l'interface utilisateur et le contrôle des LED. Pour cela, nous utiliserons des boutons-poussoirs ou un écran tactile comme interface utilisateur, permettant à l'utilisateur de saisir le message désiré.

Une fois le message saisi, l'ESP32 le recevra et le traitera, activant les LED appropriées pour afficher le texte. Pour simuler un effet de défilement, nous pourrions faire défiler le message de droite à gauche sur le panneau à LED, lui donnant ainsi un aspect dynamique et attrayant.

Pour assurer la flexibilité du système, nous pourrions également envisager d'ajouter des fonctionnalités supplémentaires telles que la possibilité de changer la couleur du texte, d'ajuster la vitesse de défilement ou même d'afficher des symboles ou des images simples sur le panneau à LED.

## Matériels

Voici la liste du matériel que l’on souhaite :
- Kit ESP 32
- 4 matrices Neopixel -> actionneur
- Un Potentiomètre -> capteur
- Capteur de proximité à ultrasons -> capteur
- Bouton 
- Cable USB
- Connexion Internet

## Liste des fonctionnalités

- Connexion à l’interface par un login
- Ajouter un bouton pour verifier si l'ESP32 est connecté
- Affichage déroulant d’un mot
- Affichage d'une phrase déroulante
- Affichage d'un chiffre déroulant
- Affichage d'un "float" déroulant.
- Contrôle luminosité des LED en fonction du potentiomètre
- Marche/Arrêt des LED en fonction du capteur de proximité
- Page Web qui envoie un mot et l'affiche en déroulant.
- Page web: on/off
- Page web : arrêt sur image (avec bouton reset)
- Change de couleur automatiquement et aléatoirement.
- Ajout d’autre fonctionnalités si on a le temps

<!-- GETTING STARTED -->
# Getting Started

Instructions concernant l'installation d'une copie local du projet

## Prérequis/Langage

- PHP 8.2
- SGDB MySQL
- Symfony CLI
- Arduino

## Schéma de déploiement

<div align="center">
  <img src="https://iutbg-gitlab.iutbourg.univ-lyon1.fr/but3-wot-23-24/trontin-pauline-marcourt-jf/-/blob/main/LEDify-website/public/assets/images/doc/Schéma-déploiement.png">
</div>

## Maquette du site

<div align="center">
   <img src="https://iutbg-gitlab.iutbourg.univ-lyon1.fr/but3-wot-23-24/trontin-pauline-marcourt-jf/-/blob/main/LEDify-website/public/assets/images/doc/maquette-home.png">
  <img src="https://iutbg-gitlab.iutbourg.univ-lyon1.fr/but3-wot-23-24/trontin-pauline-marcourt-jf/-/blob/main/LEDify-website/public/assets/images/doc/maquette-login.png">
  <img src="https://iutbg-gitlab.iutbourg.univ-lyon1.fr/but3-wot-23-24/trontin-pauline-marcourt-jf/-/blob/main/LEDify-website/public/assets/images/doc/maquette-register.png">
  <img src="https://iutbg-gitlab.iutbourg.univ-lyon1.fr/but3-wot-23-24/trontin-pauline-marcourt-jf/-/blob/main/LEDify-website/public/assets/images/doc/maquette-dashboard.png">
</div>

## Installation

_Objectif : Installer et démarrer le projet localement :_
1. Cloner le projet et installer les dépendances
    ```sh
    git clone https://iutbg-gitlab.iutbourg.univ-lyon1.fr/but3-wot-23-24/trontin-pauline-marcourt-jf.git
    ```
2. ```sh
    cd LEDify-website\
    composer install
3. Créez une nouvelle base de donnée et renseignez ses identifiants d'accès dans le fichier .env.local (ligne 30)
    ```php
    DATABASE_URL="mysql://root:@127.0.0.1:3306/iot-ledify?charset=utf8"
    ```
3. Installer les migrations (avec jeu de test pour démo)
    ```sh
   php bin/console doctrine:migrations:migrate
   ```
4. Lancer le serveur local symfony
    ```sh
    symfony server:start

## Sass compilation pour dev:
Compilation des feuilles de style SASS :
``` 
sass public/assets/scss/base.scss:public/assets/css/base.css -w
sass public/assets/scss/accueil.scss:public/assets/css/accueil.css -w
sass public/assets/scss/pages/login.scss:public/assets/css/pages/login.css -w
```