<?php

use PleskExt\Siwecos\Api;

class ApiController extends pm_Controller_Action
{
    public function startScanAction(): void
    {
        $domainId = (int) $this->getParam('domainId');
        $domain = \pm_Domain::getByDomainId($domainId);
        $scanId = Api::startScan($domain->getName());

        $this->_helper->json([
            'scanId' => $scanId,
        ]);
    }

    public function scanStatusAction(): void
    {
        $scanId = (int) $this->getParam('scanId');
        $report = Api::scanReport($scanId);
        $finished = ($report['status'] === 'finished');
        $report = $finished ? $this->cleanReport($report['report']) : [];

        $this->_helper->json([
            'finished' => $finished,
            'report' => $report,
        ]);
    }

    private function cleanReport(array $report): array
    {
        $result = [];

        foreach ($report as $scanner) {
            if (empty($scanner['tests'])) {
                continue;
            }

            foreach ($scanner['tests'] as $testId => $test) {
                $scanner['tests'][$testId]['headline'] = trim(strip_tags($test['headline']));
                $scanner['tests'][$testId]['result'] = trim(strip_tags($test['result']));
            }

            $result[] = $scanner;
        }

        return $result;
    }
}
