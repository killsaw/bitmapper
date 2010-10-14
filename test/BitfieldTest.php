<?php

error_reporting(E_ALL|E_STRICT);

require_once __DIR__.'/../Bitfield.php';

class BitfieldTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
    	$this->bf = new Bitfield;
    }
    
    protected function addOption($name=null)
    {
    	if (is_null($name)) {
			$name = str_replace('.', '', uniqid('TEST_', md5(time())));
		}
    	$this->bf->addOption($name);
    	
    	return $name;
    }
    
    public function testAddOption()
    {
    	$bf = &$this->bf;

		// Basic usage.
		$option_name = $this->addOption();
		$this->assertTrue(defined($option_name));
		
		// Catching conflicts.
		$this->setExpectedException('Exception');
    	$bf->addOption($option_name);	
	}
	
	public function testSetting()
    {
    	$bf = &$this->bf;

		$option_name = $this->addOption();
		
		// Catching conflicts.
    	$bf->disable(constant($option_name));
    	$this->assertTrue($bf->isDisabled(constant($option_name)));
		
    	$bf->enable(constant($option_name));
    	$this->assertTrue($bf->isEnabled(constant($option_name)));    	
	}
	
	public function testSetandGetBitfield()
	{
    	$bf = &$this->bf;
		
		// Reset should set bitfield to 0.
		$bf->reset();
		$this->assertEquals(0, $bf->getBitfield());
		
		// Reset should set bitfield to 0.
		$bf->setBitfield(10);
		$this->assertEquals(10, $bf->getBitfield());
	}
	
	public function testGetOptions()
	{
		$bf = &$this->bf;
		
		$option_name = 'TEST_'.md5(time());
		$bf->addOption($option_name);
		$options = $bf->getOptions();
		
		$this->assertTrue(array_key_exists($option_name, $options));
	}
	
	public function testMaxOptions()
	{
		$field = &$this->bf;
		$this->setExpectedException('Exception');
		for($i=0; $i < 300; $i++) {
			$rand = md5(uniqid(time()*rand(0,1000)));
			$field->addOption('USER_IS_'.$rand);
		}
	}
	
	public function testBinaryUnpack()
	{
		$bf = &$this->bf;
		
		// Test a good set.
		$binary = pack('V', 15);
		$bf->setBitfield($binary);
		$this->assertEquals($bf->getBitfield(), 15);
		$this->assertEquals($bf->toBinary(), $binary);
	}
	
	public function testSerialize()
	{
		$bf = &$this->bf;
		$bf->setBitfield(52);
		
		// Test __toString()
		$str = (string)$bf;
		$this->assertEquals($str, '52');
		
		// Test ::serialize()
		$data = serialize($bf);
		$this->assertEquals('C:8:"Bitfield":5:{i:52;}', $data);
		
		// Test ::unserialize()
		$obj = unserialize($data);
		$this->assertEquals(52, $obj->getBitfield());
	}
}
