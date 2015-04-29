<?php
/**
 * Wrapper  for BarCode Coder Library (BCC Library)
 *  BCCL Version 2.0
 *    
 *  Porting : jQuery barcode plugin 
 *  Version : 2.0.3
 *   
 *  Date    : 2013-01-06
 *  Author  : DEMONTE Jean-Baptiste <jbdemonte@gmail.com>
 *            HOUREZ Jonathan
 *             
 *  Web site: http://barcode-coder.com/
 *  dual licence :  http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html
 *                  http://www.gnu.org/licenses/gpl.html * 
 * @author Vilochane <vilochane@gmail.com>
 * @link GitHub https://github.com/Vilochane
 * @link yii http://www.yiiframework.com/forum/index.php/user/223499-vilo/
 */
namespace barcode\barcode;

use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\JsExpression;
use yii\web\View;

class BarcodeGenerator extends \yii\base\Widget {

    public $elementId; /* <div id="barcodeTarget" class="barcodeTarget"></div> OR <canvas id="canvasTarget" width="150" height="150"></canvas> */
    public $value;
    public $type; /* ean8, ean13, upc, std25, int25, code11, code39, code93, code128, codabar, msi, datamatrix */
    public $rectangular = false;
    public $settings = array();
    private $defaultSettings = array(
        'output' => 'css', /* css, bmp, svg, canvas */
        'bgColor' => '#FFFFFF', /* background color */
        'color' => '#000000', /* "1" Bars color */
        'barWidth' => 1,
        'barHeight' => 50,
        'moduleSize' => 5,
        'addQuietZone' => 0,
        'posX' => 10,
        'posY' => 20
    );

    /**
     *
     * @var type string plugin name
     */
    protected $_pluginName = 'BarcodeGenerator';

    /**
     *
     * @var type array options array
     */
    private $_displayOptions = [];

    public function init() {
        parent::init();
    }

    public function run() {
        $this->registerAssets();
    }


    /**
     * This function sets the barcode and options
     */
    private function setBarCode() {
        if (count($this->settings) > 0) {
            $this->defaultSettings = array_merge($this->defaultSettings, $this->settings);
        }
        $settings = Json::encode($this->defaultSettings);
        $output = $this->defaultSettings['output'];
        $value = $this->value;
        if ($this->rectangular === true) {
            $value = "{code: $this->value, rect: true}";
        }
        if ($output === 'canvas') {
            $initBarcode = 'clearCanvas(); $("#' . $this->elementId . '").show().barcode(value, type, settings);';
        } else {
            $initBarcode = '$("#' . $this->elementId . '").html("").show().barcode(value, type, settings);';
        }
        $js = "  var value = '$value';"
                . "var type = '$this->type';"
                . "var settings = $settings;"
                . "      function clearCanvas(){
                            var canvas = $('#" . $this->elementId . "').get(0);
                            var ctx = canvas.getContext('2d');
                            ctx.lineWidth = 1;
                            ctx.lineCap = 'butt';
                            ctx.fillStyle = '#FFFFFF';
                            ctx.strokeStyle  = '#000000';
                            ctx.clearRect (0, 0, canvas.width, canvas.height);
                            ctx.strokeRect (0, 0, canvas.width, canvas.height);
                          }"
                . "$initBarcode"
                . "";
        echo CHtml::tag('div', array('id' => $this->elementId));
        return $js;
    }

    /**
     * Registers the needed assets
     */
    public function registerAssets() {
        $view = $this->getView();
        BarcodeGenerator::register($view);
        $js = $this->setBarCode();
        $view->registerJs($js);
    }

}
