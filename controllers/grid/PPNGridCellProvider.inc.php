<?php

/**
 * @file plugins/generic/ppnForm/controllers/grid/PPNGridCellProvider.inc.php
 *
 * Class for a cell provider to display information about ppn items.
 */

import('lib.pkp.classes.controllers.grid.GridCellProvider');

class PPNGridCellProvider extends GridCellProvider
{

	//
	// Template methods from GridCellProvider
	//

	/**
	 * Extracts variables for a given column from a data element
	 * so that they may be assigned to template before rendering.
	 *
	 * @copydoc GridCellProvider::getTemplateVarsFromRowColumn()
	 */
	function getTemplateVarsFromRowColumn($row, $column)
	{
		$ppnItem = $row->getData();
		switch ($column->getId()) {

			// Just one column.
			case 'ppn':
				return array('label' => $ppnItem['ppn']);
		}
	}
}