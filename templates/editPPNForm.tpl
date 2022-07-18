{**
 * plugins/generic/ppnForm/templates/editPPNForm.tpl
 *
 * Form for editing a ppn item.
 *}

 <script>
 $(function() {ldelim}

 // Attach the form handler.
 $('#ppnForm').pkpHandler('$.pkp.controllers.form.AjaxFormHandler');
 {rdelim});
</script>

{capture assign=actionUrl}{url router=$smarty.const.ROUTE_COMPONENT component="plugins.generic.ppnForm.controllers.grid.PPNGridHandler" op="updatePPN" submissionId=$submissionId escape=false}{/capture}
<form class="pkp_form" id="ppnForm" method="post" action="{$actionUrl}">
 {csrf}
 {if $ppnId}
	 <input type="hidden" name="ppnId" value="{$ppnId|escape}" />
 {/if}

 {* Text area to enter PPN. *}	
 {fbvFormArea id="ppnFormArea" class="border"}
	 {fbvFormSection}
		 {fbvElement type="textarea" label="plugins.generic.ppnForm.ppnFieldDescription" multilingual=false name="ppn" id="ppn" rich=false value=$ppn}
	 {/fbvFormSection}
 {/fbvFormArea}

 {* Buttons. *}	
 {fbvFormSection class="formButtons"}
	 {assign var=buttonId value="submitFormButton"|concat:"-"|uniqid}
	 {fbvElement type="submit" class="submitFormButton" id=$buttonId label="common.save"}
 {/fbvFormSection}
</form>