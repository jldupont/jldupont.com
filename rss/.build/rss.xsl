<?xml version="1.0" encoding="ISO-8859-1"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

	<xsl:output method="xml" omit-xml-declaration = "yes" />

	<xsl:template match="channel" >
		<xsl:copy-of select="item" />
	</xsl:template>

</xsl:stylesheet>