<?php

class class_users
{
    public function active_user($pdo, $user_id)
    {
        try
        {
            $users = $pdo->prepare("UPDATE `users` SET `user_role` = 'user', `user_status` = 1 WHERE `users`.`user_id` = ?");
            $users->execute([$user_id]);

            $users->fetch();
        }
        catch ( Exception $error )
        {
            return $error->getMessage();
        }
    }
    
    public function delete_user($pdo, $user_id)
    {
        try
        {
            $events_users = $pdo->prepare("DELETE FROM `events_users` WHERE `events_users`.`user_id` = ?");
            $events_users->execute([$user_id]);

            $events_users->fetch();

            //$users = $pdo->prepare("DELETE FROM `users` WHERE `user_id` = ?");
            $users = $pdo->prepare("UPDATE `users` SET `user_status` = '0' WHERE `users`.`user_id` = ?");
            $users->execute([$user_id]);

            $users->fetch();
        }
        catch ( Exception $error )
        {
            return $error->getMessage();
        }
    }
    
    public function insert_user($pdo, $resp)
    {
        try
        {
            $stmt = $pdo->prepare("INSERT INTO `users` (`user_username`, `user_password`, `user_register_date`, `user_name`, `user_phone`, `user_email`, `user_birth_date`, `user_address`, `user_cc`, `user_role`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?);");
            $stmt->execute([$resp['user_add_username'], $this->encryptPassword($resp['user_add_password']), date("Y-m-d"), $resp['user_add_name'], $resp['user_add_phone'], $resp['user_add_email'], date("Y-m-d", strtotime($resp['user_add_birth_date'])), $resp['user_add_address'], $resp['user_add_cc'], $resp['user_add_role']]);

            $stmt->fetch();
        }
        catch ( Exception $error )
        {
            return $error->getMessage();
        }
    }
    
    public function encryptPassword($password) 
    {
        $encryptedPassword = password_hash($password, PASSWORD_DEFAULT);

        return $encryptedPassword;
    }
    
    public function checkValidations($pdo, &$errors, $key, $value, $sufix, $id)
    {
        $validations = new class_validations();

        switch ( $key )
        {
            case "user_" . $sufix . "_name":
                if ( $validations->isEmpty($key, $value) ) { $errors[] = $validations->isEmpty($key, $value); }
                else if ( $validations->characterLimit($key, $value, 100) ) { $errors[] = $validations->characterLimit($key, $value, 100); }
                break;
            case "user_" . $sufix . "_username":
                if ( $validations->isEmpty($key, $value) ) { $errors[] = $validations->isEmpty($key, $value); }
                else if ( $validations->characterLimit($key, $value, 100) ) { $errors[] = $validations->characterLimit($key, $value, 100); }
                else if ( $this->verifyUsernameAvailability($pdo, $value, $key, $id) ) { $errors[] = $this->verifyUsernameAvailability($pdo, $value, $key, $id); }
                break;
            case "user_" . $sufix . "_email":
                if ( $validations->isEmpty($key, $value) ) { $errors[] = $validations->isEmpty($key, $value); }
                else if ( $validations->characterLimit($key, $value, 255) ) { $errors[] = $validations->characterLimit($key, $value, 100); }
                else if ( $validations->checkEmail($key, $value) ) { $errors[] = $validations->checkEmail($key, $value); }
                else if ( $this->verifyEmailAvailability($pdo, $value, $key, $id) ) { $errors[] = $this->verifyEmailAvailability($pdo, $value, $key, $id); }
                break;
            case "user_" . $sufix . "_role":
                if ( $validations->isEmpty($key, $value) ) { $errors[] = $validations->isEmpty($key, $value); }
                else if ( $validations->characterLimit($key, $value, 50) ) { $errors[] = $validations->characterLimit($key, $value, 100); }
                break;
            case "user_" . $sufix . "_password":
                if ( $validations->isEmpty($key, $value) ) { $errors[] = $validations->isEmpty($key, $value); }
                else if ( $validations->characterLimit($key, $value, 72) ) { $errors[] = $validations->characterLimit($key, $value, 100); }
                else if ( $validations->checkPassword($key, $value) ) { $errors[] = $validations->checkPassword($key, $value); }
                break;
            case "user_" . $sufix . "_password1":
                if ( $validations->isEmpty($key, $value) ) { $errors[] = $validations->isEmpty($key, $value); }
                else if ( $validations->characterLimit($key, $value, 72) ) { $errors[] = $validations->characterLimit($key, $value, 100); }
                break;
            case "user_" . $sufix . "_address":
                if ( $validations->isEmpty($key, $value) ) { $errors[] = $validations->isEmpty($key, $value); }
                else if ( $validations->characterLimit($key, $value, 100) ) { $errors[] = $validations->characterLimit($key, $value, 100); }
                break;
            case "user_" . $sufix . "_phone":
                if ( $validations->isEmpty($key, $value) ) { $errors[] = $validations->isEmpty($key, $value); }
                else if ( $validations->checkPhone($key, $value) ) { $errors[] = $validations->checkPhone($key, $value); }
                break;
            case "user_" . $sufix . "_cc":
                if ( $validations->isEmpty($key, $value) ) { $errors[] = $validations->isEmpty($key, $value); }
                else if ( $validations->checkCC($key, $value) ) { $errors[] = $validations->checkCC($key, $value); }
                break;
            case "user_" . $sufix . "_birth_date":
                if ( $validations->isEmpty($key, $value) ) { $errors[] = $validations->isEmpty($key, $value); }
                else if ( $validations->checkDateValid($key, $value) ) { $errors[] = $validations->checkDateValid($key, $value); }
                else if ( $validations->checkFutureDate($key, $value) ) { $errors[] = $validations->checkFutureDate($key, $value); }
                break;
        }
    }
    
    public function update_user($pdo, $resp, $id)
    {
        try
        {
            $stmt = $pdo->prepare("UPDATE `users` SET `user_name` = ?, `user_username` = ?, `user_email` = ?, `user_address` = ?, `user_phone` = ?, `user_cc` = ?, `user_birth_date` = ?, `user_role` = ? WHERE `users`.`user_id` = ?");
            $stmt->execute([$resp['user_edit_name'], $resp['user_edit_username'], $resp['user_edit_email'], $resp['user_edit_address'], $resp['user_edit_phone'], $resp['user_edit_cc'], date("Y-m-d", strtotime($resp['user_edit_birth_date'])), $resp['user_edit_role'], $id]);

            $stmt->fetch();
        }
        catch ( Exception $error )
        {
            return $error->getMessage();
        }
    }
    
    private function verifyUsernameAvailability($pdo, $user, $key, $id)
    {
        try
        {
            if ( $id )
            {
                $stmt = $pdo->prepare("SELECT * FROM `users` WHERE `user_username` LIKE ? AND `user_id` != ?");
                $stmt->execute([$user, $id]);
            }
            else
            {
                $stmt = $pdo->prepare("SELECT * FROM `users` WHERE `user_username` LIKE ?");
                $stmt->execute([$user]);
            }
            
            if ( $stmt->rowCount() > 0 )
            {
                return [ "code" => 1, "error" => "The username '" . $user . "' is already in use.", "input" => $key ];
            }
        }
        catch ( Exception $error ) { return $error->getMessage(); }
    }
    
    private function verifyEmailAvailability($pdo, $email, $key, $id)
    {
        try
        {
            if ( $id )
            {
                $stmt = $pdo->prepare("SELECT * FROM `users` WHERE `user_email` LIKE ? AND `user_id` != ?");
                $stmt->execute([$email, $id]);
            }
            else
            {
                $stmt = $pdo->prepare("SELECT * FROM `users` WHERE `user_email` LIKE ?");
                $stmt->execute([$email]);
            }

            if ( $stmt->rowCount() > 0 )
            {
                return [ "code" => 1, "error" => "The email '" . $email . "' is already in use.", "input" => $key ];
            }
        }
        catch ( Exception $error ) { return $error->getMessage(); }
    }
}
