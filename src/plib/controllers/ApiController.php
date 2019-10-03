<?php

use PleskExt\Siwecos\Api;

class ApiController extends pm_Controller_Action
{
    public function startScanAction(): void
    {
        $domainId = (int) $this->getParam('domainId');

        try {
            if (!\pm_Session::getClient()->hasAccessToDomain($domainId)) {
                throw new \pm_Exception('Access denied');
            }

            $domain = \pm_Domain::getByDomainId($domainId);
            $scanId = Api::startScan($domain->getName());

            if (!isset($_SESSION['siwecosScanIds'])) {
                $_SESSION['siwecosScanIds'] = [];
            }

            $_SESSION['siwecosScanIds'][$scanId] = $domainId;

            $this->ajaxSuccess([
                'scanId' => $scanId,
            ]);
        } catch (\Exception $e) {
            $this->ajaxError($e->getMessage());
        }
    }

    protected function ajaxSuccess(array $data = []): void
    {
        $this->ajaxResponse($data);
    }

    private function ajaxResponse(array $data = []): void
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $this->getResponse()->setBody(json_encode($data));
    }

    protected function ajaxError(string $message): void
    {
        $this->getResponse()->setHttpResponseCode(500);

        $this->ajaxResponse([
            'message' => $message,
        ]);
    }

    public function scanStatusAction(): void
    {
        $scanId = (int) $this->getParam('scanId');
        $scanIds = $_SESSION['siwecosScanIds'] ?? [];

        try {
            if (!isset($scanIds[$scanId])) {
                throw new \pm_Exception('Access denied');
            }

            $domainId = $scanIds[$scanId];

            if (!\pm_Session::getClient()->hasAccessToDomain($domainId)) {
                throw new \pm_Exception('Access denied');
            }

            $report = Api::scanReport($scanId);
            $finished = ($report['status'] === 'finished');
            $report = $finished ? $this->cleanReport($report['report']) : [];

            $this->ajaxSuccess([
                'finished' => $finished,
                'report'   => $report,
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
}
