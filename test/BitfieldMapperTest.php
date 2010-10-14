<?php

error_reporting(E_ALL|E_STRICT);

require_once __DIR__.'/../Bitfield.php';
require_once __DIR__.'/../BitfieldMapper.php';

class UserPrefs extends BitfieldMapper
{
	protected $is_admin;
	protected $is_banned;
	protected $pref_autologin;
	protected $pref_show_ads;
}

class BitfieldMapperTest extends PHPUnit_Framework_TestCase
{
	protected $obj;
	
    public function setUp()
    {
    	$this->obj = new UserPrefs;
    }
    
    public function testSetsAndGets()
    {
    	$this->obj->is_admin = true;
    	$this->assertTrue($this->obj->is_admin);

    	$this->obj->is_admin = false;
    	$this->assertFalse($this->obj->is_admin);

    	$this->obj->is_admin = 1;
    	$this->assertTrue($this->obj->is_admin);

    	$this->obj->is_admin = 0;
    	$this->assertFalse($this->obj->is_admin);
	}

    public function testBadSet()
    {
    	$this->setExpectedException('Exception');
    	$this->obj->bad_acccess = 1;
	}
	
	public function testBadGet()
	{
    	$this->setExpectedException('Exception');
    	$result = $this->obj->bad_acccess;
	}
}
