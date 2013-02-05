<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
ini_set('max_execution_time', 300);

include_once('Classes/HelpClass.php');
include_once('Classes/Dish.php');
include_once('Classes/PreparationMode.php');
include_once('Classes/Diet.php');

include_once('Classes/DishNutrient.php');
include_once('Classes/PatientProfile.php');

$units = HelpClass::getUnits();
$patient = null;
$genders = HelpClass::getGenders();
$diabetesTypes = HelpClass::getDiabetesTypes();
$lifestyles = HelpClass::getLifestyles();
$dishTypes = HelpClass::getDishTypes();

if (isset($_GET['patient'])) {
    $patient = new PatientProfile();
    $patient->load($_GET['patient']);
}

if ($patient) {
    $diets = Diet::getDiet($_GET['patient']);
    $hint = null;
    if (!is_array($diets)) {
        $hint = $diets;
        unset($diets);
    } else {
        $rule = $patient->getPlanningRule();
        $hint = $rule->getHint();
        $recommendedKCals = $rule->getKCal();
        $recommendedOtherValues = $rule->getOutputs();
    }
    $impNutrients = DishNutrient::getAllImportantNutrients();
} else {
    $diets[1] = Dish::getAll();
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
        <h1>  <?php echo $patient ? "Diet for Patient: {$patient->getName()} ({$patient->getAge()})" : "Dishes"; ?>
        </h1>
        </br>
        </br>
        <a href="index.php" >Homepage...</a>
 </br></br>
        <?php if ($patient): ?>
            <b>Patient Profile:</b>
            <ul>
                <li>  
                    <?php echo $genders[$patient->getGender()]; ?>
                </li>
                <li>
                    <?php echo $patient->getHeight() . ' cm'; ?>
                </li>
                <li>
                    <?php echo $patient->getWeight() . ' kg'; ?>
                </li>
                 <li>
                   BMI:  <?php echo number_format($patient->getBMI(), 2); ?>
                </li>  
                <li>
                    <?php echo $lifestyles[$patient->getLifestyle()]; ?>
                </li>       
                <li>
                    <?php echo 'Diabetes ' . $diabetesTypes[$patient->getDiabetesType()]; ?>
                </li>
            </ul>
            <?php if (isset($recommendedKCals)): ?>
                <table>
                    <tbody>                    
                        <tr>
                            <td style="padding-bottom:10px;">
                                <b>Recommended values:</b>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                KCalories: aprox. <?php echo $recommendedKCals ?>
                            </td>
                        </tr>
                        <?php
                        if (count($impNutrients) > 0) {
                            foreach ($impNutrients as $key => $nutrient) {
                                if (isset($recommendedOtherValues[$key])) {
                                    echo "<tr>
                                 <td>{$nutrient->getName()} ({$units[$nutrient->getUnit()]}): {$recommendedOtherValues[$key]->getMinQuantity()} - {$recommendedOtherValues[$key]->getMaxQuantity()}
                                 </td>
                              </tr>";
                                }
                            }
                        }
                        ?>
                    </tbody>
                </table>
            <?php endif; ?>
        <?php endif ?>
        </br>
        </br>
        <?php if (isset($hint) && !empty($hint)): ?>
            <h2>Profile advice: </h2> </br>
            <span <?php if (!isset($diets)) echo 'style="color:red;"' ?>><?php echo $hint; ?></span>
            </br></br>

        <?php endif; ?>
        <?php if (isset($diets)): ?>
            <?php foreach ($diets as $day => $dishes): ?>                
                <?php if ($patient): ?>
                    <b>Day <?php echo $day; ?></b>
                    </br>
                    </br>
                <?php endif; ?>
                <table class="list">
                    <tbody>
                        <tr>
                            <th >
                                Name
                            </th>   
                            <th>
                                Dish Type
                            </th>      
                            <th>
                                Quantity (per portion)
                            </th>                       
                            <th>
                                Preparation Modes
                            </th>
                            <th>
                                KCalories
                            </th> 
                            <th>
                                Nutrients
                            </th>
                        </tr>
                        <?php
                        if (count($dishes) > 0) {
                            foreach ($dishes as $key => $dish) {
                                $preparationModes = PreparationMode::getAllByDishId($key);
                                $listString = "<ul class=\"modes\">";
                                foreach ($preparationModes as $id => $mode) {
                                    $listString .= "<li>{$mode->getName()}</li>";
                                }
                                $listString .= "</ul>";

                                echo "<tr>
                                    <td>{$dish->getName()}
                                    </td>
                                    <td>{$dishTypes[$dish->getDishType()]}
                                    </td>
                                    <td>{$dish->getQuantityPerPortion()} {$units[$dish->getPortionUnit()]}
                                    </td>                                    
                                    <td>
                                      {$listString}
                                    </td>
                                    <td>{$dish->getCalories()}
                                    </td>                                    
                                    <td>
                                         <a href=\"dishNutrients.php?dish={$key}\" >See nutrients...</a>
                                    </td>
                                 </tr>";
                            }
                        }
                        ?>  
                    </tbody>                
                </table>  
                    </br>
                    </br>
            <?php endforeach; ?>
        <?php endif; ?>
    </body>
</html>