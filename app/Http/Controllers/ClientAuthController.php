<?php
namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\SmsCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Twilio\Rest\Client as TwilioClient;

class ClientAuthController extends Controller
{
    public function showLoginForm()
    {
        return view('client.auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'password' => 'nullable|string',
        ]);

        $client = Client::where('phone', $request->phone)->first();
        if (!$client) {
            return back()->withErrors(['phone' => 'Клиент не найден']);
        }

        if ($request->password && Auth::guard('client')->attempt(['phone' => $request->phone, 'password' => $request->password])) {
            return redirect()->route('client.dashboard');
        }

        $code = rand(100000, 999999);
        SmsCode::create([
            'user_type' => 'client',
            'user_id' => $client->id,
            'phone' => $client->phone,
            'code' => $code,
            'expires_at' => now()->addMinutes(10),
        ]);

        $twilio = new TwilioClient(config('services.twilio.sid'), config('services.twilio.token'));
        $twilio->messages->create($client->phone, [
            'from' => config('services.twilio.from'),
            'body' => "Ваш код для входа: $code",
        ]);

        session(['login_phone' => $client->phone]);
        return redirect()->route('client.auth.verify');
    }

    public function showVerifyCodeForm()
    {
        return view('client.auth.verify');
    }

    public function verifyCode(Request $request)
    {
        $request->validate(['code' => 'required|string|size:6']);
        $phone = session('login_phone');
        if (!$phone) {
            return redirect()->route('client.auth.login')->withErrors(['code' => 'Сессия истекла']);
        }

        $smsCode = SmsCode::where('user_type', 'client')
            ->where('phone', $phone)
            ->where('code', $request->code)
            ->where('expires_at', '>=', now())
            ->first();

        if (!$smsCode) {
            return back()->withErrors(['code' => 'Неверный или просроченный код']);
        }

        $client = Client::where('phone', $phone)->first();
        Auth::guard('client')->login($client);
        $smsCode->delete();
        session()->forget('login_phone');
        return redirect()->route('client.dashboard');
    }

    public function logout()
    {
        Auth::guard('client')->logout();
        return redirect()->route('client.auth.login');
    }
}
