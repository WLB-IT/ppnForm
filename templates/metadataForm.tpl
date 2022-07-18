{**
 * plugins/generic/ppnForm/templates/metadataForm.tpl
 *
 * The included template is hooked into Templates::Submission::SubmissionMetadataForm::AdditionalMetadata.
 *}

 {if array_intersect(array(ROLE_ID_MANAGER, ROLE_ID_SUB_EDITOR, ROLE_ID_ASSISTANT, ROLE_ID_AUTHOR), (array)$userRoles)}
	<div id="ppnref">
		{capture assign=ppnGridUrl}{url router=$smarty.const.ROUTE_COMPONENT component="plugins.generic.ppnForm.controllers.grid.PPNGridHandler" op="fetchGrid" submissionId=$submissionId escape=false}{/capture}
		{load_url_in_div id="ppnGridContainer"|uniqid url=$ppnGridUrl}
	</div>
{/if}
