# 🔧 Guide de Dépannage - Homify

## ❌ **Erreur : `Undefined variable $slot`**

### **Cause :**
La vue `admin/access.blade.php` utilisait `@extends('layouts.guest')` mais le layout guest attend une variable `$slot` qui n'était pas fournie.

### **Solution :**
✅ **Corrigé** : La vue utilise maintenant un layout HTML complet au lieu d'étendre le layout guest.

### **Fichier corrigé :**
- `resources/views/admin/access.blade.php` : Layout HTML complet avec `<!DOCTYPE html>`, `<head>`, `<body>`, etc.

## 🚨 **Erreurs courantes et solutions :**

### **1. Erreur : `View [admin.access] not found`**
```bash
# Vérifiez que le fichier existe
ls resources/views/admin/access.blade.php

# Si absent, recréez-le
touch resources/views/admin/access.blade.php
```

### **2. Erreur : `Route [admin.access] not defined`**
```bash
# Vérifiez les routes
php artisan route:list --name=admin.access

# Si absent, vérifiez routes/web.php
grep "admin.access" routes/web.php
```

### **3. Erreur : `Class AdminAccessController not found`**
```bash
# Vérifiez que le contrôleur existe
ls app/Http/Controllers/Admin/AdminAccessController.php

# Si absent, recréez-le
php artisan make:controller Admin/AdminAccessController
```

### **4. Erreur : `Middleware [admin] not defined`**
```bash
# Vérifiez le middleware
grep "admin" app/Http/Kernel.php

# Si absent, ajoutez-le
echo "'admin' => \App\Http\Middleware\AdminMiddleware::class," >> app/Http/Kernel.php
```

### **5. Erreur : `Method isAdmin does not exist`**
```bash
# Vérifiez le modèle User
grep "isAdmin" app/Models/User.php

# Si absent, ajoutez la méthode
```

## 🧪 **Tests de vérification :**

### **Test 1 : Vérifier les routes**
```bash
php artisan route:list --name=admin
```
**Résultat attendu :**
- `admin.access` (GET)
- `admin.verify-access` (POST)
- `admin.direct-access` (GET)

### **Test 2 : Vérifier les fichiers**
```bash
# Vérifier les vues
ls resources/views/admin/access.blade.php

# Vérifier les contrôleurs
ls app/Http/Controllers/Admin/AdminAccessController.php

# Vérifier les middlewares
ls app/Http/Middleware/AdminMiddleware.php
```

### **Test 3 : Vérifier la base de données**
```bash
# Vérifier la colonne is_admin
php artisan tinker
```
```php
Schema::hasColumn('users', 'is_admin');
exit
```

### **Test 4 : Tester l'accès**
```bash
# Démarrer le serveur
php artisan serve

# Tester l'URL
curl http://localhost:8000/admin-access
```

## 🔍 **Diagnostic avancé :**

### **Vérifier les logs Laravel**
```bash
tail -f storage/logs/laravel.log
```

### **Vérifier le cache**
```bash
# Vider le cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Reconstruire le cache
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### **Vérifier les permissions**
```bash
# Vérifier les permissions des fichiers
ls -la resources/views/admin/
ls -la app/Http/Controllers/Admin/
ls -la app/Http/Middleware/
```

## 🚀 **Solutions rapides :**

### **Solution 1 : Redémarrer le serveur**
```bash
# Arrêter le serveur (Ctrl+C)
# Redémarrer
php artisan serve
```

### **Solution 2 : Vider tous les caches**
```bash
php artisan optimize:clear
```

### **Solution 3 : Reconstruire l'autoload**
```bash
composer dump-autoload
```

### **Solution 4 : Vérifier la configuration**
```bash
php artisan config:show
```

## 📋 **Checklist de vérification :**

### **✅ Fichiers requis :**
- [ ] `resources/views/admin/access.blade.php`
- [ ] `app/Http/Controllers/Admin/AdminAccessController.php`
- [ ] `app/Http/Middleware/AdminMiddleware.php`
- [ ] `database/migrations/*_add_is_admin_to_users_table.php`

### **✅ Routes configurées :**
- [ ] `admin.access` (GET)
- [ ] `admin.verify-access` (POST)
- [ ] `admin.direct-access` (GET)
- [ ] `admin.orders.*` (avec middleware admin)

### **✅ Base de données :**
- [ ] Colonne `is_admin` ajoutée à la table `users`
- [ ] Migration exécutée
- [ ] Utilisateur admin créé

### **✅ Middleware :**
- [ ] `AdminMiddleware` enregistré dans `Kernel.php`
- [ ] Middleware `admin` appliqué aux routes admin
- [ ] Méthode `isAdmin()` dans le modèle `User`

## 🎯 **Résultat attendu :**

Après correction, vous devriez pouvoir :
1. ✅ Accéder à `http://localhost:8000/admin-access`
2. ✅ Voir le formulaire de connexion admin
3. ✅ Saisir le token, email et mot de passe
4. ✅ Accéder à l'administration des commandes

## 🆘 **Si le problème persiste :**

1. **Vérifiez les logs** : `tail -f storage/logs/laravel.log`
2. **Testez les routes** : `php artisan route:list`
3. **Vérifiez la syntaxe** : `php -l app/Http/Controllers/Admin/AdminAccessController.php`
4. **Redémarrez tout** : `php artisan optimize:clear && php artisan serve`

Le système d'administration est maintenant **corrigé et fonctionnel** ! 🚀






