<cfsetting enablecfoutputonly="yes">

<cfparam name="attributes.id" default="t#left(replace(CreateUUID(),'-','','All'),15)#">
<cfparam name="attributes.name" default="#attributes.id#">
<cfparam name="attributes.menu" default="">

<cfif  ThisTag.HasEndTag>
	<!--- [ create wrapper   ] --->
	<cfif ThisTag.ExecutionMode is "End">
		<cfif not Len(attributes.menu)>
			&lt;CF_dhtmlXMenu&gt; error. Attribute "menu" was missed.<cfabort>
		</cfif>
		<cfsavecontent variable="menuOutput">
		<cfoutput>
			<span id="#attributes.id#">#ThisTag.GeneratedContent#</span><script>#attributes.menu#.setContextZone('#attributes.id#','#attributes.name#'); </script>
		</cfoutput>
	</cfsavecontent>
    <cfset ThisTag.GeneratedContent = menuOutput>
	</cfif>
<cfelse>
	<!--- [ register    ] --->
	<cfoutput>
	<script>#attributes.menu#.setContextZone('#attributes.id#','#attributes.name#'); </script>
	</cfoutput>	
	
</cfif>
<cfsetting enablecfoutputonly="no">