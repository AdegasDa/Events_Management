<?php

class class_events
{
    public function delete_event($pdo, $event_id)
    {
        try
        {
            $events_users = $pdo->prepare("DELETE FROM `events_users` WHERE `events_users`.`event_id` = ?");
            $events_users->execute([$event_id]);

            $events_users->fetch();
            
            $events_artists = $pdo->prepare("DELETE FROM `artists_events` WHERE `artists_events`.`event_id` = ?");
            $events_artists->execute([$event_id]);

            $events_artists->fetch();

            //$event = $pdo->prepare("DELETE FROM `events` WHERE `event_id` = ?");
            $event = $pdo->prepare("UPDATE `events` SET `event_status` = '0' WHERE `events`.`event_id` = ?");
            $event->execute([$event_id]);

            $event->fetch();
        }
        catch ( Exception $error )
        {
            return $error->getMessage();
        }
    }
    
    public function categories($pdo)
    {
        try
        {
            $stmt = $pdo->prepare("SELECT * FROM `category` WHERE `category`.`category_type` = 2 AND `category`.`category_status` = 1 ORDER BY `category_name` ASC");
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
    
    public function artists($pdo, $id)
    {
        try
        {
            $stmt = $pdo->prepare("SELECT * FROM `artists` WHERE `artists`.`artist_status` = 1 ORDER BY `artists`.`artist_name` ASC");
            $stmt->execute();

            $return['all'] = array();
            
            while ( $row = $stmt->fetch() )
            {
                $return['all'][] = [ "id" => $row['artist_id'], "name" => $row['artist_name'] ];
            }
            
            $selected = $pdo->prepare("SELECT * FROM `artists_events` WHERE `event_id` = ?");
            $selected->execute([$id]);
            
            $return['selected'] = array();
            
            while ( $row = $selected->fetch() )
            {
                $return['selected'][] = [ "id" => $row['artist_id'] ];
            }

            return $return;
        }
        catch ( Exception $error ) { return $error->getMessage(); }
    }
    
    public function subscribers($pdo, $id)
    {
        try
        {
            $stmt = $pdo->prepare("SELECT * FROM `users` WHERE `users`.`user_status` = 1 ORDER BY `users`.`user_name` ASC");
            $stmt->execute();

            $return['all'] = array();
            
            while ( $row = $stmt->fetch() )
            {
                $return['all'][] = [ "id" => $row['user_id'], "name" => $row['user_name'] ];
            }
            
            $selected = $pdo->prepare("SELECT * FROM `events_users` WHERE `event_id` = ?");
            $selected->execute([$id]);
            
            $return['selected'] = array();
            
            while ( $row = $selected->fetch() )
            {
                $return['selected'][] = [ "id" => $row['user_id'] ];
            }

            return $return;
        }
        catch ( Exception $error ) { return $error->getMessage(); }
    }
    
    public function insert_event($pdo, $resp, $artists)
    {
        try
        {
            
            $stmt = $pdo->prepare("INSERT INTO `events`(`event_name`, `event_detail`, `event_category_id`, `event_address`, `event_location_id`, `event_date`, `event_price`, `event_limit`, `event_subscription_limit`, `event_ext_img`) VALUES (?, ? ,? ,? ,? ,? ,? ,? , ?, ?)");
            $stmt->execute([$resp['event_add_name'], $resp['event_add_detail'], $resp['event_add_category'], $resp['event_add_address'], $resp['event_add_location'], $resp['event_add_date'], number_format($resp['event_add_price'], 2), $resp['event_add_capacity'], $resp['event_add_subscription_limit'], pathinfo($resp['event_add_img']['name'], PATHINFO_EXTENSION) ]);

            $id = $pdo->lastInsertId();
            
            $validations = new class_validations();
            
            $validations->save_image($resp['event_add_img'], $id);
            
            for ( $i = 0; $i < count($artists); $i++ )
            {
                $insert_artists = $pdo->prepare("INSERT INTO `artists_events`(`artist_id`, `event_id`) VALUES (?, ?)");
                $insert_artists->execute([$artists[$i], $id]);
            }
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
            case "event_" . $sufix . "_name":
                if ( $validations->isEmpty($key, $value) ) { $errors[] = $validations->isEmpty($key, $value); }
                else if ( $validations->characterLimit($key, $value, 200) ) { $errors[] = $validations->characterLimit($key, $value, 100); }
                else if ( $this->verifyNameAvailability($pdo, $value, $key, $id) ) { $errors[] = $this->verifyNameAvailability($pdo, $value, $key, $id); }
                break;
            case "event_" . $sufix . "_category":
                if ( $validations->isEmpty($key, $value) ) { $errors[] = $validations->isEmpty($key, $value); }
                break;
            case "event_" . $sufix . "_detail":
                if ( $validations->isEmpty($key, $value) ) { $errors[] = $validations->isEmpty($key, $value); }
                break;
            case "event_" . $sufix . "_address":
                if ( $validations->isEmpty($key, $value) ) { $errors[] = $validations->isEmpty($key, $value); }
                else if ( $validations->characterLimit($key, $value, 50) ) { $errors[] = $validations->characterLimit($key, $value, 100); }
                break;
            case "event_" . $sufix . "_location":
                if ( $validations->isEmpty($key, $value) ) { $errors[] = $validations->isEmpty($key, $value); }
                break;
            case "event_" . $sufix . "_capacity":
                if ( $validations->isEmpty($key, $value) ) { $errors[] = $validations->isEmpty($key, $value); }
                break;
            case "event_" . $sufix . "_date":
                if ( $validations->isEmpty($key, $value) ) { $errors[] = $validations->isEmpty($key, $value); }
                else if ( $validations->checkDateValid($key, $value) ) { $errors[] = $validations->checkDateValid($key, $value); }
                break;
            case "event_" . $sufix . "_subscription_limit":
                if ( $validations->isEmpty($key, $value) ) { $errors[] = $validations->isEmpty($key, $value); }
                else if ( $validations->checkDateValid($key, $value) ) { $errors[] = $validations->checkDateValid($key, $value); }
                break;
            case "event_" . $sufix . "_price":
                if ( $validations->isEmpty($key, $value) ) { $errors[] = $validations->isEmpty($key, $value); }
                else if ( $validations->isPrice($key, $value) ) { $errors[] = $validations->isPrice($key, $value); }
                break;
            case "event_" . $sufix . "_img":
                if ( $validations->isEmpty($key, $value) ) { $errors[] = $validations->isEmpty($key, $value); }
                else if ( $validate = $validations->validate_image_file($key, $value) ) { $errors[] = $validate; }
                break;
        }
    }
    
    public function update_event($pdo, $resp, $id, $artists)
    {
        try
        {
            $stmt = $pdo->prepare("UPDATE `events` SET `event_name` = ?,`event_detail` = ?,`event_category_id` = ?,`event_address` = ?,`event_location_id` = ?,`event_date` = ?, `event_price` = ?, `event_limit` = ?,`event_subscription_limit` = ? WHERE `event_id` = ?");
            $stmt->execute([$resp['event_edit_name'], $resp['event_edit_detail'], $resp['event_edit_category'], $resp['event_edit_address'], $resp['event_edit_location'], $resp['event_edit_date'], number_format($resp['event_edit_price'], 2), $resp['event_edit_capacity'], $resp['event_edit_subscription_limit'], $id]);
            
            $check = $pdo->prepare("DELETE FROM `artists_events` WHERE `event_id` = ?");
            $check->execute([$id]);
            
            for ( $i = 0; $i < count($artists); $i++ )
            {
                $insert_artists = $pdo->prepare("INSERT INTO `artists_events`(`artist_id`, `event_id`) VALUES (?, ?)");
                $insert_artists->execute([$artists[$i], $id]);
            }
        }
        catch ( Exception $error )
        {
            return $error->getMessage();
        }
    }
    
    public function update_event_subs($pdo, $id, $subs)
    {
        try
        {
            $check = $pdo->prepare("DELETE FROM `events_users` WHERE `event_id` = ?");
            $check->execute([$id]);
            
            for ( $i = 0; $i < count($subs); $i++ )
            {
                $insert_artists = $pdo->prepare("INSERT INTO `events_users`(`user_id`, `event_id`) VALUES (?, ?)");
                $insert_artists->execute([$subs[$i], $id]);
            }
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
                $stmt = $pdo->prepare("SELECT * FROM `events` WHERE `event_name` LIKE ? AND `event_id` != ?");
                $stmt->execute([$name, $id]);
            }
            else
            {
                $stmt = $pdo->prepare("SELECT * FROM `events` WHERE `event_name` LIKE ? AND `event_status` = 1");
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
