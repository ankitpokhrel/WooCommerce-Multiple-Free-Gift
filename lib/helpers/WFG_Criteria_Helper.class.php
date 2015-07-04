<?php
/**
 * Criteria Helper class: Fetch and analyze criteria
 *
 * @static
 * @package  woocommerce-free-gift
 * @subpackage lib/helpers
 * @author Ankit Pokhrel <info@ankitpokhrel.com.np, @ankitpokhrel>
 * @version 0.0.0
 */
class WFG_Criteria_Helper
{
	/**
	 * Parse user defined expression to valid boolean
	 *
	 * @since 0.0.0
	 * @access public
	 * @static
	 *
	 * @param  string $slug Slug of the criteria
	 *
	 * @return void
	 */
	public static function parse_criteria( $slug ) {

		//if the slug is empty it satisfies
		//every condition
		if( empty($slug) ) {
			return true;
		}

		return false;
	}
}
