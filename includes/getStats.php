<?php

    require 'dbh.php';
    if(!isset($_SESSION['userId'])){
        header("Location: index.php");
        exit();
    }
    // check is user logged any matches
    $user = $_SESSION['userId'];           
    $matchChecker = "SELECT COUNT(user_matches.matchVal) FROM user_matches JOIN matches ON user_matches.matchVal = matches.matchId WHERE user_matches.userVal= :usid";
    if($check = oci_parse($conn, $matchChecker)){
        oci_bind_by_name($check, ':usid', $user);
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


    $graphSQL = "SELECT output.name AS teamName, SUM(output.apps) AS games, SUM(output.goals) AS goals FROM (SELECT home.teamName AS name, COUNT(DISTINCT home.matchId) AS apps, SUM(home.FTHG) AS goals FROM (SELECT teams.teamName, us.matchId, us.FTHG FROM teams JOIN (SELECT * FROM user_matches JOIN matches ON user_matches.matchVal = matches.matchId WHERE user_matches.userVal = :defstat AND matches.m_date BETWEEN TO_DATE(:dstart,'YYYY-MM-DD') AND TO_DATE(:dend,'YYYY-MM-DD'))  us on teams.teamId = us.homeTeam)  home GROUP BY home.teamName UNION ALL SELECT away.teamName AS name2, COUNT(DISTINCT away.matchId) AS apps2, SUM(away.FTAG) AS goals2 FROM (SELECT teams.teamName, us2.matchId, us2.FTAG FROM teams JOIN (SELECT * FROM user_matches JOIN matches ON user_matches.matchVal = matches.matchId WHERE user_matches.userVal = :defstat AND matches.m_date BETWEEN TO_DATE(:dstart,'YYYY-MM-DD') AND TO_DATE(:dend,'YYYY-MM-DD'))  us2 on teams.teamId = us2.awayTeam)  away GROUP BY away.teamName)  output GROUP BY output.name ORDER BY games DESC, name";
    if($graphresult = oci_parse($conn, $graphSQL)){
        oci_bind_by_name($graphresult, ':defstat', $_SESSION['defaultStatsUserId']);
        oci_bind_by_name($graphresult, ':dstart', $_SESSION['dateStart']);
        oci_bind_by_name($graphresult, ':dend', $_SESSION['dateEnd']);

        oci_execute($graphresult);
        $BigArray = Array();
        while($rows = oci_fetch_array($graphresult)){
            //put id's in array
            $SmallArray = Array();
            $SmallArray[] = $rows['TEAMNAME'];
            $SmallArray[] = $rows['GAMES'];
            $SmallArray[] = $rows['GOALS'];
            
            $BigArray[] = $SmallArray;
        }
        oci_free_statement($graphresult);
    }
    else {
        header("Location: index.php?error=dbError1");
        exit(); 
    }


    $dataPoints = array();
    foreach($BigArray as $line){
        $dataPoints[] = array("label"=> $line[0], "y"=> (int)$line[2]);
        
    }
    
    function rand_color() {
        return '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);
    }

    $teamAll = "SELECT COUNT(matches.matchId) AS total FROM matches WHERE matches.m_date BETWEEN TO_DATE(:dstart,'YYYY-MM-DD') AND TO_DATE(:dend,'YYYY-MM-DD') AND (matches.homeTeam = :defstatid OR matches.awayTeam = :defstatid)";
    if($teamquery = oci_parse($conn, $teamAll)){
        oci_bind_by_name($teamquery, ':defstatid', $_SESSION['defaultStatsId']);
        oci_bind_by_name($teamquery, ':dstart', $_SESSION['dateStart']);
        oci_bind_by_name($teamquery, ':dend', $_SESSION['dateEnd']);

        oci_execute($teamquery);
        $teamrow = oci_fetch_assoc($teamquery);

        $totalTeamGames = $teamrow['TOTAL'];


    }
    else {
        header("Location: index.php?error=dbError1");
        exit(); 
    }


    $popped = Array();
    $nameArray = Array();
    $matchArray = Array();
    $goalArray = Array();
    $gpgArray = Array();
    $colourArray = Array();
    foreach($BigArray as $element){
        $nameArray[] = $element[0];
        $matchArray[] = (int)$element[1];
        if((int)$element[2] == 0){
            $goalArray[] = 0;
        }else{

        
        $goalArray[] = (int)$element[2];
        }

        $gpgArray[] = ((int)$element[2]/(int)$element[1]);
        $colourArray[] = rand_color();
    }
   