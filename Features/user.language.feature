Feature: User language edit
    Scenario:
        Given the following data in "public.t_user_usr" exist:
            | usr_first_name | usr_last_name | usr_username | usr_username_canonical | usr_email           | usr_email_canonical | usr_salt                        | usr_password                                                                             | usr_enabled | usr_locked | usr_expired | usr_confirmation_token                      | usr_credentials_expired | cus_id | usr_status | usr_timezone |
            | TestUser       | TestUser      | TestUser     | testuser               | testuser@canaltp.fr | testuser@canaltp.fr | 4xxvm3kyqq040scwokw0woggk40k4og | 61QutBUxGwyqRv7Alodpz8aDuflM3T9tU2gTNIcN76M9c1h562OvqUpnAy5otvfdr9yO21fHhobEKAtd9BKGjQ== | TRUE        | FALSE      | FALSE       | MeRhd4K1QZyh_TuuP8M6ft4xRU-p1A6KKZn0zUfPVSk | 0                       | 1      |  1         | Europe/Paris |
        And I am on "/admin/login"
        When I fill in "Nom d'utilisateur :" with "testuser"
        And I fill in "Mot de passe :" with "Test2016*!"
        And I press "Connexion"
        And I am on "/admin/user/profil"
        Then I should see "français" in the "#edit_user_profil_language" element
        When I fill in "Langue" with "english"
        And I press "Sauvegarder"
        And I should see "Your information has been saved" in the ".alert-success" element
        And I should see "english" in the "#edit_user_profil_language" element
