Feature: Users list
    Background:
        Given the following data in "public.t_user_usr" exist:
        | usr_id | usr_first_name | usr_last_name  | usr_username   | usr_username_canonical | usr_email                 | usr_email_canonical       | usr_salt                        | usr_password                                                                             | usr_enabled | usr_locked | usr_last_login      | usr_expired | usr_confirmation_token                      | usr_password_requested_at | usr_credentials_expired | cus_id | usr_status | usr_timezone |
        | 1      | Super Admin    | Super Admin    | admin          | admin                  | admin@canaltp.fr          | admin@canaltp.fr          | mbcmilo5dg0cg40wo00008o8k4gck4s | v1JownQKqoU7//pOfQvW9pcN6txgSfMXDTvSfuPHpXhXOovRWknF9D0j8icHf8Srr+dnV5T5ahy65nEqgow19w== | TRUE        | FALSE      | 2010-12-07 10:39:15 | FALSE       | MeRhd4K1QZyh_TuuP8M6ft4xRU-p1A6KKZn0zUfPVSk | 2099-09-23 13:36:17       | 0                       | 1      |  3         | Europe/Paris |
        | 2      | TestUser       | TestUser       | testuser       | testuser               | testuser@canaltp.fr       | testuser@canaltp.fr       | 4xxvm3kyqq040scwokw0woggk40k4og | 61QutBUxGwyqRv7Alodpz8aDuflM3T9tU2gTNIcN76M9c1h562OvqUpnAy5otvfdr9yO21fHhobEKAtd9BKGjQ== | TRUE        | FALSE      | 2017-12-07 10:39:15 | FALSE       | MeRhd4K1QZyh_TuuP8M6ft4xRU-p1A6KKZn0zUfPVSk | 2099-09-23 13:36:17       | 0                       | 1      |  3         | Europe/Paris |
        | 3      | TestUserLocked | TestUserLocked | testuserlocked | testuserlocked         | testuserlocked@canaltp.fr | testuserlocked@canaltp.fr | 5xxvm3kyqq040scwokw0woggk40k4og | 65QutBUxGwyqRv7Alodpz8aDuflM3T9tU2gTNIcN76M9c1h562OvqUpnAy5otvfdr9yO21fHhobEKAtd9BKGjQ== | TRUE        | TRUE       | 2019-01-08 10:00:15 | FALSE       | MeRhd4K1QZyh_TuuP8M6ft4xRU-p1A6KKZn0zUfPVSk | 2099-09-23 13:36:17       | 0                       | 1      |  3         | Europe/Paris |
        And the following data in "public.tj_user_role_ur" exist:
        | usr_id | rol_id |
        | 1      | 5      |
        And I am on "/admin/login"
        When I fill in "username" with "admin@canaltp.fr"
        And I fill in "password" with "admin"
        And I press "Connexion"
        And I am on "/admin/user/list"

    Scenario: Validation Last Connection column
        Then I should see "Liste des utilisateurs"
        And I should see "Dernière connexion"
        And I should see "07/12/2017"
        
    Scenario: Disable listing user with locked status
        Then I should see "Liste des utilisateurs"
        And I should see "Login"
        And I should see "testuser"
        And I should not see "testuserlocked"
