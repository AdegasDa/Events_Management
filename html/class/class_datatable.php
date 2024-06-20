<?php

class class_datatable
{
    public function getData($pdo, $requestData, $columns, $sqlCount, $sqlFiltered, $sql, $callbackFunc)
    {
        // Ordenação do DataTables
        $orderColumnIndex  = isset($requestData['order'][0]['column']) ? $requestData['order'][0]['column'] : 0;
        $orderDirection = isset($requestData['order'][0]['dir']) ? $requestData['order'][0]['dir'] : 'asc';
        $orderColumn = $columns[$orderColumnIndex];

        // Registos
        $totalRecords = $this->getTotalRecords($pdo, $sqlCount, $sqlFiltered);

        // Registos filtrados
        $totalFiltered = $this->getTotalFilteredRecords($pdo, $sqlCount, $sqlFiltered);

        // Consulta SQL dos dados com paginação e ordenação
        $sql .= $sqlFiltered . " ORDER BY `$orderColumn` $orderDirection";
		
    	$sql .= "  LIMIT " . $requestData['start'] . " ," . $requestData['length'] . "   ";
    
        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        $data = array();
        
        while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) )
        {
            $nestedData = call_user_func($callbackFunc, $row);
            $data[] = $nestedData;
        }
        
        echo json_encode(array(
            "draw" => isset($requestData['draw']) ? intval($requestData['draw']) : 0,
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $totalFiltered,
            "data" => $data
        ));
    }

    private function getTotalRecords($pdo, $sql, $sqlFiltered)
    {
        $stmt = $pdo->prepare($sql . $sqlFiltered);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    private function getTotalFilteredRecords($pdo, $sql, $sqlFiltered)
    {
        $stmt = $pdo->prepare($sql . $sqlFiltered);
        $stmt->execute();
        $count = $stmt->fetchColumn();
        return $count ? $count : 0;
    }
    
    public function button_active_user()
    {
        $return = "<button id='button_active' class='btn btn-success' type='button'> <i class='fa-solid fa-user-check'> </i> </button>";

        return $return;
    }
    
    public function button_event_display()
    {
        $return = "<button id='btn_display' class='btn btn-primary' type='button'><i class='fa-regular fa-eye'></i></button>";
        
        return $return;
    }
    
    public function button_delete_association()
    {
        $return = "<button id='btn_delete' class='btn btn-danger' type='button'><i class='fas fa-user-times'></i></button>";
        
        return $return;
    }
    
    public function button_delete_user()
    {
        $return = "<button id='button_delete' class='btn btn-danger' type='button'> <i class='fas fa-user-times'> </i> </button>";

        return $return;
    }
    
    public function button_edit_user()
    {
        $return = "<button id='button_edit' class='btn btn-warning text-white' type='button'> <i class='fa fa-user-pen'> </i> </button>";

        return $return;
    }
    
    public function button_edit()
    {
        $return = "<button id='button_edit' class='btn btn-warning text-white' type='button'> <i class='fa fa-pen-to-square'> </i> </button>";

        return $return;
    }
    
    public function button_delete()
    {
        $return = "<button id='button_delete' class='btn btn-danger' type='button'> <i class='fas fa-trash'> </i> </button>";

        return $return;
    }
    
    public function button_subscribers()
    {
        $return = "<button id='button_subscribers' class='btn btn-success' type='button'> <i class='fa-solid fa-users'> </i> </button>";

        return $return;
    }
}