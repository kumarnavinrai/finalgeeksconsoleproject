<?php  
global $user;
$url = $_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
$uri = $_SERVER["REQUEST_URI"];
$host = $_SERVER["HTTP_HOST"];
$host = explode(".",$_SERVER["HTTP_HOST"]);
$host = current($host);
$previous_week = strtotime("-1 week +1 day");

$start_week = strtotime("last sunday midnight",$previous_week);
$end_week = strtotime("next saturday",$start_week);

$start_week = date("Y-m-d",$start_week);
$end_week = date("Y-m-d",$end_week);

//echo $start_week.' '.$end_week ;
$_POST["from"] = $start_week;
$_POST["to"] = $end_week;


/*
  $arr["2016-04-04"][] = array("Version 1"=>9);
                $arr["2016-04-04"][] = array("Version 2"=>5);
                print_r(current($arr["2016-04-04"])); 
                print_r(next($arr["2016-04-04"])); 
                print_r(next($arr["2016-04-04"])); 
                 print_r(next($arr["2016-04-04"])); 
                  print_r(next($arr["2016-04-04"])); 
                   print_r(next($arr["2016-04-04"])); 
                die;
*/

$adminselect = array("admin"=>"WHERE 1","adminone"=>"WHERE user_name LIKE '%..1%'","admintwo"=>"WHERE user_name LIKE '%..2%'","adminthree"=>"WHERE user_name LIKE '%..3%'","adminfour"=>"WHERE user_name LIKE '%..4%'","adminfour"=>"WHERE user_name LIKE '%..4%'","adminfive"=>"WHERE user_name LIKE '%..5%'","adminsix"=>"WHERE user_name LIKE '%..6%'","adminseven"=>"WHERE user_name LIKE '%..7%'","admineight"=>"WHERE user_name LIKE '%..8%'");


$adminselectforjs = array("admin"=>"","adminone"=>"PC..1","admintwo"=>"PC..2","adminthree"=>"PC..3","adminfour"=>"PC..4");


if (in_array('reps', $user->roles) && strpos($uri,"/node/9")) { //print_r($_POST); die;
  $datequery = isset($_POST["from"]) && isset($_POST["to"])? " AND install_date BETWEEN '".$_POST["from"]."' AND '".$_POST["to"]."'":"";
  //$qry = "SELECT * FROM appdata ".$adminselect[$host].$datequery;//." BETWEEN #07/04/1996# AND #07/09/1996#;";
  $qry = "SELECT count(id) as noofinstalls, DATE_FORMAT(install_date,'%Y-%m-%d') as indate, version as version  FROM appdata ".$adminselect[$host].$datequery." GROUP BY version, indate ORDER BY indate, version";
  //SELECT *  FROM `temponline` WHERE `user_name` LIKE '%PC..1%'
  $result = db_query($qry);

  $datequeryone = isset($_POST["from"]) && isset($_POST["to"])? " AND uninstall_date BETWEEN '".$_POST["from"]."' AND '".$_POST["to"]."'":"";
  $qryone = "SELECT count(id) as noofinstalls, DATE_FORMAT(uninstall_date,'%Y-%m-%d') as unindate, version as version  FROM appdata ".$adminselect[$host].$datequeryone." GROUP BY version, unindate ORDER BY unindate, version";
  //SELECT count(id) as noofinstalls, DATE_FORMAT(uninstall_date,'%Y-%m-%d') as unindate, version as version  FROM `appdata` GROUP BY version, unindate
  //SELECT *  FROM `temponline` WHERE `user_name` LIKE '%PC..1%'
  $resultone = db_query($qryone);


//SELECT AVG(age) as avgage, version as version FROM appdata GROUP BY version

 $qrytwo = "SELECT AVG(age) as avgage, version as version FROM appdata ".$adminselect[$host].$datequery." GROUP BY version";
  //SELECT count(id) as noofinstalls, DATE_FORMAT(uninstall_date,'%Y-%m-%d') as unindate, version as version  FROM `appdata` GROUP BY version, unindate
  //SELECT *  FROM `temponline` WHERE `user_name` LIKE '%PC..1%'
  $resulttwo = db_query($qrytwo);
  $_SESSION["perm"]="a";
}

?>
  <?php print render($page['header']); ?>

  <div id="wrapper">
    <div id="container" class="clearfix">

      <div id="header">
        <div id="logo-floater">
        <?php if ($logo || $site_title): ?>
          <?php if ($title): ?>
            <div id="branding"><strong><a href="<?php print $front_page ?>">
            <?php if ($logo): ?>
              <img src="<?php print $logo ?>" alt="<?php print $site_name_and_slogan ?>" title="<?php print $site_name_and_slogan ?>" id="logo" />
            <?php endif; ?>
            <?php print $site_html ?>
            </a></strong></div>
          <?php else: /* Use h1 when the content title is empty */ ?>
            <h1 id="branding"><a href="<?php print $front_page ?>">
            <?php if ($logo): ?>
              <img src="<?php print $logo ?>" alt="<?php print $site_name_and_slogan ?>" title="<?php print $site_name_and_slogan ?>" id="logo" />
            <?php endif; ?>
            <?php print $site_html ?>
            </a></h1>
        <?php endif; ?>
        <?php endif; ?>
        </div>

        <?php if ($primary_nav): print $primary_nav; endif; ?>
        <?php if ($secondary_nav): print $secondary_nav; endif; ?>
      </div> <!-- /#header -->

      <?php if ($page['sidebar_first']): ?>
        <div id="sidebar-first" class="sidebar">
          <?php print render($page['sidebar_first']); ?>
        </div>
      <?php endif; ?>
      
      <div id="center"><div id="squeeze"><div class="right-corner"><div class="left-corner">
          <?php print $breadcrumb; ?>
          <?php if ($page['highlighted']): ?><div id="highlighted"><?php print render($page['highlighted']); ?></div><?php endif; ?>
          <a id="main-content"></a>
          <?php if ($tabs): ?><div id="tabs-wrapper" class="clearfix"><?php endif; ?>
          <?php print render($title_prefix); ?>
          <?php if ($title): ?>
            <h1<?php print $tabs ? ' class="with-tabs"' : '' ?>><?php print $title ?></h1>
          <?php endif; ?>
          <?php print render($title_suffix); ?>
          <?php if ($tabs): ?><?php print render($tabs); ?></div><?php endif; ?>
          <?php print render($tabs2); ?>
          <?php print $messages; ?>
          <?php print render($page['help']); ?>
          <?php if ($action_links): ?><ul class="action-links"><?php print render($action_links); ?></ul><?php endif; ?>
           
          <p>
          <a href="/drup/node/add/messagetoclient" title="messagetoclient">Message to client</a>
          </p>
          <p>
        <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
        <script src="//code.jquery.com/jquery-1.10.2.js"></script>
        <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
 
        <script>
          $(function() {
            $( "#from" ).datepicker({
              defaultDate: "+1w",
              changeMonth: true,
              dateFormat: "yy-mm-dd",
              numberOfMonths: 1,
              onClose: function( selectedDate ) {
                $( "#to" ).datepicker( "option", "minDate", selectedDate );
              }
            });
            $( "#to" ).datepicker({
              defaultDate: "+1w",
              changeMonth: true,
              dateFormat: "yy-mm-dd",
              numberOfMonths: 1,
              onClose: function( selectedDate ) {
                $( "#from" ).datepicker( "option", "maxDate", selectedDate );
              }
            });
          });
        </script>  
        <form id="formrange" class=".clsformrange" method="POST" style="display:none;">
          <label for="from">From</label>
          <input type="text" id="from" name="from">
          <label for="to">to</label>
          <input type="text" id="to" name="to"> 
          <button>Go</button>     
        </form>  
        <h1>Last week instalation stats</h1>
      </p>
      <p>

      

          

          <?php if(isset($resulttwo) && $resulttwo) { ?>
          <h2>Average Install life per version.<?php   echo $message = isset($_POST["from"]) && isset($_POST["to"])? " Showing data From ".$_POST["from"]." To ".$_POST["to"]."":""; 
 ?></h2>
          <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
          <script type="text/javascript">
            google.charts.load('current', {packages: ['corechart']});     
          </script>
          <div id="containerone" style="width: 550px; height: 400px; margin: 0 auto"></div>
         

          <style>
          table, th, td {
              border: 1px solid black;
              border-collapse: collapse;
          }
          th, td {
              padding: 15px;
          }
          </style>
          <h2>Average Install life per version.<?php   echo $message = isset($_POST["from"]) && isset($_POST["to"])? " Showing data From ".$_POST["from"]." To ".$_POST["to"]."":""; 
 ?></h2>
          <div id="custtable">
            <table style="width:100%">
              
              <tr>
                <!--<th>Select</th>-->
                <th>Version</th>
                <th>Average Life</th>
                
               
                
              </tr>
            
            <?php   foreach($resulttwo as $item) {  ?>
              <tr>
                <!--<td><input type="checkbox" class="clientportno" name="clientports" value="<?php //echo $item->port; ?>"></td>-->
                <td><?php echo $item->version; $dataforgraph[] = array("version" => $item->version, "average" => round($item->avgage)); ?></td>
                <td><?php echo round($item->avgage); ?> (Days)</td>
               
               
                 

              </tr>
                        
            <?php   } ?>
            
            </table>  
            </div>
             <script language="JavaScript">
          function drawChart() {
             // Define the chart to be drawn.
             var data = new google.visualization.DataTable();
             data.addColumn('string', 'Version');
             data.addColumn('number', 'Average Life');

             data.addRows([
              <?php   if(isset($dataforgraph)) {  ?>
               <?php   foreach($dataforgraph as $item) {  ?>
                ['Version <?php echo $item["version"]; ?>', <?php echo $item["average"]; ?>],
               <?php   } ?> 
               <?php   } ?> 
               
             ]);
             
             // Set chart options
             var options = {'title':'Average Install life per version',
                'width':550,
                'height':400,
                slices: {  
                   1: {offset: 0.2},
                   3: {offset: 0.3}                  
                }
              };

             // Instantiate and draw the chart.
             var chart = new google.visualization.PieChart(document.getElementById('containerone'));
             chart.draw(data, options);
          }
          google.charts.setOnLoadCallback(drawChart);
          </script>
            <?php } ?>
          
          </p>
        <br>
          <br>
            <hr>
          <br>
          <br>
      <p>
      <h2>Total Install per version per day.<?php   echo $message = isset($_POST["from"]) && isset($_POST["to"])? " Showing data From ".$_POST["from"]." To ".$_POST["to"]."":""; 
 ?></h2>
      <!--<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>-->
      
      <p>
        <div id="curve_chart" style="width: 900px; height: 500px"></div>
      </p>
        <h2>Total Install per version per day.<?php   echo $message = isset($_POST["from"]) && isset($_POST["to"])? " Showing data From ".$_POST["from"]." To ".$_POST["to"]."":""; 
 ?></h2>
          <?php if(isset($result) && $result) { ?>
          <style>
          table, th, td {
              border: 1px solid black;
              border-collapse: collapse;
          }
          th, td {
              padding: 15px;
          }
          </style>
          <div id="custtable">
            <table style="width:100%">
              
              <tr>
                <!--<th>Select</th>-->
                <th>Version</th>
                <th>Install Date</th>
                
                <th>No of Installs</th>
                
              </tr>
            
            <?php   foreach($result as $item) {  ?>
              <tr>
                <!--<td><input type="checkbox" class="clientportno" name="clientports" value="<?php //echo $item->port; ?>"></td>-->
                <td><?php echo $item->version; ?></td>
                <td><?php echo $item->indate; ?></td>
               
                <td><?php echo $item->noofinstalls; ?></td>
                 

              </tr>
              <?php

                $arr[$item->indate][] = array($item->version=>$item->noofinstalls);
                
                
              ?>
                        
            <?php   } ?>
            <!--
                  $arr["2016-04-04"][] = array("Version 1"=>9);
                $arr["2016-04-04"][] = array("Version 2"=>5);
                print_r(current($arr["2016-04-04"])); 
                print_r(next($arr["2016-04-04"])); 
                print_r(next($arr["2016-04-04"])); 
                 print_r(next($arr["2016-04-04"])); 
                  print_r(next($arr["2016-04-04"])); 
                   print_r(next($arr["2016-04-04"])); 
                die;


             -->
            </table>  
            </div>
            <script type="text/javascript">
              //google.charts.load('current', {'packages':['corechart']});
              google.charts.setOnLoadCallback(drawChart);

              function drawChart() {
                var data = google.visualization.arrayToDataTable([
                  ['Dates', 'Version 1', 'Version 2', 'Version 3', 'Version 4', 'Version 5', 'Version 6', 'Version 7', 'Version 8'],
                  <?php if(isset($arr) && $arr){ ?>
                  <?php foreach($arr as $key => $val){ ?>  
                    ['<?php echo $key; ?>', <?php $p = current($val); echo isset($p[1])?$p[1]:0; ?>, <?php $p = next($val); echo isset($p[2])?$p[2]:0; ?>, <?php $p = next($val); echo isset($p[3])?$p[3]:0; ?>, <?php $p = next($val); echo isset($p[4])?$p[4]:0; ?>, <?php $p = next($val); echo isset($p[5])?$p[5]:0; ?>, <?php $p = next($val); echo isset($p[6])?$p[6]:0; ?>, <?php $p = next($val); echo isset($p[7])?$p[7]:0; ?>, <?php $p = next($val); echo isset($p[8])?$p[8]:0; ?>],
                  <?php } ?>  
                  <?php } ?>
                  
                ]);

                var options = {
                  title: 'Total Install per version per day.',
                  curveType: 'function',
                  legend: { position: 'bottom' }
                };

                var chart = new google.visualization.LineChart(document.getElementById('curve_chart'));

                chart.draw(data, options);
              }
            </script>
            <?php } ?>
          
          </p>
        
          <br>
          <br>
            <hr>
          <br>
          <br>
        <p>
        <h2>Total Uninstall per version per day.<?php   echo $message = isset($_POST["from"]) && isset($_POST["to"])? " Showing data From ".$_POST["from"]." To ".$_POST["to"]."":""; 
 ?></h2>
      <!--<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>-->
      
      <p>
        <div id="curve_chart_1" style="width: 900px; height: 500px"></div>
      </p>
          <h2>Total Uninstall per version per day.<?php   echo $message = isset($_POST["from"]) && isset($_POST["to"])? " Showing data From ".$_POST["from"]." To ".$_POST["to"]."":""; 
 ?></h2>

          <?php if(isset($resultone) && $resultone) { ?>
          <style>
          table, th, td {
              border: 1px solid black;
              border-collapse: collapse;
          }
          th, td {
              padding: 15px;
          }
          </style>
          <div id="custtable">
            <table style="width:100%">
              
              <tr>
                <!--<th>Select</th>-->
                <th>Version</th>
                <th>Uninstall Date</th>
                
                <th>No of Uninstalls</th>
                
              </tr>
            
            <?php  $arr = array();  foreach($resultone as $item) {  ?>
              <tr>
                <!--<td><input type="checkbox" class="clientportno" name="clientports" value="<?php //echo $item->port; ?>"></td>-->
                <td><?php echo $item->version; ?></td>
                <td><?php echo $item->unindate; ?></td>
               
                <td><?php echo $item->noofinstalls; ?></td>
                 

              </tr>
              <?php
               
                $arr[$item->unindate][] = array($item->version=>$item->noofinstalls);
                
              ?> 
            <?php   } ?>
            
            </table>  
            </div>
            <script type="text/javascript">
              //google.charts.load('current', {'packages':['corechart']});
              google.charts.setOnLoadCallback(drawChart);

              function drawChart() {
                var data = google.visualization.arrayToDataTable([
                  ['Dates', 'Version 1', 'Version 2', 'Version 3', 'Version 4', 'Version 5', 'Version 6', 'Version 7', 'Version 8'],
                  <?php if(isset($arr) && $arr){ ?>
                  <?php foreach($arr as $key => $val){ ?>  
                    ['<?php echo $key; ?>', <?php $p = current($val); echo isset($p[1])?$p[1]:0; ?>, <?php $p = next($val); echo isset($p[2])?$p[2]:0; ?>, <?php $p = next($val); echo isset($p[3])?$p[3]:0; ?>, <?php $p = next($val); echo isset($p[4])?$p[4]:0; ?>, <?php $p = next($val); echo isset($p[5])?$p[5]:0; ?>, <?php $p = next($val); echo isset($p[6])?$p[6]:0; ?>, <?php $p = next($val); echo isset($p[7])?$p[7]:0; ?>, <?php $p = next($val); echo isset($p[8])?$p[8]:0; ?>],
                  <?php } ?>  
                  <?php } ?>
                  
                ]);

                var options = {
                  title: 'Total Uninstall per version per day.',
                  curveType: 'function',
                  legend: { position: 'bottom' }
                };

                var chart = new google.visualization.LineChart(document.getElementById('curve_chart_1'));

                chart.draw(data, options);
              }
            </script>
            <?php } ?>
          
          </p>
         
          <div class="clearfix">
            <?php //print render($page['content']); ?>
          </div>
          <?php print $feed_icons ?>
          <?php print render($page['footer']); ?>
      </div></div></div></div> <!-- /.left-corner, /.right-corner, /#squeeze, /#center -->

      <?php if ($page['sidebar_second']): ?>
        <div id="sidebar-second" class="sidebar">
          <?php print render($page['sidebar_second']); ?>
        </div>
      <?php endif; ?>

    </div> <!-- /#container -->
  </div> <!-- /#wrapper -->
