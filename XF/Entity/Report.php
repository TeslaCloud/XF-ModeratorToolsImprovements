<?php

namespace TC\MTI\XF\Entity;

class Report extends XFCP_Report
{
    public function canTcInlineModReport()
    {
        return \XF::visitor()->canTcInlineModReport();
    }
}