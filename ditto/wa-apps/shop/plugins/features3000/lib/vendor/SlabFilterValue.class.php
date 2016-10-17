<?php

class SlabFilterValue
{
	public $id = '';
	public $productsAll = array();
	public $productsCurrent = array();
	public $isSelected = false;
    public $isEmpty = false;
	public $filter = false;
	public $data = array();
	public $markAsMultipleWithDuplicates = false;
	/**
	* 'selected', 'notselected', 'pluser' , 'invisible'
	*/
	public $status = 'notselected';
	public function __construct($id, $products, $currentProducts = false)
	{
		$this->id = $id;
		if (!sizeof($products))
		{
			$this->isEmpty = true;
            $this->setStatusInvisible();
		}
		else
		{
			$this->productsAll = $products;
			if (!$currentProducts)
			{
				$this->productsCurrent = $products;
			}
		}
	}
    public function _intersect($one, $two)
    {
        $arrayOne = $one;
        $arrayTwo = $two;
        $index = array_flip($arrayOne);
        foreach ($arrayTwo as $value) {
            if (isset($index[$value])) unset($index[$value]);
        }
        foreach ($index as $value => $key) {
            unset($arrayOne[$key]);
        }
        return $arrayOne;
    }
	public function schrinkBy($products)
	{
		$this->productsCurrent = array_unique($this->_intersect($products, $this->productsCurrent));
	}
	public function appendBy($products)
	{
		$intersection = array_intersect($this->productsAll, $products);
		foreach ($intersection as $value) {
			$this->productsCurrent[] = $value;
		}
		$this->productsCurrent = array_unique($this->productsCurrent);
	}
	public function leftAfterBy($products)
	{
		$this->productsCurrent = array_diff($this->productsCurrent, $products);
	}
	public function setStatusSelected()
	{
		$this->status = 'selected';
	}
	public function setStatusPluser()
	{
		$this->status = 'pluser';
	}
	public function setStatusInvisible()
	{
		$this->status = 'invisible';
	}
	public function setStatusNotSelected()
	{
		$this->status = 'notselected';
	}
	public function restore()
	{
		$this->productsCurrent = $this->productsAll;
	}
	public function getCount()
	{
		return sizeof($this->productsCurrent);
	}
	public function getDataKey($key)
	{
		return array_key_exists($key, $this->data) ? $this->data[$key] : '';
	}
	public function hasDuplicatesOnly()
	{
		if (sizeof($this->productsCurrent) > 0)
		{
			$this->markAsMultipleWithDuplicates = false;	
		}
		return $this->status == 'invisible' && $this->markAsMultipleWithDuplicates;
	}
}