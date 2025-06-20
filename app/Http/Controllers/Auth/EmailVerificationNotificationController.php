<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\NewUserAuthorizationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EmailVerificationNotificationController extends Controller
{
    /**
     * Send a new authorization request notification.
     */
    public function store(Request $request): RedirectResponse
    {
        // Obtener el usuario administrador (asumiendo que tiene el ID 1)
        $admin = User::find(1);

        if (!$admin) {
            return back()->with('error', 'No se pudo enviar la solicitud. Por favor, contacta al soporte técnico.');
        }

        // Enviar la notificación al administrador
        $admin->notify(new NewUserAuthorizationRequest($request->user()));

        return back()->with('status', 'verification-link-sent');
    }
}
