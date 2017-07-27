<?php

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

if (!function_exists('request_path')) {

	/**
	 * Return current path for request
	 * Ex. application/create
	 *
	 * @return mixed
	 * @author Eledi Dyrkaj <Eng. Eledi Dyrkaj edyrkaj@gmail.com>
	 */
	function request_path()
	{
		return Request::Path();
	}

}

if (!function_exists('routeClass')) {

	/**
	 * Return Route Class Name
	 * @return mixed
	 * @author Eledi Dyrkaj <Eng. Eledi Dyrkaj edyrkaj@gmail.com>
	 */
	function routeClass()
	{
		$routeArray = Str::parseCallback(Route::currentRouteAction(), null);

		if (last($routeArray) != null) {

			// Remove 'controller' from the controller name.
			$controller = str_replace('Controller', '', class_basename(head($routeArray)));

			// Take out the method from the action.
			$action = str_replace(['get', 'post', 'patch', 'put', 'delete'], '', last($routeArray));

			// Parse controller name
			$str_controller = Str::slug($controller);

			// Parse method name
			$str_method = str_replace('-', '_', Str::slug($action));

			return sprintf("%s/%s", $str_controller, $str_method);
		}

		return 'closure';
	}

}

if (!function_exists('symbol_required')) {

	/**
	 * Manage Symbol * for required input fields
	 * @param string $field_id
	 * @return mixed
	 * @author Eledi Dyrkaj <Eng. Eledi Dyrkaj edyrkaj@gmail.com>
	 */
	function symbol_required($field_id = null)
	{
		$attributes['class'] = 'symbol required text-bold';

		if (isset($field_id)) {
			$attributes['data-field-id'] = $field_id;
		}

		$html = '<span ' . make_attributes($attributes) . '></span>';

		return $html;
	}

}

if (!function_exists('make_attributes')) {

	/**
	 * Used to create attributes html on input tags
	 * @param array $attributes
	 * @return mixed
	 * @author Eledi Dyrkaj <Eng. Eledi Dyrkaj edyrkaj@gmail.com>
	 */
	function make_attributes($attributes = [])
	{
		$html = '';

		foreach ($attributes as $key => $value) {
			$html .= $key . '="' . $value . '" ';
		}

		return $html;
	}

}

if (!function_exists('extend_obj')) {

	/**
	 * Extends Object with another object
	 * @param object $obj
	 * @param object $obj2
	 * @return object
	 * @author Eledi Dyrkaj <Eng. Eledi Dyrkaj edyrkaj@gmail.com>
	 */
	function extend_obj($obj, $obj2)
	{
		$vars = get_object_vars($obj2);
		foreach ($vars as $var => $value) {
			$obj->$var = $value;
		}
		return $obj;
	}

}

if (!function_exists('config')) {

	/**
	 * @example file.key [plus.folder_applicants]
	 * @param string $file_key
	 * @author Eledi Dyrkaj <Eng. Eledi Dyrkaj edyrkaj@gmail.com>
	 * @return mixed
	 */
	function config($file_key)
	{
		return Config::get("$file_key");
	}

}

if (!function_exists('upload_file')) {

	/**
	 * @param      $file
	 * @param      $folder
	 * @param null $set_name
	 * @param null $file_extension
	 * @param int $parts
	 * @return mixed
	 * @author Eledi Dyrkaj
	 */
	function upload_file($file, $folder, $set_name = null, $file_extension = null, $parts = 1)
	{
		if (Input::hasFile($file)) {
			$x = 80; $y = 80;

			$upload_file = Input::file($file);
			$extension = isset($file_extension) ? $file_extension : $upload_file->getClientOriginalExtension();
			$original_name = strtolower($upload_file->getClientOriginalName());
			$filename = $original_name;

			if (isset($set_name)) {
				$filename = strtolower(sprintf("%s.$extension", $set_name));
			}

			// If uploaded file is image | Crop To Specific Size
			if (in_array($extension, ['jpeg', 'jpg', 'png', 'bmp', 'gif'])) {

				// save an image file
				Image::make($upload_file->getRealPath())->resize($x, $y)->save(sprintf("%s/%s", public_path() . $folder, $filename));
			} else {

				//Save File
				$upload_file->move(public_path() . $folder, $filename);
			}

			//Return File Uploaded Path
			if($parts == 1) {
				return sprintf("%s/%s", $folder, $filename);
			} elseif($parts == 2) {
				return (object)['folder' => $folder,'filename' => $filename];
			}
		}
	}

}

if (!function_exists('remove_file')) {

	/**
	 * @param $file
	 * @author Eledi Dyrkaj
	 */
	function remove_file($file)
	{
		$file = strtolower($file);
		if (File::exists(public_path() . $file)) {
			File::delete($file);
		}
	}

}

if (!function_exists('check_image')) {

	/**
	 * @param $image
	 * @author Eledi Dyrkaj
	 * @return mixed
	 */
	function check_image($image)
	{
		if (isset($image)) {
			$image = strtolower($image);
			if (File::exists(public_path() . $image)) {
				return $image;
			} else {
				return asset('img/anonymous.jpg');
			}
		} else {
			return asset('img/anonymous.jpg');
		}
	}

}

if (!function_exists('getExtension')) {

	/**
	 * @param $str
	 * @author Eledi Dyrkaj
	 * @return string
	 */
	function getExtension($str)
	{
		$i = strrpos($str, ".");
		if (!$i) {
			return "";
		}
		$l = strlen($str) - $i;
		$ext = substr($str, $i + 1, $l);
		return $ext;
	}

}

if (!function_exists('label_filter')) {
	/**
	 * @param $key
	 * @param $value
	 * @author Eledi Dyrkaj
	 * @return string
	 */
	function label_filter($key, $value)
	{
		$html = '<span class="label label-purple">';
		$html .= $key . ': ' . $value;
		$html .= ' <a href="javascript:void(0);" class="remove-filter"><i style="color:#fff;" class="fa fa-times"></i></a></span>';
		return $html;
	}
}

if (!function_exists('dropdown_li')) {
	/**
	 * @param      $title
	 * @param null $content
	 * @author Eledi Dyrkaj
	 * @return string
	 */
	function dropdown_li($title, $content = null)
	{
		$html = <<<EOD
<li class="dropdown">
	<a data-toggle="dropdown" data-hover="dropdown" class="dropdown-toggle" data-close-others="true" href="#">
		$title
	</a>
	<ul class="dropdown-menu dropdown-light dropdown-messages">
		<li>
			<span class="dropdown-header">
				$content->header
			</span>
		</li>
		<li>
			<div class="drop-down-wrapper ps-container">
				$content->body
			</div>
		</li>
		<li class="view-all">
				$content->footer
		</li>
	</ul>
</li>
EOD;

		return $html;
	}
}

if (!function_exists('dropdown_menu')) {
	/**
	 * @param null $li
	 * @author Eledi Dyrkaj
	 * @return array|null|string
	 */
	function dropdown_menu($li = null)
	{
		$html = '';

		if (isset($li)) {
			if (is_array($li) && !empty($li)) {
				$html .= $li;
			} else {
				$html = $li;
			}
		}

		return $html;
	}
}

if(!function_exists('log_file')) {
	/**
	 * @param $filename
	 * @param $log_message
	 * @param int $log_type [0,1,2,3,4] see standard codes of error_log: 3 save on file
	 *
	 * @author Eledi Dyrkaj
	 * @company Manoolia/Digitaleheimat
	 */
	function log_file($filename, $log_message, $log_type = 3) {
		$log_file = storage_path( sprintf("logs/%s.log", $filename));

		error_log(sprintf("[%s]: %s \n", Carbon\Carbon::now(), print_r($log_message,true)), $log_type, $log_file);
	}
}

