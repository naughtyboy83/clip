<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<?php include('header.inc'); ?>
<?php include('clip-utils.php'); ?>
</head>
<body>
<!-- wrap starts here -->
<div id="wrap">

	<!--header (custom header/navbar for license list)-->
	<div id="header">			
				
		<h1 id="logo-text"><a href="index.html" title="">CLIP</a></h1>		
		<p id="slogan">CIPPIC Licensing Information Project</p>	
		<?php include('breadcrumbs.inc'); ?>
		<div  id="nav">
			<ul>
				<li class="first"<?php print ($_GET['cat'] == 'all') ? ' id="current"' : ''; ?>><a href="license-list.php?cat=all">All licenses</a></li>
				<li <?php print ($_GET['cat'] == 'data') ? ' id="current"' : ''; ?>><a href="license-list.php?cat=data">Data</a></li>
				<li <?php print ($_GET['cat'] == 'software') ? ' id="current"' : ''; ?>><a href="license-list.php?cat=software">Software</a></li>
				<li <?php print ($_GET['cat'] == 'content') ? ' id="current"' : ''; ?>><a href="license-list.php?cat=content">Content</a></li>
				<li <?php print ($_GET['cat'] == 'international') ? ' id="current"' : ''; ?>><a href="license-list.php?cat=international">International</a></li>
				<li <?php print ($_GET['cat'] == 'Canadian') ? ' id="current"' : ''; ?>><a href="license-list.php?cat=Canadian">Canadian</a></li>
			</ul>		
		</div>
						
	<!--header ends-->					
	</div>
      
	<!-- featured starts -->	
	<div id="featured" class="clear">
	<h3 class="license-list-header">All <?php if($_GET['cat'] != 'all') print $_GET['cat'] . ' '; ?>licenses in the CLIP database:</h3>
	<ul class="license-list">				
	<?php
	   // load any cached list for this category
      $list = load_json($LICENSE_LIST_CACHE_DIR, $_GET['cat'], 'json');
      
      // if there's no cached list, need to iterate through licenses to construct the list
      if(empty($list)){
      	$list = array();
      	$titles = array();
      	
      	// for each license
			foreach(license_list() as $license_id){
				
			   // load the metadata
			   $metadata = load_json($LICENSE_DATA_DIR, $license_id, 'json');
			   
			   // check if the license belongs to the selected category
				if($_GET['cat'] == 'data' && !$metadata->domain_data)
				  continue;
				if($_GET['cat'] == 'content' && !$metadata->domain_content)
				  continue;
				if($_GET['cat'] == 'software' && !$metadata->domain_software)
				  continue;				
				if($_GET['cat'] == 'Canadian' && !preg_match('/\ACAN\-/', $license_id))
				  continue;
				if($_GET['cat'] == 'international' && preg_match('/\ACAN\-/', $license_id))
				  continue;
			
				// add the basic info necessary for the list
				$item = new stdClass();
				foreach(array('id','title','maintainer') as $attrib){
					$item->$attrib = $metadata->$attrib;
				}
				$list[] = $item;
				$titles[] = $item->title;
      	}
      	
      	// sort the list alphabetically by title
      	array_multisort($titles, $list);
      	
      	// write out the list to the cache
         $list_json = json_encode($list);
         file_put_contents($LICENSE_LIST_CACHE_DIR . '/' . $_GET['cat'] . '.json',
            $list_json);
      }
    
		// for each license in the list
		foreach($list as $metadata){
         // write out the title and a link to the license
         print "<li><span class='ltitle'><a href='license-info.php?rc=" . $_GET['cat']
           . "&id=" . $metadata->id . "'>" .
         $metadata->title . "</a></span> " .
           "[<span class='lid'>" . $metadata->id . "</span>]<br />" .
           "<span class='lmaint'>" . $metadata->maintainer . "</span></li>";
		}
	?>
	</ul>
	<!-- featured ends -->
	</div>
	
	<?php include('footer.inc'); ?>
<!-- wrap ends here -->
</div>

</body>
</html>
