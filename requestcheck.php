<?php
include "db.php";
echo " <br>welcome to dashboard <br>";
session_start();
if($_SESSION['role'] == "User"){
        echo "vous êtes utilisateur";
    }
    else{
        header("Location: admin/dashboard.php");
    }

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bibliotheque</title>
    <style type="text/css">
        table{
            border: none;
            width:100%;
        }
        tr,th{
            border-bottom: 10px solid green;
        }
        td{
            text-align:center;
            border:none;
            background-color: gray;
            
        }
        .update{
            text-decoration: none;
        }
        .delete{
            text-decoration: none;
        }
        
    
    </style>
</head>
<body>
    <table>
        <thead>
            <tr>
                <th>id_emprunt</th>
                <th>id_Utilisateur</th>
                <th>Nom d'utilisateur</th>
                <th>id_livre</th>
                <th>Titre du livre</th>
                <th>Date d'emprunt</th>
                <th>Date de retour prevue</th>
                <th>Etat du livre</th>
                <th>Action</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            while($row = mysqli_fetch_assoc($result)){
            ?>
            <tr>
                <td><?php echo "{$row['idemprunt']}" ?></td>
                <td><?php echo "{$row['id']}" ?></td>
                <td><?php echo "{$row['nom_utilisateur']}" ?></td>
                <td><?php echo "{$row['idlivre']}" ?></td>
                <td><?php echo "{$row['titre']}" ?></td>
                <td> <?php echo "{$row['dateemprunt']}" ?></td>
                <td><?php echo "{$row['dateretour']}" ?></td>
                <td> <?php echo "{$row['etat']}" ?> </td>
                <td><a class="update" href="update_transaction.php?idemprunt=<?php echo $row['id'];?>">Update</a></td>
                <td><a class="delete" href="delete_transaction.php?idemprunt=<?php echo $row['id'];?>">Supprimer</a></td>
            </tr>
            <?php
            }
            ?>
        </tbody>

    </table>
</body>
</html>