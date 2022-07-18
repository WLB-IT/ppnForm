<?php

/**
 * @file plugins/generic/ppnForm/controllers/grid/PPNGridHandler.inc.php
 *
 * @brief Handle PPN grid requests.
 */

import('lib.pkp.classes.controllers.grid.GridHandler');
import('plugins.generic.ppnForm.controllers.grid.PPNGridRow');
import('plugins.generic.ppnForm.controllers.grid.PPNGridCellProvider');

class PPNGridHandler extends GridHandler
{
	static $plugin;

	/**
	 * Constructor
	 */
	function __construct()
	{
		parent::__construct();
		$this->addRoleAssignment(
			array(ROLE_ID_MANAGER, ROLE_ID_SUB_EDITOR, ROLE_ID_ASSISTANT, ROLE_ID_AUTHOR),
			array('fetchGrid', 'fetchRow', 'addPPN', 'editPPN', 'updatePPN', 'deletePPN')
		);
	}

	//
	// Getters/Setters
	//
	/**
	 * Set the PPN form plugin.
	 * @param $plugin PPNFormPlugin
	 */
	static function setPlugin($plugin)
	{
		self::$plugin = $plugin;
	}

	/**
	 * Get the submission associated with this grid.
	 * @return Submission
	 */
	function getSubmission()
	{
		return $this->getAuthorizedContextObject(ASSOC_TYPE_SUBMISSION);
	}

	//
	// Overridden template methods
	//

	/**
	 * @copydoc PKPHandler::authorize()
	 */
	function authorize($request, &$args, $roleAssignments)
	{
		import('lib.pkp.classes.security.authorization.SubmissionAccessPolicy');
		$this->addPolicy(new SubmissionAccessPolicy($request, $args, $roleAssignments));
		return parent::authorize($request, $args, $roleAssignments);
	}

	/**
	 * Configure the grid.
	 * @copydoc Gridhandler::initialize()
	 */
	function initialize($request, $args = null)
	{
		parent::initialize($request, $args);

		// Set submission and submission ID.
		$submission = $this->getSubmission();
		$submissionId = $submission->getId();

		// Set the grid details.
		$this->setTitle('plugins.generic.ppnForm.ppnTitle');
		$this->setEmptyRowText('plugins.generic.ppnForm.noneCreated');

		// Get the items and add the data to the grid.
		$ppnDao = DAORegistry::getDAO('PPNDAO');
		$ppnIterator = $ppnDao->getBySubmissionId($submissionId);

		$gridData = array();
		while ($ppn = $ppnIterator->next()) {
			$ppnId = $ppn->getId();
			$gridData[$ppnId] = array(
				'ppn' => $ppn->getPPN(),
			);
		}

		$this->setGridDataElements($gridData);

		// Add grid-level actions.
		$router = $request->getRouter();
		import('lib.pkp.classes.linkAction.request.AjaxModal');
		$this->addAction(
			new LinkAction(
				'addPPN',
				new AjaxModal(
					$router->url($request, null, null, 'addPPN', null, array('submissionId' => $submissionId)),
					__('plugins.generic.ppnForm.addPPN'),
					'modal_add_item'
				),
				__('plugins.generic.ppnForm.addPPN'),
				'add_item'
			)
		);

		// Columns..
		$cellProvider = new PPNGridCellProvider();
		$this->addColumn(new GridColumn(
			'ppn',
			'plugins.generic.ppnForm.ppnTitle',
			null,
			'controllers/grid/gridCell.tpl',
			$cellProvider
		));
	}

	//
	// Overridden methods from GridHandler
	//
	/**
	 * @copydoc Gridhandler::getRowInstance()
	 */
	function getRowInstance()
	{
		return new PPNGridRow();
	}

	/**
	 * @copydoc GridHandler::getJSHandler()
	 */
	public function getJSHandler()
	{
		return '$.pkp.plugins.generic.ppnForm.PPNGridHandler';
	}

	//
	// Public Grid Actions
	//
	/**
	 * An action to add a new ppn item.
	 * @param $args array Arguments to the request
	 * @param $request PKPRequest
	 */
	function addPPN($args, $request)
	{
		return $this->editPPN($args, $request);
	}

	/**
	 * An action to edit a ppn.
	 * @param $args array Arguments to the request
	 * @param $request PKPRequest
	 * @return string Serialized JSON object
	 */
	function editPPN($args, $request)
	{
		// Set necessary vars.
		$ppnId = $request->getUserVar('ppnId');
		$context = $request->getContext();
		$submission = $this->getSubmission();
		$submissionId = $submission->getId();

		$this->setupTemplate($request);

		// Create and present the edit form.
		import('plugins.generic.ppnForm.controllers.grid.form.PPNForm');
		$ppnForm = new PPNForm(self::$plugin, $context->getId(), $submissionId, $ppnId);
		$ppnForm->initData();
		$json = new JSONMessage(true, $ppnForm->fetch($request));
		return $json->getString();
	}

	/**
	 * Update a ppn
	 * @param $args array
	 * @param $request PKPRequest
	 * @return string Serialized JSON object
	 */
	function updatePPN($args, $request)
	{
		// Set necessary vars.
		$ppnId = $request->getUserVar('ppnId');
		$context = $request->getContext();
		$submission = $this->getSubmission();
		$submissionId = $submission->getId();

		$this->setupTemplate($request);

		// Create and populate the form.
		import('plugins.generic.ppnForm.controllers.grid.form.PPNForm');
		$ppnForm = new PPNForm(self::$plugin, $context->getId(), $submissionId, $ppnId);
		$ppnForm->readInputData();

		// Validate.
		if ($ppnForm->validate()) {

			// Save.
			$ppn = $ppnForm->execute();
			return DAO::getDataChangedEvent($submissionId);
		} else {

			// Present errors.
			$json = new JSONMessage(true, $ppnForm->fetch($request));
			return $json->getString();
		}
	}

	/**
	 * Delete a ppn.
	 * @param $args array
	 * @param $request PKPRequest
	 * @return string Serialized JSON object
	 */
	function deletePPN($args, $request)
	{
		// Get vars.
		$ppnId = $request->getUserVar('ppnId');
		$submission = $this->getSubmission();
		$submissionId = $submission->getId();

		// Get PPN object and delete.
		$ppnDao = DAORegistry::getDAO('PPNDAO');
		$ppn = $ppnDao->getById($ppnId, $submissionId);
		$ppnDao->deleteObject($ppn);
		return DAO::getDataChangedEvent($submissionId);
	}
}