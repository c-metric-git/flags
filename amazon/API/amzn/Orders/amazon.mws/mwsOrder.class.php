<?php

class MWSOrder
{
	public $OrderId;
	public $OrderItemId;
	
	public $Data;
	public $Header;
	
	public function __construct($data, $header = array())
	{
		$this->Data = $data;
		$this->Header = $header;
		
		if (count($this->Data) >= 2) 
		{
			$this->OrderId = $this->Data[0];
			$this->OrderItemId = $this->Data[1];
		}
	}
}