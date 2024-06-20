<?php

class class_artists
{
    public function delete_artist($pdo, $artist_id)
    {
        try
        {
            $artist = $pdo->prepare("UPDATE `artists` SET `artist_status` = '0' WHERE `artists`.`artist_id` = ?");
            $artist->execute([$artist_id]);

            $artist->fetch();
        }
        catch ( Exception $error )
        {
            return $error->getMessage();
        }
    }
    
    public function insert_artist($pdo, $resp)
    {
        try
        {
            $artist = $pdo->prepare("INSERT INTO `artists`(`artist_name`, `artist_location_id`) VALUES (?, ?)");
            $artist->execute([$resp['artist_add_name'], $resp['artist_add_location']]);

            $id = $pdo->lastInsertId();

            $category = $pdo->prepare("INSERT INTO `artists_category`(`artist_id`, `category_id`) VALUES (?, ?)");
            $category->execute([$id, $resp['artist_add_category']]);

            $category->fetch();
        }
        catch ( Exception $error )
        {
            return $error->getMessage();
        }
    }
    
    public function update_artist($pdo, $resp, $id)
    {
        try
        {
            $category = $pdo->prepare("UPDATE `artists_category` SET `category_id` = ? WHERE `artist_id` = ?");
            $category->execute([$resp['artist_edit_category'], $id]);

            $category->fetch();

            $artist = $pdo->prepare("UPDATE `artists` SET `artist_name` = ?,`artist_location_id` = ? WHERE `artist_id` = ?");
            $artist->execute([$resp['artist_edit_name'], $resp['artist_edit_location'], $id]);

            $artist->fetch();
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
            case "artist_" . $sufix . "_name":
                if ( $validations->isEmpty($key, $value) ) { $errors[] = $validations->isEmpty($key, $value); }
                else if ( $validations->characterLimit($key, $value, 200) ) { $errors[] = $validations->characterLimit($key, $value, 100); }
                else if ( $this->verifyNameAvailability($pdo, $value, $key, $id) ) { $errors[] = $this->verifyNameAvailability($pdo, $value, $key, $id); }
                break;
            case "artist_" . $sufix . "_location":
                if ( $validations->isEmpty($key, $value) ) { $errors[] = $validations->isEmpty($key, $value); }
                break;
            case "artist_" . $sufix . "_category":
                if ( $validations->isEmpty($key, $value) ) { $errors[] = $validations->isEmpty($key, $value); }
                break;
        }
    }
    
    public function categories($pdo)
    {
        try
        {
            $stmt = $pdo->prepare("SELECT * FROM `category` WHERE `category`.`category_type` = 1 ORDER BY `category`.`category_name` ASC");
            $stmt->execute();

            $return = array();

            $return[] = [ "id" => 0, "name" => "Select a category..." ];

            while ( $row = $stmt->fetch() )
            {
                $return[] = [ "id" => $row['category_id'], "name" => $row['category_name'] ];
            }

            return $return;
        }
        catch ( Exception $error )
        {
            return $error->getMessage();
        }
    }
    
    public function locations($pdo)
    {
        try
        {
            $stmt = $pdo->prepare("SELECT * FROM `locations` ORDER BY `locations`.`location_country` ASC, `locations`.`location_city` ASC");
            $stmt->execute();

            $return = array();

            $return[] = [ "id" => 0, "name" => "Select a location..." ];

            while ( $row = $stmt->fetch() )
            {
                $return[] = [ "id" => $row['location_id'], "name" => $row['location_country'] . " - " . $row['location_city'] ];
            }

            return $return;
        }
        catch ( Exception $error )
        {
            return $error->getMessage();
        }
    }
    
    private function verifyNameAvailability($pdo, $name, $key, $id)
    {
        try
        {
            if ( $id )
            {
                $stmt = $pdo->prepare("SELECT * FROM `artists` WHERE `artist_name` LIKE ? AND `artist_id` != ?");
                $stmt->execute([$name, $id]);
            }
            else
            {
                $stmt = $pdo->prepare("SELECT * FROM `artists` WHERE `artist_name` LIKE ?");
                $stmt->execute([$name]);
            }
            
            if ( $stmt->rowCount() > 0 )
            {
                return [ "code" => 1, "error" => "The name '" . $name . "' is already in use.", "input" => $key ];
            }
        }
        catch ( Exception $error ) { return $error->getMessage(); }
    }
}