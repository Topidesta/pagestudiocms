<?php (defined('BASEPATH')) OR exit('No direct script access allowed');
/**
 * CMS Canvas
 *
 * @author      Mark Price
 * @copyright   Copyright (c) 2012
 * @license     MIT License
 * @link        http://cmscanvas.com
 */

class Helper_plugin extends Plugin
{
    public function date()
    {
        if ($this->attribute('date') != '')
        {
            return date($this->attribute('format', 'm/d/Y'), strtotime($this->attribute('date')));
        }
        else
        {
            return date($this->attribute('format', 'm/d/Y'));
        }
    }

    public function site_url()
    {
        return site_url($this->attribute('path', ''));
    }

    public function base_url()
    {
        return base_url($this->attribute('path', ''));
    }

    public function current_url()
    {
        return current_url();
    }

    /**
     * Creates and image with <img /> tags
     * http://stackoverflow.com/questions/138313/how-to-extract-img-src-title-and-alt-from-html-using-php
     * https://gist.github.com/virua/11285153
     *
     * @access     public 
     * @treturn    string
     */
    public function image_thumb()
    {
        // -----------------------------------------
        // Additions by Cosmo Mathieu
        // Allows for the parsing of template tag {{ html_image }} variables
        // str_replace('"', "", $string);
        // str_replace("'", "", $string);
        // -----------------------------------------
        $src = '';
        $image = $this->attribute('image');
        if($this->attribute('source', 0) === 'true') {            
            preg_match_all('/(alt|title|src)=("[^"]*")/',$image, $img);
            foreach($img[1] as $key => $item) {
                switch($item) {
                    case 'src':
                        $src = str_replace('"', "", $img[2][$key]);
                        break;
                    case 'title':
                        $title = $img[2][$key];
                        break;
                    case 'alt':
                        $alt = $img[2][$key];
                        break;
                }
            }
            
            if( ! empty($img)) {
                $image = $src;
            }
        }
        // -----------------------------------------
        // End of additions
        // -----------------------------------------
        
        return image_thumb($image, $this->attribute('width', 0), $this->attribute('height', 0), $this->attribute('crop', FALSE));
    }

    public function uri_segment()
    {
        $CI =& get_instance();
        return $CI->uri->segment($this->attribute('segment'));
    }

    /**
     * Update: Added support for content found in template tags / {{ braces }}
     *         Allows for usage of:
     *         {{ helper:ellipsis length="200" words="true" }}
     *             {{ content }}
     *         {{ /helper:ellipsis }}
     *
     * @todo       ellipsis after words and not characters. 
     *
     * @author     Cosmo Mathieu <cosmo@cosmointeractive.co>
     * @return     string 
     */
    public function ellipsis($data)
    {
        // Recieve inherited data passed to plugin from parent plugin
        $CI =& get_instance();
        $CI->load->library('parser');
        $CI->load->helper('text');
        
        $content = $this->content();       
        $attributes = $this->attributes(); // the tag parameters
        $attributes['_content'] = $this->content(); // the tags e.g. {{ conent }}
        
        // Check if we received a string or a tag with {{ open-and-close-braces }}
        // Match the tag key in the $data array and parse the corresponding info.
        if ((strpos($content,'{{') && strpos($content,'}}'))!== false) {
            $array = array (
                '{{' => '',
                '}}' => ''
            );
            $tag = trim(strtr( $content, $array ));
            $parsed_content = $data[$tag];
        } else {
            $parsed_content = $CI->parser->parse_string($content, $data, TRUE);
        }
        
        return ellipsize($parsed_content, $this->attribute('length'));
    }

    public function code($data)
    {
        $content = $this->content();
        $content = str_replace('{{', '&#123;&#123;', $content);
        $content = str_replace('}}', '&#125;&#125;', $content);
        return $content;
    }
}

