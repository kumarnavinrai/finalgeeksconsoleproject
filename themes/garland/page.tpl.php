<?php
global $user;
$url = $_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
$uri = $_SERVER["REQUEST_URI"];
$host = $_SERVER["HTTP_HOST"];
$hostfull = $host;
$hostfornode = "http://admin.pc-optimiser.com:8000";
$host = explode(".",$_SERVER["HTTP_HOST"]);
$host = current($host);
if(isset($_POST['order_by'])){
  $orderby = $_POST['order_by'];
}
if (in_array('reps', $user->roles) && !strpos($uri,"/node/add/messagetoclient")) {
   //echo "<pre>"; print_r($_SERVER); //die;
   //[HTTP_HOST] => admin.pc-optimiser.com
   //[REQUEST_URI] => /node/add/messagetoclient
   //header("Location: http://".$host.".pc-optimiser.com/node/add/messagetoclient");
   header("Location: http://".$hostfull."/drup/node/add/messagetoclient");
   die();
}

$adminselect = array("admin"=>"WHERE status = 1 ","adminone"=>"WHERE status = 1 AND user_name LIKE '%..1%'","admintwo"=>"WHERE status = 1 AND  user_name LIKE '%..2%'","adminthree"=>"WHERE status = 1 AND user_name LIKE '%..3%'","adminfour"=>"WHERE status = 1 AND user_name LIKE '%..4%'","adminfive"=>"WHERE status = 1 AND user_name LIKE '%..5%'","adminsix"=>"WHERE status = 1 AND user_name LIKE '%..6%'","adminseven"=>"WHERE status = 1 AND user_name LIKE '%..7%'","admineight"=>"WHERE status = 1 AND user_name LIKE '%..8%'");


$adminselectforjs = array("admin"=>"","adminone"=>"PC..1","admintwo"=>"PC..2","adminthree"=>"PC..3","adminfour"=>"PC..4");

//SELECT * FROM temponline LEFT JOIN appdata ON temponline.ip = appdata.ip WHERE status = 1 AND version = 1

if (in_array('reps', $user->roles) && strpos($uri,"/node/add/messagetoclient")) {
  $qry = "SELECT * FROM temponline LEFT JOIN appdata ON temponline.instance_id=appdata.instance_id ".$adminselect[$host];
  if(isset($orderby) && $orderby){
    //ORDER BY column_name ASC
    $qry .= " ORDER BY ".$orderby." ASC";
  }
  //SELECT *  FROM `temponline` WHERE `user_name` LIKE '%PC..1%'
  $result = db_query($qry);
  $_SESSION["perm"]="a";
  $ignorearray = array();
  $qryignore = "SELECT * FROM ignorelist";
  $resultignore = db_query($qryignore);
  if($resultignore){
    foreach($resultignore as $kignore => $itemignore)  
    {
      $ignorearray[] = $itemignore->instance_id;              
    }
    
  }
  /*foreach($result as $k => $item) 
              {
  echo "<pre>"; print_r($item);              
              }
  die;*/
}

?>
  <?php print render($page['header']); ?>

  <div id="wrapper"><?php //print_r($_SESSION); echo " sdfsdfsdfsf"; ?>
  
        <time></time>
  
    <!--    <div id="container">Loading ...</div>-->
    <!--<script src="socket.io/socket.io.js"></script>-->
    <script src="/drup/themes/garland/node_modules/socket.io/node_modules/socket.io-client/socket.io.js"></script>

    <script src="http://code.jquery.com/jquery-latest.min.js"></script>
    <script>

        // create a new websocket
        var socket = io.connect('<?php echo $hostfornode; ?>');
        // on message received we print all the data inside the #container div
        socket.on('notification', function (data) { 
        var usersList = '<table style="width:100%"><tr><th>Select</th><th>PC Name</th><th>IP</th><th>Port</th><th>Country</th></tr>';
        var checkboxstatearray = []; 
        $('.clientportno').each(function(i, obj) {
            if($(this).is(":checked")){
                checkboxstatearray[$(this).val()]="checked='checked'";
            }else{
                checkboxstatearray[$(this).val()]="";
            }
        });
         //console.log(checkboxstatearray); 
        //var usersList = "<dl>";
        $.each(data.users,function(index,user){ //console.log(index); console.log(user);
            /*usersList += "<dt>" + user.user_name + "</dt>\n" +
                         "<dd>" + user.ip + "\n" +
                         "<dd>" + user.port + "\n" +
                         "</dd>";*/
            var str = user.user_name;             
            var n = str.indexOf("<?php echo $adminselectforjs[$host]; ?>");
            var m = str.indexOf("<?php echo strtolower($adminselectforjs[$host]); ?>");             
           
                if(n != -1 || m != -1){
                  usersList +=  '<tr>';
                  usersList +=  '<td><input type="checkbox" class="clientportno" name="clientports" value="' + user.port + '" ' + checkboxstatearray[user.port] + ' ></td>';
                  usersList +=  '<td>'+ user.user_name + '</td>';
                  usersList +=  '<td>' + user.ip + '</td>';
                  usersList +=  '<td>' + user.port + '</td>';
                  usersList +=  '<td>' + user.source + '</td>';
                  usersList +=  '</tr>';
                }
            
            

        });
        //usersList += "</dl>";
        usersList +=  '</table>';
        //$('#container').html(usersList);
        var oldhtml = $('#custtable').html(); 
        var newhtml = usersList;
        console.log(oldhtml.length); 
        console.log(newhtml.length);
        var diff =  newhtml.length - oldhtml.length;
        console.log(diff);
        if(diff != -15){  $('#custtable').html(usersList); }

        //$('time').html('Last Update:' + data.time);
      });
    </script>
  
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
          <?php if(isset($_SESSION["message"]) && $_SESSION["message"] != ""){ ?>
          <div class="messages status">
            <h2 class="element-invisible">Client</h2>
              Client <em class="placeholder">messages</em> has been sent.</div>
          <?php $_SESSION["message"] = ""; ?>
          <?php } ?>
          <?php /* if(isset($_SESSION["perm"]) && $_SESSION["perm"] != ""){ ?>
          <div class="messages error">
          <h2 class="element-invisible">User gets offline/online quickly.</h2>
          User gets offline/online quickly. So please referesh before sending message.</div> 
          <?php $_SESSION["perm"] = ""; ?>
          <?php } */?>      

          
          <?php print $messages; ?>
          <?php print render($page['help']); ?>
          <?php if ($action_links): ?><ul class="action-links"><?php print render($action_links); ?></ul><?php endif; ?>
          <p>
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
          <p>
          <input type="checkbox" id="checkAll"/> Select All&nbsp;<input type="checkbox" id="checkAll100"/> Select First 100&nbsp;
          || <a href="/drup/node/5" title="Installation Stats">All Stats</a>
          || <a href="/drup/node/6" title="Installation Stats">Today Stats</a>
          || <a href="/drup/node/7" title="Installation Stats">Yesterday Stats</a>
          || <a href="/drup/node/8" title="Installation Stats">This Week Stats</a>
          || <a href="/drup/node/9" title="Installation Stats">Last Week Stats</a>
          || <a href="/drup/node/10" title="Installation Stats">This Month Stats</a>
          || <a href="/drup/node/11" title="Installation Stats">Month Before Stats</a>
          </p>
          <p>
            <h2>No of Today's Installations</h2>
            <?php
              $_POST["from"] = date("Y-m-d",strtotime("-1 days"));
              $_POST["to"] = date("Y-m-d H:i:s");


              $adminselect = array("admin"=>"WHERE 1","adminone"=>"WHERE user_name LIKE '%..1%'","admintwo"=>"WHERE user_name LIKE '%..2%'","adminthree"=>"WHERE user_name LIKE '%..3%'","adminfour"=>"WHERE user_name LIKE '%..4%'","adminfour"=>"WHERE user_name LIKE '%..4%'","adminfive"=>"WHERE user_name LIKE '%..5%'","adminsix"=>"WHERE user_name LIKE '%..6%'","adminseven"=>"WHERE user_name LIKE '%..7%'","admineight"=>"WHERE user_name LIKE '%..8%'");
              $adminselectforjs = array("admin"=>"","adminone"=>"PC..1","admintwo"=>"PC..2","adminthree"=>"PC..3","adminfour"=>"PC..4");


              if (in_array('reps', $user->roles) && strpos($uri,"/node/add/messagetoclient")) { //print_r($_POST); die;
                $datequery = isset($_POST["from"]) && isset($_POST["to"])? " AND install_date BETWEEN '".$_POST["from"]."' AND '".$_POST["to"]."'":"";
                //$qry = "SELECT * FROM appdata ".$adminselect[$host].$datequery;//." BETWEEN #07/04/1996# AND #07/09/1996#;";
                $qry = "SELECT count(id) as noofinstalls, DATE_FORMAT(install_date,'%Y-%m-%d') as indate, version as version  FROM appdata ".$adminselect[$host].$datequery." GROUP BY version, indate ORDER BY indate, version";
                //SELECT *  FROM `temponline` WHERE `user_name` LIKE '%PC..1%'
                $resulttotal = db_query($qry);

                $_SESSION["perm"]="a";
              }

            ?>
              <h2>Total Install per version per day.<?php   echo $message = isset($_POST["from"]) && isset($_POST["to"])? " Showing data From ".$_POST["from"]." To ".$_POST["to"]."":""; 
             ?></h2>
                      <?php if(isset($resulttotal) && $resulttotal) { ?>
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
                        
                        <?php   foreach($resulttotal as $item) {  ?>
                          <tr>
                            <!--<td><input type="checkbox" class="clientportno" name="clientports" value="<?php //echo $item->port; ?>"></td>-->
                            <td><?php echo $item->version; ?></td>
                            <td><?php echo $item->indate; ?></td>
                           
                            <td><?php echo $item->noofinstalls; ?></td>
                             

                          </tr>
                         
                                    
                        <?php   } ?>
                 
                        </table>  
                        </div>
                        <?php   } ?>
                    
          </p>
          <div id="custtable">
            <?php if($result){ ?>
            <table style="width:100%">
              
              <tr>
                <th>Select Both</th>
                <th>Select</th>
                <th>Ignore</th>
                <th>PC Name Parent</th>
                <th>Select</th>
                <th>PC Name Child</th>
                <th>Port child</th>
                <th>IP</th>
                <th>Port parent</th>
                <th class="sortby sourcecss" data="temponline.source">Country</th>
                <th class="sortby install_datecss" data="install_date">Install Date</th>
              </tr>
            <?php 
              //change array here 
              $arrayofchilds = array();
              $arrayofparents = array();
              foreach($result as $k => $item) 
              { 
                
                $version = substr($item->user_name, strpos($item->user_name, '..')+2, 1);
                $portno = $item->port;
                $ignored = 0;
                if(in_array($item->instance_id, $ignorearray)){ $portno = $portno."i"; $ignored = 1;}

                if($item->type == 1)
                {  

                  $arrayofparents[$k]=array(
                                          'id'=>$item->id,
                                          'user_name'=>$item->user_name,
                                          'port'=>$portno,
                                          'ignored'=>$ignored, 
                                          'ip'=>$item->ip,
                                          'source'=>$item->source,
                                          'type'=>$item->type,
                                          'version'=>$version,
                                          'instance_id' =>$item->instance_id,
                                          'install_date' => date('Y-m-d',strtotime($item->install_date)) 

                                          );
                }
                elseif($item->type == 2)
                {
                   $arrayofchilds[$k]=array(
                                          'id'=>$item->id,
                                          'user_name'=>$item->user_name,
                                          'port'=>$portno,
                                          'ignored'=>$ignored,
                                          'ip'=>$item->ip,
                                          'source'=>$item->source,
                                          'instance_id' =>$item->instance_id,
                                          'type'=>$item->type,
                                          'version'=>$version  

                                          );
                }
                  

              }

              if($arrayofchilds)
              {  
                foreach($arrayofchilds as $key => $val) 
                { 
                  
                  $valtosearch = $val['user_name']." Parent";
                  $found = array_search($valtosearch, array_map(function($data) {return $data['user_name'];}, $arrayofparents));
                  if(!$found)
                  {
                    $arrayofparents[$key] = array(
                                          'id'=>'',
                                          'user_name'=>'',
                                          'port'=>'',
                                          'ip'=>$val['ip'],
                                          'source'=>$val['source'],
                                          'type'=>'',
                                          'version'=>'',
                                          'child'=>  $val

                                          );
                  }
                  elseif($found && $arrayofparents[$found]['ip'] == $val['ip'])
                  {
                    $arrayofparents[$found]['child'] =  $val;
                  }
                  
                }
                ksort($arrayofparents);
              }  
              
              //echo "<pre>"; print_r($arrayofparents); die;
               
            ?>  
            
            <?php   foreach($arrayofparents as $item) { ?>
              <tr>
                <td><a href="#" class="selboth">Both</a></td>
                <td><a href="#" class="ignore">Ignore</a></td>
                
                <?php if($item['port']){ ?>
                <td><input type="checkbox" class="clientportno" name="clientports" data-instance-id="<?php echo $item['instance_id']; ?>" data="<?php echo $item['instance_id']; ?>" value="<?php echo $item['port']; ?>"  <?php if($item['ignored']){ echo "disabled"; } ?> ></td>
                <td><?php echo $item['user_name']; ?></td>
                <?php }elseif(!$item['port']){  ?>
                  <td><input type="checkbox" class="clientportno" name="clientportsoffmode"  value="" style="display:none;" ></td>  
                  <td></td>  
                <?php } ?>
                <?php if(isset($item['child'])){ ?>
                <td><input type="checkbox" class="clientportnochild" name="clientportschild" data-instance-id="<?php echo $item['child']['instance_id']; ?>" data="<?php echo $item['child']['instance_id']; ?>" value="<?php echo $item['child']['port']; ?>" <?php if($item['child']['ignored']){ echo "disabled"; } ?> ></td>
                <td><?php echo $item['child']['user_name']; ?></td>
                <td><?php echo $item['child']['port']; ?></td>
                <?php }elseif(!isset($item['child'])){ ?>
                 <td></td>
                <td></td> 
                <td></td>   
                <?php } ?>  
                <td><?php echo $item['ip']; ?></td>
                <td><?php echo $item['port']; ?></td>
                <td><?php echo $item['source']; ?></td>
                <td><?php echo isset($item['install_date'])?$item['install_date']:""; ?></td>
              </tr>
                        
            <?php   } ?>
            <?php } ?>
            </table>  
            <?php } //if($result){ ends ?>
          </div>
          </p>
          <div class="clearfix">
            <?php print render($page['content']); ?>
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
<style>

#edit-body-und-0-format{
  display: none;
}

.field-name-body{
  display: none; 
}

.form-item-title{
  display: none;  
}
#edit-preview{
 display: none;   
}
#field-portnumber-add-more-wrapper{
  display: none;
}
.sortby{
  cursor: pointer;
}
<?php if(isset($orderby) && $orderby == "temponline.source"){ ?>
  .sourcecss{
    text-decoration: underline;
  }
<?php } ?>
<?php if(isset($orderby) && $orderby == "install_date"){ ?>
  .install_datecss{
    text-decoration: underline;
  }
<?php } ?>


</style>
<script type="text/javascript">


  $(document).ready(function(){
      jQuery("#edit-title").val(" Client");
      jQuery("#edit-body-und-0-value").val("test");
      jQuery("#edit-submit").val("Send");

      

      $(".ignore").on('click',function(e){
        e.preventDefault();
        var a = $(this).parent('td').parent('tr');//prop("checked",true);
          a.find('td').each (function() {
            if ( $(this).children( "input" ).length ) {
              var val = $(this).children( "input" ).val();
              console.log("value initials" + val);
              if(val.indexOf("i") == -1){ 
                    val = val + "i";
                    console.log("value setting" + val);
                    $(this).children( "input" ).val(val);
                    $(this).children( "input" ).attr('disabled', 'disabled');
                    $(this).children( "input" ).prop('disabled', true);
                    console.log("value setting after" + val);

                    $.ajax({
                      url: "http://<?php echo $hostfull; ?>/drup/foo/bar",
                      data: {
                         id: $(this).children( "input" ).attr('data-instance-id'),
                         ignore:1
                      },
                      error: function() {
                         console.log("Error in json");
                      },
                      dataType: 'jsonp',
                      success: function(data) {
                        console.log(data);
                      },
                      type: 'POST'
                   });

              }else{
                    val = val.replace("i","");
                    console.log("value setting" + val);
                    $(this).children( "input" ).val(val);
                    $(this).children( "input" ).attr('disabled', '');
                    $(this).children( "input" ).prop('disabled', false);
                    $(this).children( "input" ).removeAttr("disabled");
                    console.log("value setting after" + val);
                    $.ajax({
                      url: "http://<?php echo $hostfull; ?>/drup/foo/bar",
                      data: {
                         id: $(this).children( "input" ).attr('data-instance-id'),
                         ignore:0
                      },
                      error: function() {
                         console.log("Error in json");
                      },
                      dataType: 'jsonp',
                      success: function(data) {
                        console.log(data);
                      },
                      type: 'POST'
                   });
              }
              
            }  
          });  
        //alert(a);
        // alert("gldkfsgfkdjhg");

      });

      $(".selboth").on('click',function(e){
        e.preventDefault();
        var a = $(this).parent('td').parent('tr');//prop("checked",true);
          a.find('td').each (function() {
            if ( $(this).children( "input" ).length ) {
              if($(this).children( "input" ).is(":checked")){ 
                    
                    $(this).children( "input" ).prop("checked",false);
                    $(this).children( "input" ).attr('checked', false);
                  
              }else{
                  $(this).children( "input" ).prop("checked",true); 
                  $(this).children( "input" ).attr('checked', true);
              }
              
            }  
          });  
        //alert(a);
        // alert("gldkfsgfkdjhg");

      });
      
      jQuery("#checkAll").on('change',function () { 
          $("input:checkbox").attr('checked', jQuery(this).attr("checked"));

                if($(this).is(":checked")){
                  
                    $('.clientportno').prop("checked",true);
                    $('.clientportnochild').prop("checked",true);
                }else{
                    $('.clientportno').prop("checked",false);
                    $('.clientportnochild').prop("checked",false);
                }
      });

       jQuery("#checkAll100").on('change',function () {  
          var maincheckbox = $(this);
          var counter = 1;
          jQuery('.clientportno').each(function(i, obj) {
            if(counter < 100){
            jQuery(this).attr('checked', maincheckbox.attr("checked"));
               if(maincheckbox.is(":checked")){
                    $(this).prop("checked",true);
                    var a = $(this).parent('td').parent('tr');//prop("checked",true);
                    a.find('td').each (function() {
                      if ( $(this).children( "input" ).length ) {
                            $(this).children( "input" ).prop("checked",true); 
                            $(this).children( "input" ).attr('checked', true);
                      }  
                    });  

                }else{
                    $(this).prop("checked",false);
                    var a = $(this).parent('td').parent('tr');//prop("checked",true);
                    a.find('td').each (function() {
                      if ( $(this).children( "input" ).length ) {
                            $(this).children( "input" ).prop("checked",false); 
                            $(this).children( "input" ).attr('checked', false);
                      }  
                    });
                }
            }
            counter = counter +1;
          });

          

      });

      jQuery("#edit-submit").on('click',function(e){
          //alert("aaa");
          
          var selectedcheckboxes;
          selectedcheckboxes = jQuery("input[name=clientports]:checked").map(function() { 
            console.log(this);
            var s = "?"+this;
            console.log(s);
            var abc = this.value;
            if(abc.indexOf("i") != -1){
                return 000;
            }
              return this.value;
            
          }).get().join(",");

          var selectedcheckboxeschild;
          selectedcheckboxeschild = jQuery("input[name=clientportschild]:checked").map(function() { console.log(this);
            var s = "?"+this;
            console.log(s);
            var abc = this.value;
            if(abc.indexOf("i") != -1){
                return 000;
            }
              return this.value;
          }).get().join(",");
          if(selectedcheckboxeschild && selectedcheckboxes){
            selectedcheckboxes = selectedcheckboxes +","+ selectedcheckboxeschild;
          }else if(selectedcheckboxeschild && !selectedcheckboxes){
            selectedcheckboxes = selectedcheckboxeschild;
          }
          
          console.log(selectedcheckboxes);
          jQuery(".form-item-field-portnumber-und-0-value input").val(selectedcheckboxes);
          /*alert(jQuery("input[name=clientports]:checked").map(function() {
              return this.value;
          }).get().join(","));*/
          //console.log(selectedcheckboxes);

          if(jQuery(".form-item-field-portnumber-und-0-value input").val() == ""){ alert("Please select ports checkbox "); }

          if(jQuery(".form-item-field-message-und-0-value input").val() == ""){ alert("Please enter message"); }



          if(jQuery(".form-item-field-portnumber-und-0-value input").val() == "" || jQuery(".form-item-field-message-und-0-value input").val() == ""){ e.preventDefault(); }



      });

      jQuery(".sortby").on('click',function(e){
      
        var sortby = $(this).attr('data');
        jQuery('<form method="POST" action="http://<?php echo $url; ?>" style="display:none;"><input type="text" name="order_by" value="'+sortby+'" /><input type="submit" value="submit" /></form>').appendTo('body').submit();
      });
       
  });

function strpos (haystack, needle, offset) {
  var i = (haystack+'').indexOf(needle, (offset || 0));
  return i === -1 ? false : i;
}
</script>