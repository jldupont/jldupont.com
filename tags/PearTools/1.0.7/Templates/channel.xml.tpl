<?xml version="1.0" encoding="ISO-8859-1" ?>
<channel version="1.0" xmlns="http://pear.php.net/channel-1.0"
  xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
  xsi:schemaLocation="http://pear.php.net/dtd/channel-1.0 http://pear.php.net/dtd/channel-1.0.xsd">
 <name>$name$</name> <!-- must be base URI -->
 <summary>$summary$</summary>
 <suggestedalias>$alias$</suggestedalias>
 <servers>
  <primary>
   <rest>
    <baseurl type="REST1.0">$uri$</baseurl>
    <baseurl type="REST1.1">$uri$</baseurl>
   </rest>
  </primary>
 </servers>
</channel>