<?php

/**
 * @file
 * Default theme implementation to display the basic html structure of a single
 * Drupal page.
 *
 * Variables:
 * - $css: An array of CSS files for the current page.
 * - $language: (object) The language the site is being displayed in.
 *   $language->language contains its textual representation.
 *   $language->dir contains the language direction. It will either be 'ltr' or 'rtl'.
 * - $rdf_namespaces: All the RDF namespace prefixes used in the HTML document.
 * - $grddl_profile: A GRDDL profile allowing agents to extract the RDF data.
 * - $head_title: A modified version of the page title, for use in the TITLE
 *   tag.
 * - $head_title_array: (array) An associative array containing the string parts
 *   that were used to generate the $head_title variable, already prepared to be
 *   output as TITLE tag. The key/value pairs may contain one or more of the
 *   following, depending on conditions:
 *   - title: The title of the current page, if any.
 *   - name: The name of the site.
 *   - slogan: The slogan of the site, if any, and if there is no title.
 * - $head: Markup for the HEAD section (including meta tags, keyword tags, and
 *   so on).
 * - $styles: Style tags necessary to import all CSS files for the page.
 * - $scripts: Script tags necessary to load the JavaScript files and settings
 *   for the page.
 * - $page_top: Initial markup from any modules that have altered the
 *   page. This variable should always be output first, before all other dynamic
 *   content.
 * - $page: The rendered page content.
 * - $page_bottom: Final closing markup from any modules that have altered the
 *   page. This variable should always be output last, after all other dynamic
 *   content.
 * - $classes String of classes that can be used to style contextually through
 *   CSS.
 *
 * @see template_preprocess()
 * @see template_preprocess_html()
 * @see template_process()
 *
 * @ingroup themeable
 */
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML+RDFa 1.0//EN"
  "http://www.w3.org/MarkUp/DTD/xhtml-rdfa-1.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php print $language->language; ?>" version="XHTML+RDFa 1.0" dir="<?php print $language->dir; ?>"<?php print $rdf_namespaces; ?>>

<head profile="<?php print $grddl_profile; ?>">
  <?php print $head; ?>
  <title><?php print $head_title; ?></title>
  <?php print $styles; ?>
  <?php print $scripts; ?>
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






$adminselectforjs = array("admin"=>"","adminone"=>"PC..1","admintwo"=>"PC..2","adminthree"=>"PC..3","adminfour"=>"PC..4");

//SELECT * FROM temponline LEFT JOIN appdata ON temponline.ip = appdata.ip WHERE status = 1 AND version = 1
/* page tpl start */
if (in_array('reps', $user->roles) && strpos($uri,"/node/add/messagetoclient")) {


$adminselect = array("admin"=>"WHERE status = 1 ","adminone"=>"WHERE status = 1 AND temponline.user_name LIKE '%..1%'","admintwo"=>"WHERE status = 1 AND  temponline.user_name LIKE '%..2%'","adminthree"=>"WHERE status = 1 AND temponline.user_name LIKE '%..3%'","adminfour"=>"WHERE status = 1 AND temponline.user_name LIKE '%..4%'","adminfive"=>"WHERE status = 1 AND temponline.user_name LIKE '%..5%'","adminsix"=>"WHERE status = 1 AND temponline.user_name LIKE '%..6%'","adminseven"=>"WHERE status = 1 AND temponline.user_name LIKE '%..7%'","admineight"=>"WHERE status = 1 AND temponline.user_name LIKE '%..8%'");



  $qry = "SELECT * FROM temponline LEFT JOIN appdata ON temponline.instance_id=appdata.instance_id ".$adminselect[$host];
  $qry .=" GROUP BY temponline.instance_id ";

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
  $noofmsgarray = array();
  $querynoofmsg = 'SELECT command,COUNT(command) as no FROM noofmsg GROUP BY command';
  $resultnoofmsg = db_query($querynoofmsg);
  if($resultnoofmsg){
    foreach($resultnoofmsg as $knoofmsg => $itemnoofmsg)  
    {
      $noofmsgarray[$itemnoofmsg->command] = $itemnoofmsg->no;              
    }
    
  }  
/*
  //echo "<pre>"; print_r($noofmsgarray); die;
  foreach($result as $k => $item) 
              {
  echo "<pre>"; print_r($item);              
              }
  die;*/
}
/* page tpl ends */


  ?>
</head>
<body class="<?php print $classes; ?>" <?php print $attributes;?>>

  <div id="skip-link">
    <a href="#main-content" class="element-invisible element-focusable"><?php print t('Skip to main content'); ?></a>
  </div>
  <?php print $page_top; ?>
  <?php print $page; ?>
  <?php print $page_bottom; ?>
</body>
</html>
