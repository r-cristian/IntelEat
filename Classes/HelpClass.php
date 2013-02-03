<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/config/dbconfig.php');


class HelpClass {

    public static function getGenders() {
        $sql = "SELECT * FROM gender";

        $result = mysql_query($sql);
        if (!$result) {
            die("Query didn't work. " . mysql_error());
        }
        $units = array();
        while ($row = mysql_fetch_assoc($result)) {
            $genders[$row['id']] = $row['name'];
        }

        return $genders;
    }

    public static function getUnits() {
        $sql = "SELECT * FROM unit";

        $result = mysql_query($sql);
        if (!$result) {
            die("Query didn't work. " . mysql_error());
        }
        $units = array();
        while ($row = mysql_fetch_assoc($result)) {
            $units[$row['id']] = $row['name'];
        }

        return $units;
    }

    public static function getDiabetesTypes() {
        $sql = "SELECT * FROM diabetesType";

        $result = mysql_query($sql);
        if (!$result)
            die("Query didn't work. " . mysql_error());
        $types = array();

        while ($row = mysql_fetch_assoc($result)) {
            $types[$row['id']] = $row['name'];
        }

        return $types;
    }

    public static function getDishTypes() {
        $sql = "SELECT * FROM dishType";

        $result = mysql_query($sql);
        if (!$result)
            die("Query didn't work. " . mysql_error());
        $dishTypes = array();

        while ($row = mysql_fetch_assoc($result)) {
            $dishTypes[$row['id']] = $row['name'];
        }
        return $dishTypes;
    }

    public static function getLifestyles() {
        $sql = "SELECT * FROM lifestyle";

        $result = mysql_query($sql);
        if (!$result)
            die("Query didn't work. " . mysql_error());
        $lifestyles = array();

        while ($row = mysql_fetch_assoc($result)) {
            $lifestyles[$row['id']] = $row['name'];
        }
        return $lifestyles;
    }

    public static function multiexplode($delimiters, $string) {
        $ary = explode($delimiters[0], $string);
        array_shift($delimiters);
        if ($delimiters != NULL) {
            foreach ($ary as $key => $val) {
                $ary[$key] = multiexplode($delimiters, $val);
            }
        }
        return $ary;
    }

    public static function shuffle_assoc($list) {
        if (!is_array($list))
            return $list;

        $keys = array_keys($list);
        shuffle($keys);
        $random = array();
        foreach ($keys as $key)
            $random[$key] = $list[$key];

        return $random;
    }

}
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
