<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE toto[
  <!ENTITY nbsp "&#160;" >
]>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">

<xsl:template match="/">
<table id="ListTable">
	<tr class='ListHeader'>
		<td colspan="2"  class="ListColHeaderCenter" style="white-space:nowrap;" id="host_name" width="200"></td>
		<td class="ListColHeaderCenter" style="white-space:nowrap;" id="current_state" width="70">Status</td>
		<td class="ListColHeaderCenter" style="white-space:nowrap;" id="services"></td>
	</tr>
	<xsl:for-each select="//l">
	<tr>
		<xsl:attribute name="id">trStatus</xsl:attribute>
  		<xsl:attribute name="class"><xsl:value-of select="@class" /></xsl:attribute>

				<td>
					<xsl:element name="a">
					  	<xsl:attribute name="href">oreon.php?p=201&amp;o=hd&amp;host_name=<xsl:value-of select="hn"/></xsl:attribute>
						<xsl:attribute name="class">pop</xsl:attribute>
						<xsl:value-of select="hn"/>
					</xsl:element>
				</td>

				<td>
					<xsl:element name="a">
					  	<xsl:attribute name="href">oreon.php?o=svc&amp;p=20201&amp;host_name=<xsl:value-of select="hn"/></xsl:attribute>
							<xsl:element name="img">
							  	<xsl:attribute name="src">./img/icones/16x16/view.gif</xsl:attribute>
							</xsl:element>
					</xsl:element>
					<xsl:element name="a">
					  	<xsl:attribute name="href">oreon.php?p=40210&amp;host_name=<xsl:value-of select="hn"/></xsl:attribute>
							<xsl:element name="img">
							  	<xsl:attribute name="src">./img/icones/16x16/column-chart.gif</xsl:attribute>
							</xsl:element>
					</xsl:element>
				</td>

				<td class='ListColCenter'>
							<xsl:attribute name="style">
								background-color:<xsl:value-of select="hc"/>;
    						</xsl:attribute>
						<xsl:value-of select="hs"/>
				</td>

				<td class="ListColLeft">

					<xsl:if test="sk >= 1">
						<span>
							<xsl:attribute name="style">
								background-color:<xsl:value-of select="skc"/>;
    						</xsl:attribute>
					<xsl:element name="a">
					  	<xsl:attribute name="href">oreon.php?o=svc_ok&amp;p=2020202&amp;host_name=<xsl:value-of select="hn"/></xsl:attribute>
							<xsl:value-of select="sk"/>OK
					</xsl:element>
						</span>&nbsp;
					</xsl:if>


					<xsl:if test="sw >= 1">
						<span>
							<xsl:attribute name="style">
								background-color:<xsl:value-of select="swc"/>;
    						</xsl:attribute>
					<xsl:element name="a">
					  	<xsl:attribute name="href">oreon.php?o=svc_warning&amp;p=2020202&amp;host_name=<xsl:value-of select="hn"/></xsl:attribute>
						<xsl:value-of select="sw"/>WARNING
					</xsl:element>
						</span>&nbsp;
					</xsl:if>
					<xsl:if test="sc >= 1">
						<span>
							<xsl:attribute name="style">
								background-color:<xsl:value-of select="scc"/>;
    						</xsl:attribute>
					<xsl:element name="a">
					  	<xsl:attribute name="href">oreon.php?o=svc_critical&amp;p=2020202&amp;host_name=<xsl:value-of select="hn"/></xsl:attribute>
						<xsl:value-of select="sc"/>CRITICAL
					</xsl:element>
						</span>&nbsp;
					</xsl:if>
					<xsl:if test="su >= 1">
						<span>
							<xsl:attribute name="style">
								background-color:<xsl:value-of select="suc"/>;
    						</xsl:attribute>
					<xsl:element name="a">
					  	<xsl:attribute name="href">oreon.php?o=svc_unknown&amp;p=2020202&amp;host_name=<xsl:value-of select="hn"/></xsl:attribute>
						<xsl:value-of select="su"/>UNKNOWN
					</xsl:element>
						</span>&nbsp;
					</xsl:if>
					<xsl:if test="sp >= 1">
						<span>
							<xsl:attribute name="style">
								background-color:<xsl:value-of select="spc"/>;
    						</xsl:attribute>
					<xsl:element name="a">
					  	<xsl:attribute name="href">oreon.php?o=svc_pending&amp;p=2020202&amp;host_name=<xsl:value-of select="hn"/></xsl:attribute>
						<xsl:value-of select="sp"/>PENDING
					</xsl:element>
						</span>
					</xsl:if>

				</td>
	</tr>
</xsl:for-each>
</table>
</xsl:template>
</xsl:stylesheet>