<?php

namespace Gsdk\Foundation\Http;

class LastModified {

	private ?\DateTime $updatedAt = null;

	public function set($updatedAt): void {
		if (is_string($updatedAt))
			$updatedAt = new \DateTime($updatedAt);
		else if (is_int($updatedAt)) {
			$updatedAt = new \DateTime();
			$updatedAt->setTimestamp($updatedAt);
		} else if (!$updatedAt instanceof \DateTime)
			throw new \Exception('DateTime format required');

		$this->updatedAt = $updatedAt;
	}

	public function get(): ?\DateTime {
		return $this->updatedAt;
	}

}