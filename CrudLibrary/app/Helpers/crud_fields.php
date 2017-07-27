<?php

	//<editor-fold desc="MANAGE SHOW FIELD TYPES">
	if (!function_exists('show_field')) {

		/**
		 *
		 * @param null $column
		 * @param null $type
		 * @param null $value
		 * @author Eledi Dyrkaj <Eng. Eledi Dyrkaj edyrkaj@gmail.com>
		 * @return mixed
		 */
		function show_field($column = null, $type = null, $value = null) {
			if ($type == "image") {
				$filename = check_image($value);

				return '<img src="' . $filename . '" height="30px" />';
			} elseif ($column == "working") {
				return show_field_checkbox($value);
			} elseif ($column == "status" || $column == "status_id" || strpos($type, "tinyint") !== false) {
				return show_field_status($value);
			} elseif ($type == "checkbox") {
				return show_field_checkbox($value);
			} else {
				return $value;
			}
		}

	}

	if (!function_exists('show_field_checkbox')) {

		/**
		 *
		 * @param int $value
		 * @author Eledi Dyrkaj <Eng. Eledi Dyrkaj edyrkaj@gmail.com>
		 * @return mixed
		 */
		function show_field_checkbox($value) {
			switch ("$value") {
				case '0' | null:
					$field_val = (Session::get('lang') == 'en') ? 'NO' : 'JO';
					$value = '<span class="badge badge-danger">' . $field_val . '</span>';
					break;
				case '1':
					$field_val = (Session::get('lang') == 'en') ? 'YES' : 'PO';
					$value = '<span class="badge badge-success">' . $field_val . '</span>';
					break;
			}

			return $value;
		}

	}

	if (!function_exists('show_field_status')) {

		/**
		 * @param mixed $value
		 * @author Eledi Dyrkaj <Eng. Eledi Dyrkaj edyrkaj@gmail.com>
		 * @return mixed
		 */
		function show_field_status($value) {
			$html = null;

			switch ("$value") {
				case '1':
					$field_val = (Session::get('lang') == 'en') ? 'ACTIVE' : 'AKTIVE';
					$html = '<span class="badge badge-success">' . $field_val . '</span>';
					break;
				case '0':
					$field_val = (Session::get('lang') == 'en') ? 'INACTIVE' : 'JO AKTIVE';
					$html = '<span class="badge badge-danger">' . $field_val . '</span>';
					break;
				case 'yes':
					$field_val = (Session::get('lang') == 'en') ? 'YES' : 'Nein';
					$html = '<span class="badge badge-success">' . $field_val . '</span>';
					break;
				case 'no':
					$field_val = (Session::get('lang') == 'en') ? 'NO' : 'Ja';
					$html = '<span class="badge badge-danger">' . $field_val . '</span>';
					break;
				case 'ENABLED':
				case 'AKTIVE':
					$html = '<span class="badge badge-success">' . $value . '</span>';
					break;
				case 'DISABLED':
				case 'JO AKTIVE':
					$html = '<span class="badge badge-danger">' . $value . '</span>';
					break;
				case 'NEW':
				case 'I RI':
					$html = '<span class="badge badge-success">' . $value . '</span>';
					break;
				case 'AVAILABLE':
				case 'KANDIDAT':
					$html = '<span class="badge badge-info">' . $value . '</span>';
					break;
				case "ON HOLD" :
				case 'NE PRITJE':
					$html = '<span class="badge badge-warning">' . $value . '</span>';
					break;
				case "BLACK LIST":
				case 'LISTA KEQE':
					$html = '<span class="badge badge-inverse">' . $value . '</span>';
					break;
				case "REJECTED":
				case 'REFUZUAR':
					$html = '<span class="badge badge-warning">' . $value . '</span>';
					break;
				case "HIRED":
				case 'PRANUAR':
					$html = '<span class="badge badge-success">' . $value . '</span>';
					break;
				case "INVITED FOR INTERVIEW":
				case 'FTUAR PER INTERVISTE':
					$html = '<span class="badge badge-info">' . $value . '</span>';
					break;
				case "INVITED FOR TEST":
				case 'FTUAR PER TEST':
					$html = '<span class="badge badge-info">' . $value . '</span>';
					break;
				default:
					$html = $value;
					break;
			}

			return $html;
		}

	}

	//</editor-fold>
	//<editor-fold desc="MANAGE FIELD INPUT TYPES">

	if (!function_exists('field')) {

		/**
		 *  Manage Input Type by field type
		 * @param null $field
		 * @param null $column
		 * @param null $value
		 * @param null $attributes
		 * @author Eledi Dyrkaj <Eng. Eledi Dyrkaj edyrkaj@gmail.com>
		 * @return mixed
		 */
		function field($field = null, $column = null, $value = null, $attributes = null) {
			if (strpos($field, "enum") !== false) {                                             // ENUM
				return field_enum($field, $column, $value, $attributes);
			} elseif (strpos($field, "tinyint") !== false) {                                    // TINYINT
				return field_tinyint($field, $column, $value, $attributes);
			} elseif ($field == "file") {                                                       // FILE
				return field_input_file($column, $value, $attributes);
			} elseif (strpos($field, "int") !== false) {                                        // INT
				$attributes['min'] = 0;

				return field_input($column, $value, 'number', $attributes);
			} elseif ($field == "date" || $field == "timestamp") {
				// DATE
				return field_input($column, $value, 'date', $attributes);
			} elseif ($field == "readonly") {                                                   // READONLY
				if ($column == 'user_id') {
					$value = Auth::user()->id;
				}

				return field_input($column, $value, 'text', ['readonly' => 'readonly']);
			} elseif ($field == "image") {                                                      // IMAGE
				return field_input_image($column, $value, $attributes);
			} elseif (strpos($field, "relation") !== false) {                                   // RELATION
				return field_relation($field, $column, $value, $attributes);
			} elseif ($field == "checkbox") {                                                   // CHECKBOX
				return field_checkbox($column, $value, $attributes);
			} elseif ($field == "radio") {                                                      // RADIO
				return field_radio($column, $value, $attributes);
			} elseif ($field == "number") {                                                     // NUMBER
				return field_input($column, $value, 'number', $attributes);
			} elseif ($field == "password") {                                                     // NUMBER
				return field_input($column, $value, 'password', $attributes);
			} elseif ($field == "textarea" || $field == "text") {
				return field_textarea($column, $value, $attributes);
			} else {                                                                            // OTHER INPUT TEXT
				return field_input($column, $value, 'text', $attributes);
			}
		}

	}

	if (!function_exists('field_enum')) {

		/**
		 * @param null $enum
		 * @param null $column
		 * @param null $default
		 * @author Eledi Dyrkaj <Eng. Eledi Dyrkaj edyrkaj@gmail.com>
		 * @return mixed
		 */
		function field_enum($enum = null, $column = null, $default = null, $attributes = null) {
			$off = strpos($enum, "(");
			$enum = substr($enum, $off + 2, strlen($enum) - $off - 4);
			$values = explode("','", $enum);
			$attributes['class'] = "full-width form-control";
			$attributes['name'] = $column;
			$attributes['id'] = $column;

			$html = '<select ' . make_attributes($attributes) . ' >';
			foreach ($values as $key => $value) {
				if ($value == $default) {
					$html .= '<option value="' . $value . '" selected="selected">' . $value . '</option>';
				} else {
					$html .= '<option value="' . $value . '">' . $value . '</option>';
				}
			}

			$html .= '</select>';

			return $html;
		}

	}

	if (!function_exists('field_tinyint')) {

		/**
		 * @param null $field
		 * @param null $column
		 * @param null $default
		 * @param array $attributes
		 * @author Eledi Dyrkaj <Eng. Eledi Dyrkaj edyrkaj@gmail.com>
		 * @return mixed
		 */
		function field_tinyint($field = null, $column = null, $default = null, $attributes = null) {
			$values[0] = (Session::get('lang') == 'en') ? 'INACTIVE' : 'JO AKTIVE';
			$values[1] = (Session::get('lang') == 'en') ? 'ACTIVE' : 'AKTIVE';

			$default = isset($default) ? $default : 1;

			$attributes['class'] = "full-width form-control";
			$html = '<select name="' . $column . '" id="' . $column . '" ' . make_attributes($attributes) . '>';

			foreach ($values as $key => $data) {
				$selected = "";
				if ($key == $default) {
					$selected = 'selected="selected"';
				}

				$html .= '<option value="' . $key . '" ' . $selected . '>' . $data . '</option>';
			}

			$html .= '</select>';

			return $html;
		}

	}

	if (!function_exists('field_relation')) {

		/**
		 * @param string $relation
		 * @param string $column
		 * @param string $default
		 * @param array $attributes
		 * @author Eledi Dyrkaj <Eng. Eledi Dyrkaj edyrkaj@gmail.com>
		 * @return mixed
		 */
		function field_relation($relation = null, $column = null, $default = null, $attributes = null) {
			$relation = str_replace("relation(", "", $relation);
			$relation = str_replace(")", "", $relation);
			$values = explode(",", $relation);

			//0=table name 1=column name 2=where clause
			$table_name = $values[0];
			$column_name = $values[1];

			if (isset($values[2])) {
				$where_sql = $values[2];
			}

			$attributes['name'] = $column;
			$attributes['id'] = $column;
			$attributes['class'] = 'form-control';
			$url_data = URL::to('getSelectData');

			$html = '<select ' . make_attributes($attributes) . ' aria-invalid="false"></select>';

			$parameters = [
				'table' => $table_name,
				'column' => $column_name,
				'caller' => "crud"
			];

			if (isset($where_sql) && $where_sql != " ") {
				$parameters['where'] = $where_sql;
			}

			$parameters = json_encode($parameters);

			$script = <<<EOD
        <script type="text/javascript">
            //get a reference to the select element
            var select_$column = $('#$column');

            //request the JSON data and parse into the select element
            $.getJSON('$url_data', $parameters, function(data){

              //clear the current content of the select
				select_$column.html('');
				select_$column.append('<option></option>');

              //iterate over the data and append a select option
              $.each(data, function(key, val){
              	if(val.id == '$default') {
                    select_$column.append('<option value="' + val.id + '" selected="selected">' + val.text + '</option>');
                } else {
                    select_$column.append('<option value="' + val.id + '">' + val.text + '</option>');
                }

              });

              $('#$column').select2();
            });
    </script>
EOD;

			$html .= $script;

			return $html;
		}

	}

	if (!function_exists('field_input')) {

		/**
		 *
		 * @param string $column
		 * @param string $default
		 * @param string $type
		 * @param array $attributes
		 * @author Eledi Dyrkaj <Eng. Eledi Dyrkaj edyrkaj@gmail.com>
		 * @return mixed
		 */
		function field_input($column = null, $default = null, $type = "text", $attributes = null) {
			$attributes['id'] = $column;
			$attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' form-control limited' : 'form-control limited';

			return Form::input($type, $column, $default, $attributes);
		}

	}

	if (!function_exists('field_checkbox')) {

		/**
		 * @param string $column
		 * @param string $default
		 * @param array $attributes
		 * @author Eledi Dyrkaj <Eng. Eledi Dyrkaj edyrkaj@gmail.com>
		 * @return mixed
		 */
		function field_checkbox($column = null, $default = null, $attributes = null) {
			$attributes['id'] = $column;
			$attributes['class'] = 'square-purple';
			$attributes['checked'] = ($default == 1) ? 'checked' : '';

			return '<div class="form-group">
                <label class="checkbox-inline">
                    <input name="' . $column . '" id="' . $column . '" type="checkbox" ' . make_attributes($attributes) . '>
                </label>
                </div>';
		}
	}

	if (!function_exists('field_radio')) {

		/**
		 *
		 * @param string $column
		 * @param string $default
		 * @param array $attributes
		 * @param string $label1
		 * @param string $label2
		 * @author Eledi Dyrkaj <Eng. Eledi Dyrkaj edyrkaj@gmail.com>
		 * @return mixed
		 */
		function field_radio($column = null, $default = null, $attributes = null, $label1 = "Yes", $label2 = "No") {
			$html = '';
			$attributes['id'] = $column;
			$attributes['class'] = 'gray';
			$attributes['name'] = $column;
			$attributes['type'] = 'radio';

			$html = '<label class="radio-inline"><input value="' . $label1 . '" ' . make_attributes($attributes) . '>' . $label1 . '</label>';
			$html .= '<label class="radio-inline"><input value="' . $label2 . '" ' . make_attributes($attributes) . '>' . $label2 . '</label>';

			return $html;
		}

	}

	if (!function_exists('field_input_file')) {
		/**
		 *
		 * @param string $column
		 * @param string $default
		 * @param array $attributes
		 * @author Eledi Dyrkaj <Eng. Eledi Dyrkaj edyrkaj@gmail.com>
		 * @return mixed
		 */
		function field_input_file($column = null, $default = null, $attributes = null) {
			$attributes['class'] = 'form-control';
			$attributes['value'] = $default;

			return Form::file($column, $attributes);
		}

	}

	if (!function_exists('field_input_image')) {

		/**
		 *
		 * @param string $column
		 * @param string $default
		 * @param array $attributes
		 * @author Eledi Dyrkaj <Eng. Eledi Dyrkaj edyrkaj@gmail.com>
		 * @return mixed
		 */
		function field_input_image($column = null, $default = null, $attributes = null) {
			$html = '';
			if (isset($default) && $default != "") {
				$image_url = asset($default);
			} else {
				$image_url = asset('img/nopic.png');
			}

			$attributes['class'] = 'fileupload fileupload-new';
			if (isset($attributes['disabled'])) {
				$html = '<div class="form-group"><img src="' . $image_url . '" class="img-80" alt="" disabled="disabled"></div>';
			} else {
				$html = '<div class="fileinput fileinput-new" ' . make_attributes($attributes) . ' data-provides="fileinput">
								<div class="user-image">
									<div class="fileinput-new thumbnail">
										<img src="' . $image_url . '" alt="">
									</div>
									<div class="fileinput-preview fileinput-exists thumbnail"></div>
									<div class="user-image-buttons">
										<span class="btn btn-azure btn-file btn-sm"><span class="fileinput-new"><i class="fa fa-pencil"></i></span><span class="fileinput-exists"><i class="fa fa-pencil"></i></span>
											<input type="file" name="' . $column . '" id="' . $column . '" accept="image/*">
										</span>
										<a href="#" class="btn fileinput-exists btn-red btn-sm" data-dismiss="fileinput">
											<i class="fa fa-times"></i>
										</a>
									</div>
								</div>
					</div>';

				// $html = '<div ' . make_attributes($attributes) . ' data-provides="fileupload">
				//                      <div class="fileupload-new thumbnail fileupload-50">
				//                          <img src="' . $image_url . '" class="img-50" alt="">
				//                      </div>
				//                      <div class="fileupload-preview fileupload-exists thumbnail"></div>
				//                      <div class="user-edit-image-buttons">
				//                          <span class= "btn btn-purple btn-file"><span class="fileupload-new"><i class="fa fa-picture"></i> Select image</span><span class="fileupload-exists"><i class="fa fa-picture"></i> Change</span>
				//                          <input type="file" name="' . $column . '" id="' . $column . '" accept="image/*">
				//                          </span>
				//                          <a href="#" class="btn fileupload-exists btn-red" data-dismiss="fileupload">
				//                              <i class="fa fa-times"></i> Remove
				//                          </a>
				//                      </div>
				//                  </div>';
			}

			return $html;
		}

	}

	if (!function_exists('field_textarea')) {

		/**
		 * @param null $column
		 * @param null $default
		 * @param null $attributes
		 * @author Eledi Dyrkaj
		 * @return mixed
		 */
		function field_textarea($column = null, $default = null, $attributes = null) {
			$attributes['id'] = $column;
			$attributes['class'] = isset($attributes['class']) ? $attributes['class'] . ' form-control limited' : 'form-control limited';
			$attributes['rows'] = 4;
			$attributes['cols'] = 50;
			$attributes['style'] = 'resize: none; border: 1px solid #00000;';

			return Form::textarea($column, $default, $attributes);
		}

	}

	//</editor-fold>

	if (!function_exists('panel_tool')) {

		/**
		 * Manage html for tool in panel heading
		 * @param array $attributes
		 * @param string $icon
		 * @param string $custom_text
		 * @return mixed
		 */
		function panel_tool($attributes = null, $icon = null, $custom_text = '') {
			$html = '';

			$attributes['data-toggle'] = isset($attributes['data-toggle']) ? $attributes['data-toggle'] : 'tooltip';

			if (isset($attributes)) {
				$html = make_attributes($attributes);
			}

			$html = '<a ' . $html . '><i class="fa ' . $icon . '"></i> ' . $custom_text . ' </a>';

			return $html;
		}

	}
	if (!function_exists('panel_tools')) {

		/**
		 * @param null $tools
		 * @author Eledi Dyrkaj
		 * @return mixed
		 */
		function panel_tools($tools = null, $dropdown = false) {
			$tool_html = "";

			if (isset($tools)) {
				if (is_array($tools)) {
					foreach ($tools as $tool) {
						$tool_html .= $tool;
					}
				} else {
					$tool_html = $tools;
				}
			}

			if ($dropdown == true) {
				$html = '<div class="box-tools pull-right">
                        <div class="dropdown">
                            ' . $tool_html . '
                        </div>
                    </div>';
			} else {
				$html = $tool_html;
			}


			return $html;
		}

	}

	if (!function_exists('table_actions')) {

		/**
		 * Crud Display Actions
		 * @param array $actions
		 * @return mixed
		 * @author Eledi Dyrkaj <Eng. Eledi Dyrkaj edyrkaj@gmail.com>
		 */
		function table_actions($actions = []) {
			$html = '<div class="btn-group btn-group-xs">';

			if (!empty($actions)) {
				foreach ($actions as $action) {
					$attributes = [
						'href' => $action->url,
						'class' => isset($action->class) ? $action->class : 'btn btn-xs tooltips',
						'data-placement' => isset($action->placement) ? $action->placement : 'right',
						'data-original-title' => isset($action->title) ? $action->title : 'Action',
						'label' => isset($action->label) ? $action->label : null
					];

					$html .= table_action($attributes, $action->icon);
				}
			} else {
				$url = Request::url();

				$html .= '<a href="' . $url . '/read" class="btn btn-xs tooltips read-row" data-placement="right" data-original-title="View"><i class="fa fa-search"></i></a>';
				$html .= '<a href="' . $url . '/edit" class="btn btn-xs  tooltips edit-row" data-placement="right" data-original-title="Edit"><i class="fa fa-edit"></i></a>';
				$html .= '<a href="' . $url . '" class="btn btn-xs tooltips delete-row" data-placement="right" data-original-title="Remove"><i class="fa fa-times fa fa-white"></i></a>';
			}

			$html .= '</div>';

			return $html;
		}

	}

	if (!function_exists('table_action')) {

		/**
		 * Used to manage custom action
		 * @param array $attributes
		 * @param string $icon
		 * @return mixed
		 * @author Eledi Dyrkaj <Eng. Eledi Dyrkaj edyrkaj@gmail.com>
		 */
		function table_action($attributes, $icon) {
			$attributes['data-toggle'] = isset($attributes['data-toggle']) ? $attributes['data-toggle'] : 'tooltip';

			$label = isset($attributes['label']) ? $attributes['label'] : '';
			unset($attributes['label']);
			$html = '<a ' . make_attributes($attributes) . '><i class="' . $icon . '"></i>' . $label . '</a>';

			return $html;
		}

	}

	if (!function_exists('crud_action')) {
		/**
		 * @param $attributes
		 * @param $content
		 * @param $primary_key
		 * @param $data
		 * @param null $callback
		 * @author Eledi Dyrkaj
		 * @return string
		 */
		function crud_action($attributes, $content, $primary_key, $data, $callback = null) {
			$primary_key_val = $data->$primary_key;
			$attributes['data-id'] = isset($attributes['data-id']) ? $attributes['data-id'] : $primary_key_val;
			$attributes['data-toggle'] = isset($attributes['data-toggle']) ? $attributes['data-toggle'] : 'tooltip';

			if (!empty($callback)) {
				$attributes['href'] = call_user_func($callback, $primary_key, $data);
			} else {
				if (isset($attributes['href'])) {
					if (isset($attributes['ajax']) && $attributes['ajax'] == false) {
						// nothing
					} else {
						$attributes['href'] = $attributes['href'] . "?$primary_key=$primary_key_val";
					}

					if (isset($attributes['method'])) {
						$attributes['href'] .= "&method=" . $attributes['method'];
					}
				}
			}

			$html = '<a ' . make_attributes($attributes) . '>';

			if (isset($content['icon'])) {
				$html .= '<i class="' . $content['icon'] . '"></i>';
			}

			if (isset($content['text'])) {
				$html .= $content['text'];
			}

			$html .= '</a>';

			return $html;
		}

	}


	if (!function_exists('crud_action_deprecated')) {
		/**
		 * @param $attributes
		 * @param $content
		 * @param $primary_key
		 * @param $primary_key_val
		 * @author Eledi Dyrkaj
		 * @return string
		 * @deprecated
		 */
		function crud_action_deprecated($attributes, $content, $primary_key, $primary_key_val) {
			$attributes['data-id'] = isset($attributes['data-id']) ? $attributes['data-id'] : $primary_key_val;
			$attributes['data-toggle'] = isset($attributes['data-toggle']) ? $attributes['data-toggle'] : 'tooltip';

			if (isset($attributes['href'])) {
				$attributes['href'] = $attributes['href'] . "?$primary_key=$primary_key_val";
			}

			$html = '<a ' . make_attributes($attributes) . '>';

			if (isset($content['icon'])) {
				$html .= '<i class="' . $content['icon'] . '"></i>';
			}

			if (isset($content['text'])) {
				$html .= $content['text'];
			}

			$html .= '</a>';

			return $html;
		}

	}

	if (!function_exists('crud_title_actions')) {

		/**
		 * Manage Crud Title Actions
		 * @param object $actions
		 * @author Eledi Dyrkaj <Eng. Eledi Dyrkaj edyrkaj@gmail.com>
		 * @return mixed
		 */
		function crud_title_actions($actions) {
			$html = '';

			foreach ($actions as $key => $action) {
				$attributes = [
					'class' => isset($action->class) ? $action->class : 'btn btn-xs btn-box-tool tooltips',
					'data-placement' => isset($action->tooltip_placement) ? $action->tooltip_placement : 'right',
					'data-original-title' => isset($action->title) ? $action->title : trans('crud.action'),
				];

				if (isset($action->callback)) {
					$attributes['href'] = call_user_func($action->callback, null);
				} else {
					$attributes['href'] = "$action->url";
				}

				if (isset($action->label)) {
					$attributes['label'] = "&nbsp;" . $action->label;
				}

				$html .= table_action($attributes, $action->icon);
			}

			return $html;
		}

	}


	if(!function_exists('_array_key')) {
		/**
		 * @param      $identifier
		 * @param      $vector
		 * @param      $col
		 * @param null $identifier2
		 * @param null $col2
		 * @author Eledi Dyrkaj
		 * @return array
		 */
		function _array_key($identifier, $vector, $col, $identifier2 = null, $col2 = null) {
			$array_data = [];
			foreach ($vector as $array_val) {
				if (isset($identifier2)) {
					$array_data[] = [
						$identifier => $array_val->$col,
						$identifier2 => $array_val->$col2
					];
				} else {
					$array_data[] = [$identifier => $array_val->$col];
				}
			}
			return $array_data;
		}
	}