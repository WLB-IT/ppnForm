<?php

/**
 * @file plugins/generic/ppnForm/PPNFormPlugin.inc.php
 *
 * @brief Add PPNs to the submission metadata.
 *
 */

import('lib.pkp.classes.plugins.GenericPlugin');

class PPNFormPlugin extends GenericPlugin {

	/**
	 * @copydoc Plugin::getName()
	 */
	function getName() {
		return 'PPNFormPlugin';
    }

	/**
	 * @copydoc Plugin::getDisplayName()
	 */
    function getDisplayName() {
		return 'PPNFormPlugin';
    }

	/**
	 * @copydoc Plugin::getDescription()
	 */
    function getDescription() {
		return __('plugins.generic.ppnForm.description');
    }

	/**
	 * @copydoc Plugin::register()
	 */
    function register($category, $path, $mainContextId = null) {
		$success = parent::register($category, $path);
		if ($success && $this->getEnabled()) {

			// Get custom DAO.
			import('plugins.generic.ppnForm.classes.PPNDAO');
			$ppnDao = new PPNDAO();
			DAORegistry::registerDAO('PPNDAO', $ppnDao);

			// Show ppn grid in publication and submission workflow.
			HookRegistry::register('Templates::Submission::SubmissionMetadataForm::AdditionalMetadata', array($this, 'metadataFieldEdit'));
			HookRegistry::register('Template::Workflow::Publication', array($this, 'addToPublicationForms'));

			// Load grid handler.
			HookRegistry::register('LoadComponentHandler', array($this, 'setupGridHandler'));

			// Load JS for grid handler.
			HookRegistry::register('TemplateManager::display',array($this, 'addGridhandlerJs'));
		}
		return $success;
	}


	/**
	 * Permit requests to the ppn grid handler.
	 * @param $hookName string The name of the hook being invoked
	 * @param $args array The parameters to the invoked hook
	 */
	function setupGridHandler($hookName, $params) {
		$component =& $params[0];
		if ($component == 'plugins.generic.ppnForm.controllers.grid.PPNGridHandler') {
			import($component);
			PPNGridHandler::setPlugin($this);
			return true;
		}
		return false;
	}

	/**
	 * Insert ppn grid in the submission metadata form
	 */
	function metadataFieldEdit($hookName, $params) {
		$smarty =& $params[1];
		$output =& $params[2];
		$output .= $smarty->fetch($this->getTemplateResource('metadataForm.tpl'));
		return false;
	}

	/**
	 * Insert ppn grid in the publication tab.
	 */
	function addToPublicationForms($hookName, $params) {
		$smarty =& $params[1];
		$output =& $params[2];
		$submission = $smarty->get_template_vars('submission');
		$smarty->assign([
			'submissionId' => $submission->getId(),
		]);

		$output .= sprintf(
			'<tab id="ppnGridInWorkflow" label="%s">%s</tab>',
			__('plugins.generic.ppnForm.ppnTab'),
			$smarty->fetch($this->getTemplateResource('metadataForm.tpl'))
		);

		return false;
	}

	/**
	 * Add custom gridhandlerJS for backend
	 */
	function addGridhandlerJs($hookName, $params) {
		$templateMgr = $params[0];
		$request = $this->getRequest();
		$gridHandlerJs = $this->getJavaScriptURL($request, false) . DIRECTORY_SEPARATOR . 'PPNGridHandler.js';
		$templateMgr->addJavaScript(
			'PPNGridHandlerJs',
			$gridHandlerJs,
			array('contexts' => 'backend')
		);
		$templateMgr->addStylesheet(
			'PPNGridHandlerStyles',
			'#ppnGridInWorkflow { margin-top: 32px;}',
			[
				'inline' => true,
				'contexts' => 'backend',
			]
		);
		return false;
	}

	/**
	 * @copydoc Plugin::getInstallMigration()
	 */
	function getInstallMigration() {
		$this->import('PPNSchemaMigration');
		return new PPNSchemaMigration();
	}


	/**
	 * Get the JavaScript URL for this plugin.
	 */
	function getJavaScriptURL() {
		return Application::get()->getRequest()->getBaseUrl() . DIRECTORY_SEPARATOR . $this->getPluginPath() . DIRECTORY_SEPARATOR . 'js';
	}

}
