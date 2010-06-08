/**
 * TreeView.js
 * Requires jQuery, plugin Treeview, plugin jCookie
 * Modified 'treeview.css' for usage on GooglePages
 *  (removed hierarchy in 'url' tags)
 * 
 * @author Jean-Lou Dupont
 * @version 0.1
 */
document.write('<link rel="stylesheet" href="jquery.treeview.mod.css" />');
$(document).ready(function() {
	$("#tree").treeview({
		collapsed: true,
		animated: "fast",
		prerendered: true,
		persist: "location"
	});
})