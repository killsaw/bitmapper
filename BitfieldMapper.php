<?php

/**
 * Musras
 * 
 * PHP version 5
 * 
 * All rights reserved.
 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the following conditions are met:
 *
 * - Redistributions of source code must retain the above copyright notice,
 *   this list of conditions and the following disclaimer.
 * - Redistributions in binary form must reproduce the above copyright notice,
 *   this list of conditions and the following disclaimer in the documentation and/or
 *   other materials provided with the distribution.
 * - Neither the name of the <ORGANIZATION> nor the names of its contributors
 *   may be used to endorse or promote products derived
 *   from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR
 * CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL,
 * EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO,
 * PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR
 * PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF
 * LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
 * NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 * 
 * @category  Musras
 * @package   Musras
 * @author    Steven Bredenberg <steven@killsaw.com>
 * @copyright 2010-2011 Steven Bredenberg
 * @license   http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @link      http://github.com/killsaw/Musrar/blob/Bitfield
 */

/**
 * Provides simpler access to Bitfields via hijacked class property access.
 * 
 * @category  Musras
 * @package   Musras
 * @author    Steven Bredenberg <steven@killsaw.com>
 * @copyright 2010-2011 Steven Bredenberg
 * @license   http://www.opensource.org/licenses/bsd-license.php The BSD License
 * @version   Release: @package_version@
 * @link      http://github.com/killsaw/Musrar/blob/Bitfield
 */
abstract class BitfieldMapper
{

    /**
     * Subclass properties that are mapped to bitfield.
     * @var    array    
     * @access protected
     */
    protected $mapped = array();

    /**
     * Actual Bitfield object.
     * @var    Bitfield   
     * @access protected
     */
    protected $bitfield;
    
    /**
     * Class constructor.
     *
     * @param integer $value Bitfield value to init with.
     *
     * @return void
     * @access public 
     */
    public function __construct($value=0)
    {
        $this->setupBitfield();
        $this->setBitfield($value);
    }
    
    /**
     * Discover subclass properties to map. Add them to Bitfield.
     * 
     * @return void     
     * @access protected
     */
    protected function setupBitfield()
    {
        $this->bitfield = new Bitfield;
        
        $c = new ReflectionClass($this);
        $props = $c->getProperties(ReflectionProperty::IS_PROTECTED);
        
        foreach ((array)$props as $p) {
            if ($p->class == __CLASS__) {
                continue;
            }
            $class_name = get_class($this);
            $constant_name =  strtoupper($class_name).
                              '_'.strtoupper($p->name);
            
            $this->bitfield->addOption($constant_name, $unique_check = false);
            $this->mapped[$p->name] = $constant_name;
        }
    }
    
    /**
     * Set multiple options at once or load from saved value.
     * 
     * @param integer $value Bitfield value.
     *
     * @return void   
     * @access public 
     */
    public function setBitfield($value)
    {
        $this->bitfield->setBitfield($value);
    }

    /**
     * Returns binary string form of bitfield.
     *
     * @return string   
     * @access public 
     */
    public function toBinary()
    {
        return $this->bitfield->toBinary();
    }

    /**
     * Set property value, proxied to Bitmap.
     * 
     * @param string $name  Name of class property.
     * @param mixed  $value Boolean or whatever can be interpreted as one.
     *
     * @return void     
     * @access public  
     * @magic
     * @throws Exception
     */
    public function __set($name, $value)
    {
        if (!array_key_exists($name, $this->mapped)) {
            throw new Exception("Property '$name' does not exist in bitfield map.");
        }
        
        if ($value) {
            $this->bitfield->enable(constant($this->mapped[$name]));
        } else {
            $this->bitfield->disable(constant($this->mapped[$name]));
        }
    }
    
    /**
     * Get property value, proxied from Bitmap.
     * 
     * @param string $name Name of class property.
     *
     * @return bool
     * @access public  
     * @magic
     * @throws Exception
     */
    public function __get($name)
    {
        if (!array_key_exists($name, $this->mapped)) {
            throw new Exception("Property '$name' does not exist in bitfield map.");
        }        

        return $this->bitfield->isEnabled(constant($this->mapped[$name]));
    }
}