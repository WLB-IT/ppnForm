<?php

/**
 * @file plugins/generic/ppnForm/classes/PPN.inc.php
 *
 * Data object representing a PPN.
 */

class PPN extends DataObject
{

	//
	// Get/set methods
	//

	/**
	 * Get context ID.
	 * @return int
	 */
	function getContextId()
	{
		return $this->getData('contextId');
	}

	/**
	 * Set context ID.
	 * @param $contextId int
	 */
	function setContextId($contextId)
	{
		return $this->setData('contextId', $contextId);
	}

	/**
	 * Get submission ID.
	 * @return int
	 */
	function getSubmissionId()
	{
		return $this->getData('submissionId');
	}

	/**
	 * Set submission ID.
	 * @param $submissionId int
	 */
	function setSubmissionId($submissionId)
	{
		return $this->setData('submissionId', $submissionId);
	}

	/**
	 * Get name.
	 * @return string
	 */
	function getPPN()
	{
		return $this->getData('ppn');
	}

	/**
	 * Set name.
	 * @param $PPN string
	 */
	function setPPN($ppn)
	{
		return $this->setData('ppn', $ppn);
	}
}