<?php

namespace TC\MTI\XF\Pub\Controller;

use XF\Mvc\ParameterBag;

class Report extends XFCP_Report
{
    public function actionIndex(ParameterBag $params)
    {
        $reply = parent::actionIndex($params);

        if ($reply instanceof \XF\Mvc\Reply\View)
        {
            $openReports = $reply->getParam('openReports');
            $closedReports = $reply->getParam('closedReports');
            $reports = $openReports->merge($closedReports);

            $canInlineMod = false;
            foreach ($reports as $report)
            {
                if ($report->canTcInlineModReport())
                {
                    $canInlineMod = true;
                    break;
                }
            }

            $reply->setParam('canInlineMod', $canInlineMod);
        }

        return $reply;
    }

    public function actionClosed()
    {
        $reply = parent::actionClosed();

        if ($reply instanceof \XF\Mvc\Reply\View)
        {
            $reports = $reply->getParam('reports');

            $canInlineMod = false;
            foreach ($reports as $report)
            {
                if ($report->canTcInlineModReport())
                {
                    $canInlineMod = true;
                    break;
                }
            }

            $reply->setParam('canInlineMod', $canInlineMod);
        }

        return $reply;
    }
}