<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\OtpCode;
use App\Models\Client;
use App\Models\Master;
use App\Models\AuthProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function sendCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'user_type' => 'required|in:client,master',
        ]);

        // Удаляем старые коды
        OtpCode::where('identifier', $request->email)->where('type', 'email')->delete();

        // Генерация кода
        $code = Str::random(6);
        OtpCode::create([
            'identifier' => $request->email,
            'type' => 'email',
            'code' => Hash::make($code),
            'expires_at' => Carbon::now()->addMinutes(5),
        ]);

        // Отправка email
        Mail::raw("Ваш код подтверждения: $code", function ($message) use ($request) {
            $message->to($request->email)->subject('Код подтверждения YourOrd');
        });

        return redirect()->route('auth.verify-form')->with([
            'message' => 'Код отправлен',
            'email' => $request->email,
            'user_type' => $request->user_type
        ]);
    }

    public function showVerifyForm()
    {
        return view('auth.verify-code');
    }

    public function verifyCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|string',
            'user_type' => 'required|in:client,master',
        ]);

        $otp = OtpCode::where('identifier', $request->email)
            ->where('type', 'email')
            ->where('used', false)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if ($otp && Hash::check($request->code, $otp->code)) {
            $otp->update(['used' => true]);

            $model = $request->user_type === 'client' ? Client::class : Master::class;
            $user = $model::where('email', $request->email)->first();

            if (!$user) {
                $user = $model::create(['email' => $request->email]);
            }

            AuthProvider::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'user_type' => $request->user_type,
                    'provider' => 'email',
                    'provider_id' => $request->email,
                ]
            );

            Auth::guard($request->user_type)->login($user);
            return redirect()->route($request->user_type . '.dashboard');
        }

        return redirect()->back()->withErrors(['code' => 'Неверный или истекший код']);
    }

    public function logout(Request $request)
    {
        if (Auth::guard('client')->check()) {
            Auth::guard('client')->logout();
        } elseif (Auth::guard('master')->check()) {
            Auth::guard('master')->logout();
        }
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
