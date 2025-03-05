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
