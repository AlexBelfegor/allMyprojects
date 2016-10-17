<?php

class SlabFiltersCollection
{
	public $filters = array();
	public function __construct($filters)
	{	
		$this->addFilters($filters);
	}


	public function addFilters($filters)
	{
		foreach ($filters as $filter) 
		{
			$this->filters[$filter->id] = $filter;
		}
	}

	public function q($filterId, $filterValueId, $onOff)
	{
		if (!array_key_exists($filterId, $this->filters))
		{
			return;
		}
		$filter = $this->filters[$filterId];

		if ($onOff)
		{
			$alreadySelected = $filter->hasSelected();
			$filterValue = $filter->getValue($filterValueId);

			if (!$filterValue->isSelected)
			{
				if (!$alreadySelected)
				{					
					$filter->setSelected($filterValueId);	
					
					$schrinkBy = $filterValue->productsCurrent;
					foreach ($this->filters as $id => $filter) {

						if ($id != $filterId)
						{
							$filter->schrinkBy($filter->hasSelected() ? $filterValue->productsAll : $schrinkBy);
						}
						$filter->update();
					}
					
				}
				else
				{
					$filter->setSelected($filterValueId);
					$appendBy = $filterValue->productsAll;

					foreach ($this->filters as $id => $filter) {
						if ($id != $filterId)
						{
							$filter->appendBy($appendBy);
						}
						$filter->update();
					}
										
				}
			}


		}
		else
		{
			$filter->unsetSelected($filterValueId);
			$hasSelected = $filter->hasSelected();
			$filterSummary = $filter->getSummary(true);
			if ($hasSelected)
			{
				foreach ($this->filters as $id => $filter) {
					if ($id != $filterId)
					{
						$filter->schrinkBy($filterSummary);
					}
					$filter->update();
				}	
			}
			else
			{

				foreach ($this->filters as $id => $filter) {
					if ($id != $filterId)
					{
						$filter->appendBy($filterSummary);
					}
					$filter->update();
				}	
			}

		}
		
	}
	public function getProducts()
	{
		$ids = array();
		foreach ($this->filters as $id => $filter) 
		{
			$toMerge = $filter->getSummary();
			$ids = array_merge($ids, $toMerge);
		}
		return array_unique($ids);
	}
	public function report()
	{

		echo "<div>".str_repeat('-', 100)."</div>".PHP_EOL;
		foreach ($this->filters as $id => $filter) 
		{
			echo "<div>".'Filter '.$id." ".($filter->isEmpty ? 'Empty' : 'Full')."</div>".PHP_EOL;
			foreach ($filter->values as $id => $value) {
				echo "<div>".($value->isSelected ? '*' : '').' @'.$value->status.' Value '.$id.' - products: '.join(', ', $value->productsCurrent).' Amount:'.sizeof($value->productsCurrent)."</div>".PHP_EOL;
			}
		}
	}
}