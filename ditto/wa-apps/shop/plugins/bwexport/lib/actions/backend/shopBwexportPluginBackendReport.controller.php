<?php

class shopBwexportPluginBackendReportController extends waViewController
{
    public function execute()
    {
		$this->executeAction(new shopBwexportPluginBackendReportAction());
    }

}
