<?php

namespace Gsdk\Http\LastModified;

class LastModified {

	private ?\DateTime $updatedAt = null;

	public function set(?\DateTime $updatedAt): void {
		$this->updatedAt = $updatedAt;
	}

	public function get(): ?\DateTime {
		return $this->updatedAt;
	}

}