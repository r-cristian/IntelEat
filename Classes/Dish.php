<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/config/dbconfig.php');

class Dish {

    private $id = null;
    private $name;
    private $quantityPerPortion;
    private $preparationMode;
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
        $this->quantityPerPortion = $row['age'];
        $this->preparationMode = PreparationMode::getAllByDishId($row['id']);
        $this->calories = $row['calories'];       
        $this->nutrients = Nutrient::getAllByDishId($row['id']);

        return true;
    }

    public static function getAll() {
        $sql = "SELECT * FROM patient";

        $result = mysql_query($sql);
        if (!$result)
            return false;
        $row = mysql_fetch_assoc($result);
        if (!$result)
            return false;
        $patients = array();
        while ($row = mysql_fetch_assoc($result)) {
            $patients[$row['id']] = new PatientProfile();
            $patients[$row['id']]->load($row['id']);
        }
        return $patients;
    }

    public static function deleteById($id) {
        $sql = "DELETE FROM patient 
                 WHERE id = {$id}";

        $result = mysql_query($sql);
        if (!$result)
            return false;
        return true;
    }

}

?>
