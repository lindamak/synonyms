<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="styles/main_style.css" type="text/css">
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <!-- jQuery library -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.0/jquery.min.js"></script>
    <!-- Latest compiled JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="styles/custom_nav.css" type="text/css">
    <title>Play Synonyms</title>
</head>

<body>

<?php

  $nav_selected = "ADMIN"; 
  $left_buttons = "YES"; 
  $left_selected = "CREATENiN"; 

  include("./nav.php");
  global $db;

  ?>

<?php
    include 'db_configuration.php';
	include 'puzzleGenerator.php';
?>

<?php 

if(isset($_POST['puzzleWord']))

            $word = htmlspecialchars($_POST['puzzleWord']);

            //echo "Input Word:", $word . "<br>";

            $htmlTable = '<div class="container"><h1 style="color:red;">"' . $word . '"</h1><table class="table table-condensed main-tables" id="puzzle_table" ><thead><tr><th>Clue</th><th>Synonym</th><th>Word</th></tr></thead><tbody>';
            
            //echo $htmlTable;
            $wordCharacters = str_split($word);
            $wordCharacterSize = count($wordCharacters);
            $showworsCharacters = implode(',', $wordCharacters);
          
            //echo "It contains ", $wordCharacterSize, " characters: ";
            //echo $showworsCharacters . "<br>";
            
            foreach ($wordCharacters as $char){

              $pos = array_search($char, $wordCharacters) + 1;
              $query = "SELECT SynonymWord FROM synonyms WHERE SynonymWord LIKE '%$char%'";
                
              $stmt = $db->prepare($query);
              $stmt->execute();
              $stmt->store_result();
              //$numOfRows = $stmt->num_rows;
              $stmt->bind_result($wordsWithChar);
              $arrayWord = array();

              //echo "Words containing the letter ", $char, " in our database are:" ."<br>";


              while($stmt->fetch()){
                array_push($arrayWord, $wordsWithChar);
               // array_push($arrayClue, $wordsWithDClueID);
                //echo  $wordsWithChar. "<br>";
                //echo $wordsWithDClueID. "<br>";
            }

           // echo "Out of these, the word randomly selected is: ";

            $index = array_rand($arrayWord);
            $tempselectedWord = $arrayWord[$index];
            //echo $tempselectedWord. "<br>";
            $stmt->close();

            $clueIDQuery = "SELECT ClueID FROM synonyms WHERE SynonymWord Like '$tempselectedWord'"; 
            $stmt = $db->prepare($clueIDQuery);
            $stmt->execute();
            $stmt->store_result();
            $stmt->bind_result($clueID);
            $stmt->fetch();
              //echo  $clueID. "<br>";
            $stmt->close();


            $masterWordQuery = "SELECT SynonymWord FROM synonyms WHERE SynID = '$clueID' AND ClueID = '$clueID'"; 
            $stmt = $db->prepare($masterWordQuery);
            $stmt->execute();
            $stmt->store_result();
            //$numOfRows = $stmt->num_rows;
            $stmt->bind_result($masterWord);
            $stmt->fetch();
            
            /**
             * If the master word is different from the temp selected word then,
             *  the word has a master word.
             */
              if ($masterWord != $tempselectedWord){
                $newpos = $pos -1;
                $len = strlen($tempselectedWord);

                // storing value for the clue;

              $htmlTable .= "<tr><td align='center' style='vertical-align: middle;'>" . $pos . '/' . $wordCharacterSize . "</td>";
              
              //master word represents the synonym in the table
              $htmlTable .= "<td align='center' style='vertical-align: middle;'>" . $masterWord. "</td>";

              /**
               * Now, printing out the the letter to be revealed in the word 
               * plus empty spaces to be filled by the user. 
               */
              $htmlTable .= "<td style='vertical-align: middle;'>";
             // $htmlTable .= '<input class="altPuzzleInput active" type="text" maxLength="7" value="' . $char . '" style="display:none;" readonly/>';
                      $flag = false;
                      for ($j = 0; $j < $len; $j++) {
                          if (($j === $newpos) && !$flag) {
                              $htmlTable .= '<input class="puzzleInput word_char active" type="text" maxLength="7" value="' . $char . '" style="display:inline" readonly/>';
                              $flag = true;
                          } else {
                              $htmlTable .= '<input class="puzzleInput word_char" type="text" maxLength="7" value="" style="display:inline"/>';
                          }
                      }
                      $htmlTable .='</td>';
                      
                //echo " The Output for character ", $char, " is:". "<br>";
                //echo $pos, "/", $wordCharacterSize, ",", $masterWord, "," ,$tempselectedWord. "<br>";
                //echo " Which will be displaying as:". "<br>";
                //echo $pos, "/", $wordCharacterSize, ",", $masterWord, "," ,$tempselectedWord. "<br>";

              }
              else{
                //echo $tempselectedWord, " Does not have a master word so, the new selected word is:";

                $index = array_rand($arrayWord);
                $tempselectedWord = $arrayWord[$index];
                $newpos = $pos -1;
                $len = strlen($tempselectedWord);

                //echo  $tempselectedWord. "<br>";

                $stmt->close();


                $clueIDQuery = "SELECT ClueID FROM synonyms WHERE SynonymWord Like '$tempselectedWord'"; 
                $stmt = $db->prepare($clueIDQuery);
                $stmt->execute();
                $stmt->store_result();
                //$numOfRows = $stmt->num_rows;
                $stmt->bind_result($clueID);
                $stmt->fetch();
                $stmt->close();


               $masterWordQuery = "SELECT SynonymWord FROM synonyms WHERE SynID = '$clueID' AND ClueID = '$clueID'"; 
               $stmt = $db->prepare($masterWordQuery);
               $stmt->execute();
               $stmt->store_result();
               //$numOfRows = $stmt->num_rows;
               $stmt->bind_result($masterWord);
               $stmt->fetch();


              //echo  $tempselectedWord, " has a master word called ", "$masterWord". "<br>";
              //echo " The Output for character ", $char, " is:". "<br>";
              $htmlTable .= "<tr><td align='center' style='vertical-align: middle;'>" . $pos . '/' . $wordCharacterSize . "</td>";
          
              $htmlTable .= "<td align='center' style='vertical-align: middle;'>" . $masterWord. "</td>";
              $htmlTable .= "<td style='vertical-align: middle;'>";
              //$htmlTable .= '< input class="altPuzzleInput active" type="text" maxLength="7" value="' . $char . '" style="display:none;" readonly/>';
                      $flag = false;
                      for ($j = 0; $j < $len; $j++) {
                          if (($j === $newpos) && !$flag) {
                              $htmlTable .= '<input class="puzzleInput word_char active" type="text" maxLength="7" value="' . $char . '" style="display:inline" readonly/>';
                              $flag = true;
                          } else {
                              $htmlTable .= '<input class="puzzleInput word_char" type="text" maxLength="7" value="" style="display:inline"/>';
                          }
                      }
                      $htmlTable .='</td>';
              //echo $pos, "/", $wordCharacterSize, ",", $masterWord, "," ,$tempselectedWord. "<br>";
              //echo " Which will be displaying as:". "<br>";
              //echo $pos, "/", $wordCharacterSize, ",", $masterWord, "," ,$tempselectedWord. "<br>";

            }



          }

          $htmlTable .= "</div>";
          $htmlTable .= '</tbody></table><img id="success_photo" class="success" src="pic/thumbs_up.png" alt="Success!" style="display:none"></div>';

          echo $htmlTable;

//createTableFooter();


    //       function createTableFooter()
    // {
    //     $buttons = '<div class="container" ><input class="main-buttons" type="button" name="submit_solution" 
    //             value="Submit Solution";>
    //   <input class="main-buttons" type="button" name="changeInputMode" 
    //             value="Change Input Mode" </div>';
    //     $admin_buttons = '<div class="container"><input class="word_char active" type="hidden" maxLength="2" name="minLength" value="' . $this->minLength . '" 
    //             style="display:inline"/><input class="word_char active" type="hidden" maxLength="2" name="maxLength" 
    //             value="' . $maxLength . '" style="display:inline"/><input class="word_char active" type="hidden" 
    //             name="puzzleWord" value="' . $word . '"/> <input 
    //             class="word_char active" type="hidden" maxLength="2" name="position" value="' . $pos . '" style="display:inline"/>
    //             <div style="text-align:center">
    //             <input class="main-buttons" type="submit" name="UpdateImages" value="Update Images"/>
    //             <input class="main-buttons" type="submit" name="iDesign" value="Refresh"/>
    //             <input class="main-buttons" type="submit" name="saveIDesign" value="Save"/></div></div>';
    // }

?>

<button class='main-buttons' onclick="submitSolution()">Submit Solution</button>
	<button class='main-buttons' onclick="showSolution()">Show Solution</button>
	<button class='main-buttons' onclick="changeInput()">Change Input Mode</button>
	<br />
	<br />
</body>

</html>