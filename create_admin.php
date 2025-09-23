<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\Hash;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;

echo "=== Création d'un utilisateur administrateur ===\n\n";

// Vérifier les utilisateurs existants
echo "Utilisateurs existants :\n";
$users = User::all(['id', 'name', 'email', 'is_admin']);
foreach ($users as $user) {
    echo "- ID: {$user->id} | Nom: {$user->name} | Email: {$user->email} | Admin: " . ($user->is_admin ? 'Oui' : 'Non') . "\n";
}

echo "\n";

// Demander les informations
echo "Créer un nouvel administrateur :\n";
$name = readline("Nom complet : ");
$email = readline("Email : ");
$password = readline("Mot de passe : ");

if (empty($name) || empty($email) || empty($password)) {
    echo "❌ Tous les champs sont requis.\n";
    exit(1);
}

// Vérifier si l'email existe déjà
if (User::where('email', $email)->exists()) {
    echo "❌ Un utilisateur avec cet email existe déjà.\n";
    exit(1);
}

// Créer l'utilisateur admin
try {
    $user = User::create([
        'name' => $name,
        'email' => $email,
        'password' => Hash::make($password),
        'is_admin' => true,
    ]);

    echo "✅ Administrateur créé avec succès !\n";
    echo "ID: {$user->id}\n";
    echo "Nom: {$user->name}\n";
    echo "Email: {$user->email}\n";
    echo "Admin: " . ($user->is_admin ? 'Oui' : 'Non') . "\n";
    echo "\n🔐 ACCÈS ADMINISTRATEUR SÉCURISÉ :\n";
    echo "=====================================\n";
    echo "URL d'accès admin : http://localhost:8000/admin-access\n";
    echo "URL directe (token) : http://localhost:8000/admin-access-homify-admin-2024-secure\n";
    echo "URL directe (token 2) : http://localhost:8000/admin-access-admin-access-2024-homify\n";
    echo "URL directe (token 3) : http://localhost:8000/admin-access-system-admin-homify-2024\n";
    echo "\n📋 TOKENS D'ACCÈS VALIDES :\n";
    echo "- homify-admin-2024-secure\n";
    echo "- admin-access-2024-homify\n";
    echo "- system-admin-homify-2024\n";
    echo "\n⚠️  IMPORTANT : Gardez ces URLs et tokens secrets !\n";

} catch (Exception $e) {
    echo "❌ Erreur lors de la création : " . $e->getMessage() . "\n";
    exit(1);
}
