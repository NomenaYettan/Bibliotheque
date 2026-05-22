<?php
include "db.php";
$sql = "select * from livre";
$result = mysqli_query($conn,$sql);
if(!$result){
    echo "error!: {$result->error}";
}
else{

}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bibliotheque</title>
    <style type="text/css">
        *{
            margin:0;
            padding: 0;
            overflow-x: hidden;
        }
        header{
            padding: 30px;
            position: fixed;
            top: 0;
            width:100%;
            background-color: grey;
            text-align: center;
        }
        footer{
            position: fixed;
            width: 100%;
            padding: 10px;
            background-color: grey;
            text-align: center;
            bottom: 0;
        }
        .indexsection{
            display: flex;
            flex-wrap: wrap;
            margin-top: 200px;
            justify-content: center;
        }
        .indexsection div{
            width: 200px;
            text-align: center;
        }
        .indexsection img{
            width:100%;
        }
    </style>
</head>
<body>
    <header>
        <h1>BIBLIOTHEQUE SALT IVORY</h1>
    </header>
    <section class="indexsection">
        <?php 
        while($row = mysqli_fetch_assoc($result)){
        ?>
            <div>
            <img src="image/<?php echo "{$row['image']}"?>" width="50" height="200" >
            <p>Titre du livre:<?php echo "{$row['titre']}" ?></p>
            <p>Auteur: <?php echo "{$row['auteur']}" ?></p>
            <p>ISBN: <?php echo "{$row['isbn']}" ?></p>
            <p>Qantité: <?php echo "{$row['quantite']}" ?></p>
            <?php $lien = "borrow.php?idlivre=" . $row['idlivre']; ?>
            <a href="<?php echo $lien; ?>">Emprunter</a>
        </div>
        <?php
        }
        ?>
    </section>
    <footer>
        <p>copyright@nomenayettan</p>
    </footer>
    
</body>
</html>