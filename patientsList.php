<?php
include_once('Classes/HelpClass.php');
include_once('Classes/PatientProfile.php');

$genders = HelpClass::getGenders();
$diabetesTypes = HelpClass::getDiabetesTypes();
$lifestyles = HelpClass::getLifestyles();

if(isset($_GET['delete'])){
    PatientProfile::deleteById($_GET['delete']);
}

$patients = PatientProfile::getAll();
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
        <link rel="stylesheet" type="text/css" href="css/styles.css">
    </head>
    <body>
        <h1>  Patients
        </h1>
        </br>
        </br>
        <form action="" method="post">
            <table class="list">
                <tbody>
                    <tr>
                        <th >
                            Name
                        </th>
                        <th>
                            Age
                        </th>
                        <th>
                            Gender
                        </th>
                        <th>
                            Height
                        </th>
                        <th>
                            Weight
                        </th>
                        <th>
                            Lifestyle
                        </th>
                        <th>
                            Diabetes type
                        </th>
                        <th>
                            Options
                        </th>                      
                    </tr>
                    <?php
                    if (count($patients) > 0) {
                        foreach ($patients as $key => $patient) {
                            echo "<tr>
                                    <td>{$patient->getName()}
                                    </td>
                                    <td>{$patient->getAge()}
                                    </td>
                                    <td>{$genders[$patient->getGender()]}
                                    </td>
                                    <td>{$patient->getHeight()}
                                    </td>
                                    <td>{$patient->getWeight()}
                                    </td>
                                    <td>{$lifestyles[$patient->getLifestyle()]}
                                    </td>
                                    <td>{$diabetesTypes[$patient->getDiabetesType()]}
                                    </td>
                                    <td>
                                    <a href=\"profile.php?patient={$key}\" ><image width=\"15px\" height=\"15px\" src=\"images/edit.png\" alt=\"edit\"/></a>
                                    <a href=\"patientsList.php?delete={$key}\" ><image width=\"15px\" height=\"15px\" src=\"images/delete.png\" alt=\"delete\"/></a>
                                    </td>
                                 </tr>";
                        }
                    }
                    ?>  
                </tbody>                
            </table>
        </form>
    </body>
</html>
