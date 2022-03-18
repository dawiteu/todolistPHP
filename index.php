<?php
error_reporting(E_ALL); 


require('./connection.php'); 

function secure($string){
    return trim(htmlentities(htmlspecialchars($string))); 
}


$items_todo = $dbcon -> query('SELECT * FROM todoitem WHERE item_archived = 0'); 
$items_arch = $dbcon -> query('SELECT * FROM todoitem WHERE item_archived = 1');

// var_dump( mysqli_num_rows($items_todo) ); 

$page = isset($_GET['page']) ? secure($_GET['page']) : ""; 


?>
<style>
    *{
        font-family: sans-serif;
    }
    #container{
        width:80%; 
        margin:0 auto; 
        text-align:center;
    }
    #container table{
        width:100%;
        text-align: center;
        border:1px solid red;
    }
    #container table th{
        text-decoration: underline;
        /* border-bottom:1px solid black; */
    }
    #additem{
        width:max-content;
        padding:1%; 
        margin:5% auto; 
        border:1px solid grey; 
    }
    .formitem{
        margin:5px;
    }
    button{
        margin:5px; 
    }
    form{
        margin:0;
        padding:0;
    }

</style>

<div id="container">
    <?php 



    if($page === "er") echo "erreur ds le formulaire. (refresh 2s)"; /*header('refresh:2;url=index.php');  */

    if($page === "change"){ // change [status [ do / to do ] / archive ] 
        
        $itemid = isset($_GET['id']) ? secure($_GET['id']) : 0;  

        $item = $dbcon->query("SELECT * FROM todoitem WHERE item_id = '".$itemid."' "); 

        if(mysqli_num_rows($item) === 1){
            echo "item id: " .$itemid; 

            switch(secure($_GET['what'])){
                case 'archive':
                    echo "archive;";
                break;
    
                case 'status':
                    $item = $item->fetch_assoc(); 
                    //echo "status ". $item['item_title']; 
                    $newStatus = $item['item_do'] ?  0 : 1 ; // if status == do, then status change to todo.  
                    $query = $dbcon->query("UPDATE todoitem SET item_do = ".$newStatus." WHERE item_id = ". $itemid);
                    if($query){
                        header('location:index.php');
                    }else{
                        echo "Error update item..."; 
                    }
                break;
    
                default:
                    header('location:index.php'); 
                break;
                    
            }


        }else{
            die("item not found!");
        }




        if($itemid){

        }

    }


    if($page === "additem"){
        $title = secure($_POST['itemtitle']); 

        if(($title) && strlen($title) > 0){
            echo "try to add " . $title; 

            $query = "INSERT INTO todoitem (item_id, item_title, item_do, item_archived) VALUES (null, '". $title . "', '0','0')";
            if($dbcon->query($query)){
                header('location:index.php');
            }else{
                echo "Erreur lors de la requete SQL." . $dbcon->error; 
            }
        }else{
            header('location:index.php?page=er');
        }

    }else{ 
        echo "<p> TO DO LIST: </p> ";

        if(mysqli_num_rows($items_todo) || mysqli_num_rows($items_arch)){ 
            echo "<table>"; 

            echo " <tr><th>Item no:</th> <th>item name</th> <th> Item status (do / to do) </th> <th>Action</th></tr>";

                if(mysqli_num_rows($items_todo) > 0){
                    foreach($items_todo as $item){
                        echo "
                        <tr>
                            <td>".$item['item_id']."</td>
                            <td>".$item['item_title']."</td>
                            <td>". ($item['item_do'] == 0 ? 'to do' : 'do' ) ."</td>
                            <td style='border:1px solid black; display:flex; justify-content:center; '> 

                                <form method='POST' action='?page=change&what=status&id=".$item['item_id']."'>
                                    <button title='DONE'>DONE</button>
                                </form>

                                <form method='POST' action='?page=change&what=archive&id=".$item['item_id']."'>
                                    <button>ARCHIVE</button> 
                                </form>

                            </td>
                        </tr>";
                    }
                }
                if(mysqli_num_rows($items_arch) > 0){
                    echo "<tr><th colspan='4'>Archived items:</th></tr> "; 
                    while($row = mysqli_fetch_assoc($items_arch)){
                        echo "<tr><td>".$row['item_id']."</td><td>".$row['item_title']."</td><td>".($item['item_do'] == 0 ? 'to do' : 'do' )."</td></tr>"; 
                    }
                }   
                
            echo "</table>";

        }else{ 

            echo "<p>Il y a pas de chose a faire.</p>"; 

        } 

    }
    ?>

    <div id="additem">
        <p>Add item to do: </p>
        <form action="?page=additem" method="POST">
            <div class="formitem">
                <label for="itemtitle">Title item:</label>
                <input type="text" name="itemtitle" maxlength="50" required />
            </div>
            <div class="formitem">
                <input type="submit" value="Add item to do >" >
            </div>
        </form>
    </div>


</div>