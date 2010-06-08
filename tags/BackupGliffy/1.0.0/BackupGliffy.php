<?php
/**
 *  JLD_BackupGliffy
 *
 *  @author Jean-Lou Dupont
 *  @version 1.0.0
 */

require_once 'JLD/Delicious/DeliciousPosts.php';
require_once 'JLD/Gliffy/Gliffy.php';
require_once 'JLD/BackupGliffy/BackupGliffy.php';

// configuration
require_once "JLD/BackupGliffy/config.php";

// gets posts
$posts = new JLD_DeliciousPosts( $feed );
$posts->run();

// main loop
foreach( $posts as $post ) {

	$g = JLD_Gliffy::newFromDeliciousPost( $post );
	assert( $g instanceof JLD_Gliffy );

	$i = $g->getPictureIterator();
	$title = $g->title;
	
	foreach( $i as $index => $repr ) {

		$id   = $repr->id;
		$ext  = $repr->ext;
		$size = $repr->size;
		$url  = $repr->url;
		echo "\n* Represention of $title: $index - size $size - ext $ext";	

		$contents = file_get_contents( $url );
		if ( $contents === false )
			echo ": error fetching";
		else {
			echo ": fetching OK";
			$r = file_put_contents( $dest . $title . '.' . $size . '.' . $ext, $contents );
			echo " - write " . ( ($r == strlen( $contents ) ) ? "success":"failed" );
		}
	}
	
}//foreach
