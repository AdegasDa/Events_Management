<?php

class class_categories
{
    public function delete_category($pdo, $category_id)
    {
        try
        {
            $artists_category = $pdo->prepare("DELETE FROM `artists_category` WHERE `artists_category`.`category_id` = ?");
            $artists_category->execute([$category_id]);

            $artists_category->fetch();
            
            $pdo->query('SET foreign_key_checks = 0');
            
            $categories = $pdo->prepare("DELETE FROM `category` WHERE `category`.`category_id` = ?");
//            $categories = $pdo->prepare("UPDATE `category` SET `category_status` = '0' WHERE `category`.`category_id` = ?");
            $categories->execute([$category_id]);
            //do some stuff here
            $pdo->query('SET foreign_key_checks = 1');
            

            $categories->fetch();
        }
        catch ( Exception $error )
        {
            return $error->getMessage();
        }
    }
    
    public function insert_category($pdo, $resp)
    {
        try
        {
            $stmt = $pdo->prepare("INSERT INTO `category` (`category_name`, `category_type`) VALUES (?, ?);");
            $stmt->execute([$resp['category_add_name'], $resp['category_add_type']]);

            $stmt->fetch();
        }
        catch ( Exception $error )
        {
            return $error->getMessage();
        }
    }
    
    public function checkValidations($pdo, &$errors, $key, $value, $sufix, $id)
    {
        $validations = new class_validations();

        switch ( $key )
        {
            case "category_" . $sufix . "_name":
                if ( $validations->isEmpty($key, $value) ) { $errors[] = $validations->isEmpty($key, $value); }
                else if ( $validations->characterLimit($key, $value, 255) ) { $errors[] = $validations->characterLimit($key, $value, 255); }
                else if ( $this->verifyNameAvailability($pdo, $value, $key, $id) ) { $errors[] = $this->verifyNameAvailability($pdo, $value, $key, $id); }
                break;
            case "category_" . $sufix . "_type":
                if ( $validations->isEmpty($key, $value) ) { $errors[] = $validations->isEmpty($key, $value); }
                break;
        }
    }
    
    public function update_category($pdo, $resp, $id)
    {
        try
        {
            $stmt = $pdo->prepare("UPDATE `category` SET `category_name` = ?, `category_type` = ? WHERE `category`.`category_id` = ?");
            $stmt->execute([$resp['category_edit_name'], $resp['category_edit_type'], $id]);

            $stmt->fetch();
        }
        catch ( Exception $error )
        {
            return $error->getMessage();
        }
    }
    
    private function verifyNameAvailability($pdo, $category_name, $key, $id)
    {
        try
        {
            if ( $id )
            {
                $stmt = $pdo->prepare("SELECT * FROM `category` WHERE `category_name` LIKE ? AND `category_id` != ?");
                $stmt->execute([$category_name, $id]);
            }
            else
            {
                $stmt = $pdo->prepare("SELECT * FROM `category` WHERE `category_name` LIKE ?");
                $stmt->execute([$category_name]);
            }
            
            if ( $stmt->rowCount() > 0 )
            {
                return [ "code" => 1, "error" => "The name '" . $category_name . "' is already in use.", "input" => $key ];
            }
        }
        catch ( Exception $error ) { return $error->getMessage(); }
    }
}