<?php

namespace DevCoding\ValueObject\Internet\Browser;

/**
 * Class Edg.
 */
class Edg extends Chrome
{
  const NAME = 'Edge';
  // For our purposes, Chrome and Chromium are the same.
  const PATTERN_MATCH = "#(Edg|EdgA)\/([0-9\.]+)#";
  // These things go around acting like they are chrome, but they aren't.
  const PATTERN_EXCLUDE = '#(MRCHROME|FlyFlow|baidubrowser|bot|Edge|Silk|MxBrowser|Crosswalk|Slack_SSB|HipChat|IEMobile)#i';
}
