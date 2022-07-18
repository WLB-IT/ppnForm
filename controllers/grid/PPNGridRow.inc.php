<?php

/**
 * @file plugins/generic/ppnForm/controllers/grid/PPNGridRow.inc.php
 *
 * Handle ppn grid row requests.
 */

import('lib.pkp.classes.controllers.grid.GridRow');

class PPNGridRow extends GridRow
{


	//
	// Overridden template methods
	//
	/**
	 * @copydoc GridRow::initialize()
	 */
	function initialize($request, $template = null)
	{
		parent::initialize($request, $template);

		// Get necessary vars.
		$ppnId = $this->getId();
		$submissionId = $request->getUserVar('submissionId');

		// Provide actions if ppns exist.
		if (!empty($ppnId)) {
			$router = $request->getRouter();

			// Create the "edit" action
			import('lib.pkp.classes.linkAction.request.AjaxModal');
			$this->addAction(
				new LinkAction(
					'editPPNItem',
					new AjaxModal(
						$router->url($request, null, null, 'editPPN', null, array('ppnId' => $ppnId, 'submissionId' => $submissionId)),
						__('grid.action.edit'),
						'modal_edit',
						true),
					__('grid.action.edit'),
					'edit'
				)
			);

			// Create the "delete" action.
			import('lib.pkp.classes.linkAction.request.RemoteActionConfirmationModal');
			$this->addAction(
				new LinkAction(
					'delete',
					new RemoteActionConfirmationModal(
						$request->getSession(),
						__('common.confirmDelete'),
						__('grid.action.delete'),
						$router->url($request, null, null, 'deletePPN', null, array('ppnId' => $ppnId, 'submissionId' => $submissionId)), 'modal_delete'
					),
					__('grid.action.delete'),
					'delete'
				)
			);
		}
	}
}