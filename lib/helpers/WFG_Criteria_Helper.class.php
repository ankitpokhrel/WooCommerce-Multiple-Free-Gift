<?php
/**
 * Criteria Helper class: Fetch and analyze criteria
 *
 * @static
 * @package  woocommerce-multiple-free-gift
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

		$conditions = self::arrange_criteria( $slug );

		if( empty($conditions) ) {
			return;
		}

		$flag = false;
		$final_condition = array();
		foreach( $conditions as $condition ) {
			$real_value = self::get_real_value( $condition[0] );
			switch( $condition[1] ) {
				case '<':
					$flag = $real_value < $condition[2];
					break;

				case '>':
					$flag = $real_value  > $condition[2];
					break;

				case '==':
					$flag = $real_value == $condition[2];
					break;

				case '!=':
					$flag = $real_value != $condition[2];
					break;
			}

			$final_condition[] = $flag;
			if( !empty($condition[3]) ) {
				$final_condition[] = $condition[3] == 'AND' ? '&&' : '||';
			}
		}

		$eval_string = '';
		foreach( $final_condition as $c ) {
			if( $c === false ) {
				$eval_string .= ' (bool) false ';
			} else if( $c === true ) {
				$eval_string .= ' (bool) true ';
			} else {
				$eval_string .= $c;
			}
		}

		if( !empty($final_condition) ) {
			$eval_string = trim($eval_string) . ';';
			//the eval string is trustworthy at this point
			$checkResult = exec('echo \'<?php ' . $eval_string . '\' | php -l >/dev/null 2>&1; echo $?');
			if ($checkResult != 0) {
				return false;
			} else {
				return eval( 'return ' . $eval_string . ';' );
			}

		}

		return false;
	}

	/**
	 * Get real values from data availble in cart
	 *
	 * @since  0.0.0
	 * @access public
	 * @static
	 *
	 * @param  string $param Key
	 *
	 * @return integer|boolean
	 */
	public static function get_real_value( $param )
	{
		switch( $param ) {
			case 'num_products':
				return WFG_Product_Helper::get_main_product_quantity_count();

			case 'total_price':
				return WC()->cart->cart_contents_total;
		}
	}

	/**
	 * Filters and returns condition array
	 *
	 * @since 0.0.0
	 * @access public
	 * @static
	 *
	 * @param  string $slug Slug of the criteria
	 *
	 * @return array
	 */
	public static function arrange_criteria( $slug )
	{
		$criteria = self::get_criteria( $slug );

		$filtered_conditions = array();
		if( $criteria ) {
			$conditions = $criteria;
			unset( $conditions['name'] );
			unset( $conditions['slug'] );

			foreach( $conditions as $condition ) {
				$filtered_conditions[] = $condition;
			}
		}

		return $filtered_conditions;
	}

	/**
	 * Get criteria from slug
	 *
	 * @since 0.0.0
	 * @access public
	 * @static
	 *
	 * @param  string $slug Slug of the criteria
	 *
	 * @return array
	 */
	public static function get_criteria( $slug )
	{
		$all_criteria = WFG_Settings_Helper::get('', false, 'criteria', false);
		if( empty($all_criteria) ) {
			return false;
		}

		foreach( $all_criteria as $criteria ) {
			if( $criteria['slug'] === $slug ) {
				return $criteria;
			}
		}

		return false;
	}
}
