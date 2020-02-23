<?xml version="1.0" encoding="utf-8"?>
<!--
/**
 * zCorrecteurs.fr est le logiciel qui fait fonctionner www.zcorrecteurs.fr
 *
 * Copyright (C) 2012-2019 Corrigraphie
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Parseur de zCode, feuille de style pour
 * la transformation en HTML.
 *
 * @copyright  mwsaz <mwksaz@gmail.com> 2010
 */
-->


<xsl:stylesheet
	version="1.0"
	omit-xml-declaration="no"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
	xmlns:php="http://php.net/xsl"
	exclude-result-prefixes="php"
>
	<xsl:output
		indent="no"
		method="xml"
		encoding="utf-8"
	/>

	<!-- Pas de XSL 2.0 pour PHP pour le moment :(

	<xsl:function name="verifier_lien">
		<xsl:param name="lien"/>

		<xsl:variable name="p">
			<xsl:value-of select="substring-before($lien, ':')"/>
		</xsl:variable>
		<xsl:choose>
			<xsl:when test="$p='apt' or $p='ftp' or $p='http' or $p='https'">1</xsl:when>
			<xsl:otherwise>0</xsl:otherwise>
		</xsl:choose>
	</xsl:function>

	-->


	<xsl:template match="barre">
		<span class="barre"><xsl:apply-templates/></span>
	</xsl:template>
	<xsl:template match="gras">
		<strong><xsl:apply-templates/></strong>
	</xsl:template>
	<xsl:template match="italique">
		<em class="italique"><xsl:apply-templates/></em>
	</xsl:template>
	<xsl:template match="souligne">
		<span class="souligne"><xsl:apply-templates/></span>
	</xsl:template>



	<xsl:template match="attention">
		<div class="rmq attention"><xsl:apply-templates/></div>
	</xsl:template>
	<xsl:template match="erreur">
		<div class="rmq erreur"><xsl:apply-templates/></div>
	</xsl:template>
	<xsl:template match="information">
		<div class="rmq information"><xsl:apply-templates/></div>
	</xsl:template>
	<xsl:template match="question">
		<div class="rmq question"><xsl:apply-templates/></div>
	</xsl:template>



	<xsl:template match="titre1">
		<xsl:variable name="ancre" select="php:functionString('\Zco\Bundle\ParserBundle\Feature\CoreFeature::ancre', .)" />
		<xsl:choose>
			<xsl:when test="$ancre != ''">
				<h3 id="{$ancre}">
					<xsl:apply-templates/>
					<a href="#{$ancre}" class="titre_ancre">¶</a>
				</h3>
			</xsl:when>
			<xsl:otherwise>
				<h3>
					<xsl:apply-templates/>
				</h3>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	<xsl:template match="titre2">
		<xsl:variable name="ancre" select="php:functionString('\Zco\Bundle\ParserBundle\Feature\CoreFeature::ancre', .)" />
		<xsl:choose>
			<xsl:when test="$ancre != ''">
				<h4 id="{$ancre}">
					<xsl:apply-templates/>
					<a href="#{$ancre}" class="titre_ancre">¶</a>
				</h4>
			</xsl:when>
			<xsl:otherwise>
				<h4>
					<xsl:apply-templates/>
				</h4>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>



	<xsl:template match="exposant">
		<sup><xsl:apply-templates/></sup>
	</xsl:template>
	<xsl:template match="indice">
		<sub><xsl:apply-templates/></sub>
	</xsl:template>
	<xsl:template match="acronyme">
		<acronym title="{@valeur}"><xsl:apply-templates/></acronym>
	</xsl:template>
	<xsl:template match="faute">
		<del class="faute" title="{@correction}"><xsl:apply-templates/></del>
	</xsl:template>


	<xsl:template match="email">
		<xsl:choose>
			<xsl:when test="@nom">
				<a href="mailto:{@nom}"><xsl:apply-templates/></a>
			</xsl:when>
			<xsl:otherwise>
				<a href="mailto:{.}"><xsl:apply-templates/></a>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	<xsl:template name="raccourcirLien">
		<xsl:param name="lien"/>
		<xsl:param name="tailleMax" select="'50'"/>
		<xsl:variable name="taille" select="string-length($lien)"/>
		<xsl:choose>
			<xsl:when test="$taille &gt; $tailleMax">
				<xsl:variable name="quart" select="floor($tailleMax div 4)"/>
				<xsl:value-of select="substring($lien, 0, 3 * $quart)"/>
				<xsl:text> […] </xsl:text>
				<xsl:value-of select="substring($lien, $taille - $quart)"/>
			</xsl:when>
			<xsl:otherwise>
				<xsl:value-of select="$lien"/>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	<xsl:template match="lien">
		<xsl:choose>
			<xsl:when test="@url and php:functionString('\Zco\Bundle\ParserBundle\Feature\CoreFeature::verifierLien', @url)">
				<xsl:element name="a">
					<xsl:attribute name="href">
						<xsl:choose>
							<xsl:when test="substring(@url, 1, 4)='ftp.'">
								<xsl:text>ftp://</xsl:text>
								<xsl:value-of select="@url"/>
							</xsl:when>
							<xsl:when test="substring(@url, 1, 4)='www.'">
								<xsl:text>http://</xsl:text>
								<xsl:value-of select="@url"/>
							</xsl:when>
							<xsl:otherwise>
								<xsl:value-of select="@url"/>
							</xsl:otherwise>
						</xsl:choose>
					</xsl:attribute>
					<xsl:apply-templates/>
				</xsl:element>
			</xsl:when>
			<xsl:when test="php:functionString('\Zco\Bundle\ParserBundle\Feature\CoreFeature::verifierLien', .)">
				<xsl:element name="a">
					<xsl:attribute name="href">
						<xsl:choose>
							<xsl:when test="substring(., 1, 4)='ftp.'">
								<xsl:text>ftp://</xsl:text>
								<xsl:value-of select="."/>
							</xsl:when>
							<xsl:when test="substring(., 1, 4)='www.'">
								<xsl:text>http://</xsl:text>
								<xsl:value-of select="."/>
							</xsl:when>
							<xsl:otherwise>
								<xsl:value-of select="."/>
							</xsl:otherwise>
						</xsl:choose>
					</xsl:attribute>
					<xsl:call-template name="raccourcirLien">
						<xsl:with-param name="lien" select="."/>
					</xsl:call-template>
				</xsl:element>
			</xsl:when>

			<xsl:otherwise>
				<xsl:value-of select="."/>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>



	<xsl:template match="liste">
		<xsl:choose>
			<xsl:when test="@type='1' or @type='i' or @type='I' or @type='a' or @type='A'">
				<ol class="liste_{@type}"><xsl:apply-templates select="puce"/></ol>
			</xsl:when>

			<xsl:when test="@type='disque' or @type='cercle' or @type='rectangle' or @type='rien'">
				<ul class="liste_{@type}"><xsl:apply-templates select="puce"/></ul>
			</xsl:when>

			<xsl:otherwise>
				<ul class="liste_cadratin"><xsl:apply-templates select="puce"/></ul>
			</xsl:otherwise>

		</xsl:choose>
	</xsl:template>
	<xsl:template match="puce">
		<li><xsl:apply-templates/></li>
	</xsl:template>



	<xsl:template match="image">
		<xsl:variable name="protocole" select="substring-before(., ':')"/>
		<xsl:choose>
			<xsl:when test="$protocole='http' or $protocole=''">
				<xsl:element name="img">
					<xsl:attribute name="src"><xsl:value-of select="php:functionString('\Zco\Bundle\ParserBundle\Feature\CoreFeature::imageLien', .)"/></xsl:attribute>
					<xsl:choose>
						<xsl:when test="@legende">
							<xsl:attribute name="alt"><xsl:value-of select="@legende"/></xsl:attribute>
							<xsl:attribute name="title"><xsl:value-of select="@legende"/></xsl:attribute>
						</xsl:when>
						<xsl:otherwise>
							<xsl:attribute name="alt">Image utilisateur</xsl:attribute>
						</xsl:otherwise>
					</xsl:choose>
				</xsl:element>
			</xsl:when>
			<xsl:otherwise>
				<xsl:value-of select="."/>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	<xsl:template match="math">
		<xsl:variable name="formule" select="php:functionString('rawurlencode', .)"/>
		<img src="/cgi-bin/mimetex.cgi?{$formule}" alt="{.}"/>
	</xsl:template>



	<xsl:template match="couleur">
		<xsl:variable name="c" select="@nom"/>
		<xsl:choose>
			<xsl:when test="$c='argent' or $c='blanc' or $c='bleu' or $c='bleugris' or $c='gris' or $c='jaune' or $c='marine' or $c='marron' or $c='noir' or $c='olive' or $c='orange' or $c='rose' or $c='rouge' or $c='turquoise' or $c='vertc' or $c='vertf' or $c='violet'">
				<span class="{$c}"><xsl:apply-templates/></span>
			</xsl:when>
			<xsl:otherwise>
				<xsl:apply-templates/>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	<xsl:template match="police">
		<xsl:variable name="p" select="@nom"/>
		<xsl:choose>
			<xsl:when test="$p='arial' or $p='courier' or $p='geneva' or $p='impact' or $p='optima' or $p='times'">
				<span class="{$p}"><xsl:apply-templates/></span>
			</xsl:when>
			<xsl:otherwise>
				<xsl:apply-templates/>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	<xsl:template match="taille">
		<xsl:variable name="t" select="@valeur"/>
		<xsl:choose>
			<xsl:when test="$t='ttpetit' or $t='tpetit' or $t='petit' or $t='normal' or $t='gros' or $t='tgros' or $t='ttgros'">
				<span class="{$t}"><xsl:apply-templates/></span>
			</xsl:when>
			<xsl:otherwise>
				<xsl:apply-templates/>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>



	<xsl:template match="position">
		<xsl:variable name="p" select="@valeur"/>
		<xsl:choose>
			<xsl:when test="$p='gauche' or $p='centre' or $p='droite' or $p='justifie'">
				<div class="{$p}"><xsl:apply-templates/></div>
			</xsl:when>
			<xsl:otherwise>
				<xsl:apply-templates/>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	<xsl:template match="flottant">
		<xsl:variable name="f" select="@valeur"/>
		<xsl:choose>
			<xsl:when test="$f='gauche' or $f='droite'">
				<div class="flot_{$f}"><xsl:apply-templates/></div>
			</xsl:when>
			<xsl:when test="$f='aucun'">
				<div class="cleaner"><xsl:apply-templates/></div>
			</xsl:when>
			<xsl:otherwise>
				<xsl:apply-templates/>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>



	<xsl:template name="citationTitre">
		<xsl:param name="rid"/>
		<xsl:text>Citation</xsl:text>
		<xsl:choose>
			<xsl:when test="$rid">
				<xsl:text> : </xsl:text>
				<xsl:value-of select="substring-after($rid, ':')"/>
			</xsl:when>
			<xsl:otherwise>
				<xsl:if test="@nom"> : <xsl:value-of select="@nom"/></xsl:if>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	<xsl:template match="citation">
		<span class="citation">
			<xsl:choose>
				<xsl:when test="not(@rid) and @lien and php:functionString('\Zco\Bundle\ParserBundle\Feature\CoreFeature::verifierLien', @lien)">
					<xsl:element name="a">
						<xsl:attribute name="href">
							<xsl:choose>
								<xsl:when test="substring(@lien, 1, 4)='www.'">
									<xsl:text>http://</xsl:text>
									<xsl:value-of select="@lien"/>
								</xsl:when>
								<xsl:otherwise>
									<xsl:value-of select="@lien"/>
								</xsl:otherwise>
							</xsl:choose>
						</xsl:attribute>
						<xsl:call-template name="citationTitre"/>
					</xsl:element>
				</xsl:when>
				<xsl:when test="@rid">
					<xsl:variable name="rid" select="php:functionString('\Zco\Bundle\ParserBundle\Feature\CoreFeature::citationRid', @rid)"/>
					<xsl:choose>
						<xsl:when test="$rid">
							<xsl:variable name="sujet" select="substring-before($rid, ':')"/>
							<a href="/forum/sujet-{$sujet}-{@rid}.html">
								<xsl:call-template name="citationTitre">
									<xsl:with-param name="rid" select="$rid"/>
								</xsl:call-template>
							</a>
						</xsl:when>
						<xsl:otherwise>
							<xsl:call-template name="citationTitre"/>
						</xsl:otherwise>
					</xsl:choose>
				</xsl:when>
				<xsl:otherwise>
					<xsl:call-template name="citationTitre"/>
				</xsl:otherwise>
			</xsl:choose>
		</span>
		<div class="citation2"><xsl:apply-templates/></div>
	</xsl:template>
	<xsl:template match="secret">
		<xsl:choose>
			<xsl:when test="@cache='0'">
				<span class="spoiler">Secret <a href="#" onclick="return switch_spoiler(this)">(cliquez pour afficher)</a>
				</span>
				<div class="spoiler2" onclick="return switch_spoiler(this)"><div class="spoiler3">
					<xsl:apply-templates/>
				</div></div>
			</xsl:when>
			<xsl:otherwise>
				<span class="spoiler_hidden">Secret <a href="#" onclick="return switch_spoiler_hidden(this)">(cliquez pour afficher)</a>
				</span>
				<div class="spoiler2_hidden"><div class="spoiler3_hidden">
					<xsl:apply-templates/>
				</div></div>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>



	<xsl:template match="tableau">
		<table class="tab_user">
			<xsl:apply-templates select="legende|ligne"/>
		</table>
	</xsl:template>
	<xsl:template name="tableauFusion">
		<xsl:if test="@fusion_col">
			<xsl:attribute name="colspan">
				<xsl:value-of select="number(@fusion_col)"/>
			</xsl:attribute>
		</xsl:if>

		<xsl:if test="@fusion_lig">
			<xsl:attribute name="rowspan">
				<xsl:value-of select="number(@fusion_lig)"/>
			</xsl:attribute>
		</xsl:if>
	</xsl:template>
	<xsl:template match="legende">
		<caption><xsl:value-of select="."/></caption>
	</xsl:template>
	<xsl:template match="ligne">
		<tr><xsl:apply-templates select="entete|cellule"/></tr>
	</xsl:template>
	<xsl:template match="entete">
		<xsl:element name="th">
			<xsl:call-template name="tableauFusion"/>
			<xsl:apply-templates/>
		</xsl:element>
	</xsl:template>
	<xsl:template match="cellule">
		<xsl:element name="td">
			<xsl:call-template name="tableauFusion"/>
			<xsl:apply-templates/>
		</xsl:element>
	</xsl:template>



	<xsl:template name="codeTitre">
		<xsl:text>Code : </xsl:text>
		<xsl:value-of select="php:functionString('\Zco\Bundle\ParserBundle\Feature\CoreFeature::nomCode', @type)"/>
		<xsl:if test="@titre">
			<xsl:text> - </xsl:text>
			<xsl:value-of select="@titre"/>
		</xsl:if>
	</xsl:template>
	<xsl:template match="code">
		<xsl:element name="span">
			<xsl:attribute name="class">code</xsl:attribute>
			<xsl:choose>
				<xsl:when test="@url and php:functionString('\Zco\Bundle\ParserBundle\Feature\CoreFeature::verifierLien', @url)">
					<xsl:element name="a">
						<xsl:attribute name="href">
							<xsl:choose>
								<xsl:when test="substring(@url, 1, 4)='www.'">
									<xsl:text>http://</xsl:text>
									<xsl:value-of select="@url"/>
								</xsl:when>
								<xsl:otherwise>
									<xsl:value-of select="@url"/>
								</xsl:otherwise>
							</xsl:choose>
						</xsl:attribute>
						<xsl:call-template name="codeTitre"/>
					</xsl:element>
				</xsl:when>
				<xsl:otherwise>
					<xsl:call-template name="codeTitre"/>
				</xsl:otherwise>
			</xsl:choose>
		</xsl:element>

		<xsl:variable name="type" select="php:functionString('\Zco\Bundle\ParserBundle\Feature\CoreFeature::typeCode', @type)"/>
		<div class="code2{$type}">
			<xsl:element name="zcode-code">
				<xsl:value-of select="php:function('\Zco\Bundle\ParserBundle\Feature\CoreFeature::colorerCode', .)"/>
			</xsl:element>
		</div>
	</xsl:template>
	<xsl:template match="minicode">
		<xsl:variable name="type" select="php:functionString('\Zco\Bundle\ParserBundle\Feature\CoreFeature::typeCode', @type)"/>
		<span class="code2{$type}">
			<xsl:element name="zcode-code">
				<xsl:value-of select="php:function('\Zco\Bundle\ParserBundle\Feature\CoreFeature::colorerCode', .)"/>
			</xsl:element>
		</span>
	</xsl:template>
	<xsl:template match="touche">
		<kbd><xsl:value-of select="."/></kbd>
	</xsl:template>
</xsl:stylesheet>
