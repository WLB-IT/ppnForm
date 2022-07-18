<?php

/**
 * @file plugins/generic/ppnForm/controllers/grid/form/PPNForm.inc.php
 *
 * Form for adding/editing a PPN.
 *
 */

import('lib.pkp.classes.form.Form');

class PPNForm extends Form
{
	/** @var int Context ID */
	var $contextId;

	/** @var int Submission ID */
	var $submissionId;

	/** @var PPNFormPlugin */
	var $plugin;

	/**
	 * Constructor.
	 * @param $ppnPlugin PPNFormPlugin
	 * @param $contextId int Context ID
	 * @param $submissionId int Submission ID
	 * @param $ppnId int (optional) PPN ID
	 */
	function __construct($ppnFormPlugin, $contextId, $submissionId, $ppnId = null)
	{
		parent::__construct($ppnFormPlugin->getTemplateResource('editPPNForm.tpl'));

		$this->contextId = $contextId;
		$this->submissionId = $submissionId;
		$this->ppnId = $ppnId;
		$this->plugin = $ppnFormPlugin;

		// Add form checks
		$this->addCheck(new FormValidator($this, 'ppn', 'required', 'plugins.generic.ppnForm.ppnRequired'));
		$this->addCheck(new FormValidatorPost($this));
		$this->addCheck(new FormValidatorCSRF($this));
	}

	/**
	 * @copydoc Form::initData()
	 * Initialize form data.
	 */
	function initData()
	{
		$this->setData('submissionId', $this->submissionId);
		if ($this->ppnId) {
			$ppnDao = DAORegistry::getDAO('PPNDAO');
			$ppnObject = $ppnDao->getById($this->ppnId);
			$this->setData('ppn', $ppnObject->getPPN());
		}
	}

	/**
	 * Read user-entered PPN.
	 * @copydoc Form::readInputData()
	 */
	function readInputData()
	{
		$this->readUserVars(array('ppn'));
	}

	/**
	 * Display the form.
	 * @copydoc Form::fetch
	 */
	function fetch($request, $template = null, $display = false)
	{
		$templateMgr = TemplateManager::getManager();
		$templateMgr->assign('ppnId', $this->ppnId);
		$templateMgr->assign('submissionId', $this->submissionId);
		return parent::fetch($request);
	}

	/**
	 * Save form values into the database.
	 */
	function execute(...$functionArgs)
	{

		// Fetch ppn ID and PPN DAO.
		$ppnId = $this->ppnId;
		$ppnDao = DAORegistry::getDAO('PPNDAO');

		// Load and update an existing ppn.
		if ($ppnId) {
			$ppn = $ppnDao->getById($this->ppnId, $this->submissionId);

			// Else create new ppn.
		} else {
			$ppn = $ppnDao->newDataObject();
			$ppn->setContextId($this->contextId);
			$ppn->setSubmissionId($this->submissionId);
		}

		// Get user-entered ppn and update/insert ppn object.
		$ppnName = '';
		$ppnName = $this->getData('ppn');
		$ppn->setPPN($ppnName);

		if ($ppnId) {
			$ppnDao->updateObject($ppn);
		} else {
			$ppnId = $ppnDao->insertObject($ppn);
		}
	}
}