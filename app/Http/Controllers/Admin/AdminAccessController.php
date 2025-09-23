<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminAccessController extends Controller
{
    /**
     * Tokens d'accès admin (changez ces valeurs)
     */
    private $adminTokens = [
        'homify-admin-2024-secure',
        'admin-access-2024-homify',
        'system-admin-homify-2024',
    ];

    /**
     * Afficher la page d'accès admin.
     */
    public function showAccessForm()
    {
        return view('admin.access');
    }

    /**
     * Vérifier l'accès admin avec token.
     */
    public function verifyAccess(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Vérifier le token
        if (!in_array($request->token, $this->adminTokens)) {
            return back()->withErrors(['token' => 'Token d\'accès invalide.'])->withInput();
        }

        // Vérifier les identifiants
        $credentials = $request->only('email', 'password');
        
        if (!auth()->attempt($credentials)) {
            return back()->withErrors(['email' => 'Identifiants incorrects.'])->withInput();
        }

        // Vérifier si l'utilisateur est admin
        if (!auth()->user()->isAdmin()) {
            auth()->logout();
            return back()->withErrors(['email' => 'Accès refusé. Droits administrateur requis.'])->withInput();
        }

        // Rediriger vers l'admin
        return redirect()->route('admin.dashboard')->with('success', 'Accès administrateur autorisé.');
    }

    /**
     * Accès direct avec token dans l'URL.
     */
    public function directAccess($token)
    {
        // Vérifier le token
        if (!in_array($token, $this->adminTokens)) {
            abort(404, 'Page non trouvée');
        }

        // Vérifier si l'utilisateur est connecté et admin
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            return redirect()->route('admin.access')->with('error', 'Connexion administrateur requise.');
        }

        return redirect()->route('admin.dashboard');
    }

    /**
     * Générer un nouveau token d'accès (pour les super admins).
     */
    public function generateToken()
    {
        // Cette méthode pourrait être utilisée pour générer de nouveaux tokens
        $newToken = 'admin-' . bin2hex(random_bytes(16)) . '-' . date('Y');
        
        return response()->json([
            'token' => $newToken,
            'message' => 'Nouveau token généré. Ajoutez-le à la liste des tokens valides.'
        ]);
    }
}