
# Progressif Media - Savoirdessiner

Bonjour l'équipe de Progressif Media, vous trouverez sur ce répertoire Github, mon test de création de sites web à partir de votre maquette "savoir dessiner".
J'ai pris beaucoup de plaisir à la construire et j'espère que vous apprécierez également.

Mon process de travail est disponible à la suite de ce readme, je vous souhaite donc une agréable lecture, je reste à votre disposition au besoin.

Pour vous expliquer ma manière de travailler, je vous emmène avec moi dans un parcours chronologique de travail, réparti en 3 jours.

TL:TR Vous voulez un résumé ?
- Vendredi : Perte de temps
- Samedi : Mise en place du BO + des champs
- Dimanche : Stylisation CSS
- Lundi : Readme, installation, responsive


**Vous souhaitez voir le projet ?**

Lien du site : https://savoirdessiner.maxime-eloir.fr/


**Vous souhaitez installer le projet ?**

Il y a un dossier extra dans ce projet, il s'agit d'une exportation réalisée avec duplicator, mettez la à la racine de votre projet, connectez vous à votre BDD et le tour est joué ! 



## Chronologie de travail
### Vendredi - 1h30
Après réception de la maquette, j'ai commencé à chercher quel était le meilleur angle d'attaque pour réaliser à bien cette mission ! Deux options se présentaient à moi :
- Faire un simple squelette html en intégrant du php
OU
- Créer toute une arborescence wordpress afin d'être au plus proche d'un véritable rendu de projet

Bien entendu... J'ai choisi la seconde solution, plus complexe à mettre en place mais tellement plus satisfaisant ! 

Pour créer mon wordpress en local, j'utilise le logiciel *Local By Flywheel*, qui permet de faire une installation propre de wordpress, d'installer un certificat ssl et d'avoir une URL modifiée. Fini le "localhost/savoirdessiner", bienvenue savoirdessiner.local !

Cependant, en faisant la mise à jour du logiciel, j'ai découvert qu'ils avaient implémenté un système de blueprint, permettant de sélectionner un template de site déjà créé avec les thèmes et les plugins déjà installés ! 

Quel gain de temps ! Sauf... Sauf quand je décide de faire une installation de base et que je passe au final 1h30 de mon temps à créer un blueprint (qui me resservira plus tard!) *au lieu de finalement travailler sur le projet...*


### Samedi - 4h 
Bon, mon installation de wordpress et faite ! Mes extensions fétiches sont installés : 
- ACF Pro
- ACF Content Analysis for Yoast SEO
- Contact Form 7
- Contact Form CFDB7
- Converter for Media
- Timber
- Yoast SEO

A ces plugins, j'ai ajouté **Woocommerce** pour répondre aux besoins du projet : j'avais besoin d'afficher dans le header à droite des informations relatives à woocommerce.

---


**Il est donc temps de s'occuper à la configuration du thème :**
- Enregistrement de styles/scripts utiles [^1]
- Enregistrement de menu dans le functions.php
- Enregistrement des blocs ACF
- Création des champs ACF et remplissage des informations
- Création des fichiers acf-block et mise en place de l'architecture HTML

[^1]: Pour permettre une plus grande flexibilité et rapidité dans mon travail, j'utilise la grille Bootstrap comme grille de référence. Je me suis rendu compte que votre maquette, une fois ramené à une largeur d'écran approximatif de 1400px, rentrait quasiment à la perfection dans une grille Bootstrap. De bootstrap, je n'utilise que le colonnage et vous comprendrez pourquoi par la suite.


Suite à ça, je me suis attardé sur l'utilisation de l'API et la manière dont j'allais afficher les informations.

- J'ai créé une fonction qui me permettra d'appeler l'API en utilisant du Curl dans le block de formations, avec la particularité qu'il est possible de faire passer une variable.
**Cette variable est définie dans la page options > API.**

Si elle est vide, aucun tri est fait, si la variable est rempli, un tri sera effectué en fonction du mot mis.

```
$array = array_filter($respJSON, function ($k) use ($filter) {
			if (str_contains($k->title, $filter)) {
				return $k;
			}
		});
```


Travaillant  depuis peu avec Timber et par extension, les templates TWIG, j'ai passé du temps à comprendre comment appeler une fonction et utiliser la bonne écriture.


### Dimanche - 9h
La structure HTML et mes éléments configurés, il ne fallait plus que faire le CSS ! 

J'ai passé la journée à travailler sur le CSS de mes blocks, l'architecture ayant été mis en place la veille, je ne devais plus qu'intégrer le style.
Cependant, trois particularités que j'aimerais énonce : 

- La typographie utilisé est la Josh*, non disponible sur Google Font. Seule la Josh est sur la plate-forme mais il y a une légère différence en terme d'épaisseur de typographie. Ainsi, j'ai pris la décision de venir charger directement sur le projet les typographies .woff.

- Pour mon HTML/CSS, j'ai utilisé la méthodologie BEM ( Block Element Modifier ), cela me permet de cibler correctement mes balises mais surtout, si je décide de transformer un h3 en h4, cela n'aura pas d'impact sur mon code car j'utilise mes classes pour styliser mes éléments.

- J'ai toujours eu du mal avec les maquettes d'Adobe XD, les tailles typographiques me paraissent toujours beaucoup trop grosses une fois rendus en site web. C'est pourquoi pour la grande majorité des éléments typographiques, j'ai baissé de 5px ! 


J'y ai ajouté des très légères animations sur les svg, sur les boutons, et j'ai également géré une modal d'affichage de vidéo sur le premier bloc.

Mon travail en CSS fait, il m'a fallu ensuite vérifier la version mobile : 
- Diminution de quelques tailles d'images et de texte
- Changement de l'ordre des éléments pour faire passer l'image avant le texte
- Centrage des éléments du footer
- Mise en place d'un menu Hamburger
- Gestion du sous-menu sur mobile

### Lundi - 3h

Une fois tout cela fait, voilà maintenant 1h que j'écris ce README, tentant d'être le plus clair possible, mais je me rends compte que je perds peut être les gens dans ces explications...
J'ai créé un sous-somaine et une redirection pour que vous puissiez voir le site dans les meilleures conditions.

## Points d'amélioration


Dans l'ensemble, je pense que je ferais des animations personnalisés, un loader au chargement de la page, des animations plus poussés sur certains éléments interactifs...

- Création du feuille de style par block
- Venir diviser ma page functions.php en plus petit fichier pour mieux s'y retrouver.
- Optimiser les fonctions dans le functions.php
- Rajouter des animations plus intéressantes au scroll



# Merci d'avoir lu jusqu'ici.

Maxime Eloir
maxime.eloir@gmail.com
www.maxime-eloir.fr






