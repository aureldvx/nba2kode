# NBA2KODE Aurélien DEVAUX
Ce repository contient l'ensemble de mon rendu, côté PHP mais aussi côté TypeScript. Pour que les deux parties soient fonctionnelles, merci de respecter la procédure d'installation suivante.

> Créer une base de données qu'il sera nécessaire de renseigner dans le `.env` du répertoire `back`

## PHP

### Installation
```bash
# Entrée dans le répertoire dédié au back
cd back

# Installation des dépendances Composer
composer install

# Installation des dépendances Node
npm install
```

### Configuration
```bash
cd back # Si pas déjà à l'intérieur

# Build de la feuille de style
npm run build

# Renommer le .env.example en .env et apdater les variables d'environnement

# Lancement d'un serveur PHP
php -S localhost:6587
```

### Mise en route
- Accéder à la page http://localhost:6587/first-run pour initialiser la base de données et synchroniser les données de l'API distante
- Accéder à la page http://localhost:6587/signup pour créer un compte
- Une fois le compte créé, se connecter
- Générer une clé d'API à copier dans le .env du répertoire `front`
- Bonne visite !

## TypeScript

### Installation
```bash
# Entrée dans le répertoire dédié au back
cd front

# Installation des dépendances Node
npm install
```

### Configuration
```bash
cd front # Si pas déjà à l'intérieur

# Build de la feuille de style
npm run build

# Renommer le .env.example en .env et apdater les variables d'environnement

# Lancement d'un serveur Node
npm run dev
```

### Mise en route
- Accéder à la page http://localhost:3000
- Bonne visite !
