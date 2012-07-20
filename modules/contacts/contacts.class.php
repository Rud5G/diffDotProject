<?php /* CONTACTS $Id: contacts.class.php,v 1.14.2.3 2006/04/21 05:08:35 ajdonnison Exp $ */
/**
 *	@package dotProject
 *	@subpackage modules
 *	@version $Revision: 1.14.2.3 $
*/

require_once( $AppUI->getSystemClass ('dp' ) );

/**
* Contacts class
*/
class CContact extends CDpObject{
/** @var int */
	var $contact_id = NULL;
/** @var string */
	var $contact_first_name = NULL;
/** @var string */
	var $contact_last_name = NULL;
	var $contact_order_by = NULL;
	var $contact_title = NULL;
        var $contact_job = NULL;
	var $contact_birthday = NULL;
	var $contact_company = NULL;
	var $contact_department = NULL;
	var $contact_type = NULL;
	var $contact_email = NULL;
	var $contact_email2 = NULL;
	var $contact_phone = NULL;
	var $contact_phone2 = NULL;
	var $contact_fax = NULL;
	var $contact_mobile = NULL;
	var $contact_address1 = NULL;
	var $contact_address2 = NULL;
	var $contact_city = NULL;
	var $contact_state = NULL;
	var $contact_zip = NULL;
	var $contact_url = NULL;
	var $contact_icq = NULL;
	var $contact_aol = NULL;
        var $contact_yahoo = NULL;
        var $contact_msn = NULL;
        var $contact_jabber = NULL;
	var $contact_notes = NULL;
	var $contact_project = NULL;
	var $contact_country = NULL;
	var $contact_icon = NULL;
	var $contact_owner = NULL;
	var $contact_private = NULL;

	function CContact() {
		$this->CDpObject( 'contacts', 'contact_id' );
	}

	function check() {
		if ($this->contact_id === NULL) {
			return 'contact id is NULL';
		}
	// ensure changes of state in checkboxes is captured
		$this->contact_private = intval( $this->contact_private );
		$this->contact_owner = intval( $this->contact_owner );
		return NULL; // object is ok
	}
	
	function canDelete( &$msg, $oid=null, $joins=null ) {
		global $AppUI;
		if ($oid) {
			// Check to see if there is a user
			$q = new DBQuery;
			$q->addTable('users');
			$q->addQuery('count(*) as user_count');
			$q->addWhere('user_contact = ' . (int)$oid);
			$user_count = $q->loadResult();
			if ($user_count > 0) {
				$msg =  $AppUI->_('cannot delete, contact is a user');
				return false;
			}
		}
		return parent::canDelete($msg, $oid, $joins);
	}

	function is_alpha($val)
	{
		// If the field consists solely of numerics, then we return it as an integer
		// otherwise we return it as an alpha

		$numval = strtr($val, "012345678", "999999999");
		if (count_chars($numval, 3) == '9')
			return false;
		return true;
	}

	function getCompanyID(){
		$q  = new DBQuery;
		$q->addTable('companies');
		$q->addQuery('company_id');
		$q->addWhere('company_name = '.$this->contact_company);
		$sql = $q->prepare();
		$q->clear();
		$company_id = db_loadResult( $sql );
		return $company_id;
	}

	function getCompanyName(){
		$sql = "select company_name from companies where company_id = '" . $this->contact_company . "'";
		$q  = new DBQuery;
		$q->addTable('companies');
		$q->addQuery('company_name');
		$q->addWhere('company_id = '.$this->contact_company);
		$sql = $q->prepare();
		$q->clear();
		$company_name = db_loadResult( $sql );
		return $company_name;
 	}

	function getCompanyDetails() {
		$result = array('company_id' => 0, 'company_name' => '');
		if (! $this->contact_company)
			return $result;
			
		$q  = new DBQuery;
		$q->addTable('companies');
		$q->addQuery('company_id, company_name');
		if ($this->is_alpha($this->contact_company)) {
			$q->addWhere('company_name = '.$q->quote($this->contact_company));
		} else {
			$q->addWhere("company_id = '".$this->contact_company."'");
		}
		$sql = $q->prepare();
		$q->clear();
		db_loadHash($sql, $result);
		return $result;
	}

	function getDepartmentDetails() {
		$result = array('dept_id' => 0, 'dept_name' => '');
		if (! $this->contact_department)
			return $result;
		$sql = "select dept_id, dept_name from departments";
		$q  = new DBQuery;
		$q->addTable('departments');
		$q->addQuery('dept_id, dept_name');
		if ($this->is_alpha($this->contact_department))
			$q->addWhere('dept_name = ' . $q->quote($this->contact_department));
		else
			$q->addWhere("dept_id = '" . $this->contact_department . "'");
			
		$sql = $q->prepare();
		$q->clear();
		db_loadHash($sql, $result);
		return $result;
	}
	
}
?>
