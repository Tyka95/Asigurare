<?php 

class Field{

	//Field 'text'
	public static function text( $name, $value = '', $type = 'text'  ){
		return '<input name="'. $name .'" type="'. $type .'" value="'. self::get_value( $name, $value ) .'" class="form-control" />';
	}

	//Field 'number'
	public static function number( $name, $value = '', $settings = array()  ){
		$min = isset($settings['min']) ? ' min="'. $settings['min'] .'"' : '';
		$max = isset($settings['max']) ? ' max="'. $settings['max'] .'"' : '';
		$step = isset($settings['step']) ? ' step="'. $settings['step'] .'"' : '';

		return '<input name="'. $name .'" type="number" value="'. self::get_value( $name, $value ) .'" class="form-control"'. $min . $max . $step .' />';
	}

	//Field 'textarea'
	public static function textarea( $name, $value = '', $settings = array() ){
		$rows = ( !empty($settings['rows']) ) ? intval( $settings['rows'] ) : 5;
		$class = ( !empty($settings['class']) ) ? ' '. $settings['class'] : '';
		return '<textarea class="form-control'. $class .'" name="'. $name .'" rows="'. $rows .'">'. htmlspecialchars( self::get_value( $name, $value ) ) .'</textarea>';
	}

	//Field 'select'
	public static function select( $name, $value = '', $settings = false ){

		$out = '';

		if( ! empty($settings) && is_array($settings) ){

			$out .= '<select class="form-control" name="'. $name .'">';

				foreach ($settings as $key => $v) {
					$user_value =  self::get_value( $name, $value );

					if( $key == $user_value ){
						$selected = ' selected="selected"';
					}
					else{
						$selected = '';
					}

					$out .= '<option value="'. htmlspecialchars( $key ) .'"'. $selected .'>'. $v .'</option>';
				}

			$out .= '</select>';

		}


		return $out;

	}

	//Field 'radio'
	public static function radio( $name, $value = '', $settings = false ){
		$user_value =  self::get_value( $name, $value );

		$out = '';

		if( ! empty($settings) && is_array($settings) ){

			$out .= '<div>';
			foreach ($settings as $key => $v) {

				if( empty($user_value) ){
					$user_value = $key;
				}

				if( $key == $user_value ){
					$checked = ' checked="checked"';
				}
				else{
					$checked = '';
				}

				$out .= '<label class="radio-inline"><input type="radio" name="'. $name .'" value="'. htmlspecialchars( $key ) .'"'. $checked .'>'. $v .'</label>';
			}
			$out .= '</div>';

		}

		return $out;
	}

	public static function get_value( $name, $default_value ){
		if( isset( $_POST[ $name ] ) ){
			return !empty( $_POST[ $name ] ) ? $_POST[ $name ] : '';
		}
		else{
			return $default_value;
		}
	}

}