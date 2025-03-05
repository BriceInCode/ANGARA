# Objectif : Créer un projet Laravel pour une API RESTful avec validation par OTP via email ou WhatsApp

## 1. Accueil - Demande des informations utilisateur (email ou numéro de téléphone)
Lors de la première visite sur la page de votre application, l'utilisateur se voit proposer deux options :
- Entrer son adresse email
- Entrer son numéro de téléphone

**Remarque** : Ces informations (email ou téléphone) sont uniques pour chaque utilisateur et seront utilisées pour l'identification.

### Processus :
L'utilisateur choisit l'option qu'il préfère (email ou téléphone). Lorsque l'utilisateur soumet ses informations :
1. Une session est créée pour cet utilisateur. Cette session est inactive par défaut (en attente de validation via OTP).
2. Un code OTP unique de 5 chiffres est généré et envoyé à l'utilisateur via le canal approprié (email ou WhatsApp).
3. Si l'utilisateur a renseigné un email, un service d'envoi d'email est utilisé pour envoyer le code OTP à l'adresse fournie.
4. Si l'utilisateur a renseigné un numéro de téléphone, un service d'envoi de messages (par exemple, WhatsApp) est utilisé pour envoyer le code OTP au numéro indiqué.

---

## 2. Validation de la session par le code OTP
Une fois que l'utilisateur a reçu le code OTP par l'un des canaux mentionnés, il est invité à entrer ce code dans un formulaire sur la plateforme.

### a- Processus de validation :
1. L'utilisateur entre le code OTP.
2. Le système vérifie si le code correspond à celui généré et associé à la session de l'utilisateur.
3. Si le code OTP est valide, la session de l'utilisateur est activée. Un message est renvoyé :  
   **"Votre session a été créée avec succès. Vous pouvez maintenant procéder à votre demande de document."**

### b- Processus de sécurité :
Pour éviter les abus, limitez le nombre de tentatives de saisie du code OTP :
- Après 3 tentatives échouées, bloquez l'utilisateur pendant un certain temps ou demandez-lui de demander un nouvel OTP.
- **Expiration de l'OTP** : L'OTP doit avoir une date d'expiration, après laquelle il ne sera plus valide (par exemple, 10 minutes après la génération de l'OTP).
- La session de l'utilisateur n'est valide que pendant **2 heures** après sa création.

---

## 3. Partie 3 : Informations Documentaires

### Objectif :
L’objectif de cette partie est de collecter les informations nécessaires pour traiter la demande de document de l'utilisateur après la validation de sa session via le code OTP. Une fois que l'utilisateur a validé son code OTP et que sa session est activée, il sera redirigé vers une page où il devra fournir des informations spécifiques liées à sa demande.

Chaque demande sera associée à un **numéro de demande unique** qui sera généré automatiquement et affiché à l'utilisateur.

### Processus :
#### a- Génération d'un numéro de demande unique :
Dès que la session de l'utilisateur est activée (après la validation de l'OTP), le système génère automatiquement un numéro de demande unique au format suivant :  
**P0-YYYYMMDD-XXXXX**  
- **YYYYMMDD** : La date actuelle (année, mois, jour).  
- **XXXXX** : Un compteur unique pour garantir l’unicité du numéro de la demande.

Exemple : **P0-20241009-000250**

Avant d’attribuer et d'afficher ce numéro, le système doit s’assurer qu’il est unique dans la base de données. Le processus pour générer un numéro de demande unique est le suivant :
- Le système vérifie que le numéro généré **n'existe pas déjà** dans la base de données.
- Si un doublon est détecté, un nouveau numéro est généré jusqu'à ce qu'il soit unique.

#### b- Collecte des informations documentaires :
Une fois que le numéro de la demande a été généré et attribué à l'utilisateur, celui-ci sera invité à remplir les informations nécessaires à la demande du document. Voici les différentes catégories d'informations demandées.

### 1. Informations sur le Document :
L'utilisateur doit spécifier les informations concernant le document qu'il souhaite obtenir. Ce sont les champs suivants :
- **Type de Document** : L'utilisateur doit sélectionner parmi une liste prédéfinie (ici, un **enum** qui contient la liste des documents).
- **Raison de la Demande** : L'utilisateur doit indiquer la raison pour laquelle il demande ce document (par exemple, pour un mariage, pour une demande de visa, etc.).

### 2. Informations de Centre d'Etat Civil :
L'utilisateur devra fournir plusieurs informations relatives à son acte de naissance et au centre d'état civil :
- **Référence du Centre d'Etat Civil** : L'identifiant du centre d'état civil qui a enregistré l'acte.
- **Numéro de l'Acte de Naissance** : Le numéro d'enregistrement de l'acte de naissance.
- **Date de création de l'Acte de Naissance** : La date à laquelle l'acte a été enregistré.
- **Sur la Déclaration de** : Le responsable qui a dressé l’acte de naissance (souvent un fonctionnaire).
- **Par nous** : Indication que cet acte a été effectué par l’autorité compétente.

### 3. Informations Personnelles :
Ces informations sont directement liées à l'utilisateur et doivent être saisies pour compléter la demande :
- **Prénom** : Le prénom de l'utilisateur.
- **Nom** : Le nom de l'utilisateur.
- **Sexe** : L'utilisateur doit sélectionner son sexe parmi une liste d'options (par exemple, masculin, féminin).
- **Date de Naissance** : La date de naissance de l'utilisateur.
- **Lieu de Naissance** : La ville ou le pays où l'utilisateur est né.

**Sexe** doit être une **énumération** avec les valeurs disponibles : Masculin, Féminin, Autre.

### 4. Informations Parentales :
Ces informations concernent les parents de l'utilisateur et doivent être fournies pour permettre le traitement complet de la demande :
- **Nom(s) et prénom(s) du père** : Le nom et le prénom du père de l'utilisateur.
- **Date de Naissance du père** : La date de naissance du père.
- **Lieu de Naissance du père** : La ville ou le pays de naissance du père.
- **Profession du père** : La profession du père.
- **Nom(s) et prénom(s) de la mère** : Le nom et le prénom de la mère de l'utilisateur.
- **Date de Naissance de la mère** : La date de naissance de la mère.
- **Lieu de Naissance de la mère** : La ville ou le pays de naissance de la mère.
- **Profession de la mère** : La profession de la mère.

### 5. Validation et enregistrement des informations :
Une fois que l'utilisateur a rempli toutes les informations demandées, l’enregistrement ne s’effectuera que si tous les champs obligatoires sont renseignés correctement :
- Si tous les champs sont validés, les informations sont enregistrées dans la base de données et un message de succès est affiché à l'utilisateur.
- Si des champs sont manquants ou mal remplis, l’utilisateur sera invité à les corriger.

L'enregistrement se fait uniquement après que tous les champs aient été renseignés correctement. Une fois l'enregistrement effectué, pour cette session, les informations relatives à la demande seront envoyées à l'utilisateur.

### 6. Message de confirmation après enregistrement :
Une fois que la demande est enregistrée, un message de confirmation sera affiché à l'utilisateur avec le numéro unique de la demande :
```plaintext
Votre demande a été enregistrée avec succès.
Votre numéro de demande est : P0-20241009-000250



## 4. Téléchargement du Document

## Objectif

Cette fonctionnalité permet à l'utilisateur de télécharger ou de mettre à jour un document officiel après avoir complété toutes les étapes de validation des informations et la génération d'un numéro de demande unique.

## Processus

### 1. Affichage de l'Option de Téléchargement et Mise à Jour du Document
- Une fois que toutes les informations ont été validées et enregistrées, l'utilisateur est dirigé vers une page où il peut télécharger ou mettre à jour le document demandé.
- Cette page affiche le numéro de la demande unique généré précédemment, tel que `P0-20241009-000250`, pour référence.
- Si l'utilisateur a déjà téléchargé un document pour cette demande, une option pour **mettre à jour le document** sera visible. L'option pour mettre à jour doit clairement indiquer que le document actuel sera **écrasé** et **remplacé** par le nouveau fichier.

### 2. Téléchargement ou Mise à Jour du Fichier
- Lorsque l'utilisateur clique sur l'option **"Télécharger le Fichier"** ou **"Mettre à jour le Document"**, il peut sélectionner un nouveau fichier à télécharger.
- **Critères de validation du fichier :**
  - **Filtrage des Types de Documents** : Seuls certains types de fichiers sont acceptés :
    - PDF
    - DOCX
    - JPG
    - PNG
    - JPEG
    - D'autres formats de fichiers doivent être rejetés, et un message d'erreur sera affiché à l'utilisateur pour lui indiquer le type de fichier attendu.
  - **Vérification de la Taille du Fichier** : Le fichier ne doit pas dépasser une certaine taille limite (par exemple, 10 Mo). Si le fichier est trop grand, un message d'erreur sera affiché.

### 3. Écrasement et Suppression de l'Ancien Document
- Si un document est déjà téléchargé pour cette demande et qu'un autre fichier est téléchargé, l'ancien document est automatiquement **écrasé** et **supprimé** avant l'ajout du nouveau fichier.
- Une notification sera affichée à l'utilisateur pour l'informer de l'écrasement de l'ancien fichier.

### 4. Vérification de l'Intégrité du Fichier
- Avant le téléchargement, le système peut vérifier l'intégrité du fichier pour s'assurer qu'il n'a pas été altéré (par exemple, en utilisant un algorithme de somme de contrôle comme SHA-256).
- Une option **"Vérifier l'Intégrité du Fichier"** peut être fournie pour permettre à l'utilisateur de valider l'intégrité avant de soumettre le fichier.

### 5. Confirmation de Téléchargement ou Mise à Jour
- Après un téléchargement réussi ou une mise à jour, un message de confirmation est affiché à l'utilisateur, indiquant que le fichier a été téléchargé ou mis à jour avec succès.
- Ce message peut inclure des informations supplémentaires concernant l'utilisation du document téléchargé ou mis à jour.

### 6. Options de Ré-téléchargement
- Si l'utilisateur souhaite télécharger le fichier à nouveau, une option de ré-téléchargement sera disponible pendant une période limitée (par exemple, 24 heures après la première demande).
- Si un nouveau document a été téléchargé, l'utilisateur pourra télécharger la version la plus récente du fichier.

## Sécurité et Confidentialité

- **Protection des Données** : Les fichiers téléchargés sont protégés par des mesures de sécurité, telles que le chiffrement lors du transfert et du stockage, afin de garantir qu'ils ne peuvent pas être interceptés ou modifiés par des tiers non autorisés.
  
- **Accès Restreint** : Seuls les utilisateurs ayant complété toutes les étapes nécessaires et dont la session est active peuvent accéder à cette fonctionnalité de téléchargement ou de mise à jour du document.

- **Audit et Suivi des Modifications** : Chaque téléchargement ou mise à jour de fichier est journalisé pour des raisons de traçabilité et de sécurité, afin de savoir quel utilisateur a téléchargé ou mis à jour quel document et à quel moment cela a eu lieu.

## Filtrage des Types de Documents Autorisés

Voici les types de fichiers autorisés pour le téléchargement :

- **PDF** (.pdf)
- **Microsoft Word** (.docx)
- **Image JPG** (.jpg)
- **Image PNG** (.png)
- **Image JPEG** (.jpeg)

### Restrictions
- Tout autre type de fichier sera rejeté avec un message d'erreur.
- La taille maximale des fichiers doit être définie et communiquée à l'utilisateur (par exemple, 10 Mo).

---

**Note** : Cette fonctionnalité est conçue pour assurer la gestion sécurisée des documents, tout en permettant aux utilisateurs de facilement mettre à jour et vérifier les fichiers qu'ils ont téléchargés.
