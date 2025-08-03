<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\OtpCode;
use App\Models\Master;
use App\Models\MasterAuthProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class MasterAuthController extends Controller
{
    public function showLoginForm()
    {
        return view('master.auth.login');
    }

    public function sendCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        OtpCode::where('identifier', $request->email)->where('type', 'email')->delete();

        $code = '';
        for ($i = 0; $i < 6; $i++) {
            $code .= (string)rand(0, 9);
        }
        $code = "123456";
        OtpCode::create([
            'identifier' => $request->email,
            'type' => 'email',
            'code' => Hash::make($code),
            'expires_at' => Carbon::now()->addMinutes(5),
        ]);

        Mail::raw("Ваш код подтверждения: $code", function ($message) use ($request) {
            $message->to($request->email)->subject('Код подтверждения YourOrd');
        });

        return redirect()->route('master.auth.verify-form')->with([
            'message' => 'Код отправлен',
            'email' => $request->email,
        ]);
    }

    public function showVerifyForm()
    {
        return view('master.auth.verify-code');
    }

    public function verifyCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|string',
        ]);

        $otp = OtpCode::where('identifier', $request->email)
            ->where('type', 'email')
            ->where('used', false)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if ($otp && Hash::check($request->code, $otp->code)) {
            $otp->update(['used' => true]);

            $master = Master::where('email', $request->email)->first();
            if (!$master) {
                $master = Master::create(['email' => $request->email]);
            }

            MasterAuthProvider::updateOrCreate(
                [
                    'master_id' => $master->id,
                    'provider' => 'email',
                    'provider_id' => $request->email,
                ]
            );

            Auth::guard('master')->login($master);
            return redirect()->route('master.dashboard');
        }

        return redirect()->back()->withErrors(['code' => 'Неверный или истекший код']);
    }

    public function logout(Request $request)
    {
        Auth::guard('master')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/master/login');
    }
}
