<?php
include_once('Classes/HelpClass.php');
include_once('Classes/Dish.php');
include_once('Classes/DishNutrient.php');

$units = HelpClass::getUnits();
$dish = null;
if (isset($_GET['dish'])) {
    $dish = new Dish();
    $dish->load($_GET['dish']);
} if ($dish) {
    $nutrients = DishNutrient::getAllByDishId($_GET['dish']);
} else {
    $nutrients = DishNutrient::getAllNutrients();
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
        <link rel="stylesheet" type="text/css" href="css/styles.css">
    </head>
    <body>
        <h1>  <?php echo $dish ? "Nutrients for {$dish->getName()}" : "List of all Nutrients" ?>
        </h1>
        </br>
        </br>
        <table>
            <tbody>              
                <?php
                if (count($nutrients) > 0) {
                    foreach ($nutrients as $key => $nutrient) {
                        $q = $nutrient->getQuantity();
                        if ($q) {
                            echo "<tr>
                                    <td>{$nutrient->getName()}:
                                    </td>  
                                    <td>{$q} {$units[$nutrient->getUnit()]}
                                    </td>
                                  </tr>";
                        } else {
                            echo "<tr>
                                    <td>{$nutrient->getName()} ({$units[$nutrient->getUnit()]})
                                    </td>
                                 </tr>";
                        }
                    }
                }
                ?>                  
                <tr>                 
                    <td style="padding-top: 20px;" <?php echo $dish ? "colspan=\"2\"" : ""?>>
                        <a href="dishesList.php" ><?php echo $dish ? "Back" : "Go"?> to dishes list...</a>
                    </td>
                </tr>              
            </tbody>                
        </table>       
    </body>
</html>
