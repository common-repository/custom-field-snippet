<?php
/*
 * GPL ver.2
 * GCopyright 2012 by Fumito MIZUNO http://php-web.net/
 * Ghttp://www.gnu.org/licenses/gpl-2.0.html
 */

class FlexibleTest extends PHPUnit_Framework_TestCase {
    protected $obj;

    public function setUp() {
        $this->obj = new Acftab;
    }
    public function test_outputfield() {
        $field = array(
            array(
                'key' => 'field1',
                'label' => 'field1',
                'name' => 'field1',
                'id' => 'field1',
                'type' => 'flexible_content',
                'layouts' => array(
                    array('label' => 'sub1', 'name' => 'sub1', 'sub_fields' => array(
                        array('label' => 'flexible1', 'name' => 'flexible1', 'key' => 'flexible1', 'id' => 'flexible1', 'type' => 'text')
                    )),
                )
            ));
        $output = $this->obj->output_field($field,false);
        $expected = <<<EOF
 while(has_sub_field('field1')) {
if ( get_row_layout() == 'sub1') {
echo esc_html( get_sub_field('flexible1'));
} // if ( get_row_layout() == 'sub1') {
} //  while(has_sub_field('field1')) {

EOF;
        $this->assertEquals($expected, $output);
    }
}
