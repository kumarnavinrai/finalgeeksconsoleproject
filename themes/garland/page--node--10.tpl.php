<?php  
global $user;
$url = $_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
$uri = $_SERVER["REQUEST_URI"];
$host = $_SERVER["HTTP_HOST"];
$hostoriginal = $host;
$host = explode(".",$_SERVER["HTTP_HOST"]);
$checkhost = $host;
$host = current($host);
$hostforparentchild = next($checkhost);//'children';'parent';//next($checkhost);
$parentchildquery = "";
if($hostforparentchild == 'parent'){
  $parentchildquery = " AND type = 1";
}elseif ($hostforparentchild == 'children') {
  $parentchildquery = " AND type = 2";
}

//echo $start_week.' '.$end_week ;
$_POST["from"] = date("Y-m-01");
$_POST["to"] = date("Y-m-d");
$_POST["to"] = $_POST["to"]." 23:59:59";
//echo $date = date("Y-m-01").' To '.date("Y-m-d"); die;
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
$fullurlforpagination =  $hostoriginal.$uri; 
$fullurlforpagination =  reset(explode("?",$fullurlforpagination));


$adminselectforgraph = array("admin"=>"['Dates', 'Version 0','Version 1', 'Version 2', 'Version 3', 'Version 4', 'Version 5', 'Version 6', 'Version 7', 'Version 8'],",
                             "adminone"=>"['Dates', 'Version 1'],",
                             "admintwo"=>"['Dates', 'Version 2'],",
                             "adminthree"=>"['Dates', 'Version 3'],",
                             "adminfour"=>"['Dates', 'Version 4'],",
                             "adminfive"=>"['Dates', 'Version 5'],",
                             "adminsix"=>"['Dates', 'Version 6'],",
                             "adminseven"=>"['Dates', 'Version 7'],",
                             "admineight"=>"['Dates', 'Version 8'],"
                             );

$adminselectforgraphsecondstring = array("admin"=>array(0,1,2,3,4,5,6,7,8,9),
                             "adminone"=>array(1),
                             "admintwo"=>array(2),
                             "adminthree"=>array(3),
                             "adminfour"=>array(4),
                             "adminfive"=>array(5),
                             "adminsix"=>array(6),
                             "adminseven"=>array(7),
                             "admineight"=>array(8)
                             );


if (in_array('reps', $user->roles) && strpos($uri,"/node/10")) { //print_r($_POST); die;
  $datequery = isset($_POST["from"]) && isset($_POST["to"])? " AND install_date BETWEEN '".$_POST["from"]."' AND '".$_POST["to"]."'":"";
  //$qry = "SELECT * FROM appdata ".$adminselect[$host].$datequery;//." BETWEEN #07/04/1996# AND #07/09/1996#;";
  //$qry = "SELECT count(id) as noofinstalls, DATE_FORMAT(install_date,'%Y-%m-%d') as indate, version as version  FROM appdata ".$adminselect[$host].$datequery." GROUP BY version, indate ORDER BY indate, version";
  $qry = "SELECT count(id) as noofinstalls, DATE_FORMAT(install_date,'%Y-%m-%d') as indate, version as version,type as type  FROM appdata ".$adminselect[$host].$datequery." GROUP BY version, type, indate ORDER BY indate, version";
  //SELECT *  FROM `temponline` WHERE `user_name` LIKE '%PC..1%'
  $result = db_query($qry);

  $datequeryone = isset($_POST["from"]) && isset($_POST["to"])? " AND uninstall_date BETWEEN '".$_POST["from"]."' AND '".$_POST["to"]."'":"";
  $uninstalladminselecthost = str_replace('WHERE', "WHERE uninstall_date != '' AND ", $adminselect[$host]);
  $qryone = "SELECT count(id) as noofinstalls, DATE_FORMAT(uninstall_date,'%Y-%m-%d') as unindate, version as version  FROM appdata ".$uninstalladminselecthost.$datequeryone." GROUP BY version, unindate ORDER BY unindate, version";
  //SELECT count(id) as noofinstalls, DATE_FORMAT(uninstall_date,'%Y-%m-%d') as unindate, version as version  FROM `appdata` GROUP BY version, unindate
  //SELECT *  FROM `temponline` WHERE `user_name` LIKE '%PC..1%'
  $resultone = db_query($qryone);


//SELECT AVG(age) as avgage, version as version FROM appdata GROUP BY version

 $qrytwo = "SELECT AVG(age) as avgage, version as version FROM appdata ".$adminselect[$host].$datequery." AND uninstall_date != '' GROUP BY version";
  //SELECT count(id) as noofinstalls, DATE_FORMAT(uninstall_date,'%Y-%m-%d') as unindate, version as version  FROM `appdata` GROUP BY version, unindate
  //SELECT *  FROM `temponline` WHERE `user_name` LIKE '%PC..1%'
  $resulttwo = db_query($qrytwo);
  $_SESSION["perm"]="a";
  if(isset($_REQUEST['exporttocsv'])){
    $qrydetailsofinstallcsv = "SELECT source,install_date,user_name,ip FROM appdata ".$adminselect[$host].$datequery." ".$parentchildquery;
    
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=data.csv');
 
    // create a file pointer connected to the output stream
    $output = fopen('php://output', 'w');
 
    // output the column headings
    fputcsv($output, array('Country', 'Install Date', 'Pc Name', 'IP Address'));
 
    // fetch the data
    $rows = db_query($qrydetailsofinstallcsv);
 
    // loop over the rows, outputting them
    foreach ($rows as $key => $value) {
       
     $row = array('"'.$value->source.'"','"'.$value->install_date.'"','"'.$value->user_name.'"','"'.$value->ip.'"'); 
    
     fputcsv($output, $row);
    }
    die;
  }
}

$_POST["to"] = date("Y-m-d");
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
          <?php //print $messages; ?>
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
          <input type="text" id="from" name="from" value="<?php echo $_POST["from"]; ?>" >
          <label for="to">to</label>
          <input type="text" id="to" name="to" value="<?php echo $_POST["to"]; ?>" > 
          <button>Go</button>     
        </form>  
        <h1>This Month's Installation Stats</h1>
        <br/>
      
      </p>
      <p>

      

          

          <?php if(isset($resulttwo) && $resulttwo) { ?>
          <h2 style="display:none;">Average Install life per version.<?php   echo $message = isset($_POST["from"]) && isset($_POST["to"])? " Showing data From ".$_POST["from"]." To ".$_POST["to"]."":""; 
 ?></h2>
          <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
          <script type="text/javascript">
            google.charts.load('current', {packages: ['corechart']});     
          </script>
          <div id="containerone" style="width: 550px; height: 400px; margin: 0 auto;display:none;"></div>
         

          <style>
          table, th, td {
              border: 1px solid black;
              border-collapse: collapse;
          }
          th, td {
              padding: 15px;
          }
          </style>
          <h2 style="display:none;">Average Install life per version.<?php   echo $message = isset($_POST["from"]) && isset($_POST["to"])? " Showing data From ".$_POST["from"]." To ".$_POST["to"]."":""; 
 ?></h2>
          <div id="custtable" style="display:none;">
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
            <hr style="display:none;">
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
          <div id="custtable" class="noofinstalltbl">
             <table style="width:100%">
              
              <tr>
                <!--<th>Select</th>-->
                 <th>Version</th>
                  <th>Type</th>
                  <th>Install Date</th>
                  <th>No of Installs</th>
                
              </tr>
            
            <?php   foreach($result as $item) {  ?>
              <tr>
                <td><?php echo $item->version; ?></td>
                <td><?php echo $item->type==1?"Parent":"Child"; ?></td>
                <td><?php echo $item->indate; ?></td>
                <td><?php echo $item->noofinstalls; ?></td>
              </tr>
              <?php
                if($item->type==1){
                  if($item->version && in_array($item->version, $adminselectforgraphsecondstring[$host]))
                  $arr[$item->indate][$item->version] = $item->noofinstalls;
                }  
                
                
              ?>
                        
            <?php   } ?>
           
            </table> 
            </div>
            <script type="text/javascript">
              //google.charts.load('current', {'packages':['corechart']});
              google.charts.setOnLoadCallback(drawChart);

              function drawChart() {
                var data = google.visualization.arrayToDataTable([
                  <?php echo $adminselectforgraph[$host]; ?>
                  <?php if(isset($arr) && $arr){ ?>
                  <?php foreach($arr as $key => $val){  ?>  
                    ['<?php echo $key; ?>', <?php $p = $val; ?> <?php if(in_array(0, $adminselectforgraphsecondstring[$host])){ ?><?php  echo isset($p[0])?$p[0]:0; ?>,<?php } ?> <?php if(in_array(1, $adminselectforgraphsecondstring[$host])){ ?><?php echo isset($p[1])?$p[1]:0; ?>,<?php } ?> <?php if(in_array(2, $adminselectforgraphsecondstring[$host])){ ?><?php echo isset($p[2])?$p[2]:0; ?>,<?php } ?> <?php if(in_array(3, $adminselectforgraphsecondstring[$host])){ ?><?php echo isset($p[3])?$p[3]:0; ?>,<?php } ?> <?php if(in_array(4, $adminselectforgraphsecondstring[$host])){ ?><?php echo isset($p[4])?$p[4]:0; ?>,<?php } ?> <?php if(in_array(5, $adminselectforgraphsecondstring[$host])){ ?><?php echo isset($p[5])?$p[5]:0; ?>,<?php } ?> <?php if(in_array(6, $adminselectforgraphsecondstring[$host])){ ?><?php echo isset($p[6])?$p[6]:0; ?>,<?php } ?> <?php if(in_array(7, $adminselectforgraphsecondstring[$host])){ ?><?php echo isset($p[7])?$p[7]:0; ?>,<?php } ?> <?php if(in_array(8, $adminselectforgraphsecondstring[$host])){ ?><?php echo isset($p[8])?$p[8]:0; ?><?php } ?>],
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
        <a href="http://<?php echo $fullurlforpagination; ?>?exporttocsv=1" title="messagetoclient">Export to csv</a>
          
          </p>
          <h1>Details of instalations</h1>
          <br>
          <br>
            <?php
  $qrydetailsofinstall = "SELECT * FROM appdata ".$adminselect[$host].$datequery." ".$parentchildquery;
  $qrydetailsofinstallcount = "SELECT count(id) as cid FROM appdata ".$adminselect[$host].$datequery." ".$parentchildquery;
 
  /* Get total number of records */
         $rec_limit = 10;
         $sql = $qrydetailsofinstallcount;
         $retval = db_query( $sql );
         
         //if there is any count of record then do anything else
         if( $retval ) 
         {
          
           foreach($retval as $item) {
              
              $rec_count = $item->cid;
           }
          
           
           if( isset($_GET{'page'} ) ) {
              $page = $_GET{'page'} + 1;
              $offset = $rec_limit * $page ;
           }else {
              $page = 0;
              $offset = 0;
           }
           
           $left_rec = $rec_count - ($page * $rec_limit);
           $sql = $qrydetailsofinstall." LIMIT $offset, $rec_limit";
              
           $retval = db_query( $sql );
            echo '<div id="dcustid">';
            echo '<table style="width:100%">';
            echo '<tr>';
                echo '<th>Country</th>';
                echo '<th>Install Date</th>';
                echo '<th>PC Name</th>';
                echo '<th>IP Address</th>';
            echo '</tr>';
           
           foreach($retval as $item) {
              
            echo '<tr>';
            echo '<td>'.$item->source.'</td>';
            echo '<td>'.$item->install_date.'</td>';
            echo '<td>'.$item->user_name.'</td>';
            echo '<td>'.$item->ip.'</td>';
            echo '</tr>';
                         
            
           }
           echo '</table>';
           
 
           $total_pages = ceil($rec_count / $rec_limit);
           if( $page > 0 ) {
              $last = $page - 2;
              echo '<a href = "http://'.$fullurlforpagination.'?page='.$last.'">Last 10 Records</a> |';
              if($total_pages-1 > $page)
              {
                echo '<a href = "http://'.$fullurlforpagination.'?page='.$page.'">Next 10 Records</a>';
              }  
           }else if( $page == 0 ) {
              echo '<a href = "http://'.$fullurlforpagination.'?page='.$page.'">Next 10 Records</a>';
           }else if( $left_rec < $rec_limit ) {
              $last = $page - 2;
              echo '<a href = "http://'.$fullurlforpagination.'?page='.$last.'">Last 10 Records</a>';
           }
           echo '</div>';
       }//if count check if ends

 
?>
          <br>
          <br>
 
 
        <p>
        <h2 style="display:none;">Total Uninstall per version per day.<?php   echo $message = isset($_POST["from"]) && isset($_POST["to"])? " Showing data From ".$_POST["from"]." To ".$_POST["to"]."":""; 
 ?></h2>
      <!--<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>-->
      
      <p>
        <div id="curve_chart_1" style="width: 900px; height: 500px;display:none;"></div>
      </p>
          <h2 style="display:none;">Total Uninstall per version per day.<?php   echo $message = isset($_POST["from"]) && isset($_POST["to"])? " Showing data From ".$_POST["from"]." To ".$_POST["to"]."":""; 
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
          <div id="custtable" style="display:none;">
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
               
                $arr[$item->unindate][$item->version] = $item->noofinstalls;
                
              ?> 
            <?php   } ?>
            
            </table>  
            </div>
            <script type="text/javascript">
              //google.charts.load('current', {'packages':['corechart']});
              google.charts.setOnLoadCallback(drawChart);

              function drawChart() {
                var data = google.visualization.arrayToDataTable([
                  ['Dates', 'Version 0','Version 1', 'Version 2', 'Version 3', 'Version 4', 'Version 5', 'Version 6', 'Version 7', 'Version 8'],
                  <?php if(isset($arr) && $arr){ ?>
                  <?php foreach($arr as $key => $val){ ?>  
                    ['<?php echo $key; ?>', <?php $p = $val; echo isset($p[0])?$p[0]:0; ?>, <?php echo isset($p[1])?$p[1]:0; ?>, <?php echo isset($p[2])?$p[2]:0; ?>, <?php echo isset($p[3])?$p[3]:0; ?>, <?php echo isset($p[4])?$p[4]:0; ?>, <?php echo isset($p[5])?$p[5]:0; ?>, <?php echo isset($p[6])?$p[6]:0; ?>, <?php echo isset($p[7])?$p[7]:0; ?>, <?php echo isset($p[8])?$p[8]:0; ?>],
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
  <script type="text/javascript">
    $(document).ready(function(){
        setTimeout(function(){
          
          var table = $('.noofinstalltbl');
          var $tdsl;
          var versionl;
          var typel;
          var idatel;
              
          table.find('tr').each(function (i) {
           

              var $tds = $(this).find('td'),
                  version = $tds.eq(0).text(),
                  type = $tds.eq(1).text(),
                  idate = $tds.eq(2).text();

              if(i > 1)
              {
                console.log("datenow--"+idate);
                console.log("dateprev--"+idatel);
                if(idate == idatel && versionl == version && typel == 'Child' && type == 'Parent')
                {
                  var currentrowhtml = $(this).html();
                  var lastrowindex = i-1;
                  var lastrow = table.find('tr').eq(lastrowindex);
                  var lastrowhtml = lastrow.html();
                  console.log("lastrowhtml--"+lastrowhtml);
                  console.log("currentrowhtml--"+currentrowhtml);
                  $(this).html(lastrowhtml);
                  lastrow.html(currentrowhtml);
                }
              }    

                  $tdsl = $(this).find('td');
                  versionl = $tdsl.eq(0).text();
                  typel = $tdsl.eq(1).text();
                  idatel = $tdsl.eq(2).text();
               

              
          });
        }, 1000);

         setTimeout(function(){
          
          var table = $('.noofinstalltbl');
          var porc = "<?php echo $hostforparentchild; ?>";
          var tocheckporc;
          if(porc.toLowerCase() == 'parent'){
             tocheckporc = 'child'; 
          }
          if(porc.toLowerCase() == 'children'){
             tocheckporc = 'parent'; 
          }
          table.find('tr').each(function (i) {
                  var $tds = $(this).find('td'),
                  version = $tds.eq(0).text(),
                  type = $tds.eq(1).text(),
                  idate = $tds.eq(2).text();
                  console.log(type+"---"+version+"---"+idate);
                  if(type.toLowerCase() == tocheckporc){
                    $(this).css("display", "none");
                  }  
              });
        },1000);
         
    });
  </script>