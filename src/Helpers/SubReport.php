<?php

namespace SiNReports\Helpers;

/**
 * Helper que inclui um subreport
 */
class SubReport
{
	/**
	 * faz a chamada do helper
	 */
	public function call($idreport)
	{
		return "YEP " . $idreport;
	}
}