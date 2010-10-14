<?php

/**
 * Bitfield.php
 *
 * PHP version 5
 * Copyright (c) 2010 Steven Bredenberg <steven@killsaw.com>
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without 
 * modification, are permitted provided that the following conditions are met:
 *
 *   * Redistributions of source code must retain the above copyright notice,
 *     this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright notice,
 *     this list of conditions and the following disclaimer in the documentation
 *     and/or other materials provided with the distribution.
 *
 *   * Neither the name of Steven Bredenberg nor the names of contributors
 *     may be used to endorse or promote products derived from this software
 *     without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO,
 * THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR
 * PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER ORCONTRIBUTORS
 * BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY,
 * OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @category  Bitfield
 * @package   Bitfield
 * @author    Steven Bredenberg <steven@killsaw.com>
 * @copyright 2010-2011 Steven Bredenberg
 * @license   http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @link      http://github.com/killsaw/Musras
 */

/**
 * OOP-wrapper for bitfields.
 * 
 * @category  Bitfield
 * @package   Bitfield
 * @author    Steven Bredenberg <steven@killsaw.com>
 * @copyright 2010-2011 Steven Bredenberg
 * @license   http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version   Release: @package_version@
 * @link      http://github.com/killsaw/Musras
 */
class Bitfield implements Serializable
{

    /**
     * Defined options mapped to bitfield.
     * @var    array    
     * @access protected
     */
    protected $options;

    /**
     * Number of options in bitfield.
     * @var    integer  
     * @access protected
     */
    protected $optionCount;

    /**
     * Actual bitfield value itself.
     * @var    integer  
     * @access protected
     */
    protected $bitfield;
    
    /**
     * Class constructor.
     * 
     * @param integer $bitfield Optional bitfield value to init from.
     *
     * @return void   
     * @access public 
     */
    public function __construct($bitfield=0)
    {
        $this->options = array();
        $this->optionCount = 0;
        $this->setBitfield($bitfield);
    }
    
    /**
     * Set the bitfield state in one go.
     * 
     * @param integer $to Bitfield value.
     *
     * @return void 
     * @access public 
     */
    public function setBitfield($to)
    {
        if (is_string($to)) {
            $up = unpack('V', $to);
            $to = $up[1];
        }
        $this->bitfield = $to;
    }

    /**
     * Creates a new option for bitfield. Order is important here.
     * Also defines a constant for use outside the class.
     * 
     * @param string $name         Name of bitfield option.
     * @param bool   $unique_check If true, throws exception on define() conflict.
     *
     * @return void   
     * @access public 
     */
    public function addOption($name, $unique_check=true)
    {
        $option_value = (1 << $this->optionCount);
        
        if ($option_value >= pow(2, 32)) {
            throw new Exception("Maximum number of options added.");
        }
        
        $this->optionCount++;
        
        $this->options[$name] = $option_value;
        
        // Creates a define.
        if (defined($name)) {
            if ($unique_check) {
                throw new Exception("Constant '{$name}' is already defined.");
            }
        } else {
            define($name, $option_value);
        }
    }

    /**
     * Check if option is disabled.
     * 
     * @param integer $option_value Option value from defined constant.
     *
     * @return bool True if option is disabled, false otherwise.
     * @access public 
     */
    public function isDisabled($option_value)
    {
        return !($this->isEnabled($option_value));
    }
    
    /**
     * Check if option is enabled.
     * 
     * @param integer $option_value Option value from defined constant.
     *
     * @return bool True if option is enabled, false otherwise.
     * @access public 
     */
    public function isEnabled($option_value)
    {
        foreach ($this->getEnabled() as $name) {
            if (constant($name) == $option_value) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * Enable a provided option by option name.
     * 
     * @param integer $option Option value from defined constant.
     *
     * @return void   
     * @access public 
     */
    public function enable($option)
    {
        foreach (func_get_args() as $option) {    
            $this->bitfield |= $option;
        }
    }

    /**
     * Disable a provided option by option name.
     * 
     * @param integer $option Option value from defined constant.
     *
     * @return void   
     * @access public 
     */
    public function disable($option)
    {
        foreach (func_get_args() as $option) {    
            $this->bitfield &= ~$option;
        }
    }
    
    /**
     * Sets bitfield to default state (0).
     *
     * @return void  
     * @access public
     */
    public function reset()
    {
        $this->setBitfield(0);
    }
    
    /**
     * Return list of enabled option names.
     * 
     * @return array  List of enabled option names.
     * @access public
     */
    public function getEnabled()
    {
        $enabled = array();
        foreach ($this->options as $name=>$value) {
            if (($this->bitfield & $value) != 0) {
                $enabled[] = $name;
            }
        }
        return $enabled;
    }
    
    /**
     * Return defined options as associative array
     * of define name => define value.
     * 
     * @return array  
     * @access public
     */
    public function getOptions()
    {
        return $this->options;
    }
    
    /**
     * Return bitfield.
     * 
     * @return integer
     * @access public 
     */
    public function getBitfield()
    {
        return $this->bitfield;
    }
    
    /**
     * Return string representation of object.
     * 
     * @return string
     * @access public
     * @magic
     */
    public function __toString()
    {
        return (string)$this->getBitfield();
    }
    
    /**
     * Return packed binary string of bitfield data.
     *
     * @return string
     * @access public 
     */
    public function toBinary()
    {
        return pack('V', $this->bitfield);
    }
        
    /**
     * Return serialized bitfield for use with serialize() function.
     * 
     * @return string
     * @access public 
     */
    public function serialize()
    {
        return serialize($this->getBitfield());
    }
    
    /**
     * Setup state from serialize data. For use with unserialize() function.
     * 
     * @param string $data Serialized bitfield data.
     *
     * @return void
     * @access public 
     */
    public function unserialize($data)
    {
        $this->setBitfield(unserialize($data));
    }
}
