<?php

class class_validations
{
    public function isEmpty($key, $value)
    {
        if ( empty($value) )
        {
            return [ "code" => 1, "error" => "Preencha este campo", "input" => $key ];
        }
    }
    
    public function characterLimit($key, $value, $limit)
    {
        if ( strlen($value) > $limit )
        {
            return [ "code" => 1, "error" => "This field contains more than " . $limit . " characters!", "input" => $key ];
        }
    }
    
    public function checkEmail($key, $value)
    {
        if ( !filter_var($value, FILTER_VALIDATE_EMAIL) )
        {
            return [ "code" => 1, "error" => "The email provided is not valid.", "input" => $key ];
        }
    }
    
    public function checkDateValid($key, $value)
    {
        if ( strtotime($value) == false )
        {
            return [ "code" => 1, "error" => "The date provided is not valid.", "input" => $key ];
        }
    }
    
    public function checkFutureDate($key, $value)
    {
        if ( new DateTime($value) > new DateTime() )
        {
            return [ "code" => 1, "error" => "Enter a date equal to or before the current date.", "input" => $key ];
        }
    }
    
    public function checkPhone($key, $value)
    {
        $regex = "/^\+?\d{1,3}[-.\s]?\(?\d{2,4}\)?[-.\s]?\d{3,4}[-.\s]?\d{3,4}$/";
        
        if ( !preg_match($regex, $value) )
        {
            return [ "code" => 1, "error" => "The phone number provided is not valid.", "input" => $key ];
        }
        
        return preg_match("/^[0-9]{10}$/", $value); // Verifica se o telefone possui 10 dígitos numéricos
    }
    
    public function checkCC($key, $value)
    {
        // Remover espaços em branco e hífens do nº do CC
        $number = str_replace(array(' ', '-'), '', $value);

        // Verificar se o número tem o comprimento correto (8 dígitos)
        if ( strlen($number) !== 8 )
        {
            return [ "code" => 1, "error" => "The citizen card number provided is not valid.", "input" => $key ];
        }

        // Algoritmo de verificação do nº do CC
        $soma = 0;
        for ( $i = 0; $i < 8; $i++ )
        {
            $soma += $number[$i] * (9 - $i);
        }

        $type_check = $soma % 11;
        
        if ( $type_check === 0 || $type_check === 1 )
        {
            $type_check = 0;
        }
        else
        {
            $type_check = 11 - $type_check;
        }
        
        if ( intval($number[7]) === $type_check )
        {
            return [ "code" => 1, "error" => "The citizen card number provided is not valid.", "input" => $key ];
        }
    }
    
    public function checkPassword($key, $value)
    {
        $regex_uppercase = preg_match("/[A-Z]/", $value);
        $regex_lowercase = preg_match("/[a-z]/", $value);
        $regex_number = preg_match("/[0-9]/", $value);
        $regex_special = preg_match("/[^A-Za-z0-9]/", $value);
        
        if ( strlen($value) < 8 || !$regex_uppercase || !$regex_lowercase || !$regex_number || !$regex_special )
        {
            return [ "code" => 1, "error" => "Password must be at least 8 characters long, including uppercase, lowercase, numbers and special characters.", "input" => $key ];
        }
    }
    
    public function isPrice($key, $value)
    {
        $value = str_replace(',', '.', $value);
        
        if ( !is_numeric($value) )
        {
            return [ "code" => 1, "error" => "The value is not a number.", "input" => $key ];
        }
        else if ( $value < 0 )
        {
            return [ "code" => 1, "error" => "The value cannot be negative.", "input" => $key ];
        }
        else if ( !preg_match('/^\d+(\.\d{1,2})?$/', $value) )
        {
            return [ "code" => 1, "error" => "The value does not have the correct price format.", "input" => $key ];
        }
    }
    
    public function validate_image_file($key, $value)
    {
        if ( isset($value) )
        {
            $file = $value;
            
            if ( !$this->is_valid_image($file['tmp_name']) )
            {
                return [ 'code' => 1, 'error' => 'Only image files are allowed.', "input" => $key ];
            }
//            if ( $this->is_valid_image($file['tmp_name']) )
//            {
//                $uploadDir = "/var/www/images/events" . "/";
//                $uploadFilePath = $uploadDir . "event_" . $id . "." . pathinfo($file['name'], PATHINFO_EXTENSION);
//                
//                if ( !move_uploaded_file($file['tmp_name'], $uploadFilePath) )
//                {
//                    return [ 'code' => 1, 'error' => 'Error saving file.', "input" => $key ];
//                }
//            }
//            else
//            {
//                return [ 'code' => 1, 'error' => 'Only image files are allowed.', "input" => $key ];
//            }
        }
        else
        {
            return [ 'code' => 1, 'error' => 'No files sent.', "input" => $key ];
        }
    }
    
    private function is_valid_image($filename)
    {
        // Verificar se o nome do arquivo não está vazio
        if ( empty($filename) )
        {
            return false;
        }
        
        $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $fileMimeType = mime_content_type($filename);

        return in_array($fileMimeType, $allowedMimeTypes);
    }
    
    public function save_image($file, $id)
    {
        $uploadDir = "/var/www/html/assets/img/events" . "/";
        $uploadFilePath = $uploadDir . "event_" . $id . "." . pathinfo($file['name'], PATHINFO_EXTENSION);

        if ( !move_uploaded_file($file['tmp_name'], $uploadFilePath) )
        {
            return [ 'code' => 1, 'error' => 'Error saving file.' ];
        }
    }
}
