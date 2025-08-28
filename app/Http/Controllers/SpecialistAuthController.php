<?php
namespace App\Http\Controllers;

use App\Models\Specialist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class SpecialistAuthController extends Controller
{
    public function showLoginForm()
    {
        return view('specialist.auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|regex:/^\+?[1-9]\d{1,14}$/',
        ]);

        $specialist = Specialist::where('phone', $request->phone)->first();

        if (!$specialist) {
            $specialist = Specialist::create([
                'phone' => $request->phone,
                'name' => null,
                'email' => null,
                'password' => null,
            ]);
        }

        Session::put('phone', $request->phone);
        Session::put('phone_step', true);

        return redirect()->route('specialist.login')->with('success', 'Код отправлен на ваш номер. Для тестирования используйте код: 123456');
    }

    public function verifyCode(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $phone = Session::get('phone');

        if (!$phone) {
            return redirect()->route('specialist.login')->withErrors(['phone' => 'Сессия истекла. Введите номер телефона заново.']);
        }

        if ($request->code !== '123456') {
            return back()->withErrors(['code' => 'Неверный код.'])->withInput();
        }

        $specialist = Specialist::where('phone', $phone)->first();

        if (!$specialist) {
            return redirect()->route('specialist.login')->withErrors(['phone' => 'Специалист не найден.']);
        }

        Auth::guard('specialist')->login($specialist);
        Session::forget(['phone', 'phone_step']);

        return redirect()->route('specialist.dashboard');
    }

    public function showRegisterForm()
    {
        return view('specialist.auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:specialists',
            'phone' => 'required|string|regex:/^\+?[1-9]\d{1,14}$/|unique:specialists',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $specialist = Specialist::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
        ]);

        Auth::guard('specialist')->login($specialist);

        return redirect()->route('specialist.dashboard');
    }

    public function logout(Request $request)
    {
        Auth::guard('specialist')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('specialist.login');
    }
}
