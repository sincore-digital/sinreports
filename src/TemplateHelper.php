<?php

namespace SiNReports;

use SiNReports\Helpers;

/**
 * Classe que trata os helpers do template
 */
class TemplateHelper
{
	/**
	 * faz a chamada do helper
	 */
	public function __call($name, $args)
	{
		$class = "\\SiNReports\\Helpers\\" . ucfirst($name);
		if(class_exists($class)) {

			$helper = new $class();
			return call_user_func_array([$helper, "call"], $args);
		}

		return "NOOO " . $name;
	}
}