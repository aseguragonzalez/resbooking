<?php




/**
 * Clase para los controles option de un control Select
 *
 * @author alfonso
 */
class SelectControlItem{

    /**
     * Valor del control Option
     * @var string
     */
    public $Value = "";

    /**
     * Texto del control Option
     * @var string
     */
    public $Text = "";

    /**
     * Constructor
     * @param string $text Texto a utilizar
     * @param string $value Valor asociado
     */
    public function __construct($text="", $value = ""){
        $this->Text = $text;
        $this->Value = $value;
    }
}
