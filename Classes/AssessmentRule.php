<?php

class AssessmentRule {

    private $id;
    private $text;

    public function getId() {
        return $this->id;
    }

    public function getText() {
        return $this->text;
    }

    public function setText($text) {
        $this->text = $text;
    }

    public function __construct() {
        
    }

    public function load($id) {
        $sql = "SELECT * FROM assessmentRules 
                WHERE id = {$id}";

        $result = mysql_query($sql);
        if (!$result)
            return false;
        $row = mysql_fetch_assoc($result);

        $this->id = $row['id'];
        $this->text = $row['text'];

        return true;
    }

    public static function getAll() {
        $sql = "SELECT * FROM assessmentRules";

        $result = mysql_query($sql);
        if (!$result)
            return false;

        $rules = array();
        while ($row = mysql_fetch_assoc($result)) {
            $rules[$row['id']] = new AssessmentRule();
            $rules[$row['id']]->load($row['id']);
        }
        return $rules;
    }

}

?>
