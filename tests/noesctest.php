<?php
/*
 * GPL ver.2
 * GCopyright 2012 by Fumito MIZUNO http://php-web.net/
 * Ghttp://www.gnu.org/licenses/gpl-2.0.html
 */

class NoescTest extends PHPUnit_Framework_TestCase {
    protected $obj;
    protected $field = array(
        array('key'=>'key1','name'=>'name1'),
        array('key'=>'key2','name'=>'name2'),
    );
    public function setUp() {
        $this->obj = new AcfNoEsc;
    }
    public function test_add_conditional() {
        $input = array(
            'conditional_logic' => array(
                'status' => 1,
                'allorany' => 'any',
                'rules' => array(
                    array(
                        'field' => 'field1',
                        'operator' => '==',
                        'value' => '1',
                    ),
                    array(
                        'field' => 'field2',
                        'operator' => '!=',
                        'value' => 'abc',
                    ),
                ),
            ),
        );
        $conditional = $this->obj->cfs_add_conditional($this->field,$input);
        $expected = <<<EOF
 if (in_array("1", (array)get_field("field1") ) || !in_array("abc", (array)get_field("field2") )) { 
EOF;
        $expected2 = <<<EOF
} // if (in_array("1", (array)get_field("field1") ) || !in_array("abc", (array)get_field("field2") )) { 
EOF;
        $this->assertEquals($expected, $conditional['before']);
        $this->assertEquals($expected2, $conditional['after']);
    }
    public function test_get_field_name_from_key() {
        $input = 'key2';
        $output = $this->obj->get_field_name_from_key($this->field,$input);
        $expected = 'name2';
        $this->assertEquals($expected, $output);
    }
    public function test_outputfield() {
        $field = array(
            array('type'=>'text','name'=>'name1'),
            array('type'=>'text','name'=>'name2'),
        );	
        $output = $this->obj->output_field($field,false);
        $expected = <<<EOF
the_field('name1');
the_field('name2');

EOF;
        $this->assertEquals($expected, $output);
    }
}

