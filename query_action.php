<?php
    if (isset($_POST['name']) === true){
      $name = $_POST['name'];
      $begindate = new DateTime($_POST['begindate']);
      $enddate = new DateTime($_POST['enddate']);
      //$dateresult = $begindate->format('Y-m-d H:i:s');
      //echo $dateresult;
      $servername = "localhost";
      $username = "root";
      $password = "";
      $dbname = "wpgeojsonprototype";
      $conn = new mysqli($servername, $username, $password,$dbname); //or
    //        die("Could not connect: " . mysql_error());
      if (mysqli_connect_errno()){
        echo "Failed to connect";
      }
      $statement = $conn->prepare("SELECT `idCatRec` FROM `People` WHERE `Name` = ?");
      $statement->bind_param('s', $name);
      $statement->execute();
      $statement->bind_result($returned_idcat);
      $goodidcats = array();
      $gooddates = array();
      while($statement->fetch()){
//        echo $returned_idcat;
        array_push($goodidcats, $returned_idcat);
      }
//      foreach ($goodidcats as $value){
//        echo $value;
//      }
      function refValues($arr){
        $refs = array();
        foreach($arr as $key => $value){
          $refs[$key] = &$arr[$key];
        }
        return $arr;
      }
      $sql = "SELECT `Date` FROM `catelogrecords` WHERE ";
      $pram = '';
      $vstr = '';
      $valuearr = array();
      foreach ($goodidcats as $value){
        $sql = $sql . "`idCatRec` = ? OR ";
        $pram = $pram . "d";
        $vstr = $vstr . $value . ",";
        array_push($valuearr, $value);
      }
      $sqlstmnt = substr($sql,0,-3);
      $valuestr = substr($vstr,0,-1);
      $params[] = $pram;
      foreach ($valuearr as $value){
        array_push($params, $value);
      }
      $stmnt2 = $conn->prepare($sqlstmnt);
      call_user_func_array(array($stmnt2, "bind_param"),refValues($params));
      $stmnt2->execute();
      $stmnt2->bind_result($returned_date);
      $datesarr = array();
      while($stmnt2->fetch()){
        $refdate= new DateTime($returned_date);
        array_push($datesarr, $refdate);}
      $bool = False;
      for($i = 0; $i < count($datesarr); $i+=1){
        $testdate = $datesarr[$i]->format('Y-m-d H:i:s');
        //echo $testdate;
        if(($begindate < $datesarr[$i]) && ($datesarr[$i] < $enddate)){
          $statement3 = $conn->prepare("SELECT `Place`,`idCatRec` FROM `places` WHERE `idCatRec` = ?");
          $placeidc = $goodidcats[$i];
          $statement3->bind_param('d', $placeidc);
          $statement3->execute();
          $statement3->bind_result($returned_place,$returned_idCat);
          $placearray = array();
          while($statement3->fetch()){
            $placearray[] = array($returned_place, $returned_idCat);
            }
          foreach($placearray as $value){
            echo '('.$value[0].', '.$value[1].'), ';//.'
            //<p>
            //<a href=\'geojsons/open_textdoc.php\'>'
            //. $value[1].
            //'</a>
            //</p>';
            }
          }
          else{
            $bool = True;
          }
        }
        if ($bool == True){
          echo "Person not mentioned in given time frame";
        }
//      $stmnt2->free_result();
    //  $statement->free_result();
    //
    //    if(!$result = $db->query($sql)){
    //      die('There was an error running the query. [' . $db->error . ']');
    //    }
    //    $qresult = mysqli_query($conn, $sqli);
    //    while ($row = $result->fetch_assoc()){
    //      echo $row['AssociationsArray' . '<br />'];
    //    }
    //    mysqli_close($conn);
    }
?>
