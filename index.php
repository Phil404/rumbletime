<!DOCTYPE HTML>
<?php
  $key = "key";
  set_time_limit(100);
  error_reporting(0);
?>
<html>
  <head>
    <meta charset="utf-8">
    <title>IT´S RUUUUMBLETIME!</title>
    <link rel="stylesheet" href="rumble.css">
  </head>
  <body>
    <nav>
      <form method="GET">
        <input type="text" name="username" placeholder="Summoner">
        <select name="region" size="1">
          <option value="euw">EU West</option>
          <option value="na">NA</option>
        </select>
        <input type="submit" value="RUMBLE!" id="submit">
      </form>
    </nav>
    <section>
      <?php
        if($_GET['username'] != ""){
          if($namecheck = json_decode(file_get_contents("https://".$_GET['region'].".api.pvp.net/api/lol/".$_GET['region']."/v1.4/summoner/by-name/".str_replace(' ','',strtolower($_GET['username']))."?api_key=".$key), true)){
            $sumID = $namecheck[str_replace(' ','',strtolower($_GET['username']))]['id'];
            if($gameinfo = json_decode(file_get_contents("https://".$_GET['region'].".api.pvp.net/observer-mode/rest/consumer/getSpectatorGameInfo/".strtoupper($_GET['region'])."1/".$sumID."?api_key=".$key), true)){
          //    print_r($gameinfo['participants'][0]); //for testing
              $map = $gameinfo['mapId'];
              if($gameinfo['gameQueueConfigId'] == "410" or $gameinfo['gameQueueConfigId'] == "400"){
                if($gameinfo['gameQueueConfigId'][0]['teamId'] == "100"){
                  $bans100 = array("0" => $gameinfo['bannedChampions'][0]['championId'], "1" => $gameinfo['bannedChampions'][2]['championId'], "2" => $gameinfo['bannedChampions'][4]['championId']);
                  $bans200 = array("0" => $gameinfo['bannedChampions'][1]['championId'], "1" => $gameinfo['bannedChampions'][3]['championId'], "2" => $gameinfo['bannedChampions'][5]['championId']);
                }else{
                  $bans100 = array("0" => $gameinfo['bannedChampions'][1]['championId'], "1" => $gameinfo['bannedChampions'][3]['championId'], "2" => $gameinfo['bannedChampions'][5]['championId']);
                  $bans200 = array("0" => $gameinfo['bannedChampions'][0]['championId'], "1" => $gameinfo['bannedChampions'][2]['championId'], "2" => $gameinfo['bannedChampions'][4]['championId']);
                }
              }
              //Summonerinfo
              $member = count($gameinfo['participants']);
              while($member != "0"){ $member--; sleep(1);
                $summoner['name'] = $gameinfo['participants'][$member]['summonerName'];
                $summoner['id'] = $gameinfo['participants'][$member]['summonerId'];
                $summoner['spell'] = array("0" => $gameinfo['participants'][$member]['spell1Id'],"1" => $gameinfo['participants'][$member]['spell2Id']);
                $summoner['champion'] = $gameinfo['participants'][$member]['championId'];
                $checkMastery =  json_decode(file_get_contents("https://".$_GET['region'].".api.pvp.net/championmastery/location/".$_GET['region']."1/player/".$gameinfo['participants'][$member]['summonerId']."/champion/".$gameinfo['participants'][$member]['championId']."?api_key=".$key),true);
                if($checkMastery['championPoints'] == "0"){ $summoner['champLv'] = "new";
                }elseif($checkMastery['championLevel'] <= "4"){ $summoner['champLv'] = "beginner";
                }elseif($checkMastery['championLevel'] == "5" AND $checkMastery['championPoints'] <= "50000"){ sleep(0.6);
                  $topMastery = json_decode(file_get_contents("https://".$_GET['region'].".api.pvp.net/championmastery/location/".$_GET['region']."1/player/".$gameinfo['participants'][$member]['summonerId']."/topchampions?count=5&api_key=".$key), true);
                  if($topMastery[0]['championId'] == $checkMastery['championId']){
                    $topRest = $topMastery[1]['championPoints']+$topMastery[2]['championPoints']+$topMastery[3]['championPoints']+$topMastery[4]['championPoints'];
                    if($topRest <= $checkMastery['championPoints']){$summoner['champLv'] = "onetrickpony"; }else{ $summoner['champLv'] = "trained"; }
                  }else{ $summoner['champLv'] = "trained"; }
                }elseif($checkMastery['championLevel'] == "5" AND $checkMastery['championPoints'] >= "50000"){ sleep(0.6);
                  $topMastery = json_decode(file_get_contents("https://".$_GET['region'].".api.pvp.net/championmastery/location/".$_GET['region']."1/player/".$gameinfo['participants'][$member]['summonerId']."/topchampions?count=5&api_key=".$key), true);
                  if($topMastery[0]['championId'] == $checkMastery['championId']){
                    $topRest = $topMastery[1]['championPoints']+$topMastery[2]['championPoints']+$topMastery[3]['championPoints']+$topMastery[4]['championPoints'];
                    if($topRest <= $checkMastery['championPoints']){ $summoner['champLv'] = "onetrickpony"; }else{ $summoner['champLv'] = "expierienced"; }
                  }else{ $summoner['champLv'] = "expierienced"; }
                }else{ $summoner['champLv'] = "unknown"; } sleep(1); $summoner['ranked']['info'] = "404";
                if($rankedStats = json_decode(file_get_contents("https://".$_GET['region'].".api.pvp.net/api/lol/".$_GET['region']."/v1.3/stats/by-summoner/".$summoner['id']."/ranked?season=SEASON2016&api_key=".$key), true)){
                  $num4ranked = count($rankedStats['champions']);
                  while($num4ranked != "0"){ $num4ranked--;
                    if($rankedStats['champions'][$num4ranked]['id'] == $summoner['champion']){ $summoner['ranked']['info'] = "200";
                      $summoner['ranked']['games'] = $rankedStats['champions'][$num4ranked]['stats']['totalSessionsPlayed'];
                      $summoner['ranked']['wins'] = $rankedStats['champions'][$num4ranked]['stats']['totalSessionsWon'];
                      $summoner['ranked']['loses'] = $rankedStats['champions'][$num4ranked]['stats']['totalSessionsLost'];
                      $summoner['ranked']['kills'] = round($rankedStats['champions'][$num4ranked]['stats']['totalChampionKills'] / $summoner['ranked']['games'], 2);
                      $summoner['ranked']['deaths'] = round($rankedStats['champions'][$num4ranked]['stats']['totalDeathsPerSession'] / $summoner['ranked']['games'], 2);
                      $summoner['ranked']['assists'] = round($rankedStats['champions'][$num4ranked]['stats']['totalAssists'] / $summoner['ranked']['games'], 2);
                    }
                  }
                }
                if($gameinfo['participants'][$member]['teamId'] == "100"){ $team100[count($team100)] = $summoner; }else{ $team200[count($team200)] = $summoner; }
              }


              ?>
                <div class="left">
                  <ul>
                    <?php
                      if($gameinfo['gameQueueConfigId'] == "410" or $gameinfo['gameQueueConfigId'] == "400"){ $nBans100 = "3"; echo "<li class='bans'>";
                        while($nBans100 != "0"){ $nBans100--;
                          $champInfo = json_decode(file_get_contents("https://global.api.pvp.net/api/lol/static-data/euw/v1.2/champion/".$bans100[$nBans100]."?champData=enemytips&api_key=".$key),true);
                          echo '<img src='."'http://ddragon.leagueoflegends.com/cdn/6.9.1/img/champion/".$champInfo['key'].".png'>";
                        } echo "</li>";
                      }
                      $total100 = count($team100);
                      while($total100 != "0"){ $total100--;
                        $spell1 = json_decode(file_get_contents("https://global.api.pvp.net/api/lol/static-data/euw/v1.2/summoner-spell/".$team100[$total100]['spell'][0]."?spellData=image&api_key=".$key),true);
                        $spell2 = json_decode(file_get_contents("https://global.api.pvp.net/api/lol/static-data/euw/v1.2/summoner-spell/".$team100[$total100]['spell'][1]."?spellData=image&api_key=".$key),true);
                        $champInfo = json_decode(file_get_contents("https://global.api.pvp.net/api/lol/static-data/euw/v1.2/champion/".$team100[$total100]['champion']."?champData=enemytips&api_key=".$key),true);
                        echo '<li style="background-image:url(http://ddragon.leagueoflegends.com/cdn/img/champion/splash/'.$champInfo['key'].'_0.jpg);" onclick="document.getElementById(`infoscreen`).innerHTML = `<img src='."'http://ddragon.leagueoflegends.com/cdn/6.9.1/img/champion/".$champInfo['key'].".png'>";
                        $tipsNum = count($champInfo['enemytips']); echo '<h2>'.$team100[$total100]['name'].'</h2><h3>'.$team100[$total100]['champLv'].'</h3><ul><li><b>Tips against '.$champInfo['name'].':</b></li>';
                        while($tipsNum != "0"){ $tipsNum--; echo '<li>'.$champInfo['enemytips'][$tipsNum].'</li>'; } echo '</ul>';
                        if($team100[$total100]['ranked']['info'] == "200"){
                          echo '<table><caption>Rankedstats</caption><tr>';
                          echo '<th>Games (total)</th><td>'.$team100[$total100]['ranked']['games'].'</td><th>Wins (total)</th><td>'.$team100[$total100]['ranked']['wins'].'</td>';
                          echo '<th>Loses (total)</th><td>'.$team100[$total100]['ranked']['loses'].'</td></tr><tr>';
                          echo "<th colspan='3'>KDA (average per game)</th><td colspan='3'>".$team100[$total100]['ranked']['kills']."/".$team100[$total100]['ranked']['deaths']."/".$team100[$total100]['ranked']['assists']."</td></tr></table>`;";
                        }else{ echo '<table><caption>Rankedstats</caption><tr><th>no data available.</th></tr></table>`;'; } echo '">';
                        echo '<img src="http://ddragon.leagueoflegends.com/cdn/6.9.1/img/spell/'.$spell1['image']['full'].'"><h1>'.$team100[$total100]['name'].'</h1><br>';
                        echo '<img src="http://ddragon.leagueoflegends.com/cdn/6.9.1/img/spell/'.$spell2['image']['full'].'"><h2>'.$team100[$total100]['champLv'].'</h2></li>';
                      }
                    ?>
                  </ul>
                </div>
                <div class="center">
                  <div id ="infoscreen">
                    <p>pick a player for more information.</p>
                  </div>
                </div>
                <div class="right">
                  <ul>
                    <?php
                      if($gameinfo['gameQueueConfigId'] == "410" or $gameinfo['gameQueueConfigId'] == "400"){ $nBans200 = "3"; echo "<li class='bans'>";
                        while($nBans200 != "0"){ $nBans200--;
                          $champInfo = json_decode(file_get_contents("https://global.api.pvp.net/api/lol/static-data/euw/v1.2/champion/".$bans200[$nBans200]."?champData=enemytips&api_key=".$key),true);
                          echo '<img src='."'http://ddragon.leagueoflegends.com/cdn/6.9.1/img/champion/".$champInfo['key'].".png'>";
                        } echo "</li>";
                      }
                      $total200 = count($team200);
                      while($total200 != "0"){ $total200--;
                        $spell1 = json_decode(file_get_contents("https://global.api.pvp.net/api/lol/static-data/euw/v1.2/summoner-spell/".$team200[$total200]['spell'][0]."?spellData=image&api_key=".$key),true);
                        $spell2 = json_decode(file_get_contents("https://global.api.pvp.net/api/lol/static-data/euw/v1.2/summoner-spell/".$team200[$total200]['spell'][1]."?spellData=image&api_key=".$key),true);
                        $champInfo = json_decode(file_get_contents("https://global.api.pvp.net/api/lol/static-data/euw/v1.2/champion/".$team200[$total200]['champion']."?champData=enemytips&api_key=".$key),true);
                        echo '<li style="background-image:url(http://ddragon.leagueoflegends.com/cdn/img/champion/splash/'.$champInfo['key'].'_0.jpg);" onclick="document.getElementById(`infoscreen`).innerHTML = `<img src='."'http://ddragon.leagueoflegends.com/cdn/6.9.1/img/champion/".$champInfo['key'].".png'>";
                        $tipsNum = count($champInfo['enemytips']); echo '<h2>'.$team200[$total200]['name'].'</h2><h3>'.$team200[$total200]['champLv'].'</h3><ul><li><b>Tips against '.$champInfo['name'].':</b></li>';
                        while($tipsNum != "0"){ $tipsNum--; echo '<li>'.$champInfo['enemytips'][$tipsNum].'</li>'; } echo '</ul>';
                        if($team200[$total200]['ranked']['info'] == "200"){
                          echo '<table><caption>Rankedstats</caption><tr>';
                          echo '<th>Games (total)</th><td>'.$team200[$total200]['ranked']['games'].'</td><th>Wins (total)</th><td>'.$team200[$total200]['ranked']['wins'].'</td>';
                          echo '<th>Loses (total)</th><td>'.$team200[$total200]['ranked']['loses'].'</td></tr><tr>';
                          echo "<th colspan='3'>KDA (average per game)</th><td colspan='3'>".$team200[$total200]['ranked']['kills']."/".$team200[$total200]['ranked']['deaths']."/".$team200[$total200]['ranked']['assists']."</td></tr></table>`;";
                        }else{ echo '<table><caption>Rankedstats</caption><tr><th>no data available.</th></tr></table>`;'; } echo '">';
                        echo '<img src="http://ddragon.leagueoflegends.com/cdn/6.9.1/img/spell/'.$spell1['image']['full'].'"><h1>'.$team200[$total200]['name'].'</h1><br>';
                        echo '<img src="http://ddragon.leagueoflegends.com/cdn/6.9.1/img/spell/'.$spell2['image']['full'].'"><h2>'.$team200[$total200]['champLv'].'</h2></li>';
                      }
                    ?>
                  </ul>
                </div>
              <?php
            }else{ echo '<div class="info">'.$_GET['username'].' is not in a match!</div>'; }
          }else{ echo '<div class="info">Summoner doesn´t exist!</div>'; }
        }else{ echo '<div class="info">Enter your Summonername and rumble!</div>'; }
      ?>
    </section>
    <footer>
      <p>Rumbletime.org isn`t a official site by <b><a target="_blank" href="http://www.riotgames.com/">Riot Games</a></b> and doesn´t represent Riot Games.<br>
  		  <b><a target="_blank" href="http://euw.leagueoflegends.com/">League of Legends</a></b> and Riot Games are trademarks or registered trademarks of Riot Games, Inc. League of Legends © Riot Games, Inc.</p>
    </footer>
  </body>
</html>
