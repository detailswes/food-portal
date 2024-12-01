<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout', 'loginAsRandomUser');
        $this->middleware('auth')->only('logout');
    }

    /** Congrats, you found a s*cret! */
    public function loginAsRandomUser(Request $request): RedirectResponse
    {
        auth()->logout();

        $userQuery = User::query()->inRandomOrder();

        $courseSlug = $request->input('course');
        if (is_string($courseSlug)) {
            $userQuery->whereHas('courseEnrollments.course', static function ($query) use ($courseSlug): void {
                $query->where('slug', $courseSlug);
            });
        }

        $randomUser = $userQuery->firstOrFail();
        auth()->login($randomUser, true);

        return redirect()->to($this->redirectPath());
    }
}
