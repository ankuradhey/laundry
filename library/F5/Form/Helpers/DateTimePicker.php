<?php
/**
 * Base helper for form element dateTimePicker
 *
 * @author Darius Matulionis
 * @category Core
 * @package Core_View
 * @subpackage Helper
 *
 */
class F5_Form_Helpers_dateTimePicker extends ZendX_JQuery_View_Helper_UiWidget
{

    /**
     * @param String $id
     * @param String $value
     * @param array $params
     * @param array $attribs
     * @return String
     */
    public function dateTimePicker($id, $value = null, array $params = array(), array $attribs = array())
    {
        $attribs = $this->_prepareAttributes($id, $value, $attribs);

        $params2 = ZendX_JQuery::encodeJson($params);

        $pr = array();
        foreach ($params as $key => $val){
            $pr[] = '"'.$key.'":'.ZendX_JQuery::encodeJson ( $val );
        }
        $pr = '{'.implode(",", $pr).'}';

        $js = sprintf('%s("#%s").datetimepicker(%s);',
                ZendX_JQuery_View_Helper_JQuery::getJQueryHandler(),
                $attribs['id'],
                $pr
        );

        $this->jquery->addOnLoad($js);
        $this->jquery->addJavascriptFile('/tracersep/code/js/jquery-ui-timepicker-addon.js');

        return $this->view->formText($id, $value, $attribs);
    }
}

