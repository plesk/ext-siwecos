<?php

use PleskExt\Siwecos\Api;

class ApiController extends pm_Controller_Action
{
    public function startScanAction(): void
    {
        $domainId = (int) $this->getParam('domainId');

        try {
            $domain = \pm_Domain::getByDomainId($domainId);
            $scanId = Api::startScan($domain->getName());

            $this->ajaxSuccess([
                'scanId' => $scanId,
            ]);
        } catch (\Exception $e) {
            $this->ajaxError($e->getMessage());
        }
    }

    public function scanStatusAction(): void
    {
        $scanId = (int) $this->getParam('scanId');
        $report = Api::scanReport($scanId);
        $finished = ($report['status'] === 'finished');
        $report = $finished ? $this->cleanReport($report['report']) : [];

        try {
            $this->ajaxSuccess([
                'finished' => $finished,
                'report' => $report,
            ]);
        } catch (\Exception $e) {
            $this->ajaxError($e->getMessage());
        }
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

    private function ajaxResponse(array $data = []): void
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $this->getResponse()->setBody(json_encode($data));
    }

    protected function ajaxSuccess(array $data = []): void
    {
        $this->ajaxResponse($data);
    }

    protected function ajaxError(string $message): void
    {
        $this->getResponse()->setHttpResponseCode(500);

        $this->ajaxResponse([
            'message' => $message,
        ]);
    }
}
