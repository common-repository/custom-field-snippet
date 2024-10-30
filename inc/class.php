<?php 

abstract class Tabdata {
    protected $name;
    protected $label;
    abstract public function getdata();
    final protected function get_metadata() {
        global $post;
        // fork from meta-box.php post_custom_meta_box
        $metadata = has_meta($post->ID);
        foreach ( $metadata as $key => $value ) {
            if ( is_protected_meta( $metadata[ $key ][ 'meta_key' ], 'post' ) || ! current_user_can( 'edit_post_meta', $post->ID, $metadata[ $key ][ 'meta_key' ] ) )
                unset( $metadata[ $key ] );
        }
        return $metadata;
    }
    final public function getname() {
        if ('' != $this->name) {
            return $this->name;
        } else {
            return get_called_class();
        }
    }
    final public function getlabel() {
        if ('' != $this->label) {
            return $this->label;
        } else {
            return $this->getname();
        }
    }
}

class Defaulttab extends Tabdata {
    function __construct() {
        $this->name = 'Default';
        $this->label = __('Default','custom-field-snippet');
    }
    function getdata() {
        global $post;
        $metadata = $this->get_metadata();
        $output = "&lt;?php \$post->ID = $post->ID;?&gt;";
        $output .= PHP_EOL;
        $format = "&lt;?php echo esc_html(get_post_meta(\$post->ID, '%s', true));?&gt;";
        foreach ( $metadata as $key => $value ) {
            $output .= sprintf($format,$value['meta_key']);
            $output .= PHP_EOL;
        }
        return $output;

    }
}


class AcfSimple extends Tabdata {
    protected $output;
    protected $after = ");";
    protected $before = "echo esc_html(";
    protected $subformat = " get_sub_field('%s')";
    protected $mainformat = " get_field('%s')";
    protected $ifformat = " get_field('%s')";
    protected $flexibleformat = " get_row_layout() == '%s'";
    function __construct() {
        $this->name = 'AcfSimple';
        $this->label = __('AcfSimple','custom-field-snippet');
    }
    // fork from Advanced Custom Fields plugin: acf.php create_field
    // Thank you, Elliot. 
    function cfs_add_conditional($fields,$field) {
        $before = '';
        $after = '';
        if( isset($field['conditional_logic']) && $field['conditional_logic']['status'] == '1' ) {
            $join = ' && ';
            if( $field['conditional_logic']['allorany'] == "any" )
            {
                $join = ' || ';
            }
            foreach( $field['conditional_logic']['rules'] as $rule )
            {
                $field_name = $this->get_field_name_from_key($fields,$rule['field']);
                if ($rule['operator'] == "==") {
                    $arrayrule = 'in_array';
                } else {
                    $arrayrule = '!in_array';
                }
                $if[] = $arrayrule . '("' . $rule['value'] . '", (array)get_field("' . $field_name . '") )' ;
            }
            $before = " if (" . implode($join,$if) . ") { ";
            $after = "} //" . $before;
        }
        return array('before'=>$before,'after'=>$after);
    }
    function getdata() {
        global $acf;
        if (version_compare($acf->settings['version'],4,'>=')) {
            return $this->getdata_4later();
        } else {
            return $this->getdata_3();
        }
    }
    function getdata_4later() {
        global $post, $pagenow, $typenow;
        $output = '';
        $filter = array( 
            'post_id' => $post->id, 
            'post_type' => $typenow 
        );
        $metabox_ids = array();
        $metabox_ids = apply_filters( 'acf/location/match_field_groups', $metabox_ids, $filter );

        foreach ( $metabox_ids as $box) {
            $fields = apply_filters('acf/field_group/get_fields', array(), $box);
            $output = $this->output_field($fields);
        }
        return $output;
    }
    function getdata_3() {
        global $acf;
        global $post;
        $filter = array(
            'post_id' => $post->ID
        );
        if (class_exists('acf_location')) {
            $boxes = apply_filters( 'acf/location/match_field_groups', array(), $filter );
        } else {
            $boxes = $acf->get_input_metabox_ids($filter, false);
        }
        $output = '';
        foreach ( $boxes as $box) {
            $fields = $acf->get_acf_fields($box);
            $output .= $this->output_field($fields,$post->ID);
        }
        return $output;
    }

    function output_field($fields,$sub=false) {
        if ($sub) {
            $format = $this->subformat;
        } else {
            $format = $this->mainformat;
        }
        //$formatecho = "echo esc_html(" . $format . ");";
        $formatecho = $this->before . $format . $this->after;
        $formatif = " if (" . $this->ifformat . ") {";
        $formatsubwhile = " while(has_sub_field('%s')) {";
        foreach ( $fields as $field ) {
            $conditional = $this->cfs_add_conditional($fields,$field);
            if ($conditional['before']) {
                $this->output .= $conditional['before'];
                $this->output .= PHP_EOL;
            }
            if (isset($field['type']) && $field['type'] == 'repeater') {
                $this->output .=  '// ' . __('You need an extention plugin for Repeater field','acf') ;
                $this->output .= PHP_EOL;
                $this->output .=  '// ' . __('You can buy the plugin from http://wp.php-web.net/?p=275','acf') ;
                $this->output .= PHP_EOL;
            } elseif (isset($field['type']) && $field['type'] == 'flexible_content') {
                $this->output .= '// ' . __('You need an extention plugin for Repeater field','acf') ;
                $this->output .= PHP_EOL;
                $this->output .=  '// ' . __('You can buy the plugin from http://wp.php-web.net/?p=275','acf') ;
                $this->output .= PHP_EOL;
            } else {
                $this->output .= sprintf($formatecho,$field['name']);
            }
            $this->output .= PHP_EOL;
            if ($conditional['after']) {
                $this->output .= $conditional['after'];
                $this->output .= PHP_EOL;
            }
        }
        return $this->output;
    }
    function get_field_name_from_key($fields,$fieldname) {
        foreach ($fields as $field2) {
            if ($fieldname == $field2['key']){
                return $field2['name'];
            }
        }
        return $fieldname;
    }
}
class Acfshortcode extends Tabdata {
    protected $output;
    function __construct() {
        $this->name = 'Acfsc';
        $this->label = __('Acf Short Code','custom-field-snippet');
    }
    function getdata() {
        global $acf;
        if (version_compare($acf->settings['version'],4,'>=')) {
            return $this->getdata_4later();
        } else {
            return $this->getdata_3();
        }
    }
    function getdata_4later() {
        global $post, $pagenow, $typenow;
        $output = '';
        $filter = array( 
            'post_id' => $post->id, 
            'post_type' => $typenow 
        );
        $metabox_ids = array();
        $metabox_ids = apply_filters( 'acf/location/match_field_groups', $metabox_ids, $filter );
        foreach ( $metabox_ids as $box) {
            $fields = apply_filters('acf/field_group/get_fields', array(), $box);
            $output = $this->output_field($fields);
        }

        return $output;
    }
    function getdata_3() {
        global $acf;
        global $post;
        $filter = array(
            'post_id' => $post->ID
        );
        if (class_exists('acf_location')) {
            $boxes = apply_filters( 'acf/location/match_field_groups', array(), $filter );
        } else {
            $boxes = $acf->get_input_metabox_ids($filter, false);
        }
        $output = '';
        foreach ( $boxes as $box) {
            $fields = $acf->get_acf_fields($box);
            $output .= $this->output_field($fields,$post->ID);
        }
        return $output;
    }
    function output_field($fields,$postid='') {
        if ('' == $postid){
            global $post;
            $postid = $post->ID;
        }
        $format = '[acf field="%s" post_id="%d"]';
        foreach ( $fields as $field ) {
            $this->output .= sprintf($format,$field['name'],$postid);
            $this->output .= PHP_EOL;
        }
        return $this->output;
    }
}

