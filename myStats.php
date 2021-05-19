<?php 
    require "header.php"
?>

    <body>
        

        <div class="Border-main">
        <button class="jumpDel" id="maker">+</button>
        <?php
        
        require 'includes/dbh.php';
        require 'includes/getStats.php';
        if(!isset($_SESSION['userId'])){
            header("Location: index.php");
            exit();
        }
        echo '<script>
        function minmax(parent, button){
            if(document.getElementById(parent).style.display == "none"){
                document.getElementById(parent).style.display = "block";
                document.getElementById(button).innerHTML = "-"
            }
            else{
                document.getElementById(parent).style.display = "none";
                document.getElementById(button).innerHTML = "+"
            }
            
        }
        </script>';
        echo '<div class="errors">';
        if(isset($_GET['success'])){
            if($_GET['success'] == 'changed'){
                echo '<p style="color:green;">Table Updated!</p>';
            } 
        }
        echo '</div>';
        echo '<div class="rotate-message">Rotate Device</div>';

            echo '<div class="headers">';
            echo '<h3>My Stats</h3>';
            echo '<p>This is your statistics section, your stats are broken down into different sections with headings to show what each section contains. Press the plus button to expand each section.</p>';
            echo '<div class="jump" id="jumps" style="display:block;">';
                echo '<a href="myStats.php#AppsGraph">Teams Seen (Graph)</a>';
                echo '<a href="myStats.php#GoalGraph">Goals Seen (Graph)</a>';
                echo '<a href="myStats.php#AllGraph">GPG Graph</a>';
                echo '<a href="myStats.php#Notable" style="border-bottom:1px solid black">Notable Matches</a>';
                echo '<button id="hider" style="width: 50%; font-size: 1vw; font-family: "Roboto", sans-serif;">X</button>';

            echo '</div>';
            
            echo '<script>
            document.getElementById("hider").addEventListener("click", function(){
                minmax("jumps", "hider");
            });
            document.getElementById("maker").addEventListener("click", function(){
                document.getElementById("jumps").style.display = "block";
                document.getElementById("hider").innerHTML = "X"
            });
            </script>';
            
            echo '</div>';

            echo '<div class="headers">';

/////////////////////////////
             echo '<a name="Home" />';     
            echo '<h3 style="font-size:1.5vw; text-decoration:underline; text-align:center; float:right; margin-right:25%; padding-bottom:1%;">Basic stat for All Games</h3>';
            echo '<button class="btn-minimize" id="toggle7">+</button>';
            echo '<div id="home" style="display:none;">';
            echo '<script>
            document.getElementById("toggle7").addEventListener("click", function(){
                minmax("home", "toggle7");
            });
            </script>'; 
                          $highestScoring = "SELECT games.matchId, games.m_date, games.home, games.FTHG, games.FTAG, games.away FROM (SELECT hometable.matchId, hometable.m_date, hometable.home, hometable.FTHG, hometable.FTAG, awaytable.away FROM (SELECT matches.matchId, matches.m_date, teams.teamName AS home, matches.FTHG, matches.FTAG FROM matches JOIN teams ON matches.homeTeam = teams.teamId )  hometable JOIN (SELECT matches.matchId, teams.teamName AS away FROM matches JOIN teams ON matches.awayTeam = teams.teamId )  awaytable ON hometable.matchId = awaytable.matchId)  games WHERE games.matchid = all_game_stats.allHighestScoringMatch";
                if($result6 = oci_parse($conn, $highestScoring)){
                     oci_execute($result6);
                    $high = oci_fetch_assoc($result6);

                    echo '<h3 style="font-size:1.5vw; text-decoration:underline; text-align:center; float:right; margin-right:25%; padding-bottom:1%;">Highest Scoring Match</h3>';

                    echo '<div class="matches">';
                    echo '<table>
                            <tr>';
                            echo "<td>" . $high['M_DATE'] . "</td>";
                            echo "<td>". $high['HOME'] . "</td>";
                            echo "<td>" . $high['FTHG'] . "</td>";
                            echo "<td>" . $high['FTAG'] . "</td>";
                            echo "<td style='border-right:none'>" . $high['AWAY'] . "</td>";
                        echo '</tr>
                        </table>';
                    echo '</div>';
                }
                else {
                    header("Location: index.php?error=dbError1");
                    exit();
                }
                // worst discipline game
                $mostCards = "SELECT games.matchId, games.m_date, games.home, games.FTHG, games.FTAG, games.away, (games.hRed + games.aRed) AS reds,  (games.hYellow + games.aYellow) AS yellows, ((games.hYellow + games.aYellow) + (games.hRed + games.aRed)) AS cards FROM (SELECT hometable.matchId, hometable.m_date, hometable.home, hometable.FTHG, hometable.FTAG, awaytable.away, hometable.hRed, hometable.aRed, hometable.hYellow, hometable.aYellow FROM (SELECT matches.matchId, matches.m_date, teams.teamName AS home, matches.FTHG, matches.FTAG, matches.hRed, matches.hYellow, matches.aYellow, matches.aRed FROM matches JOIN teams ON matches.homeTeam = teams.teamId )  hometable JOIN (SELECT matches.matchId, teams.teamName AS away FROM matches JOIN teams ON matches.awayTeam = teams.teamId )  awaytable ON hometable.matchId = awaytable.matchId)  games  WHERE games.matchid = all_game_stats.allMostCardsMatch";
                if($result7 = oci_parse($conn, $mostCards)){
                   
                     oci_execute($result7);
                    $cards = oci_fetch_assoc($result7);

                    echo '<h3 style="font-size:1.5vw; text-decoration:underline; text-align:center; float:right; margin-right:25%; padding-bottom:1%;">Most Cards in a Match</h3>';

                    echo '<div class="matches">';
                    echo '<table>
                            <tr>';
                                echo "<th></th>";
                                echo "<th></th>";
                                echo "<th></th>";
                                echo "<th></th>";
                                echo "<th></th>";
                                echo "<th style='border-bottom:2px solid black; font-size:1vw;'>Red Cards</th>";
                                echo "<th style='border-bottom:2px solid black; font-size:1vw;'>Yellow Cards</th>";
                                echo "<th style='border-right:none; border-bottom:2px solid black; font-size:1vw;'>Total Cards</th>";
                            echo '</tr>';

                            echo '<tr>';
                                echo "<td>" . $cards['M_DATE'] . "</td>";
                                echo "<td>". $cards['HOME'] . "</td>";
                                echo "<td>" . $cards['FTHG'] . "</td>";
                                echo "<td>" . $cards['FTAG'] . "</td>";
                                echo "<td>" . $cards['AWAY'] . "</td>";
                                echo "<td>" . $cards['REDS'] . "</td>";
                                echo "<td>" . $cards['YELLOWS'] . "</td>";
                                echo "<td style='border-right:none'>" . $cards['CARDS'] . "</td>";
                        echo '</tr>
                        </table>';
                    echo '</div>';
            }
            else {
                header("Location: index.php?error=dbError1");
                exit();
            } 
            
                          $mostCorners = "SELECT games.matchId, games.m_date, games.home, games.FTHG, games.FTAG, games.away, (games.HCORNERS +games.ACORNERS) as CORNERS FROM (SELECT hometable.matchId, hometable.m_date, hometable.home, hometable.FTHG, hometable.FTAG, hometable.HCORNERS, hometable.ACORNERS, awaytable.away FROM (SELECT matches.matchId, matches.m_date, teams.teamName AS home, matches.FTHG, matches.FTAG, matches.HCORNERS, matches.ACORNERS FROM matches JOIN teams ON matches.homeTeam = teams.teamId )  hometable JOIN (SELECT matches.matchId, teams.teamName AS away FROM matches JOIN teams ON matches.awayTeam = teams.teamId )  awaytable ON hometable.matchId = awaytable.matchId) games WHERE games.matchid = all_game_stats.allMostCornersMatch";
                if($result8 = oci_parse($conn, $mostCorners)){
                     oci_execute($result8);
                    $most = oci_fetch_assoc($result8);

                    echo '<h3 style="font-size:1.5vw; text-decoration:underline; text-align:center; float:right; margin-right:25%; padding-bottom:1%;">Most Corners Match</h3>';

                    echo '<div class="matches">';
                    echo '<table>
                            <tr>';
                                echo "<th></th>";
                                echo "<th></th>";
                                echo "<th></th>";
                                echo "<th></th>";
                                echo "<th></th>";
                                echo "<th style='border-right:none; border-bottom:2px solid black; font-size:1vw;'>Total Corners</th>";
                            echo '</tr>';

                            echo '<tr>';
                                echo "<td>" . $most['M_DATE'] . "</td>";
                                echo "<td>". $most['HOME'] . "</td>";
                                echo "<td>" . $most['FTHG'] . "</td>";
                                echo "<td>" . $most['FTAG'] . "</td>";
                                echo "<td>" . $most['AWAY'] . "</td>";
                                echo "<td style='border-right:none'>" . $most['CORNERS'] . "</td>";
                        echo '</tr>
                        </table>';
                    echo '</div>';
                echo '</div>';

                }
                else {
                    header("Location: index.php?error=dbError1");
                    exit();
                }
                echo '<br>';
                echo '<div style="border-bottom:1px solid black; float: left; width: 80%; margin-left:10%; margin-top: 0.5%;"></div>';
            ///////////////////////////////////////////////////////////////////////////////
            echo '<a name="AppsGraph" />';
            echo '<h3 style="font-size:1.5vw; text-decoration:underline; text-align:center; float:right; margin-right:25%; padding-bottom:1%;">Number of Matches Attended Per Team '.$_SESSION['dateTitle'].'</h3>';
            echo '<button class="btn-minimize" id="toggle9">+</button>';
            echo '<div id="appsGraph" style="display:none;">';
            echo '<script>
            document.getElementById("toggle9").addEventListener("click", function(){
                minmax("appsGraph", "toggle9");
            });
            </script>';
            echo '<div class="headers" >';
            echo '<p style="margin-top:0%;"><b>This graph shows the number of matches you have attended per team.</b> Teams can be added and removed using the buttons at the bottom.</p>';
            echo '</div>';

                echo '<div class="bar"><canvas id="canvas" width="90%" height="37vw"></canvas>
                
                <div class="barbut">
                <button type="button" id="remove">Remove Data</button>
                <button type="button" id="add">Add Data</button></div></div>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js"></script>';
            echo '</div>';
            echo "<script>
            
            var ctx = document.getElementById('canvas').getContext('2d');
            var myChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels:"; echo json_encode($nameArray);
                    echo ",
                    datasets: [{
                        label: '# Of Games'
                        ,data:";  echo json_encode($matchArray);echo ",
                        backgroundColor:";
                            echo json_encode($colourArray);
                        echo ",
                        borderColor:";
                        echo json_encode($colourArray);
                        echo ",
                        borderWidth: 1
                    }]
                },
                options: {
                    legend: {
                        display: false
                    },
                    title: {
                        display: false,
                        text: 'Number of Matches Attended Per Team ".$_SESSION['dateTitle']."',
                        fontSize: 25
                    },
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true,
                                min: 0
                            }
                        }]
                    }
                }
            });

            var storeLab = [];
            var storeDat = [];
            var emptyArray = [];

            document.getElementById('add').addEventListener('click', function(){
                if(storeLab.length == 0){
                    myChart.update();
                }
                else{
                    myChart.data.labels.push(storeLab.pop());
                    
                    myChart.data.datasets.forEach((dataset) => {
                        dataset.data.push(storeDat.pop());
                        
                    });
                    myChart.update();
                }
            });
            
            document.getElementById('remove').addEventListener('click', function(){
                if(myChart.data.labels.length == 0){
                    myChart.update();
                }
                else{
                    storeLab.push(myChart.data.labels.pop());
                    myChart.data.datasets.forEach((dataset) => {
                        storeDat.push(dataset.data.pop());
                    });
                
                    myChart.update();
                }
            });
            
            </script>";
            echo '<div style="border-bottom:1px solid black; float: left; width: 80%; margin-left:10%; margin-top: 0.5%;"></div>';

            //////////////////////////////////////////////////////////////////////////
            echo '<a name="GoalGraph" />';
            echo '<h3 style="font-size:1.5vw; text-decoration:underline; text-align:center; float:right; margin-right:25%; padding-bottom:1%;">Number of Goals Seen Per Team '.$_SESSION['dateTitle'].'</h3>';
            echo '<button class="btn-minimize" id="toggle10">+</button>';
            echo '<div id="goalGraph" style="display:none;">';
            echo '<script>
            document.getElementById("toggle10").addEventListener("click", function(){
                minmax("goalGraph", "toggle10");
            });
            </script>';
            echo '<div class="headers" >';
            echo '<p style="margin-top:0%;"><b>This graph shows the number of goals you have seen per team.</b> Teams can be added and removed using the buttons at the bottom, and you can change the graph from a circle to a semi-circle.</p>';
            echo '</div>';

                echo '<div class="bar"><canvas id="canvas2" width="90%" height="30vw"></canvas>
                <div class="barbut">
                <button type="button" id="remove2" style="float:left; margin-right:0.4%">Remove Data</button>
                <button type="button" id="add2"  style="float:left">Add Data</button>
                <button type="button" id="semi" style="margin-left:-18%; margin-top:6%">Semi/Full Circle</button></div></div>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js"></script>';
            echo '</div>';
            echo "<script>
            
            var optionsPie = {
                tooltipTemplate: '<%= label %> - <%= value %>'
            }

            var ctx = document.getElementById('canvas2').getContext('2d');
            var doughnut = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels:"; echo json_encode($nameArray);
                    echo ",
                    datasets: [{
                        data:";  echo json_encode($goalArray);echo ",
                        backgroundColor:";
                            echo json_encode($colourArray);
                        echo ",
                        borderColor:";
                        echo json_encode($colourArray);
                        echo ",
                        borderWidth: 1
                    }]
                },
                options: {
                    legend: {
                        display: false
                    },
                    title: {
                        display: false,
                        text: 'Number of Goals Seen Per Team ".$_SESSION['dateTitle']."',
                        fontSize: 25
                    }
                    
                }
            });

            var storeLab2 = [];
            var storeDat2 = [];
            var emptyArray2 = [];

            document.getElementById('add2').addEventListener('click', function(){
                if(storeLab2.length == 0){
                    doughnut.update();
                }
                else{
                    doughnut.data.labels.push(storeLab2.pop());
                    
                    doughnut.data.datasets.forEach((dataset) => {
                        dataset.data.push(storeDat2.pop());
                        
                    });
                    doughnut.update();
                }
            });
            
            document.getElementById('remove2').addEventListener('click', function(){
                if(doughnut.data.labels.length == 0){
                    doughnut.update();
                }
                else{
                    storeLab2.push(doughnut.data.labels.pop());
                    doughnut.data.datasets.forEach((dataset) => {
                        storeDat2.push(dataset.data.pop());
                    });
                
                    doughnut.update();
                }
            });

            document.getElementById('semi').addEventListener('click', function(){
                if (window.doughnut.options.circumference === Math.PI) {
                    window.doughnut.options.circumference = 2 * Math.PI;
                    window.doughnut.options.rotation = -Math.PI / 2;
                } else {
                    window.doughnut.options.circumference = Math.PI;
                    window.doughnut.options.rotation = -Math.PI;
                }
    
                window.doughnut.update();
            });

            </script>";
            echo '<div style="border-bottom:1px solid black; float: left; width: 80%; margin-left:10%; margin-top: 0.5%;"></div>';

            /////////////////////////////////////////////////////////////////////////////
            echo '<a name="AllGraph" />';
            echo '<h3 style="font-size:1.5vw; text-decoration:underline; text-align:center; float:right; margin-right:25%; padding-bottom:1%;">Matches, Goals and Goals Per Game Per Team '.$_SESSION['dateTitle'].'</h3>';
            echo '<button class="btn-minimize" id="toggle11">+</button>';
            echo '<div id="allGraph" style="display:none;">';
            echo '<script>
            document.getElementById("toggle11").addEventListener("click", function(){
                minmax("allGraph", "toggle11");
            });
            </script>';
            echo '<div class="headers" >';
            echo '<p style="margin-top:0%;"><b>This graph shows the number of matches, goals and goals per game for teams you have seen.</b> Teams can be added and removed using the buttons at the bottom, and bars can be removed by clicking the keys at the top.</p>';
            echo '</div>';

                echo '<div class="bar"><canvas id="canvas3" width="85%" height="35vw"></canvas>
                
                <div class="barbut">
                <button type="button" id="remove3">Remove Data</button>
                <button type="button" id="add3">Add Data</button></div></div>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js"></script>';
            echo '</div>';
            echo "<script>
            
            var ctx = document.getElementById('canvas3').getContext('2d');
            var all = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels:"; echo json_encode($nameArray);
                    echo ",
                    datasets: [{
                        label: '# Of Games'
                        ,data:";  echo json_encode($matchArray);echo ",
                        backgroundColor: '#ffb347 ',
                        hoverBackgroundColor: '#ffa500',
                        hoverBorderColor: '#ffa500',
                        borderColor: '#ffb347 ',
                        borderWidth: 1
                    },
                    {
                        label: '# Of Goals'
                        ,data:";  echo json_encode($goalArray);echo ",
                        backgroundColor: '#b19cd9 ',
                        hoverBackgroundColor: '#6a0dad  ',
                        borderColor: '#b19cd9',
                        borderWidth: 1
                    },
                    {
                        label: '# Of Goals Per Game'
                        ,data:";  echo json_encode($gpgArray);echo ",
                        backgroundColor: '#77dd77 ',
                        hoverBackgroundColor: '#00ff00 ',
                        borderColor: '#77dd77',
                        borderWidth: 1
                    }
                    ]
                },
                options: {
                    legend: {
                        display: true
                    },
                    title: {
                        display: false,
                        text: 'Matches, Goals and Goals Per Game Per Team ".$_SESSION['dateTitle']."',
                        fontSize: 25
                    },
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true,
                                min: 0
                            }
                        }]
                    }
                }
            });

            var storeLab3 = [];
            var storeDat3 = [];
            
            

            document.getElementById('add3').addEventListener('click', function(){
                if(storeLab3.length == 0){
                    all.update();
                }
                else{
                    all.data.labels.push(storeLab3.pop());
                    // fixes ordering 
                    var a = storeDat3[storeDat3.length-1];
                    storeDat3[storeDat3.length-1] = storeDat3[storeDat3.length-3];
                    storeDat3[storeDat3.length-3] = a;
                    all.data.datasets.forEach((dataset) => {
                        dataset.data.push(storeDat3.pop());
                        
                        
                    });
                    all.update();
                }
            });
            
            document.getElementById('remove3').addEventListener('click', function(){
                if(all.data.labels.length == 0){
                    all.update();
                }
                else{
                    storeLab3.push(all.data.labels.pop());
                    all.data.datasets.forEach((dataset) => {
                        storeDat3.push(dataset.data.pop());
                        
                    });
                
                    
                    
                
                    all.update();
                }
            });

            
            
            </script>";
            echo '<div style="border-bottom:1px solid black; float: left; width: 80%; margin-left:10%; margin-top: 0.5%;"></div>';

            ///////////////////////////////////////////////////////////////////////////////////////////
            echo '<a name="Notable" />';
            echo '<h3 style="font-size:2vw; text-decoration:underline; text-align:center; float:right; margin-right:25%; padding-bottom:1%;">Notable Matches </h3>'; 
            echo '<button class="btn-minimize" id="toggle12">+</button>';
            echo '<div id="notable" style="display:none;">';
            echo '<script>
            document.getElementById("toggle12").addEventListener("click", function(){
                minmax("notable", "toggle12");
            });
            </script>';
                $getMatches = "SELECT user_matches.userVal, games.matchId, games.m_date, games.home, games.FTHG, games.FTAG, games.away FROM (SELECT hometable.matchId, hometable.m_date, hometable.home, hometable.FTHG, hometable.FTAG, awaytable.away FROM (SELECT matches.matchId, matches.m_date, teams.teamName AS home, matches.FTHG, matches.FTAG FROM matches JOIN teams ON matches.homeTeam = teams.teamId )  hometable JOIN (SELECT matches.matchId, teams.teamName AS away FROM matches JOIN teams ON matches.awayTeam = teams.teamId )  awaytable ON hometable.matchId = awaytable.matchId)  games JOIN user_matches ON games.matchId = user_matches.matchVal WHERE user_matches.userVal = :defstatid ORDER BY games.m_date DESC";
                if($result5 = oci_parse($conn, $getMatches)){
                    oci_bind_by_name($result5, ':defstatid', $_SESSION['defaultStatsUserId']);
                    oci_execute($result5);
                    $result5row = oci_parse($conn, $getMatches);
                     oci_bind_by_name($result5row, ':defstatid', $_SESSION['defaultStatsUserId']);
                     oci_execute($result5row);
                    $numResults = oci_fetch_all($result5row, $res);
                    $counter = 0;
        
                    while($row = oci_fetch_assoc($result5)){
                        if($counter == 0){
                            //first row
                            echo '<h3 style="font-size:1.5vw; text-decoration:underline; text-align:center; float:right; margin-right:25%; padding-bottom:1%;">Most Recent Match</h3>';

                            echo '<div class="matches">';
                            echo '<table>
                                    <tr>';
                                    echo "<td>" . $row['M_DATE'] . "</td>";
                                    echo "<td>". $row['HOME'] . "</td>";
                                    echo "<td>" . $row['FTHG'] . "</td>";
                                    echo "<td>" . $row['FTAG'] . "</td>";
                                    echo "<td style='border-right:none'>" . $row['AWAY'] . "</td>";
                                echo '</tr>
                                </table>';
                            echo '</div>';
                            $counter++;

                        }
                        else if (++$counter == $numResults) {
                            // last row
                            echo '<h3 style="font-size:1.5vw; text-decoration:underline; text-align:center; float:right; margin-right:25%; padding-bottom:1%;">First Match Logged</h3>';

                            echo '<div class="matches">';
                            echo '<table>
                                    <tr>';
                                    echo "<td>" . $row['M_DATE'] . "</td>";
                                    echo "<td>". $row['HOME'] . "</td>";
                                    echo "<td>" . $row['FTHG'] . "</td>";
                                    echo "<td>" . $row['FTAG'] . "</td>";
                                    echo "<td style='border-right:none'>" . $row['AWAY'] . "</td>";
                                echo '</tr>
                                </table>';
                            echo '</div>';
                        }
                    }
                }
                else{
                   header("Location: index.php?error=dbError1");
                    exit();
                }
                // highest scoring game 
                //$hsmid = 1200;
                /*$findHighest = "BEGIN :hmi := highestScoringMatch(:defstatid); END;";
                $result6Helper = oci_parse($conn, $findHighest);
                oci_bind_by_name($result6Helper, ':defstatid', $_SESSION['defaultStatsUserId']);
                oci_bind_by_name($result6Helper, ':hmi', $hsmid);
                oci_execute($result6Helper);*/

                $highestScoring = "SELECT user_matches.userVal, games.matchId, games.m_date, games.home, games.FTHG, games.FTAG, games.away FROM (SELECT hometable.matchId, hometable.m_date, hometable.home, hometable.FTHG, hometable.FTAG, awaytable.away FROM (SELECT matches.matchId, matches.m_date, teams.teamName AS home, matches.FTHG, matches.FTAG FROM matches JOIN teams ON matches.homeTeam = teams.teamId )  hometable JOIN (SELECT matches.matchId, teams.teamName AS away FROM matches JOIN teams ON matches.awayTeam = teams.teamId )  awaytable ON hometable.matchId = awaytable.matchId)  games JOIN user_matches ON games.matchId = user_matches.matchVal WHERE games.matchid =user_game_stats.highestScoringMatch(:defstatid)";
                if($result6 = oci_parse($conn, $highestScoring)){
                    oci_bind_by_name($result6, ':defstatid', $_SESSION['defaultStatsUserId']);
                     oci_execute($result6);
                    $high = oci_fetch_assoc($result6);

                    echo '<h3 style="font-size:1.5vw; text-decoration:underline; text-align:center; float:right; margin-right:25%; padding-bottom:1%;">Highest Scoring Match</h3>';

                    echo '<div class="matches">';
                    echo '<table>
                            <tr>';
                            echo "<td>" . $high['M_DATE'] . "</td>";
                            echo "<td>". $high['HOME'] . "</td>";
                            echo "<td>" . $high['FTHG'] . "</td>";
                            echo "<td>" . $high['FTAG'] . "</td>";
                            echo "<td style='border-right:none'>" . $high['AWAY'] . "</td>";
                        echo '</tr>
                        </table>';
                    echo '</div>';
                }
                else {
                    header("Location: index.php?error=dbError1");
                    exit();
                }
                // worst discipline game
                $mostCards = "SELECT user_matches.userVal, games.matchId, games.m_date, games.home, games.FTHG, games.FTAG, games.away, (games.hRed + games.aRed) AS reds,  (games.hYellow + games.aYellow) AS yellows, ((games.hYellow + games.aYellow) + (games.hRed + games.aRed)) AS cards FROM (SELECT hometable.matchId, hometable.m_date, hometable.home, hometable.FTHG, hometable.FTAG, awaytable.away, hometable.hRed, hometable.aRed, hometable.hYellow, hometable.aYellow FROM (SELECT matches.matchId, matches.m_date, teams.teamName AS home, matches.FTHG, matches.FTAG, matches.hRed, matches.hYellow, matches.aYellow, matches.aRed FROM matches JOIN teams ON matches.homeTeam = teams.teamId )  hometable JOIN (SELECT matches.matchId, teams.teamName AS away FROM matches JOIN teams ON matches.awayTeam = teams.teamId )  awaytable ON hometable.matchId = awaytable.matchId)  games JOIN user_matches ON games.matchId = user_matches.matchVal WHERE games.matchid = user_game_stats.mostCardsMatch(:defstatid) ";
                if($result7 = oci_parse($conn, $mostCards)){
                    oci_bind_by_name($result7, ':defstatid', $_SESSION['defaultStatsUserId']);
                     oci_execute($result7);
                    $cards = oci_fetch_assoc($result7);

                    echo '<h3 style="font-size:1.5vw; text-decoration:underline; text-align:center; float:right; margin-right:25%; padding-bottom:1%;">Most Cards in a Match</h3>';

                    echo '<div class="matches">';
                    echo '<table>
                            <tr>';
                                echo "<th></th>";
                                echo "<th></th>";
                                echo "<th></th>";
                                echo "<th></th>";
                                echo "<th></th>";
                                echo "<th style='border-bottom:2px solid black; font-size:1vw;'>Red Cards</th>";
                                echo "<th style='border-bottom:2px solid black; font-size:1vw;'>Yellow Cards</th>";
                                echo "<th style='border-right:none; border-bottom:2px solid black; font-size:1vw;'>Total Cards</th>";
                            echo '</tr>';

                            echo '<tr>';
                                echo "<td>" . $cards['M_DATE'] . "</td>";
                                echo "<td>". $cards['HOME'] . "</td>";
                                echo "<td>" . $cards['FTHG'] . "</td>";
                                echo "<td>" . $cards['FTAG'] . "</td>";
                                echo "<td>" . $cards['AWAY'] . "</td>";
                                echo "<td>" . $cards['REDS'] . "</td>";
                                echo "<td>" . $cards['YELLOWS'] . "</td>";
                                echo "<td style='border-right:none'>" . $cards['CARDS'] . "</td>";
                        echo '</tr>
                        </table>';
                    echo '</div>';
                echo '</div>';
            }
            else {
                header("Location: index.php?error=dbError1");
                exit();
            } 
            echo '</div>';  



        ?>
        </div>
    </body>

<?php 
    require "footer.php"
?>