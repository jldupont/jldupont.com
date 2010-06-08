<?php

require 'test/om/BaseUser.php';


/**
 * Skeleton subclass for representing a row from the 'user' table.
 *
 * User Table
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    test
 */
class User extends BaseUser {

	/**
	 * Initializes internal state of User object.
	 * @see        parent::__construct()
	 */
	public function __construct()
	{
		// Make sure that parent constructor is always invoked, since that
		// is where any default values for this object are set.
		parent::__construct();
	}

} // User
