<?php
/**
 * BootstrapSelect2 class file.
 * @author Christoffer Niska <christoffer.niska@gmail.com>
 * @copyright Copyright &copy; Christoffer Niska 2013-
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @package yiistrap-widgets.widgets
 */

/**
 * Methods accessible through the 'TbWidget' class:
 * @method string resolveId($id = null)
 * @method string publishAssets($path, $forceCopy = false)
 * @method void registerCssFile($url, $media = '')
 * @method void registerScriptFile($url, $position = null)
 * @method string resolveScriptVersion($filename, $minified = false)
 * @method boolean registerPlugin($name, $selector, $options = array(), $position = CClientScript::POS_END)
 * @method boolean registerEvents($selector, $events, $position = CClientScript::POS_END)
 * @method CClientScript getClientScript()
 */
class BootstrapSelect2 extends CInputWidget
{
    /**
     * @var array raw data (key=>value).
     */
    public $data = array();

    /**
     * @var boolean whether to create a normal select element or a hidden field.
     */
    public $asDropDownList = true;

    /**
     * @var array initial options that should be passed to the plugin.
     */
    public $options = array();

    /**
     * @var string path to widget assets.
     */
    public $assetPath;

    /**
     * Initializes the widget.
     */
    public function init()
    {
        parent::init();
        Yii::import('bootstrap.behaviors.TbWidget');
        $this->attachBehavior('tbWidget', new TbWidget);
        if ($this->asDropDownList === false) {
            $this->data = $this->normalizeData($this->data);
        }
        if (!isset($this->assetPath)) {
            $this->assetPath = Yii::getPathOfAlias('vendor.ivaynberg.select2');
        }
        if (TbArray::popValue('block', $this->htmlOptions, false)) {
            TbHtml::addCssClass('input-block-level', $this->htmlOptions);
        }
    }

    /**
     * Runs the widget.
     */
    public function run()
    {
        list($name, $id) = $this->resolveNameID();
        $id = $this->resolveId();
        echo TbHtml::openTag('div', array('class' => 'dovre-select2'));
        if ($this->hasModel()) {
            if ($this->asDropDownList) {
                echo TbHtml::activeDropDownList($this->model, $this->attribute, $this->data, $this->htmlOptions);
            } else {
                echo TbHtml::activeHiddenField($this->model, $this->attribute, $this->htmlOptions);
            }
        } else {
            if ($this->asDropDownList) {
                echo TbHtml::dropDownList($name, $this->value, $this->data, $this->htmlOptions);
            } else {
                echo TbHtml::hiddenField($name, $this->value, $this->htmlOptions);
            }
        }
        echo '</div>';
        if ($this->asDropDownList === false && !isset($this->options['data'])) {
            $this->options['data'] = $this->data;
        }
        $options = !empty($this->options) ? CJavaScript::encode($this->options) : '';
        $this->publishAssets($this->assetPath);
        $this->registerCssFile('/select2.css');
        $this->registerScriptFile('/select2.js', CClientScript::POS_HEAD);
        $this->getClientScript()->registerScript(__CLASS__ . '#' . $id, "jQuery('#{$id}').select2({$options});");
    }

    /**
     * Normalized the given data into the format supported by select2.
     * @param array $rawData the raw data (key=>value).
     * @return array the normalized data.
     * @see http://ivaynberg.github.io/select2/
     */
    protected function normalizeData($rawData)
    {
        $data = array();
        foreach ($rawData as $key => $value) {
            if (is_array($value)) {
                $item = array('text' => $key, 'children' => $this->normalizeData($rawData[$key]));
            } else {
                $item = array('id' => $key, 'text' => $value);
            }
            $data[] = $item;
        }
        return $data;
    }
}