<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/config/dbconfig.php');

class Nutrient {

    private $id = null;
    private $name;
    private $unit;

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function getUnit() {
        return $this->unit;
    }

    public function setUnit($unit) {
        $this->unit = $unit;
    }


    public function __construct() {
        
    }
    
   public function load($id) {
        $sql = "SELECT * FROM nutrient 
                WHERE id = {$id}";

        $result = mysql_query($sql);
        if (!$result)
            return false;
        $row = mysql_fetch_assoc($result);

        $this->id = $row['id'];
        $this->name = $row['name'];
        $this->unit = $row['unit'];       

        return true;
    }

    public static function getAllByDishId($id) {
        $sql = "SELECT dish.id as dish, nutrient.id as nutrient
                FROM dishNutrients
                     INNER JOIN dish
                         ON dishNutrients.dishId = dish.id
                     INNER JOIN nutrient 
                         ON dishNutrients.nutrientId = nutrient.id
                WHERE dish.id = {$id}";

        $result = mysql_query($sql);
        if (!$result)
            return false;
        $row = mysql_fetch_assoc($result);
        if (!$result)
            return false;
        $nutrients = array();
        while ($row = mysql_fetch_assoc($result)) {
            $nutrients[$row['nutrient']] = new Nutrient();
            $nutrients[$row['nutrient']]->load($row['nutrient']);
        }
        return $nutrients;
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
