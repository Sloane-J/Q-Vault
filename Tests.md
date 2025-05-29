q-vault-97198806:~/Q-Vault{main}$ composer test
> @php artisan config:clear --ansi

   INFO  Configuration cache cleared successfully.  

> @php artisan test
    
## mail testing to do later
   FAIL  Tests\Feature\Auth\PasswordResetTest
  ✓ reset password link screen can be rendered                                   0.25s  
  ⨯ reset password link can be requested                                         0.13s  
  ⨯ reset password screen can be rendered                                        0.10s  
  ⨯ password can be reset with valid token                                      0.12s                                                        0.14s  
  