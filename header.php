<?php
    session_start();
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset='utf-8'>
        <meta name='viewport' content="width=device-width, initial-scale=1.0"/>
        
        <link rel="stylesheet" href="style/style.css?version=177" type="text/css">
        
        <link rel="shortcut icon" type="image/png" href="pictures/favicon.png"/>
        <link href="https://fonts.googleapis.com/css?family=Roboto&display=swap" rel="stylesheet">
        
    </head>
    <body>
        <header>
        
            <a href='index.php' style="float:left; width:4.7%;text-decoration: none; color:white; font-size: 1.3vw; font-family: Impact, sans-serif; border-right: 1px solid black">
                <img src="pictures/house2.png" alt='home' width=93% height=93% style="float:left" title="Home">&nbsp&nbsp&#8239Home 
            </a>
        
            <h1 style="color: white">EPL-App</h1>
            <div class='header-buttons'>
                <?php
                    if(isset($_SESSION['userId'])){
                        echo '<form action="includes/logout.php" method="post">
                        <button type="submit" name="logout-submit" title="Logout" style="color:white">Logout</button>
                        </form>';
                        echo '</div>';
                        
                        echo '<a href="profile.php" style="float:right; width:4.7%;text-decoration: none; color:white; font-size: 1.3vw; font-family: Impact, sans-serif; border-right: 1px solid black">';
                                echo '<img src="pictures/profile2.png" alt="home" width=85% height=85% title="My Profile">&nbspProfile 
                                </a>';

                        echo '<a href="myStats.php" style="float:right; width:4.7%;text-decoration: none; color:white; font-size: 1.2vw; font-family: Impact, sans-serif; margin-right:0.5%; border-right: 1px solid black">';
                        echo '<img src="pictures/stats2.png" alt="home" width=85% height=85% title="My Stats">&nbsp&nbspStats 
                        </a>';

                        echo '<a href="myMatches.php" style="float:right; width:4.7%;text-decoration: none; color:white; font-size: 1.2vw; font-family: Impact, sans-serif; margin-right:1%; border-right: 1px solid black">';
                        echo '<img src="pictures/football2.png" alt="home" width=85% height=85% title="My Matches">Matches 
                        </a>';
                            
                        echo '<a href="index.php?success=search" style="float:right; width:4.7%;text-decoration: none; color:white; font-size: 1.2vw; font-family: Impact, sans-serif; margin-right:1%; border-right: 1px solid black">';
                        echo '<img src="pictures/search2.png" alt="home" width=85% height=85% title="Search">&nbspSearch 
                        </a>';
                    }
                    else{
                        echo '<a href="signup.php" style="float:right; width:4.7%;text-decoration: none; color:white; font-size: 1.2vw; font-family: Impact, sans-serif;margin-right: 0.5%;margin-left: 0.5%;">
                        <img src="pictures/register2.png" alt="home" width=85% height=85% title="Sign Up">&nbsp&nbspSign Up</a>
                        </div>';
                    }
                ?>
            <div class='header-login'>
                <?php
                    if(isset($_SESSION['userId'])){
                        
                    }
                    else{
                        echo '<form action="includes/login.php" method="post">
                        <input type="text" name="mailuid" placeholder="email...">
                        <input type="password" name="pwd" placeholder="password...">
                        <button type="submit" name="login-submit">Login</button>
                        </form>';
                    }
                ?>
            </div>
        </header>
    </body> 
<html>
    
