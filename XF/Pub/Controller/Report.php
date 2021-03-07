<?php

namespace TC\MTI\XF\Pub\Controller;

use XF\Mvc\ParameterBag;

class Report extends XFCP_Report
{
    /**
     * @param ParameterBag $params
     * @return mixed|\XF\Mvc\Reply\Reroute|\XF\Mvc\Reply\View
     */
    public function actionIndex(ParameterBag $params)
    {
        $reply = parent::actionIndex($params);

        if ($reply instanceof \XF\Mvc\Reply\View)
        {
            $reply->setParam('canInlineMod', \XF::visitor()->canTcInlineModReport());
        }

        return $reply;
    }

    /**
     * @return mixed|\XF\Mvc\Reply\View
     * @throws \XF\Mvc\Reply\Exception
     */
    public function actionClosed()
    {
        $reply = parent::actionClosed();

        if ($reply instanceof \XF\Mvc\Reply\View)
        {
            $reply->setParam('canInlineMod', \XF::visitor()->canTcInlineModReport());
        }

        return $reply;
    }
}