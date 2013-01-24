<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/config/dbconfig.php');

class PreparationMode {

    private $id = null;
    private $name;    

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function __construct() {
        
    }
    
   public function load($id) {
        $sql = "SELECT * FROM preparationMode 
                WHERE id = {$id}";

        $result = mysql_query($sql);
        if (!$result)
            return false;
        $row = mysql_fetch_assoc($result);

        $this->id = $row['id'];
        $this->name = $row['name'];          

        return true;
    }

    public static function getAllByDishId($id) {
        $sql = "SELECT dish.id as dish, preparationMode.id as preparationMode
                FROM dishPreparationMode
                     INNER JOIN dish
                         ON dishPreparationMode.dishId = dish.id
                     INNER JOIN preparationMode 
                         ON dishPreparationMode.preparationModeId = preparationMode.id
                WHERE dish.id = {$id}";

        $result = mysql_query($sql);
        if (!$result)
            return false;
        $row = mysql_fetch_assoc($result);
        if (!$result)
            return false;
        $preparationModes = array();
        while ($row = mysql_fetch_assoc($result)) {
            $preparationModes[$row['preparationMode']] = new PreparationMode();
            $preparationModes[$row['preparationMode']]->load($row['preparationMode']);
        }
        return $preparationModes;
    }

    public static function getAll() {
        $sql = "SELECT * FROM nutrient";

        $result = mysql_query($sql);
        if (!$result)
            return false;
        $row = mysql_fetch_assoc($result);
        if (!$result)
            return false;
           $nutrients = array();
        while ($row = mysql_fetch_assoc($result)) {
            $nutrients[$row['id']] = new Nutrient();
            $nutrients[$row['id']]->load($row['id']);
        }
        return $nutrients;
    }



}

?>
