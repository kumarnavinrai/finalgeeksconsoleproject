<?php
global $user;
$url = $_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
$uri = $_SERVER["REQUEST_URI"];
$host = $_SERVER["HTTP_HOST"];
$hostfull = $host;
$hostfornode = "http://admin.pc-optimiser.com:8000";
$host = explode(".",$_SERVER["HTTP_HOST"]);
$host = current($host);

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
  $qry = "SELECT * FROM temponline ".$adminselect[$host];
  //SELECT *  FROM `temponline` WHERE `user_name` LIKE '%PC..1%'
  $result = db_query($qry);
  $_SESSION["perm"]="a";
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
          || <a href="/drup/node/5" title="Installation Stats">Installation Stats</a>
          </p>
          <div id="custtable">
            <?php if($result){ ?>
            <table style="width:100%">
              
              <tr>
                <th>Select Both</th>
                <th>Select</th>
                <th>PC Name Parent</th>
                <th>Select</th>
                <th>PC Name Child</th>
                <th>Port child</th>
                <th>IP</th>
                <th>Port parent</th>
                <th>Country</th>
              </tr>
            <?php 
              //change array here 
              $arrayofchilds = array();
              $arrayofparents = array();
              foreach($result as $k => $item) 
              { 
                
                $version = substr($item->user_name, strpos($item->user_name, '..')+2, 1);
                
                if($item->type == 1)
                {  
                  $arrayofparents[$k]=array(
                                          'id'=>$item->id,
                                          'user_name'=>$item->user_name,
                                          'port'=>$item->port,
                                          'ip'=>$item->ip,
                                          'source'=>$item->source,
                                          'type'=>$item->type,
                                          'version'=>$version  

                                          );
                }
                elseif($item->type == 2)
                {
                   $arrayofchilds[$k]=array(
                                          'id'=>$item->id,
                                          'user_name'=>$item->user_name,
                                          'port'=>$item->port,
                                          'ip'=>$item->ip,
                                          'source'=>$item->source,
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
                <?php if($item['port']){ ?>
                <td><input type="checkbox" class="clientportno" name="clientports" value="<?php echo $item['port']; ?>"></td>
                <td><?php echo $item['user_name']; ?></td>
                <?php }elseif(!$item['port']){ ?>
                  <td></td>  
                  <td></td>  
                <?php } ?>
                <?php if(isset($item['child'])){ ?>
                <td><input type="checkbox" class="clientportnochild" name="clientportschild" value="<?php echo $item['child']['port']; ?>"></td>
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
</style>
<script type="text/javascript">


  $(document).ready(function(){
      jQuery("#edit-title").val(" Client");
      jQuery("#edit-body-und-0-value").val("test");
      jQuery("#edit-submit").val("Send");

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
              return this.value;
          }).get().join(",");

          var selectedcheckboxeschild;
          selectedcheckboxeschild = jQuery("input[name=clientportschild]:checked").map(function() {
              return this.value;
          }).get().join(",");
          if(selectedcheckboxeschild){
            selectedcheckboxes = selectedcheckboxes + selectedcheckboxeschild;
          }
          
          console.log("------------");
          console.log(selectedcheckboxes); 
          console.log("------------");
          console.log(selectedcheckboxeschild); 
          console.log("------------");
          return;
          jQuery(".form-item-field-portnumber-und-0-value input").val(selectedcheckboxes);
          /*alert(jQuery("input[name=clientports]:checked").map(function() {
              return this.value;
          }).get().join(","));*/
          //console.log(selectedcheckboxes);

          if(jQuery(".form-item-field-portnumber-und-0-value input").val() == ""){ alert("Please select ports checkbox "); }

          if(jQuery(".form-item-field-message-und-0-value input").val() == ""){ alert("Please enter message"); }



          if(jQuery(".form-item-field-portnumber-und-0-value input").val() == "" || jQuery(".form-item-field-message-und-0-value input").val() == ""){ e.preventDefault(); }



      });



          
  });

</script>