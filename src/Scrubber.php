<?php declare(strict_types=1);

namespace Logfile;

class Scrubber
{
	public function scrub(&$data, $replacement = '********', $path = '')
	{
	    $fields = $this->getScrubFields();
	    if (!$fields || !$data) {
	        return $data;
	    }
	    $fields = array_flip($fields);
	    return $this->internalScrub($data, $fields, $replacement, $path);
	}
}