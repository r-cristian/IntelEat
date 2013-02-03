<?php
include_once('Classes/HelpClass.php');
include_once('Classes/Dish.php');
include_once('Classes/PreparationMode.php');
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
                    <?php echo $lifestyles[$patient->getLifestyle()]; ?>
                </li>       
                <li>
                    <?php echo 'Diabetes ' . $diabetesTypes[$patient->getDiabetesType()]; ?>
                </li>
            </ul>

            <table>
                <tbody>                    
                    <tr>
                        <td>
                            <b>Recommended values:</b>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Calories
                        </td>
                    </tr>
                    <?php
                    if (count($impNutrients) > 0) {
                        foreach ($impNutrients as $key => $nutrient) {
                            echo "<tr>
                                 <td>{$nutrient->getName()} ({$units[$nutrient->getUnit()]})
                                 </td>
                              </tr>";
                        }
                    }
                    ?>
                </tbody>
            </table>
        <?php endif ?>
        </br>
        </br>
      
        <?php if(isset($diets)): ?>
            <?php foreach($diets as $day=>$dishes): ?>                
                <?php if($patient): ?>
                    <h2>Day <?php echo $day; ?></h2>
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
                                Calories
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
            <?php endforeach; ?>
        <?php endif; ?>
    </body>
</html>
