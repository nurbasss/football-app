<?php 
    require "header.php"
?>

    <body>
        <div class="Border-main">
            
            <?php
                require 'includes/dbh.php';
                if(!isset($_SESSION['userId'])){
                    header("Location: index.php");
                    exit();
                }
                echo '<div class="errors">';
                if(isset($_GET['error'])){
                    if($_GET['error'] == 'dbError' || $_GET['error'] == 'dbError1'){
                        echo '<p>Database Error!</p>';
                    }
                    else if($_GET['error'] == 'undoneBefore'){
                        echo '<p>Match already undone!</p>';
                    }
                    
                }
                else if(isset($_GET['success'])){
                    if($_GET['success'] == 'deleted'){
                        echo '<p style="color:green">Match successfully deleted!</p>';
                    }
                    else if($_GET['success'] == 'undone'){
                        echo '<p style="color:green">Delete successfully undone!</p>';
                    }
                    else if($_GET['success'] == 'favourited'){
                        echo '<p style="color:green">Macth favourited!</p>';
                    }
                    else if($_GET['success'] == 'unfavourited'){
                        echo '<p style="color:green">Match un-favourited!</p>';
                    }
                }
                // reset to favourite
                $_SESSION['defaultStatsId'] = $_SESSION['favTeamId'];
                $_SESSION['defaultStatsName'] = $_SESSION['userTeam']; 
                $_SESSION['defaultStatsUserId'] = $_SESSION['userId'];
                $_SESSION['defaultStatsUserEmail'] = $_SESSION['userEmail'];
                // reset dates to all time
                $_SESSION['dateStart'] = '2015-07-01';
                $_SESSION['dateEnd'] = '2020-06-01';
                $_SESSION['dateTitle'] = 'All Time';
                
                echo '</div>';
                echo '<div class="jump">';              
                    echo '<a href="myMatches.php#Top" style="border-bottom:1px solid black;">Top</a>';
                echo '</div>';
                echo '<a name="Top" />';
                echo '<div class="rotate-message">Rotate Device</div>';
                echo '<div class="headers">';
                echo '<h3 >My Matches</h3>';
                echo '<p>Below are all the matches which you have attended, press the <img src="pictures/-.PNG" height=2% width=2%> button to remove the match from your logged matches. Press <img src="pictures/i.PNG" height=2% width=2%> button to see more information about the match.
                Press the Circle button at the end of each row to favourite a match. You can use the "Undo" button to undo your last delete. Click on team names to search for matches for that team.</p>';

                //echo '</div>';
                if(!empty($_SESSION['lastDeleted'])){
                    //only show button if there is something to undo
                    $undo = $_SESSION['lastDeleted'];
                    echo "<form class='undo-button' action='includes/undo.php' method='post'> <button type='submit' name='matchId' value=".$undo." >undo</button></form></td>";  
                }

                $user = $_SESSION['userId'];           
                $matchChecker = "SELECT COUNT(user_matches.matchVal) FROM user_matches JOIN matches ON user_matches.matchVal = matches.matchId WHERE user_matches.userVal= :usr";
                if($check = oci_parse($conn, $matchChecker)){
                    oci_bind_by_name($check, ':usr',$user);
                    oci_execute($check);
                    $row = oci_fetch_assoc($check);
                
                    $totalNumMatches= $row['COUNT(USER_MATCHES.MATCHVAL)'];
                    if($totalNumMatches == 0){
                        // No matches Logged 
                        header("Location: index.php?error=noMatchesLogged");
                        exit();
                    }
                
                }
                else{
                    header("Location: index.php?error=dbError");
                    exit();
                }

                // 2019/20
                echo '<a name="19/20" />';
                $sql = "SELECT * FROM (SELECT user_matches.userVal, user_matches.fav, games.matchId, games.m_date, games.home, games.FTHG, games.FTAG, games.away FROM (SELECT hometable.matchId, hometable.m_date, hometable.home, hometable.FTHG, hometable.FTAG, awaytable.away FROM (SELECT matches.matchId, matches.m_date, teams.teamName AS home, matches.FTHG, matches.FTAG FROM matches JOIN teams ON matches.homeTeam = teams.teamId ) hometable JOIN (SELECT matches.matchId, teams.teamName AS away FROM matches JOIN teams ON matches.awayTeam = teams.teamId ) awaytable ON hometable.matchId = awaytable.matchId) games JOIN user_matches ON games.matchId = user_matches.matchVal WHERE user_matches.userVal = :usr ORDER BY games.m_date DESC) m_results WHERE m_date BETWEEN TO_DATE('2019-07-01','YYYY-MM-DD') AND TO_DATE('2020-06-01','YYYY-MM-DD') ORDER BY m_date DESC";
                if($result = oci_parse($conn, $sql)){
                    oci_bind_by_name($result, ':usr', $user);
                    oci_execute($result);
                    if(true){
                        echo '<h2 style="float:left; font-size: 1vw; margin-left: 10%; margin-top 2%; text-decoration:underline;width:75%;">2019/20</h2>';
                        echo "<table class='outTable'>";
                            echo "<tr>";  
                                echo "<th style='border-left:none;'></th>";                                 
                                echo "<th>Date</th>";
                                echo "<th>Home</th>";
                                echo "<th>Home goals</th>";
                                echo "<th>Away goals</th>";
                                echo "<th>Away</th>";
                                echo "<th style='border-right:none;'></th>"; 
                                echo "<th style='border:none; background-color:#F8F8F8;'>Fav</th>";                                  
                            echo "</tr>";
                            $counter = 1;
                        while($row = oci_fetch_assoc($result)){
                            echo "<tr>"; 
                            echo "<td> <form action='match.php' method='post'><input type='hidden' name='page' value='myMatches'><input type='hidden' name='pos' value='19/20'><button type='submit' name='matchId' value=". $row['MATCHID']." >i</button></form></td>";                        

                            if($row['HOME'] == $_SESSION['userTeam']){
                                // user team bold if home
                                echo "<td>" . $row['M_DATE'] . "</td>";
                                echo "<td><form action='searchResult.php' method='post'><input type='hidden' name='Date' value='AllTime'><button type='submit' name='team' value='".$row['HOME']."' style='background-color:Transparent; border:none; font-size: 1.2vw; font-weight:bold;'>".$row['HOME']."</button></form></td>";
                                echo "<td>" . $row['FTHG'] . "</td>";
                                echo "<td>" . $row['FTAG'] . "</td>";
                                echo "<td><form action='searchResult.php' method='post'><input type='hidden' name='Date' value='AllTime'><button type='submit' name='team' value='".$row['AWAY']."' style='background-color:Transparent; border:none; font-size: 1.2vw;'>".$row['AWAY']."</button></form></td>";
                                // creates a button with name and value = match id
                                // post hides id in URL

                                echo "<td> <form action='includes/delete.php' method='post'><input type='hidden' name='page' value='myMatches'> <input type='hidden' name='pos' value='19/20'><button type='submit' name='matchId' value=". $row['MATCHID']." >-</button></form></td>";                        
                            }
                            else if($row['AWAY'] == $_SESSION['userTeam']){
                                //user team bold if away
                                echo "<td>" . $row['M_DATE'] . "</td>";
                                echo "<td><form action='searchResult.php' method='post'><input type='hidden' name='Date' value='AllTime'><button type='submit' name='team' value='".$row['HOME']."' style='background-color:Transparent; border:none; font-size: 1.2vw;'>".$row['HOME']."</button></form></td>";
                                echo "<td>" . $row['FTHG'] . "</td>";
                                echo "<td>" . $row['FTAG'] . "</td>";
                                echo "<td><form action='searchResult.php' method='post'><input type='hidden' name='Date' value='AllTime'><button type='submit' name='team' value='".$row['AWAY']."' style='background-color:Transparent; border:none; font-size: 1.2vw; font-weight:bold;'>".$row['AWAY']."</button></form></td>";
                                // creates a button with name and value = match id
                                // post hides id in URL
                                echo "<td> <form action='includes/delete.php' method='post'><input type='hidden' name='page' value='myMatches'> <input type='hidden' name='pos' value='19/20'><button type='submit' name='matchId' value=". $row['MATCHID']." >-</button></form></td>";                        
                            }
                            else {
                                echo "<td>" . $row['M_DATE'] . "</td>";
                                echo "<td><form action='searchResult.php' method='post'><input type='hidden' name='Date' value='AllTime'><button type='submit' name='team' value='".$row['HOME']."' style='background-color:Transparent; border:none; font-size: 1.2vw;'>".$row['HOME']."</button></form></td>";
                                echo "<td>" . $row['FTHG'] . "</td>";
                                echo "<td>" . $row['FTAG'] . "</td>";
                                echo "<td><form action='searchResult.php' method='post'><input type='hidden' name='Date' value='AllTime'><button type='submit' name='team' value='".$row['AWAY']."' style='background-color:Transparent; border:none; font-size: 1.2vw;'>".$row['AWAY']."</button></form></td>";
                                // creates a button with name and value = match id
                                // post hides id in URL
                                echo "<td> <form action='includes/delete.php' method='post'><input type='hidden' name='page' value='myMatches'> <input type='hidden' name='pos' value='19/20'><button type='submit' name='matchId' value=". $row['MATCHID']." >-</button></form></td>";                        
                            }
                            if($row['FAV'] == 'Y'){
                                echo '<td style="border:none; background-color:#F8F8F8; width:3.5%;"><form action="includes/fav.php" method="post" class="favBut"><input type="hidden" name="pos" value="19/20"><button type="submit" name="unfav-Id"  value='.$row['MATCHID'].' style="background-color:#77DD77;"></button></form></td>';
                            }
                            else {
                                echo '<td style="border:none; background-color:#F8F8F8; width:3.5%;"><form action="includes/fav.php" method="post" class="favBut"><input type="hidden" name="pos" value="19/20"><button type="submit" name="fav-Id" value='.$row['MATCHID'].'></button></form></td>';                            
                            }                     
                            echo "</tr>";
                        }
                        echo "</table>";
                        
                    }
                } 
                else{
                    header("Location: index.php?error=dbError");
                    exit();
                }  

                echo '<a name="18/19" />';

                // 2018/19
                $sql = "SELECT * FROM (SELECT user_matches.userVal, user_matches.fav, games.matchId, games.m_date, games.home, games.FTHG, games.FTAG, games.away FROM (SELECT hometable.matchId, hometable.m_date, hometable.home, hometable.FTHG, hometable.FTAG, awaytable.away FROM (SELECT matches.matchId, matches.m_date, teams.teamName AS home, matches.FTHG, matches.FTAG FROM matches JOIN teams ON matches.homeTeam = teams.teamId ) hometable JOIN (SELECT matches.matchId, teams.teamName AS away FROM matches JOIN teams ON matches.awayTeam = teams.teamId ) awaytable ON hometable.matchId = awaytable.matchId) games JOIN user_matches ON games.matchId = user_matches.matchVal WHERE user_matches.userVal = :usr ORDER BY games.m_date DESC) m_results WHERE m_date BETWEEN TO_DATE('2018-07-01','YYYY-MM-DD') AND TO_DATE('2019-06-01','YYYY-MM-DD') ORDER BY m_date DESC";
                if($result = oci_parse($conn, $sql)){
                    oci_bind_by_name($result, ':usr', $user);
                    oci_execute($result);
                    if(true){
                        echo '<h2 style="float:left; font-size: 1vw; margin-left: 10%; margin-top 2%; text-decoration:underline;width:75%;">2018/19</h2>';
                        echo "<table class='outTable'>";
                            echo "<tr>";  
                                echo "<th style='border-left:none;'></th>";                                 
                                echo "<th>Date</th>";
                                echo "<th>Home</th>";
                                echo "<th>Home goals</th>";
                                echo "<th>Away goals</th>";
                                echo "<th>Away</th>";
                                echo "<th style='border-right:none;'></th>"; 
                                echo "<th style='border:none; background-color:#F8F8F8;'>Fav</th>";                                  
                            echo "</tr>";
                            $counter = 1;
                        while($row = oci_fetch_assoc($result)){
                            echo "<tr>"; 
                            echo "<td> <form action='match.php' method='post'><input type='hidden' name='page' value='myMatches'><input type='hidden' name='pos' value='18/19'><button type='submit' name='matchId' value=". $row['MATCHID']." >i</button></form></td>";                        

                            if($row['HOME'] == $_SESSION['userTeam']){
                                // user team bold if home
                                echo "<td>" . $row['M_DATE'] . "</td>";
                                echo "<td><form action='searchResult.php' method='post'><input type='hidden' name='Date' value='AllTime'><button type='submit' name='team' value='".$row['HOME']."' style='background-color:Transparent; border:none; font-size: 1.2vw; font-weight:bold;'>".$row['HOME']."</button></form></td>";
                                echo "<td>" . $row['FTHG'] . "</td>";
                                echo "<td>" . $row['FTAG'] . "</td>";
                                echo "<td><form action='searchResult.php' method='post'><input type='hidden' name='Date' value='AllTime'><button type='submit' name='team' value='".$row['AWAY']."' style='background-color:Transparent; border:none; font-size: 1.2vw;'>".$row['AWAY']."</button></form></td>";
                                // creates a button with name and value = match id
                                // post hides id in URL

                                echo "<td> <form action='includes/delete.php' method='post'><input type='hidden' name='page' value='myMatches'> <input type='hidden' name='pos' value='18/19'><button type='submit' name='matchId' value=". $row['MATCHID']." >-</button></form></td>";                        
                            }
                            else if($row['AWAY'] == $_SESSION['userTeam']){
                                //user team bold if away
                                echo "<td>" . $row['M_DATE'] . "</td>";
                                echo "<td><form action='searchResult.php' method='post'><input type='hidden' name='Date' value='AllTime'><button type='submit' name='team' value='".$row['HOME']."' style='background-color:Transparent; border:none; font-size: 1.2vw;'>".$row['HOME']."</button></form></td>";
                                echo "<td>" . $row['FTHG'] . "</td>";
                                echo "<td>" . $row['FTAG'] . "</td>";
                                echo "<td><form action='searchResult.php' method='post'><input type='hidden' name='Date' value='AllTime'><button type='submit' name='team' value='".$row['AWAY']."' style='background-color:Transparent; border:none; font-size: 1.2vw; font-weight:bold;'>".$row['AWAY']."</button></form></td>";
                                // creates a button with name and value = match id
                                // post hides id in URL
                                echo "<td> <form action='includes/delete.php' method='post'><input type='hidden' name='page' value='myMatches'> <input type='hidden' name='pos' value='18/19'><button type='submit' name='matchId' value=". $row['MATCHID']." >-</button></form></td>";                        
                            }
                            else {
                                echo "<td>" . $row['M_DATE'] . "</td>";
                                echo "<td><form action='searchResult.php' method='post'><input type='hidden' name='Date' value='AllTime'><button type='submit' name='team' value='".$row['HOME']."' style='background-color:Transparent; border:none; font-size: 1.2vw;'>".$row['HOME']."</button></form></td>";
                                echo "<td>" . $row['FTHG'] . "</td>";
                                echo "<td>" . $row['FTAG'] . "</td>";
                                echo "<td><form action='searchResult.php' method='post'><input type='hidden' name='Date' value='AllTime'><button type='submit' name='team' value='".$row['AWAY']."' style='background-color:Transparent; border:none; font-size: 1.2vw;'>".$row['AWAY']."</button></form></td>";
                                // creates a button with name and value = match id
                                // post hides id in URL
                                echo "<td> <form action='includes/delete.php' method='post'><input type='hidden' name='page' value='myMatches'> <input type='hidden' name='pos' value='18/19'><button type='submit' name='matchId' value=". $row['MATCHID']." >-</button></form></td>";                        
                            }
                            if($row['FAV'] == 'Y'){
                                echo '<td style="border:none; background-color:#F8F8F8; width:3.5%;"><form action="includes/fav.php" method="post" class="favBut"><input type="hidden" name="pos" value="18/19"><button type="submit" name="unfav-Id"  value='.$row['MATCHID'].' style="background-color:#77DD77;"></button></form></td>';
                            }
                            else {
                                echo '<td style="border:none; background-color:#F8F8F8; width:3.5%;"><form action="includes/fav.php" method="post" class="favBut"><input type="hidden" name="pos" value="18/19"><button type="submit" name="fav-Id" value='.$row['MATCHID'].'></button></form></td>';                            
                            }                     
                            echo "</tr>";
                        }
                        echo "</table>";
                        
                    }
                } 
                else{
                    header("Location: index.php?error=dbError");
                    exit();
                }  
                echo '<a name="17/18" />';

                // 2017/18
                $sql = "SELECT * FROM (SELECT user_matches.userVal, user_matches.fav, games.matchId, games.m_date, games.home, games.FTHG, games.FTAG, games.away FROM (SELECT hometable.matchId, hometable.m_date, hometable.home, hometable.FTHG, hometable.FTAG, awaytable.away FROM (SELECT matches.matchId, matches.m_date, teams.teamName AS home, matches.FTHG, matches.FTAG FROM matches JOIN teams ON matches.homeTeam = teams.teamId ) hometable JOIN (SELECT matches.matchId, teams.teamName AS away FROM matches JOIN teams ON matches.awayTeam = teams.teamId ) awaytable ON hometable.matchId = awaytable.matchId) games JOIN user_matches ON games.matchId = user_matches.matchVal WHERE user_matches.userVal = :usr ORDER BY games.m_date DESC) m_results WHERE m_date BETWEEN TO_DATE('2017-07-01','YYYY-MM-DD') AND TO_DATE('2018-06-01','YYYY-MM-DD') ORDER BY m_date DESC";
                if($result = oci_parse($conn, $sql)){
                    oci_bind_by_name($result, ':usr', $user);
                    oci_execute($result);
                    if(true){
                        echo '<h2 style="float:left; font-size: 1vw; margin-left: 10%; margin-top 2%; text-decoration:underline;width:75%;">2017/18</h2>';
                        echo "<table class='outTable'>";
                            echo "<tr>";  
                                echo "<th style='border-left:none;'></th>";                                 
                                echo "<th>Date</th>";
                                echo "<th>Home</th>";
                                echo "<th>Home goals</th>";
                                echo "<th>Away goals</th>";
                                echo "<th>Away</th>";
                                echo "<th style='border-right:none;'></th>"; 
                                echo "<th style='border:none; background-color:#F8F8F8;'>Fav</th>";                                  
                            echo "</tr>";
                            $counter = 1;
                        while($row = oci_fetch_assoc($result)){
                            echo "<tr>"; 
                            echo "<td> <form action='match.php' method='post'><input type='hidden' name='page' value='myMatches'><input type='hidden' name='pos' value='17/18'><button type='submit' name='matchId' value=". $row['MATCHID']." >i</button></form></td>";                        

                            if($row['HOME'] == $_SESSION['userTeam']){
                                // user team bold if home
                                echo "<td>" . $row['M_DATE'] . "</td>";
                                echo "<td><form action='searchResult.php' method='post'><input type='hidden' name='Date' value='AllTime'><button type='submit' name='team' value='".$row['HOME']."' style='background-color:Transparent; border:none; font-size: 1.2vw; font-weight:bold;'>".$row['HOME']."</button></form></td>";
                                echo "<td>" . $row['FTHG'] . "</td>";
                                echo "<td>" . $row['FTAG'] . "</td>";
                                echo "<td><form action='searchResult.php' method='post'><input type='hidden' name='Date' value='AllTime'><button type='submit' name='team' value='".$row['AWAY']."' style='background-color:Transparent; border:none; font-size: 1.2vw;'>".$row['AWAY']."</button></form></td>";
                                // creates a button with name and value = match id
                                // post hides id in URL

                                echo "<td> <form action='includes/delete.php' method='post'><input type='hidden' name='page' value='myMatches'> <input type='hidden' name='pos' value='17/18'><button type='submit' name='matchId' value=". $row['MATCHID']." >-</button></form></td>";                        
                            }
                            else if($row['AWAY'] == $_SESSION['userTeam']){
                                //user team bold if away
                                echo "<td>" . $row['M_DATE'] . "</td>";
                                echo "<td><form action='searchResult.php' method='post'><input type='hidden' name='Date' value='AllTime'><button type='submit' name='team' value='".$row['HOME']."' style='background-color:Transparent; border:none; font-size: 1.2vw;'>".$row['HOME']."</button></form></td>";
                                echo "<td>" . $row['FTHG'] . "</td>";
                                echo "<td>" . $row['FTAG'] . "</td>";
                                echo "<td><form action='searchResult.php' method='post'><input type='hidden' name='Date' value='AllTime'><button type='submit' name='team' value='".$row['AWAY']."' style='background-color:Transparent; border:none; font-size: 1.2vw; font-weight:bold;'>".$row['AWAY']."</button></form></td>";
                                // creates a button with name and value = match id
                                // post hides id in URL
                                echo "<td> <form action='includes/delete.php' method='post'><input type='hidden' name='page' value='myMatches'> <input type='hidden' name='pos' value='17/18'><button type='submit' name='matchId' value=". $row['MATCHID']." >-</button></form></td>";                        
                            }
                            else {
                                echo "<td>" . $row['M_DATE'] . "</td>";
                                echo "<td><form action='searchResult.php' method='post'><input type='hidden' name='Date' value='AllTime'><button type='submit' name='team' value='".$row['HOME']."' style='background-color:Transparent; border:none; font-size: 1.2vw;'>".$row['HOME']."</button></form></td>";
                                echo "<td>" . $row['FTHG'] . "</td>";
                                echo "<td>" . $row['FTAG'] . "</td>";
                                echo "<td><form action='searchResult.php' method='post'><input type='hidden' name='Date' value='AllTime'><button type='submit' name='team' value='".$row['AWAY']."' style='background-color:Transparent; border:none; font-size: 1.2vw;'>".$row['AWAY']."</button></form></td>";
                                // creates a button with name and value = match id
                                // post hides id in URL
                                echo "<td> <form action='includes/delete.php' method='post'><input type='hidden' name='page' value='myMatches'> <input type='hidden' name='pos' value='17/18'><button type='submit' name='matchId' value=". $row['MATCHID']." >-</button></form></td>";                        
                            }
                            if($row['FAV'] == 'Y'){
                                echo '<td style="border:none; background-color:#F8F8F8; width:3.5%;"><form action="includes/fav.php" method="post" class="favBut"><input type="hidden" name="pos" value="17/18"><button type="submit" name="unfav-Id"  value='.$row['MATCHID'].' style="background-color:#77DD77;"></button></form></td>';
                            }
                            else {
                                echo '<td style="border:none; background-color:#F8F8F8; width:3.5%;"><form action="includes/fav.php" method="post" class="favBut"><input type="hidden" name="pos" value="17/18"><button type="submit" name="fav-Id" value='.$row['MATCHID'].'></button></form></td>';                            
                            }                     
                            echo "</tr>";
                        }
                        echo "</table>";
                    }
                } 
                else{
                    header("Location: index.php?error=dbError");
                    exit();
                }  
                echo '<a name="16/17" />';

                //2016/17
                $sql = "SELECT * FROM (SELECT user_matches.userVal, user_matches.fav, games.matchId, games.m_date, games.home, games.FTHG, games.FTAG, games.away FROM (SELECT hometable.matchId, hometable.m_date, hometable.home, hometable.FTHG, hometable.FTAG, awaytable.away FROM (SELECT matches.matchId, matches.m_date, teams.teamName AS home, matches.FTHG, matches.FTAG FROM matches JOIN teams ON matches.homeTeam = teams.teamId ) hometable JOIN (SELECT matches.matchId, teams.teamName AS away FROM matches JOIN teams ON matches.awayTeam = teams.teamId ) awaytable ON hometable.matchId = awaytable.matchId) games JOIN user_matches ON games.matchId = user_matches.matchVal WHERE user_matches.userVal = :usr ORDER BY games.m_date DESC) m_results WHERE m_date BETWEEN TO_DATE('2016-07-01','YYYY-MM-DD') AND TO_DATE('2017-06-01','YYYY-MM-DD') ORDER BY m_date DESC";
                if($result = oci_parse($conn, $sql)){
                    oci_bind_by_name($result, ':usr', $user);
                    oci_execute($result);
                    if(true){
                        echo '<h2 style="float:left; font-size: 1vw; margin-left: 10%; margin-top 2%; text-decoration:underline;width:75%;">2016/17</h2>';
                        echo "<table class='outTable'>";
                            echo "<tr>";  
                                echo "<th style='border-left:none;'></th>";                                 
                                echo "<th>Date</th>";
                                echo "<th>Home</th>";
                                echo "<th>Home goals</th>";
                                echo "<th>Away goals</th>";
                                echo "<th>Away</th>";
                                echo "<th style='border-right:none;'></th>"; 
                                echo "<th style='border:none; background-color:#F8F8F8;'>Fav</th>";                                  
                            echo "</tr>";
                            $counter = 1;
                        while($row = oci_fetch_assoc($result)){
                            echo "<tr>"; 
                            echo "<td> <form action='match.php' method='post'><input type='hidden' name='page' value='myMatches'><input type='hidden' name='pos' value='16/17'><button type='submit' name='matchId' value=". $row['MATCHID']." >i</button></form></td>";                        

                            if($row['HOME'] == $_SESSION['userTeam']){
                                // user team bold if home
                                echo "<td>" . $row['M_DATE'] . "</td>";
                                echo "<td><form action='searchResult.php' method='post'><input type='hidden' name='Date' value='AllTime'><button type='submit' name='team' value='".$row['HOME']."' style='background-color:Transparent; border:none; font-size: 1.2vw; font-weight:bold;'>".$row['HOME']."</button></form></td>";
                                echo "<td>" . $row['FTHG'] . "</td>";
                                echo "<td>" . $row['FTAG'] . "</td>";
                                echo "<td><form action='searchResult.php' method='post'><input type='hidden' name='Date' value='AllTime'><button type='submit' name='team' value='".$row['AWAY']."' style='background-color:Transparent; border:none; font-size: 1.2vw;'>".$row['AWAY']."</button></form></td>";
                                // creates a button with name and value = match id
                                // post hides id in URL

                                echo "<td> <form action='includes/delete.php' method='post'><input type='hidden' name='page' value='myMatches'> <input type='hidden' name='pos' value='16/17'><button type='submit' name='matchId' value=". $row['MATCHID']." >-</button></form></td>";                        
                            }
                            else if($row['AWAY'] == $_SESSION['userTeam']){
                                //user team bold if away
                                echo "<td>" . $row['M_DATE'] . "</td>";
                                echo "<td><form action='searchResult.php' method='post'><input type='hidden' name='Date' value='AllTime'><button type='submit' name='team' value='".$row['HOME']."' style='background-color:Transparent; border:none; font-size: 1.2vw;'>".$row['HOME']."</button></form></td>";
                                echo "<td>" . $row['FTHG'] . "</td>";
                                echo "<td>" . $row['FTAG'] . "</td>";
                                echo "<td><form action='searchResult.php' method='post'><input type='hidden' name='Date' value='AllTime'><button type='submit' name='team' value='".$row['AWAY']."' style='background-color:Transparent; border:none; font-size: 1.2vw; font-weight:bold;'>".$row['AWAY']."</button></form></td>";
                                // creates a button with name and value = match id
                                // post hides id in URL
                                echo "<td> <form action='includes/delete.php' method='post'><input type='hidden' name='page' value='myMatches'> <input type='hidden' name='pos' value='16/17'><button type='submit' name='matchId' value=". $row['MATCHID']." >-</button></form></td>";                        
                            }
                            else {
                                echo "<td>" . $row['M_DATE'] . "</td>";
                                echo "<td><form action='searchResult.php' method='post'><input type='hidden' name='Date' value='AllTime'><button type='submit' name='team' value='".$row['HOME']."' style='background-color:Transparent; border:none; font-size: 1.2vw;'>".$row['HOME']."</button></form></td>";
                                echo "<td>" . $row['FTHG'] . "</td>";
                                echo "<td>" . $row['FTAG'] . "</td>";
                                echo "<td><form action='searchResult.php' method='post'><input type='hidden' name='Date' value='AllTime'><button type='submit' name='team' value='".$row['AWAY']."' style='background-color:Transparent; border:none; font-size: 1.2vw;'>".$row['AWAY']."</button></form></td>";
                                // creates a button with name and value = match id
                                // post hides id in URL
                                echo "<td> <form action='includes/delete.php' method='post'><input type='hidden' name='page' value='myMatches'> <input type='hidden' name='pos' value='16/17'><button type='submit' name='matchId' value=". $row['MATCHID']." >-</button></form></td>";                        
                            }
                            if($row['FAV'] == 'Y'){
                                echo '<td style="border:none; background-color:#F8F8F8; width:3.5%;"><form action="includes/fav.php" method="post" class="favBut"><input type="hidden" name="pos" value="16/17"><button type="submit" name="unfav-Id"  value='.$row['MATCHID'].' style="background-color:#77DD77;"></button></form></td>';
                            }
                            else {
                                echo '<td style="border:none; background-color:#F8F8F8; width:3.5%;"><form action="includes/fav.php" method="post" class="favBut"><input type="hidden" name="pos" value="16/17"><button type="submit" name="fav-Id" value='.$row['MATCHID'].'></button></form></td>';                            
                            }                     
                            echo "</tr>";
                        }
                        echo "</table>";
                    }
                } 
                else{
                    header("Location: index.php?error=dbError");
                    exit();
                }  
                echo '<a name="15/16" />';

                // 2015/16 
                $sql = "SELECT * FROM (SELECT user_matches.userVal, user_matches.fav, games.matchId, games.m_date, games.home, games.FTHG, games.FTAG, games.away FROM (SELECT hometable.matchId, hometable.m_date, hometable.home, hometable.FTHG, hometable.FTAG, awaytable.away FROM (SELECT matches.matchId, matches.m_date, teams.teamName AS home, matches.FTHG, matches.FTAG FROM matches JOIN teams ON matches.homeTeam = teams.teamId ) hometable JOIN (SELECT matches.matchId, teams.teamName AS away FROM matches JOIN teams ON matches.awayTeam = teams.teamId ) awaytable ON hometable.matchId = awaytable.matchId) games JOIN user_matches ON games.matchId = user_matches.matchVal WHERE user_matches.userVal = :usr ORDER BY games.m_date DESC) m_results WHERE m_date BETWEEN TO_DATE('2015-07-01','YYYY-MM-DD') AND TO_DATE('2016-06-01','YYYY-MM-DD') ORDER BY m_date DESC";
                if($result = oci_parse($conn, $sql)){
                    oci_bind_by_name($result, ':usr', $user);
                    oci_execute($result);
                    if(true){
                        echo '<h2 style="float:left; font-size: 1vw; margin-left: 10%; margin-top 2%; text-decoration:underline;width:75%;">2015/16</h2>';
                        echo "<table class='outTable'>";
                            echo "<tr>";  
                                echo "<th style='border-left:none;'></th>";                                 
                                echo "<th>Date</th>";
                                echo "<th>Home</th>";
                                echo "<th>Home goals</th>";
                                echo "<th>Away goals</th>";
                                echo "<th>Away</th>";
                                echo "<th style='border-right:none;'></th>"; 
                                echo "<th style='border:none; background-color:#F8F8F8;'>Fav</th>";                                  
                            echo "</tr>";
                            $counter = 1;
                        while($row = oci_fetch_assoc($result)){
                            echo "<tr>"; 
                            echo "<td> <form action='match.php' method='post'><input type='hidden' name='page' value='myMatches'><input type='hidden' name='pos' value='15/16'><button type='submit' name='matchId' value=". $row['MATCHID']." >i</button></form></td>";                        

                            if($row['HOME'] == $_SESSION['userTeam']){
                                // user team bold if home
                                echo "<td>" . $row['M_DATE'] . "</td>";
                                echo "<td><form action='searchResult.php' method='post'><input type='hidden' name='Date' value='AllTime'><button type='submit' name='team' value='".$row['HOME']."' style='background-color:Transparent; border:none; font-size: 1.2vw; font-weight:bold;'>".$row['HOME']."</button></form></td>";
                                echo "<td>" . $row['FTHG'] . "</td>";
                                echo "<td>" . $row['FTAG'] . "</td>";
                                echo "<td><form action='searchResult.php' method='post'><input type='hidden' name='Date' value='AllTime'><button type='submit' name='team' value='".$row['AWAY']."' style='background-color:Transparent; border:none; font-size: 1.2vw;'>".$row['AWAY']."</button></form></td>";
                                // creates a button with name and value = match id
                                // post hides id in URL

                                echo "<td> <form action='includes/delete.php' method='post'><input type='hidden' name='page' value='myMatches'> <input type='hidden' name='pos' value='15/16'><button type='submit' name='matchId' value=". $row['MATCHID']." >-</button></form></td>";                        
                            }
                            else if($row['AWAY'] == $_SESSION['userTeam']){
                                //user team bold if away
                                echo "<td>" . $row['M_DATE'] . "</td>";
                                echo "<td><form action='searchResult.php' method='post'><input type='hidden' name='Date' value='AllTime'><button type='submit' name='team' value='".$row['HOME']."' style='background-color:Transparent; border:none; font-size: 1.2vw;'>".$row['HOME']."</button></form></td>";
                                echo "<td>" . $row['FTHG'] . "</td>";
                                echo "<td>" . $row['FTAG'] . "</td>";
                                echo "<td><form action='searchResult.php' method='post'><input type='hidden' name='Date' value='AllTime'><button type='submit' name='team' value='".$row['AWAY']."' style='background-color:Transparent; border:none; font-size: 1.2vw; font-weight:bold;'>".$row['AWAY']."</button></form></td>";
                                // creates a button with name and value = match id
                                // post hides id in URL
                                echo "<td> <form action='includes/delete.php' method='post'><input type='hidden' name='page' value='myMatches'> <input type='hidden' name='pos' value='15/16'><button type='submit' name='matchId' value=". $row['MATCHID']." >-</button></form></td>";                        
                            }
                            else {
                                echo "<td>" . $row['M_DATE'] . "</td>";
                                echo "<td><form action='searchResult.php' method='post'><input type='hidden' name='Date' value='AllTime'><button type='submit' name='team' value='".$row['HOME']."' style='background-color:Transparent; border:none; font-size: 1.2vw;'>".$row['HOME']."</button></form></td>";
                                echo "<td>" . $row['FTHG'] . "</td>";
                                echo "<td>" . $row['FTAG'] . "</td>";
                                echo "<td><form action='searchResult.php' method='post'><input type='hidden' name='Date' value='AllTime'><button type='submit' name='team' value='".$row['AWAY']."' style='background-color:Transparent; border:none; font-size: 1.2vw;'>".$row['AWAY']."</button></form></td>";
                                // creates a button with name and value = match id
                                // post hides id in URL
                                echo "<td> <form action='includes/delete.php' method='post'><input type='hidden' name='page' value='myMatches'> <input type='hidden' name='pos' value='15/16'><button type='submit' name='matchId' value=". $row['MATCHID']." >-</button></form></td>";                        
                            }
                            if($row['FAV'] == 'Y'){
                                echo '<td style="border:none; background-color:#F8F8F8; width:3.5%;"><form action="includes/fav.php" method="post" class="favBut"><input type="hidden" name="pos" value="15/16"><button type="submit" name="unfav-Id"  value='.$row['MATCHID'].' style="background-color:#77DD77;"></button></form></td>';
                            }
                            else {
                                echo '<td style="border:none; background-color:#F8F8F8; width:3.5%;"><form action="includes/fav.php" method="post" class="favBut"><input type="hidden" name="pos" value="15/16"><button type="submit" name="fav-Id" value='.$row['MATCHID'].'></button></form></td>';                            
                            }                     
                            echo "</tr>";
                        }
                        echo "</table>";
                    }
                } 
                else{
                    header("Location: index.php?error=dbError");
                    exit();
                }     
                echo '</div>';
            ?>
        </div>
    </body>

<?php 
    require "footer.php"
?>