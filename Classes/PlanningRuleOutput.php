<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/config/dbconfig.php');

class PlanningRuleOutput {

    private $ruleId = null;
    private $nutrientId;
    private $minQuantity;
    private $maxQuantity;

    public function getRuleId() {
        return $this->ruleId;
    }

    public function getNutrientId() {
        return $this->nutrientId;
    }

    public function getMinQuantity() {
        return $this->minQuantity;
    }

    public function getMaxQuantity() {
        return $this->maxQuantity;
    }
    
    public function __construct() {
        
    }

    public function load($ruleId, $nutrientId) {
        $sql = "SELECT * FROM planningRuleOutput 
                WHERE ruleId = {$ruleId} AND nutrientId = {$nutrientId}";

        $result = mysql_query($sql);
        if (!$result)
            return false;
        $row = mysql_fetch_assoc($result);

        $this->ruleId = $row['ruleId'];
        $this->nutrientId = $row['nutrientId'];
        $this->minQuantity = $row['minQuantity'];
        $this->maxQuantity = $row['maxQuantity'];

        return true;
    }

    public static function getAllByRuleId($ruleId) {
        $sql = "SELECT *
                FROM planningRuleOutput                     
                WHERE ruleId = {$ruleId}";

        $result = mysql_query($sql);

        if (!$result)
            return false;
        $outputs = array();
        while ($row = mysql_fetch_assoc($result)) {
            $outputs[$row['nutrientId']] = new PlanningRuleOutput();
            $outputs[$row['nutrientId']]->load($row['ruleId'], $row['nutrientId']);
        }
        return $outputs;
    }

}

?>
