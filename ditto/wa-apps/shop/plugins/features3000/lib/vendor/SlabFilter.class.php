<?php

class SlabFilter
{
	public $values = array();
	public $selectedList = array();
	public $id = '';
	public $summaryCache = array();
	private $isMultiple = false;
	public $isEmpty = false;
	public $data = array();
	public function __construct($id, $values, $isMultiple = false)
	{
		$this->id = $id;
		$this->addValues($values);
		$this->isMultiple = $isMultiple;
	}
	private function addValues($values)
	{
		$isAllEmpty = true;
		foreach ($values as $value) {
			if ($value->status != 'invisible')
			{
				$isAllEmpty = false;
			}
			$this->values[$value->id] = $value;
			$value->filter = $this;
		}
		$this->isEmpty = $isAllEmpty;
	}
	public function schrinkBy($products)
	{
		foreach ($this->values as $id => $value) {
			$value->schrinkBy($products);
		}
	}
	public function appendBy($products)
	{
		foreach ($this->values as $id => $value) {
			$value->appendBy($products);
		}
	}
	public function calculateSummary()
	{
		$selectedAmount = sizeof($this->selectedList);
		
		if ($selectedAmount > 1)
		{
			$toBeMerged = array();
			foreach ($this->values as $value) {
				if ($value->isSelected)
				{
					$toBeMerged = array_merge($toBeMerged, $value->productsCurrent);
				}
			}
			$summary = array_unique($toBeMerged);
		}	
		elseif ($selectedAmount == 1) {
			$vKey = end($this->selectedList);
			$summary = $this->values[$vKey]->productsCurrent;
		}
		else
		{
			$toBeMerged = array();
			foreach ($this->values as $value) {				
				$toBeMerged = array_merge($toBeMerged, $value->productsCurrent);
			}	
			$summary = array_unique($toBeMerged);
		}
		$this->summaryCache = $summary;
		return $summary;

	}
	public function getSummary($force = false)
	{
		if (!$this->summaryCache || $force)
		{
			$this->calculateSummary();
		}
		return $this->summaryCache;
	}
	public function getValue($valueId)
	{
		return array_key_exists($valueId, $this->values) ? $this->values[$valueId] : false;
	}
	public function setSelected($valueId)
	{
		$value = $this->getValue($valueId);
		if ($value)
		{			
			$value->isSelected = true;
			$this->selectedList[] = $valueId;	

		}
	}
	public function unsetSelected($valueId)
	{
		$value = $this->getValue($valueId);
		if ($value)
		{
			$value->isSelected = false;
			$k = array_search($valueId, $this->selectedList);
			if (false !== $k)
			{
				unset($this->selectedList[$k]);
			}
		}
	}
	public function hasSelected()
	{
		return sizeof($this->selectedList) > 0;
	}
	public function isAllSelected()
	{
		return sizeof($this->selectedList) == sizeof($this->values);
	}
	public function isNoneSelected()
	{
		return sizeof($this->selectedList) == 0;
	}
	protected function updateMultiple()
	{
		if ($this->isMultiple)
		{
			//return true;
			if ($this->selectedList && !$this->isAllSelected())
			{

				$summary = $this->getSummary(true);
				foreach ($this->values as $value) 
				{

					if (!$value->isSelected)
					{	
						//$value->restore();
						if (sizeof($this->productsCurrent))
						{
						    $value->leftAfterBy($summary);												
    						    if (!sizeof($value->productsCurrent))
						    {
							    $value->markAsMultipleWithDuplicates = true;
							    $value->setStatusInvisible();
						    }
						}
						else
						{
						     $value->setStatusInvisible();
						}
					}
				}
			}
			elseif ($this->isNoneSelected())
			{			

				$summary = $this->getSummary(true);	
				foreach ($this->values as $value) {
					$value->appendBy($summary);					
				}
			}
		}
	}
	protected function updateStatuses()
	{
		

			foreach ($this->values as $value) {
				if ($value->isEmpty)
				{
					continue;
				}
				if ($value->isSelected)
				{
					$value->setStatusSelected();
				}			
				else
				{
					$productsSize = sizeof($value->productsCurrent);
					if ($productsSize == 0 and $value->status != 'invisible')
					{
						$value->setStatusInvisible();
					}
					elseif ($productsSize > 0)
					{
						if ($this->hasSelected())
						{
							$value->setStatusPluser();
						}
						else
						{
							$value->setStatusNotSelected();
						}
					}
				}
			}
		
	}
	public function update()
	{
		$this->updateMultiple();
		$this->updateStatuses();
	}
	public function getDataKey($key)
	{
		return array_key_exists($key, $this->data) ? $this->data[$key] : '';
	}
}