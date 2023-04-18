<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
  /**
   * Redirect the user to the Google authentication page.
   *
   * @return \Illuminate\Http\Response
   */
  public function redirectToGoogle()
  {
    return Socialite::driver('google')->redirect();
  }

  /**
   * Obtain the user information from Google.
   *
   * @return \Illuminate\Http\Response
   */
  public function handleGoogleCallback()
  {
    try {
      $user = Socialite::driver('google')->user();
    } catch (\Exception $e) {
      return redirect('/dashboard');
    }

    // Check if the user already exists
    $existingUser = User::where('email', $user->email)->first();

    if ($existingUser) {
      // Log the user in
      Auth::login($existingUser, true);
    } else {
      // Create a new user account
      $newUser = new User;
      $newUser->name = $user->name;
      $newUser->email = $user->email;
      $newUser->google_id = $user->id;
      $newUser->password = bcrypt('123456');
      $newUser->save();

      // Log the user in
      Auth::login($newUser, true);
    }

    return redirect('/dashboard');
  }
}
