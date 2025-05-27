q-vault-97198806:~/Q-Vault{main}$ composer test
> @php artisan config:clear --ansi

   INFO  Configuration cache cleared successfully.  

> @php artisan test

   PASS  Tests\Unit\ExampleTest
  ✓ that true is true

   FAIL  Tests\Feature\Auth\AuthenticationTest
  ✓ login screen can be rendered                                                 0.81s  
  ⨯ users can authenticate using the login screen                                0.25s  
  ⨯ users can not authenticate with invalid password                             0.21s  
  ✓ users can logout                                                             0.09s  

   PASS  Tests\Feature\Auth\EmailVerificationTest
  ✓ email verification screen can be rendered                                    0.10s  
  ✓ email can be verified                                                        0.07s  
  ✓ email is not verified with invalid hash                                      0.05s  

   FAIL  Tests\Feature\Auth\PasswordConfirmationTest
  ✓ confirm password screen can be rendered                                      0.16s  
  ⨯ password can be confirmed                                                    0.13s  
  ⨯ password is not confirmed with invalid password                              0.12s  

   FAIL  Tests\Feature\Auth\PasswordResetTest
  ✓ reset password link screen can be rendered                                   0.25s  
  ⨯ reset password link can be requested                                         0.13s  
  ⨯ reset password screen can be rendered                                        0.10s  
  ⨯ password can be reset with valid token                                       0.12s  

   FAIL  Tests\Feature\Auth\RegistrationTest
  ✓ registration screen can be rendered                                          0.33s  
  ⨯ new users can register                                                       0.14s  

   FAIL  Tests\Feature\DashboardTest
  ✓ guests are redirected to the login page                                      0.05s  
  ⨯ authenticated users can visit the dashboard                                  0.05s  

   PASS  Tests\Feature\ExampleTest
  ✓ returns a successful response                                                0.05s  

   FAIL  Tests\Feature\Settings\PasswordUpdateTest
  ⨯ password can be updated                                                      0.14s  
  ⨯ correct password must be provided to update password                         0.10s  

   FAIL  Tests\Feature\Settings\ProfileUpdateTest
  ✓ profile page is displayed                                                    0.61s  
  ⨯ profile information can be updated                                           0.14s  
  ⨯ email verification status is unchanged when email address is unchanged       0.14s  
  ⨯ user can delete their account                                                0.18s  
  ⨯ correct password must be provided to delete account   