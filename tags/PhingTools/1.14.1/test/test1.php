<?php

	$path1 = "ZendFramework-1.8.1-minimal.zip";
	$path2 = "ZendFramework-1.8.2-minimal";
	$path3 = "ZendFramework-1.8.3.zip";
	
	$result1 = preg_match("/\-(.*?)\-/", $path1, $matches1);
	$result2 = preg_match("/\-([\.0-9]+)/", $path2, $matches2);
	$result3 = preg_match("/\-([\.0-9]+)/", $path3, $matches3);
	
	print $matches1[1]."\n";
	print $matches2[1]."\n";
	
	print trim($matches3[1]," .");
	
	
