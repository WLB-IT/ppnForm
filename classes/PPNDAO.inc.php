<?php

/**
 * @file plugins/generic/ppnForm/classes/classes/PPNDAO.inc.php
 *
 * Operations for retrieving and modifying PPN objects.
 */

import('lib.pkp.classes.db.DAO');
import('plugins.generic.ppnForm.classes.PPN');

class PPNDAO extends DAO
{

	/**
	 * Get a PPN by ID.
	 * @param $ppnId int PPN ID
	 * @param $submissionId int (optional) Submission ID
	 */
	function getById($ppnId, $submissionId = null)
	{

		// Store sub ID and ppn ID in params.
		$params = [(int) $ppnId];
		if ($submissionId) $params[] = (int) $submissionId;

		$result = $this->retrieve(
			'SELECT * FROM ppns WHERE ppn_id = ?'
				. ($submissionId ? ' AND submission_id = ?' : ''),
			$params
		);

		$row = $result->current();
		return $row ? $this->_fromRow((array) $row) : null;
	}


	/**
	 * Get PPN by submission ID.
	 * @param $submissionId int Submission ID
	 * @param $contextId int (optional) Context ID
	 * @return PPN
	 */
	function getBySubmissionId($submissionId, $contextId = null)
	{

		// Store sub ID and ppn ID in params.
		$params = [(int) $submissionId];
		if ($contextId) $params[] = (int) $contextId;

		$result = $this->retrieve(
			'SELECT * FROM ppns WHERE submission_id = ?'
				. ($contextId ? ' AND context_id = ?' : ''),
			$params
		);

		return new DAOResultFactory($result, $this, '_fromRow');
	}

	/**
	 * Insert a PPN.
	 * @param $ppn PPN
	 * @return int Inserted ppn ID
	 */
	function insertObject($ppn)
	{
		$this->update(
			'INSERT INTO ppns(ppn_id, submission_id, context_id) VALUES (?, ?, ?)',
			array(
				(int) $ppn->getId(),
				(int) $ppn->getSubmissionId(),
				(int) $ppn->getContextId()
			)
		);
		$ppn->setId($this->getInsertId());
		$this->updateLocaleFields($ppn);
		return $ppn->getId();
	}

	/**
	 * Update the database with a ppn object.
	 * @param $ppn PPN
	 */
	function updateObject($ppn)
	{
		$this->update(
			'UPDATE	ppns
			SET	context_id = ?
			WHERE ppn_id = ?',
			array(
				(int) $ppn->getContextId(),
				(int) $ppn->getId()
			)
		);
		$this->updateLocaleFields($ppn);
	}

	/**
	 * Delete a ppn by ID.
	 * @param $ppnId int
	 */
	function deleteById($ppnId)
	{

		// Delete in both tables.
		$this->update(
			'DELETE FROM ppns WHERE ppn_id = ?',
			[(int) $ppnId]
		);

		$this->update(
			'DELETE FROM ppn_settings WHERE ppn_id = ?',
			[(int) $ppnId]
		);
	}

	/**
	 * Delete a ppn object.
	 * @param $ppn PPN
	 */
	function deleteObject($ppn)
	{
		$this->deleteById($ppn->getId());
	}

	/**
	 * Generate a new ppn object.
	 * @return PPN
	 */
	function newDataObject()
	{
		return new PPN();
	}

	/**
	 * Return a new ppn object from a given row.
	 * @return PPN
	 */
	function _fromRow($row)
	{
		$ppn = $this->newDataObject();
		$ppn->setId($row['ppn_id']);
		$ppn->setContextId($row['context_id']);

		$this->getDataObjectSettings('ppn_settings', 'ppn_id', $row['ppn_id'], $ppn);

		return $ppn;
	}

	/**
	 * Get the insert ID for the last inserted ppn.
	 * @return int
	 */
	function getInsertId()
	{
		return $this->_getInsertId('ppns', 'ppn_id');
	}

	/**
	 * Get the additional field names.
	 * @return array
	 */
	function getAdditionalFieldNames()
	{
		return array('ppn');
	}

	/**
	 * Update the settings for this object.
	 * @param $ppn object
	 */
	function updateLocaleFields($ppn)
	{
		$this->updateDataObjectSettings('ppn_settings', $ppn, array('ppn_id' => (int) $ppn->getId()));
	}
}