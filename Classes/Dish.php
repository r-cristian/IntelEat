<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/config/dbconfig.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/Classes/PreparationMode.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/Classes/DishNutrient.php');

class Dish {

    private $id = null;
    private $name;
    private $quantityPerPortion;
    private $preparationMode;
    private $portionUnit;
    private $calories;
    private $nutrients;

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }
    
     public function getPortionUnit() {
        return $this->portionUnit;
    }

    public function setPosrtionUnit($portionUnit) {
        $this->portionUnit = $portionUnit;
    }

    public function getQuantityPerPortion() {
        return $this->quantityPerPortion;
    }

    public function setQuantityPerPortion($quantityPerPortion) {
        $this->quantityPerPortion = $quantityPerPortion;
    }

    public function getPreparationMode() {
        return $this->preparationMode;
    }

    public function setPreparationMode($preparationMode) {
        $this->preparationMode = $preparationMode;
    }

    public function getCalories() {
        return $this->calories;
    }

    public function setCalories($calories) {
        $this->calories = $calories;
    }

    public function getNutrients() {
        return $this->nutrients;
    }

    public function setNutrients($nutrients) {
        $this->nutrients = $nutrients;
    }

    public function __construct() {
        
    }

    public function load($id) {
        $sql = "SELECT * FROM dish 
                WHERE id = {$id}";

        $result = mysql_query($sql);
        if (!$result)
            return false;
        $row = mysql_fetch_assoc($result);

        $this->id = $row['id'];
        $this->name = $row['name'];
        $this->quantityPerPortion = $row['quantityPerPortion'];
        $this->preparationMode = PreparationMode::getAllByDishId($row['id']);
        $this->calories = $row['calories']; 
        $this->portionUnit = $row['portionUnit']; 
        $this->nutrients = DishNutrient::getAllByDishId($row['id']);

        return true;
    }

    public static function getAll() {
        $sql = "SELECT * FROM dish";

        $result = mysql_query($sql);
        if (!$result)
            return false;
       
        $dishes = array();
        while ($row = mysql_fetch_assoc($result)) {
            $dishes[$row['id']] = new Dish();
            $dishes[$row['id']]->load($row['id']);
        }
        return $dishes;
    }
    
    public static function getAllForPatient($patientId) {
        return self::getAll();
    }
}

?>
